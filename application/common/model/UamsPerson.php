<?php
namespace app\common\model;
use think\Config;
use think\Db;
use think\Model;
use TimeCheer\Weixin\QYAPI\AccessToken;

class UamsPerson extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'person';
    // 设置当前模型的数据库连接
    protected $connection = "uams";

    public function getEmpNoByWxId($userid){
        $empno = $this->where(['id'=>$userid])->value('empNo');
        if($empno != null && $empno != false){
            return $empno;
        }else{
            return $this->where(['weixinId'=>$userid])->value('empNo');
        }
    }

}