<?php
namespace app\admin\controller;

use app\common\model\UserEmp;
use think\Config;
use think\Controller;

class User extends Common
{
    public function info()
    {
        $userEmp = new UserEmp();
        $info = $userEmp->exists($this->empNo,'EMP_NO');
        $this->success($info);
    }

    public function resetPassword($empNo,$password){
        Model("UserExtra")->setPassword($empNo, $password);
        $this->success([],'设置密码成功！');
    }

    public function superResetPassword($returnUrl){
        $user = new \Bmzy\Uams\User(Config::get('uams'));
        $url = $user->getResetPassword($returnUrl,session('personid'));
        $this->success($url);
    }

    public function signout(){
        Model("UserEmp")->logout();
        $this->success();
    }

}
