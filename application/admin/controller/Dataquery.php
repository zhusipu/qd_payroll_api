<?php
namespace app\admin\controller;

use app\common\model\UserEmp;
use think\Controller;
use think\File;

class Dataquery extends Common
{
    public function getFields() {
        $list = model("Field")
            ->getFields();
        $this->success(
            [
                'fieldPrefix' => model("Field")->getPrefix(),
                'fields'    =>  $list
            ]
        );
    }

    public function getList($page=1,$limit=25)
    {
        $params = request()->param();
        $map = [];
        foreach ($params as $k=>$v){
            if(in_array($k,['page','limit'])){
                continue;
            }
            $map[$k] = ['like','%'.$v.'%'];
        }
        $list = model("Data")
            ->where(
                $map
            )
            ->page($page)
            ->limit($limit)
            ->select();
        $fields = model("Field")
            ->getFields();
        $decryptFields = [];
        foreach($fields as $k => $v){
            if($v['decryptType'] !== null) {
                $decryptFields[model("Field")->getFieldName($v)] = $v['decryptType'];
            }
        }

        foreach($decryptFields as $k=>$v){
            foreach($list as $kk=>$vv){
                $fun = 'decrypt_'.$v;
                $list[$kk][$k] = $fun($vv[$k]);
            }
        }
        $this->success([
            'list'  =>  $list,
            'total' =>  model("Data")
                ->where(
                    $map
                )->count()
        ]);
    }
}
