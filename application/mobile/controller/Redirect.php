<?php
namespace app\mobile\controller;
use app\index\model;
use think\Config;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Response;
use TimeCheer\Weixin\QYAPI\AccessToken;
use TimeCheer\Weixin\QYAPI\User;

class Redirect extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    
    public function index($code,$state){
        $corpId = Config::get('workwechat.CorpID');
        $appConfig = Config::get('workwechat.app');
        $accessToken = new AccessToken($corpId, $appConfig['Secret']);
        $User = new User($accessToken->get());
        $userInfo = $User->getuserinfo($code);
        if($userInfo == false) {
            $this->error($User->getErrorMsg());
        }
        if(!isset($userInfo['UserId'])){
            echo '请使用企业微信打开此链接！';
        }else{
            $empNo = Model('UamsPerson')->getEmpNoByWxId($userInfo['UserId']);
            model('UserEmp')->login($empNo);
            $this->redirect('http://'.$_SERVER['HTTP_HOST'].Config::get('mobile.url'));
        }

    }


}