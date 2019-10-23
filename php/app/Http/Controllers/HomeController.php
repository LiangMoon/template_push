<?php

namespace App\Http\Controllers;

use App\Models\AccessTokenApi;
use App\Models\PushLog;
use App\Models\PushResultLog;
use App\Services\ExportService;
use App\Services\PthreadsService;
use App\Services\TemplateService;
use App\Services\WechatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public static $ErrorCode = [
        'OK' => 1,
        'Fail' => 0
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *  后台首页
     */
    public function index()
    {
        $oAccessTokenApi = new AccessTokenApi();
        $oAccounts = $oAccessTokenApi->getProjects();
        return view('admin.index', compact('oAccounts'));
    }

    /**
     *  模板推送页面
     */
    public function getPushPage()
    {
        $oAccessTokenApi = new AccessTokenApi();
        $oAccounts = $oAccessTokenApi->getProjects();
        return view('admin.push', compact('oAccounts'));
    }

    /**
     * 获取项目名下的模板消息
     */
    public function templateAll(Request $request)
    {
        $isSuccess = self::$ErrorCode['OK'];
        $sUrl = $request->input('url');
        if (empty($sUrl)) {
            $isSuccess = self::$ErrorCode['Fail'];
        }
        if ($isSuccess) {
            $oWechatService = new WechatService();
            $sAccessToken = $oWechatService->getAccessTokenByApi($sUrl);
            $isSuccess = $sAccessToken ? self::$ErrorCode['OK'] : self::$ErrorCode['Fail'];
        }
        $aTempInfo = [];
        if ($isSuccess) {
            $aTemplate = WechatService::getTemplate($sAccessToken);
            if (isset($aTemplate['template_list']) && $aTemplate['template_list']) {
                foreach ($aTemplate['template_list'] as $k => $v) {
                    $info = preg_match_all('/\{\{(.+?)\}\}/', $v['content'], $matches);
                    $aTempInfo[$k]['template_id'] = $v['template_id'];
                    $aTempInfo[$k]['title'] = $v['title'];
                    $aTempInfo[$k]['content'] = $v['content'];
                    $aTempInfo[$k]['tidycontent'] = $matches[0];
                }
            }
        }
        return response()->json([
            'success' => $isSuccess,
            'data' => $aTempInfo
        ]);
    }

    /**
     * 上传表格
     */
    public function uploadExcel(Request $request)
    {
        $file = $request->file('excel-upload');
        if (!$file) {
            return json_encode(array(
                'success' => false,
                'msg' => '上传失败，文件为空'
            ));
        }
        if (!$file->isValid()) return json_encode([
            'success' => false,
            'msg' => $file->getErrorMessage()
        ]);
        $sAttachFilename = $_FILES['excel-upload']['name'];
        $sSuffix = $file->getClientOriginalExtension();
        if (!in_array($sSuffix, array('csv'))) {
            return json_encode(array(
                'success' => false,
                'msg' => '请上传csv类型文件'
            ));
        }
        $sFileName = date('YmdHis', time()) . uniqid() . '.' . $sSuffix;
        $sPath = "/uploadfile/excelfile";
        $file->move(public_path($sPath), $sFileName);
        return json_encode([
            'success' => true,
            'msg' => '上传成功',
            'data' => [
                'path' => $sPath . '/' . $sFileName,
                'file_name' => $sAttachFilename
            ]
        ]);
    }

    /** 测试推送模板消息 */
    public function testSendTemplate(Request $request)
    {
        set_time_limit(0);
        $sProjectUrl = $request->input('wxplatform', 0);
        $sTemplateId = $request->input('template_id', 0);
        $aTemplateContent = $request->input('template_info');
        $sTemplateUrl = trim($request->input('template_url', ''));
        $sPushType = $request->input('push_type');
        $sTestOpenid = $request->input('test_openid');
        if ($sProjectUrl) {
            $aProjectId = AccessTokenApi::where('url', $sProjectUrl)->pluck('id')->toArray();
        }
        $iUserId = isset(Auth::user()->id) ? Auth::user()->id : '';
        PushLog::addPushLog($iUserId, $aProjectId[0], null, $sTemplateId); //添加推送信息记录
        $aTestOpenid = explode(',', $sTestOpenid);
        if(!empty($aTestOpenid)){
            session()->put('push_time', date('Y-m-d H:i:s'));   //保存推送时间，导出结果的时候用
            foreach ($aTestOpenid as $v) {
                TemplateService::pushTemplate($v, $sTemplateId, $aTemplateContent, $sTemplateUrl, $sProjectUrl, $sPushType);
            }
            return response()->json([
                'success' => true,
                'success_info' => '推送完毕',
                'error_info' => ''
            ]);
        }
        return response()->json([
            'success' => false,
            'success_info' => '',
            'error_info' => '操作失败，请刷新重试'
        ]);
    }

    /** 正式推送模板消息 */
    public function postSendTemplate(Request $request)
    {
        set_time_limit(0);
        $sProjectUrl = $request->input('wxplatform', 0);
        $sTemplateId = $request->input('template_id', 0);
        $aTemplateContent = $request->input('template_info');
        $sTemplateUrl = trim($request->input('template_url', ''));
        $sCsvPath = $request->input('excel_file', '');
        $sPushType = $request->input('push_type');
        if ($sCsvPath) {
            $sCsvPath = str_replace('_', '/', $sCsvPath);
            if ($sProjectUrl) {
                $aProjectId = AccessTokenApi::where('url', $sProjectUrl)->pluck('id')->toArray();
            }
            $iUserId = isset(Auth::user()->id) ? Auth::user()->id : '';
            PushLog::addPushLog($iUserId, $aProjectId[0], $sCsvPath, $sTemplateId); //添加推送信息记录
            $aUploadOpenIds = TemplateService::getOpenIdInfo($sCsvPath);//获取上传表格的openid
            $aOpenIds = array_unique($aUploadOpenIds);  //这里的aOPenIds要去掉重复的
            if(!empty($aOpenIds)){
                //开启多线程推送模板消息
                /*$aData = [
                    'sTemplateId' => $sTemplateId,
                    'aTemplateContent' => $aTemplateContent,
                    'sTemplateUrl' => $sTemplateUrl,
                    'sProjectUrl' => $sProjectUrl
                ];
                $iLength = count($aOpenIds);
                $iThreadWorks = 1000;   //1000个openid创建一个线程
                $iThreadNumbers = ceil($iLength / $iThreadWorks);  //要创建的线程数量
                $aThread = [];  //线程数组
                $aPushResult = [];  //线程推送结果数组
                for($i = 0; $i < $iThreadNumbers; $i++){
                    $aData['aOpenIds'] = array_slice($aOpenIds, $iThreadWorks*$i, $iThreadWorks);
                    $aThread[$i] = new PthreadsService($aData);
                    $aThread[$i]->start();
                }
                foreach ($aThread as $key => $val){
                    while ($aThread[$key]->isRunning()){
                        usleep(10);
                    }
                    if($aThread[$key]->join()){
                        $aPushResult[] = $aThread[$key]->aPushResult;
                    };
                }
                //将推送结果保存到数据库push_result_logs表
                foreach($aPushResult as $key => $val){
                    foreach ($val as $k => $v){
                        //使用队列
                        //PushResultLog::savePushResult($iUserId, $v[0], $sProjectUrl, $v[1]);
                        //不使用队列
                        PushResultLog::addPushResultLog($iUserId, $v[0], $sProjectUrl, $v[1]);
                    }
                }*/

                session()->put('push_time', date('Y-m-d H:i:s'));   //保存推送时间，导出结果的时候用
                foreach ($aOpenIds as $key => $val) {
                    if ($val) {//推送消息
                        TemplateService::pushTemplate($val, $sTemplateId, $aTemplateContent, $sTemplateUrl, $sProjectUrl, $sPushType);
                    }
                }
                return response()->json([
                    'success' => true,
                    'success_info' => '推送完毕',
                    'error_info' => ''
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'success_info' => '',
                    'error_info' => '操作失败，请刷新重试'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'success_info' => '',
            'error_info' => '操作失败，请刷新重试'
        ]);
    }

    /** 导出推送结果 */
    public function downloadRecords(Request $request)
    {
        $iUserId = isset(Auth::user()->id) ? Auth::user()->id : '';
        $sProjectUrl = $request->input('url');
        $sPushType = $request->input('push_type');
        $oPushResultLog = new PushResultLog();
        $aData = $oPushResultLog->pushRecord($iUserId, urldecode($sProjectUrl), $sPushType);
        $sPushType = $sPushType === '0' ? '测试推送' : '正式推送';
        ExportService::export($aData, $sPushType);
    }

    /** 获取微信公众号的关注用户 */
    public function fans()
    {
        $oWechatService = new WechatService();
        $sAccessToken = $oWechatService->getAccessTokenByApi('https://adhdhz.kydev.net/api/accesstoken');
        $aRes = getCurl('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$sAccessToken.'&next_openid=');
        $fp = fopen('F:\\DownLoad\\fans.csv','w');
        foreach ($aRes['data']['openid'] as $value){
            fwrite($fp, $value.",\n");
        }
        fclose($fp);
        return "save success";
    }
}
