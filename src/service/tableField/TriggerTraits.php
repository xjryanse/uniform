<?php

namespace xjryanse\uniform\service\tableField;

use xjryanse\logic\Arrays;
use xjryanse\uniform\service\UniformTableRecordService;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\universal\service\UniversalPageService;
use xjryanse\uniform\service\UniformFieldService;
/**
 * 分页复用列表
 */
trait TriggerTraits{
    /**
     * 停用
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
        if(Arrays::value($data, 'default_type') == 'enum' && !Arrays::value($data, 'default_option')){
            $tableId    = self::getInstance($uuid)->fTableId();
            $fieldId    = self::getInstance($uuid)->fFieldId();

            $tableNo    = UniformTableService::getInstance($tableId)->fTableNo();
            $field      = UniformFieldService::getInstance($fieldId)->fField();
            $data['default_option'] = $tableNo.'_'.$field;
        }
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
        $info = $this->get();
        
        $tableId = Arrays::value($info,'table_id');
        $fieldId = Arrays::value($info,'field_id');

        $recordCount = UniformTableRecordService::tableFieldRecordCount($tableId, $fieldId);
        // 字段有记录不删
        if($recordCount){
            throw new \Exception('该字段已有'.$recordCount.'条填报记录，请先删除');
        }
        // 字段有使用不删
        
        
        
        
        
        
        
    }

    /**
     * 钩子-删除后
     */
    public function ramAfterDelete() {
        
    }
}
