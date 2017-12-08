<?php
namespace app\mobile\controller;
use Elasticsearch\ClientBuilder;
use org\apache\hadoop\WebHDFS;
use think\Config;
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
        $this->success($userInfo);
    }

    public function notAvatar(){
        echo file_get_contents('.'.DS.'static'.DS.'home'.DS.'images'.DS.'avatar.png');
    }

}