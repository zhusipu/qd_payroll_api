<?php
namespace app\admin\controller;

use app\admin\model\UserEmp;
use think\Controller;

class User extends Common
{
    public function info()
    {
        $userEmp = new UserEmp();
        $info = $userEmp->exists($this->empNo,'EMP_NO');
        $this->success($info);
    }
}
