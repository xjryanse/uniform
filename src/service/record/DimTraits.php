<?php

namespace xjryanse\uniform\service\record;

/**
 * 按维度查询
 */
trait DimTraits{
    /*
     * 用户id，提取用户有申报的记录列表
     * @param type $pageItemId
     * @return type
     */
    public static function dimTableIdsByWeAppOpenid($weAppOpenid){
        $con    = [];
        $con[]  = ['we_app_openid','=',$weAppOpenid];
        return self::column('distinct table_id', $con);
    }
}
