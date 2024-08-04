<?php

namespace xjryanse\uniform\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Datetime;
use xjryanse\universal\service\UniversalPageService;
use xjryanse\generate\service\GenerateTemplateService;
use Exception;
/**
 * 
 */
class UniformTableService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\uniform\\model\\UniformTable';

    use \xjryanse\uniform\service\table\TriggerTraits;
    // 分页
    use \xjryanse\uniform\service\table\PaginateTraits;
    use \xjryanse\uniform\service\table\PageAddTraits;    
    use \xjryanse\uniform\service\table\FieldTraits;
    use \xjryanse\uniform\service\table\DoTraits;
    use \xjryanse\uniform\service\table\CalTraits;


    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    // 表字段数
                    $tableFieldCount = UniformTableFieldService::groupBatchCount('table_id', $ids);
                    // 表记录数
                    $recordCount = UniformRecordService::groupBatchCount('table_id', $ids);
                    $universalItemTableCount = UniformUniversalItemTableService::groupBatchCount('table_id', $ids);
                    $universalItemFormCount = UniformUniversalItemFormService::groupBatchCount('table_id', $ids);
                    $universalItemDetailCount = UniformUniversalItemDetailService::groupBatchCount('table_id', $ids);

                    foreach ($lists as &$v) {
                        // 表单字段数 
                        $v['tableFieldCount']           = Arrays::value($tableFieldCount, $v['id'], 0);
                        // 是否有表单字段
                        $v['hasField']                  = $v['tableFieldCount'] ? 1 : 0;
                        // 表单记录数 
                        $v['recordCount']               = Arrays::value($recordCount, $v['id'], 0);
                        // 页面表格字段数 
                        $v['universalItemTableCount']   = Arrays::value($universalItemTableCount, $v['id'], 0);
                        // 页面表单字段数
                        $v['universalItemFormCount']    = Arrays::value($universalItemFormCount, $v['id'], 0);
                        // 页面详情字段数
                        $v['universalItemDetailCount']  = Arrays::value($universalItemDetailCount, $v['id'], 0);
                        // 20230911是否有后台菜单
                        $admMenu = self::calAdmMenu($v['table_no']);
                        $v['hasAdmMenu']                = $admMenu ? 1 :0;
                        // 20231106:后台菜单是否打开
                        $v['isAdmMenuOpen']             = $admMenu ? $admMenu['status'] : 0;
                        // 20230911是否有前台小程序菜单
                        $v['hasFrontWeAppMenu']         = self::calHasFrontWeAppMenu($v['table_no']);
                        // 20230911是否有前台公众号菜单
                        $v['hasFrontWePubMenu']         = self::calHasFrontWePubMenu($v['table_no']);
                        // 20230926：是否付费活动？
                        $v['needPrize']                 = $v['prize'] ? 1 : 0; 
                        //小程序添加页（入口）
                        $v['mAddKey']                   = 'm'.$v['table_no'].'Add';
                        $v['mMyListKey']                = 'm'.$v['table_no'].'MyList';     
                        // 20231019：给后台弹窗展示数据
                        $v['pcListKey']                 = 'p'.$v['table_no'].'List';
                        // 20230926：有开始时间
                        $v['hasStartTime']              = $v['start_time'] ? 1 : 0; 
                        // 20230926：有结束时间
                        $v['hasEndTime']                = $v['end_time'] ? 1 : 0; 
                        // 20231012：时间状态:0未开始；1进行中；2已结束
                        $v['timeState']                 = Datetime::calTimeState(date('Y-m-d H:i:s'), [$v['start_time'],$v['end_time']]);
                        
                        // 20231018:生成word模板id
                        $v['generateWordTplId']         = GenerateTemplateService::keyToId($v['table_no']);
                        // 20231018：有word模板？
                        $v['hasWordTpl']                = $v['generateWordTplId'] ? 1 : 0;
                    }

                    return $lists;
                });
    }
    /**
     * 表格提表key
     */
    protected static function tablePageKeys($tableName){
        $data = [];
        //PC列表
        $data['pcListKey']      = 'p'.$tableName.'List';
        $data['hasPcList']      = UniversalPageService::getByPageKey($data['pcListKey']) ? 1 : 0;
        //PC详情
        $data['pcDetailKey']    = 'p'.$tableName.'Detail';
        $data['hasPcDetail']    = UniversalPageService::getByPageKey($data['pcDetailKey']) ? 1 : 0;
        //PC添加
        $data['pcAddKey']       = 'p'.$tableName.'Add';
        $data['hasPcAdd']       = UniversalPageService::getByPageKey($data['pcAddKey']) ? 1 : 0;
        //手机列表
        $data['wListKey']       = 'w'.$tableName.'List';
        $data['hasWList']       = UniversalPageService::getByPageKey($data['wListKey']) ? 1 : 0;
        //手机详情
        $data['wDetailKey']     = 'w'.$tableName.'Detail';
        $data['hasWDetail']     = UniversalPageService::getByPageKey($data['wDetailKey']) ? 1 : 0;
        //手机添加
        $data['wAddKey']        = 'w'.$tableName.'Add';
        $data['hasWAdd']        = UniversalPageService::getByPageKey($data['wAddKey']) ? 1 : 0;
        //小程序列表
        $data['mListKey']       = 'm'.$tableName.'List';
        $data['hasMList']       = UniversalPageService::getByPageKey($data['mListKey']) ? 1 : 0;
        //小程序详情
        $data['mDetailKey']     = 'm'.$tableName.'Detail';
        $data['hasMDetail']     = UniversalPageService::getByPageKey($data['mDetailKey']) ? 1 : 0;
        //小程序添加
        $data['mAddKey']        = 'm'.$tableName.'Add';
        $data['hasMAdd']        = UniversalPageService::getByPageKey($data['mAddKey']) ? 1 : 0;
        
        return $data;
    }
    /**
     * 生成页面的方法
     * @param type $table
     * @param type $pageType
     * @return type
     * @throws Exception
     */
    public static function defaultPageGenerate($table, $pageType){
        $method     = 'pageAdd'.ucfirst(rtrim($pageType,'Key'));
        if(!method_exists(__CLASS__, $method)){
            throw new Exception('方法'.$method.'不存在');
        }
        return self::$method($table);
    }

    /**
     * 20230326:获取数据表id
     * @return string
     */
    public static function tableToId($tableNo) {
        $con[] = ['table_no', '=', $tableNo];
        $info = self::staticConFind($con);
        return $info ? $info['id'] : '';
    }

    /**
     * 20230403:根据数据表名，提取信息
     */
    public static function getByTableNo($tableNo) {
        $con[] = ['table_no', '=', $tableNo];
        return self::staticConFind($con);
    }

    /**
     * 提取数据表的字段
     */
    public function getFields() {
        $con[] = ['table_id', '=', $this->uuid];
        $fieldIds = UniformTableFieldService::staticConColumn('field_id', $con);
        $conField[] = ['id', 'in', $fieldIds];
        return UniformFieldService::staticConList($conField);
    }
    
    /**
     * 提取系统的默认页面
     * @param type $tableName
     */
    public static function defaultPages($tableName){
        $data['pcListKey']      = self::defaultKeyPcList($tableName);
        //PC详情
        $data['pcDetailKey']    = self::defaultKeyPcDetail($tableName);
        //PC添加
        $data['pcAddKey']       = self::defaultKeyPcAdd($tableName);
        //手机列表
        $data['wListKey']       = self::defaultKeyWList($tableName);
        //手机详情
        $data['wDetailKey']     = self::defaultKeyWDetail($tableName);
        //手机添加
        $data['wAddKey']        = self::defaultKeyWAdd($tableName);
        //小程序列表
        $data['mListKey']       = self::defaultKeyMList($tableName);
        // 小程序列表-我的
        $data['mMyListKey']     = self::defaultKeyMMyList($tableName);
        //小程序详情
        $data['mDetailKey']     = self::defaultKeyMDetail($tableName);
        //小程序添加
        $data['mAddKey']        = self::defaultKeyMAdd($tableName);
        
        $keys = array_keys($data);
        // 20230906各页面名称
        $names = [
            'pcListKey'     =>'pc端列表页',
            'pcDetailKey'   =>'pc端详情页',
            'pcAddKey'      =>'PC端添加',
            'wListKey'      =>'公众号列表',
            'wDetailKey'    =>'公众号详情',
            'wAddKey'       =>'公众号添加',
            'mListKey'      =>'小程序列表',
            'mMyListKey'    =>'小程序我的列表',
            'mDetailKey'    =>'小程序详情',
            'mAddKey'       =>'小程序添加',
        ];

        $arr = [];
        foreach($keys as $key){
            $tmp                = [];
            $tmp['table']       = $tableName;
            $tmp['pageType']    = $key;
            $tmp['pageKey']     = Arrays::value($data, $key);
            $tmp['pageName']    = Arrays::value($names, $key);
            $tmp['hasPage']     = UniversalPageService::getByPageKey($tmp['pageKey']) ? 1 : 0;
            $arr[] = $tmp;
        }

        return $arr;
    }
    
    /**
     * 生成新添加数据的id
     * @createTime 2023-08-31
     * @return string
     */
    public static function newAdd(){
        $tableNo = 'T'.session(SESSION_COMPANY_ID).'A001';
        $last  = self::where()->order('table_no desc')->find();
        if($last){
            // 去除后3位
            $preFix     = substr($last['table_no'], 0, -3);
            $number     = intval(substr($last['table_no'], -3)) + 1;
            $tableNo    = $preFix. sprintf('%03d',$number);
        }
        
        $data               = [];
        $data['table_no']   = $tableNo;

        return $data;
    }
    /**
     * 复制页面，替换字段
     * @param type $rawPageId   源页面
     */
    public function copyPage($rawPageId, $newPageKey){
        // $fields     = $this->getFields();
        $fields     = UniformTableFieldService::tableIdFieldsArr($this->uuid);
        $repArr = [];
        $repArr['cus_label']        = 'label';
        $repArr['field']            = 'field';
        $repArr['default_type']     = 'type';
        $repArr['default_option']   = 'option';
        $fieldArr               = Arrays2d::keyReplace($fields, $repArr);

        // 源页面信息
        $rawPageInfo = UniversalPageService::getInstance($rawPageId)->get();
        if(!Arrays::value($rawPageInfo, 'base_table')){
            throw new Exception('页面'.$rawPageId.'没有base_table');
        }
        // 当前表信息
        $replaceArr = [];
        $replaceArr[$rawPageInfo['base_table']] = $this->fTableNo();
        $res = UniversalPageService::getInstance($rawPageId)->copyPageReplaceField($newPageKey, $fieldArr, $replaceArr);
        return $res;
    }
    /**
     * 初始化word模板
     */
    public function initTemplateWord(){
        $info = $this->get();
        
        $data['file_key']   = $info['table_no'];
        $data['cate']       = 'word';
        return GenerateTemplateService::saveGetIdRam($data);
    }
}
