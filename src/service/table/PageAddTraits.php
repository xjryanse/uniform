<?php

namespace xjryanse\uniform\service\table;

use xjryanse\universal\service\UniversalPageService;
use xjryanse\universal\service\UniversalItemFormService;
use xjryanse\uniform\service\UniformTableFieldService;
use xjryanse\logic\Arrays;
/**
 * 页面添加复用
 */
trait PageAddTraits {

    public static function defaultKeyPcList($table) {
        return 'p' . $table . 'List';
    }

    public static function defaultKeyPcDetail($table) {
        return 'p'.$table.'Detail';
    }

    public static function defaultKeyPcAdd($table) {
        return 'p'.$table.'Add';
    }

    public static function defaultKeyWList($table) {
        return 'w'.$table.'List';
    }

    public static function defaultKeyWMyList($table) {
        return 'w'.$table.'MyList';
    }

    public static function defaultKeyWDetail($table) {
        return 'w'.$table.'Detail';
    }

    public static function defaultKeyWAdd($table) {
        return 'w'.$table.'Add';
    }

    public static function defaultKeyMList($table) {
        return 'm'.$table.'List';
    }
    
    public static function defaultKeyMMyList($table) {
        return 'm'.$table.'MyList';
    }

    public static function defaultKeyMDetail($table) {
        return 'm'.$table.'Detail';
    }
    /**
     * 小程序添加
     * @param type $table
     * @return type
     */
    public static function defaultKeyMAdd($table) {
        return 'm'.$table.'Add';
    }
    
    protected static function pageAddMAdd($table){
        $pageName = self::defaultKeyMAdd($table);
        
        $fieldsArr  = UniformTableFieldService::tableFieldsArr($table);
        $fields     = array_column($fieldsArr, 'field');
        $items      = ['header','nav_bar','banner','describe','btn','form','btn'];
        $pageId     = UniversalPageService::savePage($pageName, $items, $fields);
        return $pageId;
    }
    
    protected static function pageAddPcList($table){
        $pageName = self::defaultKeyPcList($table);
        
        $fieldsArr  = UniformTableFieldService::tableFieldsArr($table);
        $fields     = array_column($fieldsArr, 'field');
        $items      = ['form','btn','table'];
        $pageId     = UniversalPageService::savePage($pageName, $items, $fields);
        return $pageId;
    }
    /**
     * 小程序个人列表页
     * @param type $table
     * @return type
     */
    protected static function pageAddMMyList($table){
        $pageName = self::defaultKeyMMyList($table);
        
        $fieldsArr  = UniformTableFieldService::tableFieldsArr($table);
        $fields     = array_column($fieldsArr, 'field');
        $items      = ['header','nav_bar','form','btn','list'];
        $pageId     = UniversalPageService::savePage($pageName, $items, $fields);
        return $pageId;
    }
    
    /**
     * 小程序详情页面
     * @param type $table
     * @return type
     */
    protected static function pageAddMDetail($table){
        $pageName = self::defaultKeyMDetail($table);
        
        $fieldsArr  = UniformTableFieldService::tableFieldsArr($table);
        $fields     = array_column($fieldsArr, 'field');
        $items      = ['header','nav_bar','detail'];
        $pageId     = UniversalPageService::savePage($pageName, $items, $fields);
        return $pageId;
    }
    /**
     * 
     */
    public static function defaultPageKey($tableName, $pageType){
        $keys = self::calDefaultPageKeys($tableName);

        return Arrays::value($keys, $pageType);
    }
    
}
