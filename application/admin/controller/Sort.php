<?php
namespace app\admin\controller;

use app\common\model\UserEmp;
use think\Controller;

class Sort extends Common
{
    public function getList()
    {
        $list = model("Field")
            ->getFields();
        $this->success($list);
    }

    public function save($data){
        foreach($data as $k=>$v){
            model("Field")
                ->where([
                    'id'    =>  $v['id']
                ])
                ->update(['sort'=>$k]);
        }
        $this->success([],'保存成功！');
    }
}
