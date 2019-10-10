<?php
/** 添加URL参数
 * @param string $url 链接
 * @param array $query_data
 */
function addUrlQueryData($url, array $query_data)
{
    $query = http_build_query(array_wrap($query_data));
    if (!$query) return $url;

    if (strpos($url, '?')) {
        $url .= '&' . $query;
    } else {
        $url .= '?' . $query;
    }
    return $url;
}


/** 从字符串中匹配URL
 * @param $str
 * @return bool
 */
function findUrlFromStr($str)
{
    $pattern = '/http[s]?:\/\/[a-zA-Z0-9\_\.\-\%\&\?\+\=\~]+\.[com|cn|net|org|xyz|us|top|jp|gov|edu]+[a-zA-Z0-9\/\_\%\&\?\+&amp;%\$#\=~]*/';

    if (preg_match_all($pattern, $str, $matches, PREG_SET_ORDER)) {
        return $matches;
    }
    return false;
}

/**
 * post curl
 * @param $sUrl 请求地址
 * @param $aData 携带参数
 * @return array
 */
function postCurl($sUrl, $aData)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $aData);
    ob_start();
    curl_exec($ch);
    $content = ob_get_clean();
    curl_close($ch);
    return json_decode($content, true);
}

/**
 * 功能：get请求
 * @param $sUrl 请求地址
 * @return array
 */
function getCurl($sUrl)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}

function httpGet($url, $data = '')
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    if ($data) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    $res = curl_exec($curl);
    curl_close($curl);
    return json_decode($res, true);
}

//浏览器判断
// 判断是否是微信浏览器
function isWechat()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

function userAgentIsWeixin()
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    if (preg_match('/micromessenger/', $agent)) {
        return true;
    }
    return false;
}

// 判断访问来源是否为移动设备
function userAgentIsMobile()
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    if (preg_match('/iphone|android|ipad|windows phone|micromessenger/', $agent)) {
        return true;
    }
    return false;
}

// 判断访问来源是否为安卓设备
function userAgentIsAndroid()
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    if (preg_match('/android/', $agent)) {
        return true;
    }
    return false;
}

//判断访问来源是否为app应用
function userAgentIsApp()
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    if (preg_match('/hybrid/', $agent)) {
        return true;
    }
    return false;
}

// 判断浏览器是否为ie
function isIe()
{
    $useragent = strtolower($_SERVER ['HTTP_USER_AGENT']);
    if ((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
    if (strpos($useragent, 'msie ') !== false) return true;
    return false;
}

// 判断是否是ajax请求
function isAjax()
{
    $res = false;
    if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
        $res = true;
    }
    return $res;
}

// 判断浏览器类型' WX:微信浏览器，wap：手机其他浏览 器，PC：PC浏览器
function sourceType()
{
    @$agent = strtolower($_SERVER ['HTTP_USER_AGENT']);
    @$agents = strtolower($_SERVER ['HTTP_VIA']);
    $agent = strtolower($agent);//转换为小写
    $is_pc = (strpos($agent, 'windows nt')) ? true : false;
    //判断是否是在微信浏览器中打开
    $is_weixin = (strpos($agent, 'micromessenger')) ? true : false;
    //设备
    $is_iphone = ((strpos($agent, 'iphone')) || (strpos($agent, 'iph'))) ? true : false;
    $is_ipad = (strpos($agent, 'ipad')) ? true : false;
    $is_ipod = (strpos($agent, 'ipod')) ? true : false;
    $is_android = ((strpos($agent, 'android')) || (strpos($agent, 'adr'))) ? true : false;
    $is_wap = (strpos($agents, 'wap')) ? true : false;//网站登录
    $is_app = (strpos($agents, 'app')) ? true : false; //App登录
    if ($is_weixin) {
        return 'WX';
    }
    if ($is_iphone || $is_ipad || $is_ipod || $is_android) {
        return 'MOBILE';
    }
    if ($is_wap) {
        return 'WAP';
    }
    if ($is_app) {
        return 'APP';
    }
    return 'PC';
}

// 判断访问来源是否为手机
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;// 找不到为flase,否则为TRUE
    }
    // 判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'mobile',
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    if (isset ($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

//计算两个时间之间的差值
function timeDiff($begin_time, $end_time)
{
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    //86400 = 60*60*24
    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
}

//cookie方法封装
/*function setCookie($var, $value = '', $time = 0, $key = '')
{
    $time = $time > 0 ? $time : ($value == '' ? time() - 3600 : 0);
    $s    = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
    if (!$key) {
        $var = $var;
    }
    $_COOKIE[$var] = $value;
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            setCookie($var . '[' . $k . ']', $v, $time, '/', '', $s);
        }
    } else {
        setCookie($var, $value, $time, '/', '', $s);
    }
}*/

function getCookie($var, $key = '')
{
    if (!$key) {
        $var = $var;
    }
    return isset($_COOKIE[$var]) ? $_COOKIE[$var] : false;
}


//处理百度编辑器录入的图片成为绝对路径，一般出接口需要使用
//富文本中的<src>过滤添加域名
function addSrcUrl($content = '')
{
    $contents = preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/', function ($r) {
        $str = 'http://' . $_SERVER['HTTP_HOST'] . $r[1];
        return str_replace($r[1], $str, $r[0]);
    }, $content);
    return $contents;
}

