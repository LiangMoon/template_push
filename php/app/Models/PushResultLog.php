<?php

namespace App\Models;

use App\Jobs\SavePushResult;
use Illuminate\Database\Eloquent\Model;

class PushResultLog extends Model
{
    protected $table = 'push_result_logs';
    protected $guarded = ['id'];
    public $timestamps = true;

    public static function addPushResultLog($iUserId, $aData, $sProjectUrl, $aResult, $sPushType)
    {
        $oPushResultLog = new PushResultLog();
        $oPushResultLog->user_id = $iUserId;
        $oPushResultLog->receive_user_id = $aData ? $aData['touser'] : '';
        $oPushResultLog->template_id = $aData ? $aData['template_id'] : '';
        if ($sProjectUrl) {
            $aProjectId = AccessTokenApi::where('url', $sProjectUrl)->pluck('id')->toArray();
        }
        $oPushResultLog->project_id = $aProjectId[0];
        if ($aResult) {
            $oPushResultLog->push_result_status = $aResult['errcode'] == '0' ? 'success' : 'fail';
            $oPushResultLog->push_result_code = $aResult['errcode'];
            $oPushResultLog->push_result = json_encode($aResult);
        }
        $oPushResultLog->push_type = $sPushType;
        $oPushResultLog->save();
    }

    public static function savePushResult($iUserId, $aData, $sProjectUrl, $aResult)
    {
        $aSaveData = [];
        $aSaveData['user_id'] = $iUserId;
        $aSaveData['receive_user_id'] = $aData ? $aData['touser'] : '';
        $aSaveData['template_id'] = $aData ? $aData['template_id'] : '';
        if ($sProjectUrl) {
            $aProjectId = AccessTokenApi::where('url', $sProjectUrl)->pluck('id')->toArray();
        }
        $aSaveData['project_id'] = $aProjectId[0];
        if ($aResult) {
            $aSaveData['push_result_status'] = $aResult['errcode'] === 0 ? 'success' : 'fail';
            $aSaveData['push_result_code'] = $aResult['errcode'];
            $aSaveData['push_result'] = json_encode($aResult);
            $aSaveData['created_at'] = date("Y-m-d H:i:s");
            $aSaveData['updated_at'] = date("Y-m-d H:i:s");
        }
        dispatch(new SavePushResult($aSaveData));
    }

    public function pushRecord($iUserId, $sProjectUrl, $sPushType)
    {
        $aProjectId = AccessTokenApi::where('url', $sProjectUrl)->pluck('id')->toArray();
        //考虑一下，接连推送两次，怎么分别查找这两次的记录，从时间入手吗
        $oPushRecords = self::where('user_id',$iUserId)->where('project_id',$aProjectId[0])
            ->where('push_type',$sPushType)->where('created_at', '>=', session('push_time'))->get();
        $aData = [
            ['序号','用户openid','推送结果','状态返回码','结果消息','推送类型','推送时间']
        ];
        foreach ($oPushRecords as $key => $val){
            $aData[$key + 1]['rowId'] = $key + 1;
            $aData[$key + 1]['openid'] = $val->receive_user_id;
            $aData[$key + 1]['result'] = $val->push_result_status;
            $aData[$key + 1]['code'] = $val->push_result_code;
            $oPushResult = json_decode($val->push_result, true);
            $aData[$key + 1]['msg'] = $oPushResult['errmsg'];
            $aData[$key + 1]['push_type'] = $val->push_type === 0 ? '测试推送' : '正式推送';
            $aData[$key + 1]['time'] = $val->created_at;
        }
        return $aData;
    }
}
