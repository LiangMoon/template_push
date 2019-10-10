<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WechatService
{
    //const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token';
    const GET_TEMPLATE_URL = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=';

    /**
     * 获取微信的access_token
     */
    /*public static function getAccessToken($oAccount)
    {
        $sAccessToken = Cache::get($oAccount->access_token_name);
        if (!Cache::has($oAccount->access_token_name)) {
            //调用接口
            $aData = [
                'grant_type' => 'client_credential',
                'appid' => $oAccount->appid ?: '',
                'secret' => $oAccount->secret ?: ''
            ];
            // $appid = $oAccount->appid ?: '';
            // $secret = $oAccount->secret ?: '';
            // $sUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
            // Log::info('55555555555');
            // Log::info($sUrl);
            // $aResult = getCurl($sUrl);
            // Log::info($aResult);

            $aResult = httpGet(self::GET_ACCESS_TOKEN_URL, $aData);
            if (isset($aResult['errcode'])) {
                Log::info('获取' . $oAccount->access_token_name . 'access_token接口错误，错误码' . $aResult['errcode'] . '，错误信息：' . $aResult['errmsg']);
                return $aResult;
            } else {
                //存入缓存，时间为分钟数，提前10分钟获取
                Cache::put($oAccount->access_token_name, $aResult['access_token'], (($aResult['expires_in'] - 600) / 60));
                $sAccessToken = $aResult['access_token'];
            }
        }
        return $sAccessToken;
    }*/

    public function getAccessTokenByApi($sUrl)
    {
        $aResult = getCurl($sUrl);
        return isset($aResult['access_token']) ? $aResult['access_token'] : '';
    }

    public static function getTemplate($sAccessToken)
    {
        $sUrl = self::GET_TEMPLATE_URL . $sAccessToken;
        $res = getCurl($sUrl);
        return $res;
    }

    public function sendTemplate($sAccessToken, $sData)
    {
        $sUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $sAccessToken;
        $sResult = postCurl($sUrl, $sData);
        return $sResult;
    }
}