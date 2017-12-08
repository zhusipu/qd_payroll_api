<?php
namespace app\admin\controller;

use app\common\model\UserEmp;
use think\Controller;
use think\File;

class Import extends Common
{
    public function upload($date){
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file
                ->validate(['ext'=>'xls,xlsx'])
                ->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){

                $reader = \PHPExcel_IOFactory::createReader('Excel2007');
                $PHPExcel = $reader->load(ROOT_PATH . 'public' . DS . 'uploads'.DS.$info->getSaveName());
                $objWorksheet = $PHPExcel->getActiveSheet();
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();

                $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

                //获取导入字段
                $fields = model("Field")
                    ->getFields();
                $pk = '';
                $pkFieldInfo = [];
                $fieldPrefix = model("Field")->getPrefix();
                $hrSourceFields = [];
                $formSourceFields = [];
                $chineseNameMapping = [];
                $commonData = [];
                $fieldEncryptList = [];

                foreach ($fields as $k=>$v){
                    if($v['isPk'] === 1){
                        $pk = $v['name'];
                        $pkFieldInfo = $v;
                    }
                    $chineseNameMapping[$v['chineseName']] = $v;
                    if($v['source'] === 1){
                        $hrSourceFields[] = $v;
                    }
                    if($v['source'] === 3){
                        $formSourceFields[] = $v;
                    }
                    if($v['encryptType'] != null) {
                        $fieldEncryptList[] = $v;
                    }
                }

                //获取来源表单的公共数据
                $params = request()->param();
                foreach($formSourceFields as $k=>$v){
                    if(!isset($params[$v['name']])){
                        continue;
                    }
                    $val = $params[$v['name']];
                    if($v['encryptType'] != null){
                        $fun = 'encrypt_'.$v['encryptType'];
                        $val = $fun($val);
                    }
                    $commonData[$v['name']] = $val;
                }

                //读取excel数据
                $excelHeaderMapping = [];
                for ($row = 1; $row <= $highestRow; $row++) {
                    $rowData = array();
                    for ($col = 0; $col < $highestColumnIndex; $col++) {
                        $rowData[] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    }

                    //表头
                    if($row === 1){
                        $excelHeaderMapping = $rowData;
                    }else{
                        $addData = [];
                        $pkVal = '';

                        //处理来源导入的数据 获取来源人力的主键
                        foreach ($rowData as $k=>$v){
                            $fieldInfo = [];
                            //检查是否存在此属性
                            if(isset($chineseNameMapping[$excelHeaderMapping[$k]])){
                                $fieldInfo = $chineseNameMapping[$excelHeaderMapping[$k]];
                            }
                            if($fieldInfo['isPk'] === 1){
                                $pkVal = $v;
                            }

                            if($fieldInfo['source'] === 0){
                                if($fieldInfo['encryptType'] != null){
                                    $fun = 'encrypt_'.$fieldInfo['encryptType'];
                                    $v = $fun($v);
                                }
                                $addData[$fieldPrefix.$fieldInfo['id']] = $v;
                            }
                        }

                        //处理来源人力资源的数据
                        $userInfo = model("UserEmp")->getUserInfo($pkVal);
                        foreach($hrSourceFields as $k=>$v){
                            if(!isset($userInfo[$v['name']])){
                                continue;
                            }
                            $val = $userInfo[$v['name']];
                            if($v['encryptType'] != null){
                                $fun = 'encrypt_'.$v['encryptType'];
                                $val = $fun($val);
                            }
                            $addData[$fieldPrefix.$v['id']] = $val;
                        }

                        //处理来源表单的数据
                        $addData = array_merge($addData,$commonData);

                        //加密处理
                        $where = $commonData;
                        $where[$fieldPrefix.$pkFieldInfo['id']] = $pkVal;
                        $isset = model("Data")
                            ->where($where)
                            ->find();
                        if($isset){
                            model("Data")
                                ->where($where)
                                ->update($addData);
                        }else{
                            model("Data")->insert($addData);
                        }
                    }
                }

                $this->success([],"上传成功");
            }else{
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }
    }
}
