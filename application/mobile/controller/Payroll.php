<?php
namespace app\mobile\controller;
use Gamegos\JWT\Exception\JWTException;
use Gamegos\JWT\Exception\JWTExpiredException;
use Gamegos\JWT\Validator;
use think\Config;
use think\Cookie;

/**
 * Created by PhpStorm.
 * User: TT
 * Date: 2017/8/28
 * Time: 14:44
 */
class Payroll extends Common{
    public function getList(){

        $twoToken = Cookie::get('twoToken');
        if($twoToken){
            try{
                $validator = new Validator();
                $validator->validate($twoToken,Config::get('jwt.key'));
            } catch (JWTException $e){
                //令牌过期
                $this->error(100);
            } catch (JWTExpiredException $e){
                $this->error(100);
            }
        }else{
            $this->error(100);
        }
        $data = Model("Data")->getList($this->empNo,1,3);
        $this->success($data);
    }

    public function setPassword ($cardNo, $password){
        $userExtraInfo = Model("UserExtra")->getInfo($this->empNo);
        if ($userExtraInfo['isSetPassword'] === 1) {
            $this->error('您已经设置过初始密码了，不能重新设置，请关闭应用后重新进入。');
        }
        $userInfo = Model("UserEmp")->getUserInfo($this->empNo);
        if($userInfo['ID_CODE'] != trim($cardNo)) {
            $this->error('您输入的身份证和系统不符，请重新输入');
        }
        Model("UserExtra")->setPassword($this->empNo, $password);
        $this->success([],'设置密码成功！');
    }
    
    public function getInfo($id) {
        $info = Model("Data")->getInfo($this->empNo,$id);
        if($info){
            $fields = Model("Field")
                ->where([
                    'status'    =>  1
                ])
                ->order('sort asc')
                ->select();
            $result = [];
            foreach($fields as $k=>$v){
                if($v['isHide'] === 1){
                    continue;
                }
                $fieldName = Model("FIeld")->getFieldName($v);
                if(isset($info[$fieldName])){
                    $val = $info[$fieldName];
                    if($v['decryptType'] != null){
                        $fun = 'decrypt_'.$v['decryptType'];
                        $val = $fun($val);
                    }
                    if($v['isNullHide'] === 1){
                        if($val == '' || $val == null || $val == 0){
                            continue;
                        }
                    }
                    $result[] = [
                        'id'  =>  $v['id'],
                        'name'  =>  $v['chineseName'],
                        'val'   =>  $val
                    ];
                }
            }
            $this->success($result);
        }else{
            $this->error('未找到该条数据');
        }
    }

}