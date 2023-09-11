<?php

namespace xjryanse\uniform\service\table;

use xjryanse\logic\Arrays;
use xjryanse\user\service\UserAuthAccessService;
use xjryanse\universal\service\UniversalItemCellService;
/**
 * 计算类1
 */
trait CalTraits{
    /**
     * 计算是否有后台菜单
     * @param type $tableNo
     * @return int
     */
    public static function calHasAdmMenu($tableNo){
        $keys = self::calDefaultPageKeys($tableNo);        
        
        $url = '/Universal/'.Arrays::value($keys, 'pcListKey');
        
        $con    = [];
        $con[]  = ['url','=',$url];
        return UserAuthAccessService::staticConFind($con) ? 1: 0;
    }
    /**
     * 计算是否有小程序前台菜单
     * @param type $tableNo
     * @return int
     */
    public static function calHasFrontWeAppMenu($tableNo){
        // 提取后台小程序页面
        
        // 提取page_item_id

        // 
        $keys   = self::calDefaultPageKeys($tableNo);
        $url    = '/pages/universal/index?pageId='.Arrays::value($keys, 'mAddKey');

        $con    = [];
        $con[]  = ['url','=',$url];
        return UniversalItemCellService::staticConFind($con) ? 1: 0;
    }

    /**
     * 计算是否有公众号前台菜单
     * @param type $tableNo
     * @return int
     */
    public static function calHasFrontWePubMenu($tableNo){
        
        return 0;
    }
    
    /**
     * 
     */
    public static function calDefaultPageKeys($tableName){
        $keys                   = [];
        $keys['pcListKey']      = 'p'.$tableName.'List';
        $keys['pcDetailKey']    = 'p'.$tableName.'Detail';
        //PC添加
        $keys['pcAddKey']       = 'p'.$tableName.'Add';
        //手机列表
        $keys['wListKey']       = 'w'.$tableName.'List';
        //手机详情
        $keys['wDetailKey']     = 'w'.$tableName.'Detail';
        //手机添加
        $keys['wAddKey']        = 'w'.$tableName.'Add';
        //小程序列表-我的
        $keys['mMyListKey']       = 'm'.$tableName.'MyList';
        //小程序列表
        $keys['mListKey']       = 'm'.$tableName.'List';
        //小程序详情
        $keys['mDetailKey']     = 'm'.$tableName.'Detail';
        //小程序添加
        $keys['mAddKey']        = 'm'.$tableName.'Add';
        
        return $keys;
    }
}
