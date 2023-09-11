<?php
namespace xjryanse\uniform\model;

/**
 * 
 */
class UniformTable extends Base
{
        //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;

    
    public function setFilesAttr($value) {
        return self::setImgVal($value);
    }

    public function getFilesAttr($value) {
        return self::getImgVal($value);
    }
    
    public function setFile2Attr($value) {
        return self::setImgVal($value);
    }

    public function getFile2Attr($value) {
        return self::getImgVal($value);
    }
}