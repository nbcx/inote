<?php
/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-7-18
 * Time: 上午11: 32
 * UEditor编辑器通用上传类
 */
namespace util;

use nb\Access;

/**
 * Class Uploader
 * @package util
 *
 * @property string name  新文件名
 * @property string url   资源的完整URL地址
 * @property string original   原始文件名
 * @property string ext   文件扩展名
 * @property string mime
 * @property string path 配置中转换后的name值
 * @property string width 图片类资源的宽
 * @property string height 图片类资源的高
 */
class Uploader extends Access {

    //private $fileField; //文件域名
    //private $file; //文件上传对象
    //private $base64; //文件上传对象

    private $oriName;  //原始文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    public  $size; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息


    //上传状态映射表，国际化用户需考虑此处数据的国际化
    private $stateMap = [
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确",
        "INVALID_URL" => "非法 URL",
        "INVALID_IP" => "非法 IP"
    ];

    //配置信息
    private $config = [
        //文件访问url前戳
        'url'=>'/',
        //文件保存位置前戳
        'path'=>__APP__.'uploads',
        //上传保存路径,可以自定义保存路径和文件名格式
        'name'=>'image/{yyyy}{mm}{dd}/{time}{rand:6}',
        //上传大小限制，单位B
        'max'=>20971520,
        //允许上传文件的格式
        'allow'=>[".png", ".jpg", ".jpeg", ".gif", ".bmp"]
    ];

    /**
     * 构造函数
     * @param string $fileField 表单名称
     * @param array $config 配置项
     * @param bool $base64 是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     */
    public function __construct($config=[]) {
        $config and $this->config = $config;
    }

    /**
     * 修改上传配置
     *
     * @param array $config
     */
    public function config(array $config) {
        $this->config = array_merge(
            $this->config,
            $config
        );
        return $this;
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    public function upload($file) {
        $this->file = $file;// =  = $_FILES[$this->fileField];
        if (!$file) {
            $this->code = 'ERROR_FILE_NOT_FOUND';
            return;
        }
        if ($this->file['error']) {
            $this->code = $file['error'];
            return;
        }
        else if (!file_exists($file['tmp_name'])) {
            $this->code = 'ERROR_TMP_FILE_NOT_FOUND';
            return;
        }
        else if (!is_uploaded_file($file['tmp_name'])) {
            $this->code = 'ERROR_TMPFILE';
            return;
        }

        $this->original  = $file['name'];
        $this->size = $file['size'];
        $this->mime = $file['type'];

        //$this->fileType = $this->getFileExt();
        //$this->fullName = $this->path;
        //$this->filePath = $this->getFilePath();
        //$this->name = $this->getFileName();

        $dirname = dirname($this->fullPath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->code = 'ERROR_SIZE_EXCEED';
            return false;
        }

        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->code = 'ERROR_TYPE_NOT_ALLOWED';
            return false;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->code = 'ERROR_CREATE_DIR';
            return false;
        }
        else if (!is_writeable($dirname)) {
            $this->code = 'ERROR_DIR_NOT_WRITEABLE';
            return false;
        }

        //移动文件
        if(move_uploaded_file($file["tmp_name"], $this->fullPath) && file_exists($this->fullPath)) {
            //移动成功
            $this->stateInfo = $this->stateMap[0];
            return $this;
        }
        //移动失败
        $this->code = "ERROR_FILE_MOVE";
        return false;
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    public function base64($base64Data) {
        //$base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);

        $this->oriName = $this->config['oriName'];
        $this->fileSize = strlen($img);

        $this->fileType = $this->getFileExt();

        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        }
        else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        }
        else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }
        return true;
    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    public function remote($imgUrl) {
        $imgUrl = htmlspecialchars($imgUrl);//$this->fileField
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return;
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo("INVALID_URL");
            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo("INVALID_IP");
            return;
        }

        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allow']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            ['http' => [
                'follow_location' => false // don't follow redirects
            ]]
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match('/[\/]([^\/]*)[\.]?[^\.\/]*$/', $imgUrl, $m);

        $this->oriName = $m ? $m[1]:"";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();

        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        }
        else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }
        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        }
        else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }
        return $this->info();
    }

    /**
     * 上传错误信息
     * @return mixed
     */
    protected function _error() {
        $errCode = $this->code;
        return !$this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    protected function _ext() {
        return strtolower(strrchr($this->original, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    protected function _path() {
        //替换日期事件
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $this->config['name'];
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName, 0, strrpos($this->original, '.'));
        $oriName = preg_replace('/[\|\?\"\<\>\/\*\\\\]+/', '', $oriName);
        $format = str_replace("{filename}", $oriName, $format);

        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match('/\{rand\:([\d]*)\}/i', $format, $matches)) {
            $format = preg_replace('/\{rand\:[\d]*\}/i', substr($randNum, 0, $matches[1]), $format);
        }
        if(!$format) {
            $format = $this->config['dir'];
        }
        return $format.$this->ext;
    }

    /**
     * 获取文件名
     * @return string
     */
    protected function _name() {
        return substr(
            $this->filePath,
            strrpos($this->filePath, '/') + 1
        );
    }

    /**
     * 完整URL地址
     * @return string
     */
    protected function _url() {
        return $this->config['url'].$this->path;
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    protected function _fullPath() {
        return $this->config['path'].$this->path;
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType() {
        return in_array($this->ext, $this->config["allow"]);
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function  checkSize() {
        return $this->fileSize <= ($this->config["max"]);
    }

    /**
     * 获取图片文件的宽高
     * @return array|bool
     */
    private function fileWH() {
        if(in_array($this->ext,[".png", ".jpg", ".jpeg", ".gif", ".bmp"])) {
            $size = getimagesize($this->fullPath);
            return $size;
        }
        return [0,0];
    }

    protected function _width() {
        list($width,$this->height) = $this->fileWH();
        return $width;
    }

    protected function _height() {
        list($this->width,$height) = $this->fileWH();
        return $height;
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function info() {
        $info = [
            'state' => $this->stateInfo,
            'code' => 200,
            'path' => $this->fullName,
            'url' => $this->url,
            'name' => $this->name,
            'original' => $this->oriName,
            'ext' => $this->fileType,
            'size' => $this->fileSize,
            'full'=>$this->filePath
        ];
        return $info;
    }

}