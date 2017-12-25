<?php
namespace app\mobile\controller;

use think\Config;
use think\Controller;
use TimeCheer\Weixin\QYAPI\AccessToken;
use TimeCheer\Weixin\QYAPI\JsapiTicket;

class Index extends Controller
{
    public function getWxConfig($url){
        $corpId = Config::get('workwechat.CorpID');
        $appConfig = Config::get('workwechat.app');
        $accessToken = new AccessToken($corpId, $appConfig['Secret']);
        $jsapiTicket = new JsapiTicket($accessToken->get());
        $config = $jsapiTicket->get($url);
        $config['appId'] = $corpId;
        $config['beta'] = true;
        $config['debug'] = true;
        $config['jsApiList'] = ['hideOptionMenu'];
        $this->result($config,0,0,'json');
    }
}
