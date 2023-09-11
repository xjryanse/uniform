<?php
namespace xjryanse\uniform\model;

/**
 * 
 */
class UniformTableField extends Base
{
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;

    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'table_id',
            'uni_name'  =>'uniform_table',
            'uni_field' =>'id',
            'del_check' => true
        ],
        [
            'field'     =>'field_id',
            'uni_name'  =>'uniform_table',
            'uni_field' =>'id',
            'del_check' => true
        ],
    ];
}