<?php

namespace xjryanse\uniform\service\table;

use xjryanse\uniform\service\UniformRecordService;
/**
 * 分页复用列表
 */
trait PaginateTraits{
    
    /**
     * 查询当前小程序已填写的表单信息
     * 20230926:协企通，用户已报名的列表
     * @param type $con
     * @param type $order
     * @param type $perPage
     * @param type $having
     * @param type $field
     */
    public static function paginateForWeAppJoined($con = [], $order = '', $perPage = 10, $having = '', $field = "*") {
        $weAppOpenid = session(SESSION_OPENID);
        $tableIds = UniformRecordService::dimTableIdsByWeAppOpenid($weAppOpenid);

        $con[] = ['id','in',$tableIds];

        $lists = self::paginate($con, $order, $perPage, $having, $field);
        return $lists;
    }
    /**
     * 小程序展示（只显示状态开的）
     * @param type $con
     * @param type $order
     * @param type $perPage
     * @param type $having
     * @param type $field
     */
    public static function paginateForWeApp($con = [], $order = 'sort', $perPage = 10, $having = '', $field = "*"){
        $con[] = ['status','=',1];
        $resp = self::paginate($con, $order, $perPage, $having, $field);
        
        foreach($resp['data'] as &$v){
            if($v['timeState'] != 1){
                $v['mAddKey'] = '';
            }
        }
        return $resp;
    }

}