//生成随机字符串
function createNonceStr($length = 16)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

//下载文件
function downloadWechatFile($sUrl)
{
    $resourceCh = curl_init($sUrl);
    curl_setopt($resourceCh, CURLOPT_HEADER, 0);
    curl_setopt($resourceCh, CURLOPT_NOBODY, 0); //只取body头
    curl_setopt($resourceCh, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($resourceCh, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($resourceCh, CURLOPT_RETURNTRANSFER, 1);
    $package = curl_exec($resourceCh);
    $httpinfo = curl_getinfo($resourceCh);
    curl_close($resourceCh);
    $imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));
    return $imageAll;
}

/**
 * 获取星期
 * @return mixed|string
 */
function getWeek($time)
{
    $w = date('w', strtotime($time));
    $array = array(
        0 => '星期日',
        1 => '星期一',
        2 => '星期二',
        3 => '星期三',
        4 => '星期四',
        5 => '星期五',
        6 => '星期六'
    );
    if (isset($array[$w])) return $array[$w];
    return '';
}

function uploadsPath($path = '')
{
    return public_path('uploads') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

//将秒数转换为时间（年、天、小时、分、秒）
function sec2Time($time)
{
    if (is_numeric($time)) {
        $value = array(
            "years" => 0, "days" => 0, "hours" => 0,
            "minutes" => 0, "seconds" => 0,
        );
        if ($time >= 31556926) {
            $value["years"] = floor($time / 31556926);
            $time = ($time % 31556926);
        }
        if ($time >= 86400) {
            $value["days"] = floor($time / 86400);
            $time = ($time % 86400);
        }
        if ($time >= 3600) {
            $value["hours"] = floor($time / 3600);
            $time = ($time % 3600);
        }
        if ($time >= 60) {
            $value["minutes"] = floor($time / 60);
            $time = ($time % 60);
        }
        $value["seconds"] = floor($time);
        //return (array) $value;
        $t = $value["years"] . "年" . $value["days"] . "天" . " " .
            $value["hours"] . "小时" . $value["minutes"] . "分" . $value["seconds"] . "秒";
        Return $t;

    } else {
        return (bool)FALSE;
    }
}

//获取ip 地址
function getIp()
{
    if (PHP_SAPI == 'cli')
        return 'unknown';
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : 'unknown';
}

function get_ip()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } else {
        $cip = "unknown！";
    }
    return $cip;
}

/**
 * @param string $address 地址
 * @param string $city 城市名
 * @param string $ak ak
 * @return array|null
 */
function getLatLng($address = '', $city = '', $ak = '')
{
    $result = array();
    $url = "http://api.map.baidu.com/geocoder/v2/?output=json&address=" . $address . "&city=" . $city . "&ak=" . $ak;
    $data = getCurl($url);
    if (!empty($data) && $data['status'] == 0) {
        $result['lat'] = $data['result']['location']['lat'];
        $result['lng'] = $data['result']['location']['lng'];
        return $result;//返回经纬度结果
    } else {
        return null;
    }
}

/**
 * 求两个已知经纬度之间的距离,单位为米
 *
 * @param lng1 $ ,lng2 经度
 * @param lat1 $ ,lat2 纬度
 * @return float 距离，单位米
 */
function getDistance($lng1, $lat1, $lng2, $lat2)
{
    // 将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
}

/* 创建目录 */
function mkdirs($path, $mode = 0777)
{
    if (!file_exists($path)) {
        mkdirs(dirname($path), $mode);
        mkdir($path, $mode);
    }
}