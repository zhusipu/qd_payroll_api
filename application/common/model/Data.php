<?php
namespace app\common\model;
use think\Db;
use think\Model;

class Data extends Model
{
    public function getList($pkValue, $page, $limit, $order = 'createTime desc'){
        $pk = Model("Field")->getFieldPk();
        $list = $this
            ->where([
                $pk => $pkValue
            ])
            ->page($page, $limit)
            ->order($order)
            ->select();
        $total = $this
            ->where([
                $pk => $pkValue
            ])->count();
        return [
            'list'  =>  $list,
            'total' =>  $total
        ];

    }
    public function getInfo($pkValue, $id){
        $pk = Model("Field")->getFieldPk();
        return $this
            ->where([
                $pk => $pkValue,
                'id'    =>  $id
            ])
            ->find();

    }
}
