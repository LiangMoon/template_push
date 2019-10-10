<?php

namespace App\Services;

use App\Models\PushResultLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TemplateService
{
    /**
     * 执行模板消息推送
     */
    public static function pushTemplate($sOpenid, $sTemplateId, $aTemplateContent, $sTemplateUrl = '', $sProjectUrl, $sPushType)
    {
        $aContent = array();
        $iCount = count($aTemplateContent);
        if ($iCount > 1) {
            $sFirst = $aTemplateContent[0];
            $sRemark = $aTemplateContent[$iCount - 1];
            array_shift($aTemplateContent);
            array_pop($aTemplateContent);
            if (!empty($aTemplateContent)) {
                //获取内容数组的中间值
                foreach ($aTemplateContent as $k => $v) {
                    $aContent['keyword' . ($k + 1)] = ['value' => $v, 'color' => '#173177'];
                }
            }
            $aContent['first'] = ['value' => $sFirst, 'color' => '#173177'];
            $aContent['remark'] = ['value' => $sRemark, 'color' => '#173177'];
        }
        $aData = array(
            'touser' => $sOpenid,
            'template_id' => $sTemplateId,
            'url' => isset($sTemplateUrl) ? $sTemplateUrl : '',
            'data' => $aContent
        );
        $sData = json_encode($aData);
        $aResult = self::pushResult($sProjectUrl, $sData);
        //Log::info($sOpenid);
        //Log::info($aResult);
        $iUserId = isset(Auth::user()->id) ? Auth::user()->id : '';
        PushResultLog::addPushResultLog($iUserId, $aData, $sProjectUrl, $aResult, $sPushType);
        return true;
    }

    /* 最终发送推送模板消息请求 */
    public static function pushResult($sUrl, $sData)
    {
        $oWechatService = new WechatService();
        $sAccessToken = $oWechatService->getAccessTokenByApi($sUrl);
        $aResult = $oWechatService->sendTemplate($sAccessToken, $sData);
        return $aResult;
    }

    /* 根据文件路径读取文件openid信息 */
    public static function getOpenIdInfo($sFilePath)
    {
        $sFilePath = public_path() . $sFilePath;
        $sFilePath = str_replace('\\', '/', $sFilePath);
        if (!file_exists($sFilePath)) {
            return false;
        }
        $rHandle = fopen($sFilePath, "r");
        //导入的所有openid
        $aOpenIds = array();
        while ($aData = fgetcsv($rHandle)) {
            foreach ($aData as $k => $v) {
                $res = preg_match('/^o[\w\-]{27}/', $v, $matches);
                if($res == 1){
                    $aOpenIds[] = $v;
                }
            }
        }
        fclose($rHandle);
        return $aOpenIds;
    }
}