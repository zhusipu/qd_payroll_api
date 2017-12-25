<?php
namespace app\admin\controller;

use app\common\model\UserEmp;
use Bmzy\Uams\User;
use think\Config;
use think\Controller;

class Signin extends Common
{
    public function index($username = '', $password = '', $remember = false)
    {
        $vUser = new \app\admin\validate\User();
        if(!$vUser->check([
            'username'  =>  $username,
            'password'  =>  $password,
            'remember'  =>  $remember
        ])) {
            $this->error($vUser->getError());
        }
        $User = new User(Config::get('uams'));
        $result = $User->login($username, $password);
        if(!$result){
            $this->error($User->getErrorMsg());
        }else{
            $UserEmp = new UserEmp();
            session('personid',$result['personid']);
            //查看人力资源库有没有该用户
            $info = $UserEmp->getUserInfo($result['empNo']);
            if($info){
                $userExtra = model("UserExtra")->getInfo($info['EMP_NO']);
                if($userExtra['isSuperAdmin'] != 1){
                    $this->error('您无权登录此系统');
                }
                $this->success($UserEmp->login($info['EMP_NO']),'登陆成功!');
            }else{
                $this->error('本地系统中不存在此用户');
            }
        }
    }
}
