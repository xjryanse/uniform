<?php

namespace xjryanse\uniform\logic;

use xjryanse\generate\service\GenerateTemplateService;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\uniform\service\UniformRecordService;
use xjryanse\uniform\service\UniformTableRecordService;
use xjryanse\universal\service\UniversalPageService;
use xjryanse\universal\service\UniversalItemTableService;
use think\Db;
use xjryanse\logic\Strings;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use think\facade\Request;
use Exception;

/**
 * 
 */
class UniformTableLogic {

    use \xjryanse\traits\InstTrait;

    protected function tableNo() {
        $tableNo = $this->uuid;
        if (Strings::isSnowId($this->uuid)) {
            $tableNo = UniformTableService::getInstance($this->uuid)->fTableNo();
        }
        return $tableNo;
    }

    /**
     * 表信息
     * @return type
     */
    protected function tableInfo() {
        $con[] = ['table_no', '=', $this->tableNo()];
        return UniformTableService::staticConFind($con);
    }

    /**
     * 数据表sql
     */
    protected function tableSql() {
        $tableNo = $this->tableNo();
        $table = UniformTableRecordService::tableSql($tableNo);
        $recordTable = UniformRecordService::tableSql($tableNo);

        return '(select * from ' . $recordTable . ' as baseTable left join ' . $table . ' as mainTable on baseTable.id = mainTable.record_id) as recordTable';
    }

    public function dbTable() {
        return Db::table($this->tableSql());
    }

    /**
     * 20230328:封装get
     * @param type $id
     * @return type\
     */
    public function get($id, $param = []) {
        $info = Db::table($this->tableSql())->where('id', $id)->find();
        // 20230718数据处理
        $uPageId   = Arrays::value($param, 'uPageId','');
        $tableNo    = Arrays::value($param, 'table','');
        if($uPageId){
            $info = self::dataDealGet($info, $uPageId, $tableNo);
        }
        
        return $info;
    }

    /**
     * 查询全部数据（一般用于导出）
     * @param type $con
     * @param type $order
     * @param type $field
     */
    public function list($con = [], $order = '', $field = '*') {
        $res = Db::table($this->tableSql())->where($con)->order($order)->field($field)->select();
        return $res;
    }
    
    /**
     * 统计数量
     * @param type $con
     * @param type $order
     * @param type $field
     */
    public function count($con = []) {
        $res = Db::table($this->tableSql())->where($con)->count();
        return $res;
    }
    /**
     * 20230329：分页列表
     * @param type $con
     * @param type $order
     * @param type $perPage
     * @param type $having
     * @param type $field
     * @param type $withSum
     * @return type
     */
    // ($con = [], $order = '', $perPage = 10, $having = '', $field = "*", $withSum = false) 
    public function paginate($con = [], $order = '', $perPage = 10, $param = []) {
        // 20230717:入参提取
        $having     = '';
        $field      = Arrays::value($param, 'field','*');
        $withSum    = false;
        $uTableId   = Arrays::value($param, 'uTableId','');
        $tableNo    = Arrays::value($param, 'table','');
        
        $page = Request::param('page', 1);
        $start = ($page - 1) * intval($perPage);

        $res = Db::table($this->tableSql())->where($con)->order($order)->field($field)->limit($start, intval($perPage))->select();
        //20220619：如果查询结果数小于分页条数，则结果数即总数
        $total = $page == 1 && count($res) < $perPage ? count($res) : Db::table($this->tableSql())->where($con)->count(1);
        // 采用跟TP框架一样的数据格式
        $resp['data'] = $res;
        
        $resp['data'] = self::dataDealArr($res, $uTableId, $tableNo);

        $resp['current_page'] = $page;
        $resp['total'] = $total;
        $resp['per_page'] = intval($perPage);
        $resp['last_page'] = ceil($resp['total'] / intval($perPage));
//        $resp['$con']           =   $con;
//        $resp['$tableSql']           =   $this->tableSql();

        return $resp;
    }

    /**
     * 20230331 数据删除
     * @param type $ids
     * @return type
     */
    public function del($ids) {
        UniformRecordService::checkTransaction();
        $tableNo = $this->tableNo();
        // 删主记录
        UniformRecordService::tableDataDeleteBatch($tableNo, $ids);
        // 删衍生明细
        return UniformTableRecordService::tableDataDeleteBatch($tableNo, $ids);
    }

    public function save($data) {
        UniformRecordService::checkTransaction();
        $tableNo = $this->tableNo();
        $id = UniformRecordService::tableDataSaveGetId($tableNo, $data);
        return UniformTableRecordService::tableDataSave($tableNo, $data, $id);
    }

    /**
     * 20230331：信息生成并下载
     * @param type $templateKey
     * @return string
     */
    public function infoGenerateDownload($templateKey, $id) {
        UniformTableRecordService::tableSql();

        $data = $this->get($id);

        $data['yearmonthDate'] = date('Y年m月d日', strtotime($data['create_time']));

        $templateId = GenerateTemplateService::keyToId($templateKey);
        if(!$templateId){
            throw new Exception('模板文件key'.$templateKey.'不存在');
        }

        $res = GenerateTemplateService::getInstance($templateId)->generate($data);
        // http会被谷歌浏览器拦截无法下载
        $respData['url'] = Request::domain() .'/'. $res['file_path'];
        //文件名带后缀
        $tableInfo = $this->tableInfo();
        $respData['fileName'] = Strings::dataReplace($tableInfo['word_name'], $data) . '.docx';

        return $respData;
    }

    /**
     * 20230717：处理属性数据(数组)
     * @param type $data
     */
    protected static function dataDealArr($data, $uTableId, $tableNo) {
        // 20230717:提取字段明细
        $picFields = UniversalItemTableService::typeFields($uTableId, $tableNo, 'uplimage');
        /* 图片字段提取 */
        if ($picFields) {
            $data = Arrays2d::picFieldCov($data, $picFields);
        }
        return $data;
    }

    /**
     * 20230717：处理属性数据(单条)
     * @param type $data
     */
    protected static function dataDealGet($data, $uPageId, $tableNo) {
        // 20230717:提取字段明细
        $picFields = UniversalPageService::getInstance($uPageId)->typeFields( $tableNo, 'uplimage');
        /* 图片字段提取 */
        if ($picFields) {
            $data = Arrays::picFieldCov($data, $picFields);
        }
        return $data;
    }

}
