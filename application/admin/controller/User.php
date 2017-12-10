<?php
namespace app\admin\controller;

use app\common\model\UserEmp;
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

}
