<?php

namespace xjryanse\uniform\service\table;

use xjryanse\logic\Arrays;
use xjryanse\uniform\service\UniformTableFieldService;
use xjryanse\universal\service\UniversalPageService;
use xjryanse\universal\service\UniversalPageItemService;
use xjryanse\universal\service\UniversalItemCellService;
use xjryanse\user\service\UserAuthAccessService;
use xjryanse\system\logic\ConfigLogic;
use Exception;
/**
 * 分页复用列表
 */
trait DoTraits{
    /**
     * 传入一个数量，初始化字段
     */
    public function doInitFields($param){
        $num = Arrays::value($param, 'num');
        if(!$num){
            throw new Exception('num必须');
        }
        if($num > 30){
            throw new Exception('最多支持30个字段');
        }

        // 已有字段的表不写
        $con = [];
        $con[] = ['table_id','=',$this->uuid];
        $count = UniformTableFieldService::count($con);
        if($count){
            throw new Exception('该表已有字段，不支持批量初始化'.$this->uuid);
        }

        $res = UniformTableFieldService::tableFieldInit($this->uuid, $num);   
        return $res;
    }
    
    /**
     * 复制页面，字段用本表
     * 20230909
     */
    public function doCopyPage($param){
        // 两个参数只传一个，或都传都可以，都传需匹配
        $rawPageId  = Arrays::value($param, 'rawPageId');
        $rawPageKey = Arrays::value($param, 'rawPageKey');
        if($rawPageKey){
            $pageId = UniversalPageService::keyToId($rawPageKey);
            if($rawPageId && $rawPageId != $pageId){
                throw new Exception('rawPageKey和rawPageId不匹配'.$rawPageKey);
            }
            $rawPageId = $pageId;
        }
        if(!$rawPageId){
            throw new Exception('rawPageId或rawPageKey必须');
        }

        $newPageKey = Arrays::value($param, 'newPageKey');
        if(!$newPageKey){
            throw new Exception('newPageKey必须');
        }
        // 执行复制逻辑
        return $this->copyPage($rawPageId, $newPageKey);
    }

    /**
     * 添加至后台菜单
     */
    public function doAddAdmMenu($param = []){
        $info       = $this->get();
        $tableNo    = Arrays::value($info, 'table_no');

        $keys       = self::calDefaultPageKeys($tableNo);  
        $pageKey    = Arrays::value($keys, 'pcListKey');
        if(!UniversalPageService::isPageKeyExist($pageKey)){
            throw new Exception('后台页面不存在，请先生成'.$pageKey);
        }

        $url    = '/Universal/'.Arrays::value($keys, 'pcListKey');
        $name   = Arrays::value($info, 'name');
        // 后台菜单父级
        $data = [];
        $data['pid'] = ConfigLogic::config('uniformPcAdmPid');
        
        return UserAuthAccessService::addAccess($name, $url,'manage',$data);
    }

    public function doAddFrontWeAppMenu($param = []){
        $info       = $this->get();
        $tableNo    = Arrays::value($info, 'table_no');

        $pageKey    = 'm26ManageHome';
        $pageId     = UniversalPageService::keyToId($pageKey);

        $con        = [];
        $con[]      = ['page_id','=',$pageId];
        $pageItemsIds = UniversalPageItemService::where($con)->column('id');

        $cone   = [];
        $cone[] = ['page_item_id','in',$pageItemsIds];
        $lastCellObj    = UniversalItemCellService::where($cone)->order('id desc')->find();
        $lastCell       = $lastCellObj ? $lastCellObj->toArray() : [];
        // 20230910:增加替换字符串
        $keys               = self::calDefaultPageKeys($tableNo);          
        $lastCell['id']     = self::mainModel()->newId();
        $lastCell['title']  = Arrays::value($info, 'name');
        $lastCell['url']    = '/pages/universal/index?pageId='.Arrays::value($keys, 'mAddKey');

        return UniversalItemCellService::saveGetId($lastCell);
        
    }
    /**
     * 20231106：关闭前台菜单
     */
    public function doToggleAdmMenu(){
        $tableNo = $this->fTableNo();
        $admMenu = self::calAdmMenu($tableNo);
        if(!$admMenu){
            throw new Exception('菜单不存在，请先生成');
        }
        // 1变0；0变1
        $status = $admMenu['status'] ? 0 : 1;
        // 开变关，关变开
        return UserAuthAccessService::getInstance($admMenu['id'])->updateRam(['status'=>$status]);
    }

    /**
     * 
     */
    public function doInitTemplateWord () {
        
        return $this->initTemplateWord();
    }

}
