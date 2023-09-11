<?php

namespace xjryanse\uniform\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Strings;
use xjryanse\uniform\logic\UniformTableLogic;

/**
 * 
 */
class UniformRecordService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\uniform\\model\\UniformRecord';

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    foreach ($lists as &$v) {
                        
                    }
                    return $lists;
                });
    }

    public static function extraPreSave(&$data, $uuid) {
        $wordName = UniformTableService::getInstance($data['table_id'])->fWordName();
        $data['record_name'] = self::getRecordName($wordName, $data);
    }

    /**
     * 20230405
     * @param array $data
     * @param type $uuid
     */
    public static function extraPreUpdate(&$data, $uuid) {
        $tableId = self::getInstance($uuid)->fTableId();
        $info = UniformTableLogic::getInstance($tableId)->get($uuid);
        $infoNew = array_merge($info, $data);
        // 记录名称
        $wordName = UniformTableService::getInstance($infoNew['table_id'])->fWordName();

        $data['record_name'] = self::getRecordName($wordName, $infoNew);
    }

    /**
     * 获取记录名称
     */
    public static function getRecordName($wordName, $data) {
        return Strings::dataReplace($wordName, $data);
    }

    public static function tableSql($tableNo = '') {
        //1、检测是否传入了表名/表id
        if (empty($tableNo)) {
            return '';
        }
        //2、获取表字段
        $tableId = UniformTableService::tableToId($tableNo);
        $con[] = ['table_id', '=', $tableId];
        //4，返回查询语句
        return self::where($con)->buildSql();
    }

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
     * 数据保存提取id
     * @param type $tableNo
     * @param array $data
     */
    public static function tableDataSaveGetId($tableNo, $data) {
        self::checkTransaction();
        $data['table_id'] = UniformTableService::tableToId($tableNo);
        return self::saveGetId($data);
    }

    public static function tableDataDeleteBatch($tableNo, $ids = []) {
        self::checkTransaction();
        $tableId = UniformTableService::tableToId($tableNo);
        $con[] = ['table_id', '=', $tableId];
        $con[] = ['id', 'in', $ids];
        return self::where($con)->delete();
    }
    /**
     * 表单的记录数
     * 一般用于控制删除
     */
    public static function tableRecordCount($tableId){
        $con = [];
        $con[] = ['table_id','=',$tableId];
        return self::where($con)->count();
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

    public function fTableId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 字段名
     */
    public function fField() {
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

}
