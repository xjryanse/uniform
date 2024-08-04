<?php

namespace xjryanse\uniform\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use Exception;
/**
 * 
 */
class UniformTableFieldService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\uniform\\model\\UniformTableField';

    use \xjryanse\uniform\service\tableField\PaginateTraits;
    use \xjryanse\uniform\service\tableField\FieldTraits;
    use \xjryanse\uniform\service\tableField\TriggerTraits;

    /**
     * 【ok】校验字段是否属于表
     * @param type $table       表名或表id
     * @param type $fieldId    字段id
     * @return type
     */
    public static function checkField($table, $fieldId) {
        $tableId = UniformTableService::tableToId($table);
        $con[] = ['table_id', '=', $tableId];
        $con[] = ['field_id', '=', $fieldId];
        return self::where($con)->find();
    }

    /*
     * 搜索字段：
     * 形如：
     * ['like']=>['aa','bb','cc','dd','ee']
     * ['equal']=>['aa','bb','cc','dd','ee']
     */

    public static function getSearchFields($tableNo) {
        $tableId = UniformTableService::tableToId($tableNo);
        $con[] = ['table_id', '=', $tableId];
        $tableFields = self::staticConList($con);
        // 提取字段对象
        $fieldsArr = UniformFieldService::staticConList();
        $fieldsObj = array_column($fieldsArr, 'field', 'id');
        //拼接查询条件
        $searchFields = [];
        foreach ($tableFields as $v) {
            if ($v['search_type']) {
                $searchFields[$v['search_type']][] = Arrays::value($fieldsObj, $v['field_id']);
            }
        }
        return $searchFields;
    }
    /**
     * 20230831：表名提取字段
     * @param type $tableNo
     * @return type
     */
    public static function tableFieldsArr($tableNo) {
        $tableId = UniformTableService::tableToId($tableNo);
        return self::tableIdFieldsArr($tableId);
    }
    /**
     * 20230906:数据表id,提取字段
     * @param type $tableId
     * @return type
     */
    public static function tableIdFieldsArr($tableId) {
        $con[] = ['table_id', '=', $tableId];
        $tableFields = self::staticConList($con);
        // 提取字段对象
        $fieldsArr = UniformFieldService::staticConList();
        $fieldsObj = array_column($fieldsArr, 'field', 'id');
        foreach($tableFields as &$v){
            $v['field'] = Arrays::value($fieldsObj, $v['field_id']);
        }
        return $tableFields;
    }
    /**
     * 初始化
     */
    public static function tableFieldInit($tableId, $rowCount){
        $fields     = [];
        for($i=1; $i<= $rowCount; $i++){
            // 拼接字段名
            $fieldName = 'uf_'.sprintf("%03d", $i);
            $fieldId = UniformFieldService::fieldToId($fieldName);
            if(!$fieldId){
                throw new Exception($fieldName.'字段不存在，请联系开发');
            }
            $fields[] = ['table_id'=>$tableId,'field_id'=>$fieldId];
        }
        // 批量保存
        return self::saveAllRam($fields);
    }
    /**
     * 表单的字段数
     * @param type $tableId
     * @return type
     */
    public static function tableFieldCount($tableId){
        $con = [];
        $con[] = ['table_id','=',$tableId];
        return self::where($con)->count();
    }

}
