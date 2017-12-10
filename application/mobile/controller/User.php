<?php
namespace app\mobile\controller;
use Elasticsearch\ClientBuilder;
use Gamegos\JWT\Encoder;
use Gamegos\JWT\Token;
use org\apache\hadoop\WebHDFS;
use think\Config;
use think\Cookie;
use think\Url;

/**
 * Created by PhpStorm.
 * User: TT
 * Date: 2017/8/28
 * Time: 14:44
 */
class User extends Common{
    public function getUid(){
        $this->success($this->getEmpNo());
    }

    public function getInfo(){
        $userInfo = model("UserEmp")->getUserInfo($this->empNo);
        $userInfo['extra'] = model("UserExtra")->getInfo($this->empNo);
        $this->success($userInfo);
    }

    public function notAvatar(){
        echo file_get_contents('.'.DS.'static'.DS.'home'.DS.'images'.DS.'avatar.png');
    }

    public function twoSignin($password) {
        $result = Model("UserExtra")
            ->checkPassword($this->empNo,$password);
        if($result){
            $token = new Token();
            $token->setClaim('sub','two');
            $token->setClaim('exp',time() + 15 * 60);
            $encoder = new Encoder();
            $encoder->encode($token,Config::get('jwt.key'),Config::get('jwt.alg'));

            Cookie::init(Config::get('cookie'));
            Cookie::set('twoToken',$token->getJWT());
            $this->success($token->getJWT());
        } else {
            $this->error('密码错误！');
        }
    }

    public function GetTwoTokenInfo(){
        $this->success(Cookie::get('twoToken'));

    }
}