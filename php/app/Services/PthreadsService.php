<?php

namespace App\Services;

class PthreadsService extends \Thread
{
    protected $aOpenIds;    //要推送的openid
    protected $sTemplateId;     //模板消息id
    protected $aTemplateContent;    //模板消息内容
    protected $sTemplateUrl;    //模板消息的url
    protected $sProjectUrl;     //推送项目获取access_token的url
    protected $oWechatService;
    public $aPushResult;    //保存推送结果

    public function __construct($aData)
    {
        $this->aOpenIds = $aData['aOpenIds'];
        $this->sTemplateId = $aData['sTemplateId'];
        $this->aTemplateContent = $aData['aTemplateContent'];
        $this->sTemplateUrl = $aData['sTemplateUrl'];
        $this->sProjectUrl = $aData['sProjectUrl'];
        $this->oWechatService = new WechatService();
    }

    public function run()
    {
        $aContent = $this->handleContent($this->aTemplateContent);
        $aPushResult = [];
        foreach ($this->aOpenIds as $key => $val) {
            //推送消息
            $aPushResult[] = $this->pushTemplate($val, $this->sTemplateId, $aContent, $this->sTemplateUrl, $this->sProjectUrl);
        }
        $this->aPushResult = $aPushResult;
    }

    //处理模板消息的内容
    protected function handleContent($aTemplateContent)
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
        return $aContent;
    }

    //推送模板消息
    protected function pushTemplate($sOpenid, $sTemplateId, $aContent, $sTemplateUrl = '', $sProjectUrl)
    {
        $aData = array(
            'touser'      => $sOpenid,
            'template_id' => $sTemplateId,
            'url'         => isset($sTemplateUrl) ? $sTemplateUrl : '',
            'data'        => $aContent
        );
        $sData = json_encode($aData);
        $aResult = $this->pushResult($sProjectUrl, $sData);
        return [$aData, $aResult];
    }

    //真正推送消息的方法
    protected function pushResult($sProjectUrl, $sData)
    {
        $sAccessToken = $this->oWechatService->getAccessTokenByApi($sProjectUrl);
        $sResult = $this->oWechatService->sendTemplate($sAccessToken, $sData);
        return $sResult;
    }
}