<?php
namespace app\common\model;
use think\Db;
use think\Model;

class Field extends Model
{
    protected $fieldPrefix = 'f';
    public function getFields() {
        return $this
            ->where(
                [
                    'status'    =>  '1'
                ]
            )
            ->order('sort asc')
            ->select();
    }
    public function getPrefix(){
        return $this->fieldPrefix;
    }

    public function getFieldPk () {
        $pk = null;
        $pkInfo = $this->where(['isPk' => 1])->find();
        if($pkInfo){
            $pk = $this->getFieldName($pkInfo);
        }

        if($pk === null){
            $pk = 'EMP_NO';
        }
        return $pk;
    }
    
    public function getFieldName($field){
        switch ($field['source']) {
            case 0:
            case 1:
                return $this->getPrefix().$field['id'];
            case 3:
                return $field['name'];
        }
    }
}
