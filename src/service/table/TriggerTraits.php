<?php

namespace xjryanse\uniform\service\table;

use xjryanse\uniform\service\UniformRecordService;
use xjryanse\uniform\service\UniformTableFieldService;

use Exception;
/**
 * 分页复用列表
 */
trait TriggerTraits{

    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }

    public static function extraPreUpdate(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }
    
    public function extraPreDelete() {
        self::stopUse(__METHOD__);
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
        // 判断是否有记录，有记录不让删
        $recordCount = UniformRecordService::tableRecordCount($this->uuid);
        if($recordCount){
            throw new Exception('该表单已有'.$recordCount.'条填写记录，不可删');
        }
        // 判断是否有字段，有字段不让删
        $fieldCount = UniformTableFieldService::tableFieldCount($this->uuid);
        if($fieldCount){
            throw new Exception('为避免误操作，请先删除'.$fieldCount.'个关联字段');
        }
    }

    /**
     * 钩子-删除后
     */
    public function ramAfterDelete() {
        
    }
}
