<?php

namespace xjryanse\uniform\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\wechat\service\WechatWePubFansUserService;
/**
 * 
 */
class UniformTableManagerService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\uniform\\model\\UniformTableManager';

    public static function tableIdManagerIds($tableId, $con = []) {
        $con[] = ['table_id', '=', $tableId];
        $con[] = ['status', '=', 1];
        $userIds = self::staticConColumn('manager_id', $con);
        return $userIds;
    }

    public static function tableIdOpenids($tableId){
        $con[]      = ['wx_tpl_notice','=',1];
        $userIds    = self::tableIdManagerIds($tableId, $con);
        $cond[] = ['user_id', 'in', $userIds];
        // $cond[] = ['user_id','=',$info['seller_user_id']];
        $openids = WechatWePubFansUserService::column('openid', $cond);
        return $openids;
    }
    /**
     * 表key处理
     * @param type $tableNo
     * @param type $con
     * @return type
     */
    public static function tableManagerIds($tableNo, $con = []) {
        $tableId = UniformTableService::tableToId($tableNo);
        
        return self::tableIdManagerIds($tableId, $con);
    }

    public static function tableOpenids($tableNo){
        $tableId = UniformTableService::tableToId($tableNo);
        
        return self::tableIdOpenids($tableId);
    }
    
}
