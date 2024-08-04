<?php

namespace xjryanse\uniform\service\tableField;

use xjryanse\logic\ModelQueryCon;
use xjryanse\logic\DataList;
use xjryanse\logic\Arrays2d;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\universal\service\UniversalPageService;
use xjryanse\wechat\service\WechatWeAppQrSceneService;
use xjryanse\uniform\service\UniformTableDefaultPageService;
use Exception;
/**
 * 分页复用列表
 */
trait PaginateTraits{

    public function tableFieldPagePaginate($con, $order = '', $perPage = 10, $having = '', $field = "*", $withSum = false){
        // $fields = self::tableIdFieldsArr();
        $tableId   = ModelQueryCon::parseValue($con, 'table_id');
        if(!$tableId){
            throw new Exception('table_id必须');
        }

        $fields = self::tableIdFieldsArr($tableId);
        $tableName = UniformTableService::getInstance($tableId)->fTableNo();
        
        $lists  = UniformTableDefaultPageService::lists();

        $data = [];
        foreach($lists as $v){
            $data[$v['key']] = UniformTableService::defaultPageKey($tableName, $v['key']);
        }

        $arrList = [];
        $tmp = [];
        $tmp['pageType']    = '';
        $tmp['table']       = $tableName;
        $tmp['table_id']    = $tableId;
        $tmp['pageKey']     = '字段标签';
        $tmp['hasPage']     = -1;
        $tmp['pageId']      = '';
        $tmp['weAppQrSceneId']      = '';
        $tmp['hasWeAppQrScene']      = '';
        
        $tmp['pageCate']    = '';

        foreach ($fields as $f) {
            $tmp[$f['field']] = $f['cus_label'];
        }
        $arrList[] = $tmp;

        // 拼接页面和字段    
        foreach($data as $k=>$v) {
            $tmp            = [];
            $tmp['pageType']= $k;
            $tmp['table']   = $tableName;
            $tmp['table_id']= $tableId;
            $tmp['pageKey'] = $v;
            $tmp['hasPage'] = UniversalPageService::getByPageKey($tmp['pageKey']) ? 1 : 0;
            $tmp['pageId']  = UniversalPageService::keyToId($tmp['pageKey']);
            $tmp['pageCate']  = UniversalPageService::getInstance($tmp['pageId'])->fCate();
            $tmp['weAppQrSceneId']      = WechatWeAppQrSceneService::fromTableIdToId($tmp['pageId']);
            $tmp['hasWeAppQrScene']     = $tmp['weAppQrSceneId'] ? 1 : 0;

            
            $pageId = UniversalPageService::keyToId($v);
            $standardFields = UniversalPageService::getInstance($pageId)->standardFields();

            foreach ($fields as $f) {
                $con    = [];
                $con[]  = ['itemType','=','form'];
                $con[]  = ['field','=',$f['field']];
                // 单条
                $tObj = Arrays2d::listFind($standardFields, $con);
    
                $tmp[$f['field']] = $tObj ? $tObj['label'].'-'. $tObj['type'] : '';
            }

            $arrList[] = $tmp;
        }

        $pgLists        = DataList::dataPackPaginate($arrList, false, [], $perPage, $thisPage);

        foreach ($fields as $va) {
            // 20230604:控制前端页面显示的动态站点:字段格式：universal_item_table表
            $pgLists['fdynFields'][] = ['id' => self::mainModel()->newId(), 'name' => $va['field'], 'label' => $va['field'], 'type' => 'text'];
        }
        
        return $pgLists;
    }
    
}
