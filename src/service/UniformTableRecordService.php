<?php

namespace xjryanse\uniform\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 
 */
class UniformTableRecordService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\uniform\\model\\UniformTableRecord';

    /**
     * 钩子-保存前
     */
    public static function ramPreSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-保存后
     */
    public static function ramAfterSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新前
     */
    public static function ramPreUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新后
     */
    public static function ramAfterUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-删除前
     */
    public function ramPreDelete() {
        
    }

    /**
     * 钩子-删除后
     */
    public function ramAfterDelete() {
        
    }

    /**
     * 20230326:提取数据表记录
     * @param type $table
     * @param type $having
     * @param type $order
     * @return boolean
     */
    public static function tableSql($tableNo = '') {
        //1、检测是否传入了表名/表id
        if (empty($tableNo)) {
            return '';
        }
        //2、获取表字段
        $tableId = UniformTableService::tableToId($tableNo);
        $fields = UniformTableService::getInstance($tableId)->getFields();
        //3、组织查询字段
        // $field[] = 'table_id';
        $field[] = 'record_id';
        foreach ($fields as &$v) {
            $field[] = "MAX(CASE field_id when '" . $v['id'] . "' then value else '" . $v['default_value'] . "' end) '" . $v['field'] . "'";
        }
        $con[] = ['table_id', '=', $tableId];
        //4，返回查询语句
        return self::where($con)->field($field)
                        ->group('record_id')->buildSql();
    }

    /*
     * 20230326:表数据更新
     * @param type $table       表名
     * @param type $data        保存数据
     * @param type $recordId    保存的记录id
     * @return boolean
     */

    public static function tableDataSave($table, $data, $recordId) {
        self::checkTransaction();
        $dataArr = self::dataArrCov($table, $data, $recordId);
        if (!$dataArr) {
            return false;
        }
        // 20230405:清除已有的记录
        $tableId = UniformTableService::tableToId($table);
        $con[] = ['table_id', '=', $tableId];
        $con[] = ['record_id', '=', $recordId];
        $con[] = ['field_id', 'in', array_column($dataArr, 'field_id')];
        self::where($con)->delete();

        self::saveAll($dataArr);
        return $recordId;
    }

    /**
     * 20230331：数据批量删除
     * @param type $tableNo       
     * @param type $ids
     */
    public static function tableDataDeleteBatch($tableNo, $ids = []) {
        self::checkTransaction();
        $tableId = UniformTableService::tableToId($tableNo);
        $con[] = ['table_id', '=', $tableId];
        $con[] = ['record_id', 'in', $ids];
        return self::where($con)->delete();
    }

    /**
     * 【ok】数据转化(一维转二维)
     * @param type $table
     * @param type $data
     * @return type
     */
    protected static function dataArrCov($table, $data, $recordId) {
        //1、获取表单自增id
        if (!$data) {
            return [];
        }

        $res = [];
        foreach ($data as $k => &$v) {
            $tmp = [];
            $tmp['field_id'] = UniformFieldService::fieldToId($k);
            if (!UniformTableFieldService::checkField($table, $tmp['field_id'])) {
                continue;
            }
            //清除已有的记录：
            $tmp['table_id'] = UniformTableService::tableToId($table);
            $tmp['record_id'] = $recordId;
            $tmp['value'] = &$v;

            $res[] = $tmp;
        }

        return $res;
    }

    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 表编号
     */
    public function fTableId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 字段编号
     */
    public function fFieldId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 记录编号
     */
    public function fRecordId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 记录值
     */
    public function fValue() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /*
     * 计算字段是否有记录
     */
    public static function tableFieldRecordCount($tableId, $fieldId){
        $con    = [];
        $con[]  = ['table_id','in',$tableId];
        $con[]  = ['field_id','in',$fieldId];
        $res    = UniformTableRecordService::where($con)->count();
        return $res;
    }
    
}
