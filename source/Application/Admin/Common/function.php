<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */
function SendMail($address, $title, $message) {
	return false;
	
	
	import('Org.PHPMailer.phpmailer');
	$mail = new \PHPMailer();
	$mail->IsSMTP();	// 设置PHPMailer使用SMTP服务器发送Email
	$mail->CharSet = 'UTF-8';
	if(is_array($address)){
		foreach ($address as $val){
			$mail->addAddress($val);
		}
	}else{
		$mail->AddAddress($address);// 添加收件人地址，可以多次使用来添加多个收件人
	}

	// 	$mail->Body = $message;	// 设置邮件正文
	$mail->msgHTML($message);
	$mail->From = C('MAIL_ADDRESS');	// 设置邮件头的From字段。
	$mail->FromName = '广告通通知';	// 设置发件人名字
	$mail->Subject = $title;	// 设置邮件标题
	$mail->Host = C('MAIL_SMTP');	// 设置SMTP服务器。
	$mail->SMTPAuth = true;	// 设置为“需要验证”
	// 设置用户名和密码。
	$mail->Username = C('MAIL_LOGINNAME');
	$mail->Password = C('MAIL_PASSWORD');
	$result = $mail->Send();
	return $result;
}

function curl_post($url, array $post = NULL, array $options = array()) {
	$defaults = array(
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_POSTFIELDS => http_build_query($post)
	);
	$ch = curl_init();
	curl_setopt_array($ch, ($options + $defaults));
	if( ! $result = curl_exec($ch)) {
		trigger_error(curl_error($ch));
	}
	curl_close($ch);
	return $result;
}

function get_curr_uid(){
	return is_login();
}

function encryptPassword($password, $salt) {
	$password = trim($password);
	return md5($password.$salt);
}

function random_string($len, $type='str') {
	if($type=='str') {
		$chars 	= 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
	} elseif($type=='int') {
		$chars	= '0123456789';
	}

	$chars	= str_shuffle($chars);
	$str	= substr($chars, 0, $len);
	return $str;
}

/**
 * 获取扩展模型对象
 * @param  integer $model_id 模型编号
 * @return object         模型对象
 */
function logic($model_id){
    $name  = parse_name(get_document_model($model_id, 'name'), 1);
    $class = is_file(MODULE_PATH . 'Logic/' . $name . 'Logic' . EXT) ? $name : 'Base';
    $class = MODULE_NAME . '\\Logic\\' . $class . 'Logic';
    return new $class($name);
}

/* 解析列表定义规则*/

function get_list_field($data, $grid){

    // 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =    $data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

    // 链接支持
    if('title' == $grid['field'][0] && '目录' == $data['type'] ){
        // 目录类型自动设置子文档列表链接
        $grid['href']   =   '[LIST]';
    }
    if(!empty($grid['href'])){
        $links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href   =   str_replace(
                    array('[DELETE]','[EDIT]','[LIST]'),
                    array('setstatus?status=-1&ids=[id]',
                    'edit?id=[id]&model=[model_id]&cate_id=[category_id]',
                    'index?pid=[id]&model=[model_id]&cate_id=[category_id]'),
                    $href);

                // 替换数据变量
                $href   =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]  =   '<a href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
    }
    return $value;
}

/* 解析插件数据列表定义规则*/

function get_addonlist_field($data, $grid,$addon){
    // 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =    $data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

    // 链接支持
    if(!empty($grid['href'])){
        $links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href   =   str_replace(
                    array('[DELETE]','[EDIT]','[ADDON]'),
                    array('del?ids=[id]&name=[ADDON]','edit?id=[id]&name=[ADDON]',$addon),
                    $href);

                // 替换数据变量
                $href   =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]  =   '<a href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
    }
    return $value;
}

// 获取模型名称
function get_model_by_id($id){
    return $model = M('Model')->getFieldById($id,'title');
}

// 获取属性类型信息
function get_attribute_type($type=''){
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
        'string'    =>  array('字符串','varchar(255) NOT NULL'),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'date'      =>  array('日期','int(10) NOT NULL'),
        'datetime'  =>  array('时间','int(10) NOT NULL'),
        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
        'select'    =>  array('枚举','char(50) NOT NULL'),
        'radio'     =>  array('单选','char(10) NOT NULL'),
        'checkbox'  =>  array('多选','varchar(100) NOT NULL'),
        'editor'    =>  array('编辑器','text NOT NULL'),
        'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
        'file'      =>  array('上传附件','int(10) UNSIGNED NOT NULL'),
    );
    return $type?$_type[$type][0]:$_type;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null){
    if(!isset($status)){
        return false;
    }
    switch ($status){
        case -1 : return    '已删除';   break;
        case 0  : return    '禁用';     break;
        case 1  : return    '正常';     break;
        case 2  : return    '待审核';   break;
        default : return    false;      break;
    }
}

// 获取数据的状态操作
function show_status_op($status) {
    switch ($status){
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';       break;
        default : return    false;      break;
    }
}

/**
 * 获取文档的类型文字
 * @param string $type
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_document_type($type = null){
    if(!isset($type)){
        return false;
    }
    switch ($type){
        case 1  : return    '目录'; break;
        case 2  : return    '主题'; break;
        case 3  : return    '段落'; break;
        default : return    false;  break;
    }
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    $list = C('CONFIG_GROUP_LIST');
    return $group?$list[$group]:'';
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
function extra_menu($extra_menu,&$base_menu){
    foreach ($extra_menu as $key=>$group){
        if( isset($base_menu['child'][$key]) ){
            $base_menu['child'][$key] = array_merge( $base_menu['child'][$key], $group);
        }else{
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 获取参数的所有父级分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category($cid){
    if(empty($cid)){
        return false;
    }
    $cates  =   M('Category')->where(array('status'=>1))->field('id,title,pid')->order('sort')->select();
    $child  =   get_category($cid); //获取参数分类的信息
    $pid    =   $child['pid'];
    $temp   =   array();
    $res[]  =   $child;
    while(true){
        foreach ($cates as $key=>$cate){
            if($cate['id'] == $pid){
                $pid = $cate['pid'];
                array_unshift($res, $cate); //将父分类插入到数组第一个元素前
            }
        }
        if($pid == 0){
            break;
        }
    }
    return $res;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 获取当前分类的文档类型
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_type_bycate($id = null){
    if(empty($id)){
        return false;
    }
    $type_list  =   C('DOCUMENT_MODEL_TYPE');
    $model_type =   M('Category')->getFieldById($id, 'type');
    $model_type =   explode(',', $model_type);
    foreach ($type_list as $key=>$value){
        if(!in_array($key, $model_type)){
            unset($type_list[$key]);
        }
    }
    return $type_list;
}

/**
 * 获取当前文档的分类
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null){
    if(empty($cate_id)){
        return false;
    }
    $cate   =   M('Category')->where('id='.$cate_id)->getField('title');
    return $cate;
}

 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

// 获取子文档数目
function get_subdocument_count($id=0){
    return  M('Document')->where('pid='.$id)->count();
}



 // 分析枚举类型字段值 格式 a:名称1,b:名称2
 // 暂时和 parse_config_attr功能相同
 // 但请不要互相使用，后期会调整
function parse_field_attr($string) {
    if(0 === strpos($string,':')){
        // 采用函数定义
        return   eval('return '.substr($string,1).';');
    }elseif(0 === strpos($string,'[')){
        // 支持读取配置参数（必须是数组类型）
        return C(substr($string,1,-1));
    }
    
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null){
    if(empty($id) && !is_numeric($id)){
        return false;
    }
    $list = S('action_list');
    if(empty($list[$id])){
        $map = array('status'=>array('gt', -1), 'id'=>$id);
        $list[$id] = M('Action')->where($map)->field(true)->find();
    }
    return empty($field) ? $list[$id] : $list[$id][$field];
}


/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false){
    $list = array(
        1=>'系统',
        2=>'用户',
    );
    if($all){
        return $list;
    }
    return $list[$type];
}

function is_up_file(){
	foreach ($_FILES as $file){
		if(is_array($file['name'])){
			foreach ($file['error'] as $error){
				if($error === 0){
					return true;
				}
			}
		}else{
			if($file['error'] === 0){
				return true;
			}
				
		}
	}
}

function get_full_url() {
	$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
	return
	($https ? 'https://' : 'http://').
	(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
	(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
			($https && $_SERVER['SERVER_PORT'] === 443 ||
					$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
					substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
}

function build_website($path){
	return get_full_url() . substr($path, 1);
}




function curr_date(){
	return date('Y-m-d H:i:s',time());
}


function getUserMap($list){
	$uids = array();
	foreach ($list as $v){
		$uids[] = $v['uid'];
	}
	$user_map = D('User')->getUserMap(array_unique($uids));
	return $user_map;
}

function getMonthRange($p_sdate, $p_edate){
	$length 	= getMonths($p_sdate, $p_edate);
	$s_year 	= date('Y', strtotime($p_sdate));
	$temp_month = date('m', strtotime($p_sdate));

	$months = array();
	for($i = 0;$i < $length; $i ++){
		$months[] = date('Y-m', mktime(0, 0, 0, $temp_month + $i, 1, $s_year));
	}
	return $months;
}
/*
 * 返回两个时间之间差多少个月
 */
function getMonths($p_sdate, $p_edate) {
	$startTime	= strtotime($p_sdate);
	$endTime 	= strtotime($p_edate);
	$startMonth = date('n', $startTime);
	$endMonth 	= date('n', $endTime);
	$startYear 	= date('Y', $startTime);
	$endYear 	= date('Y', $endTime);
	$total 		= 13 - $startMonth + ($endYear - $startYear - 1) * 12 + $endMonth; 	// 计算月份差
	return $total;
}

/**
 * 
 * @param date $p_sdate Y-m-d
 * @param date $p_edate Y-m-d
 * @return multitype:string
 */
function getDaysRange($p_sdate, $p_edate){
	$days = array();
	$s_time = strtotime($p_sdate);
	$e_time = strtotime($p_edate);
	while ($s_time <= $e_time){
		$days[] = date('Y-m-d', $s_time);
		$s_time = strtotime('+1 day', $s_time);
	}
	return $days;
}

function formatMonth($month){
	if(empty($month)){
		return false;
	}
	$time = strtotime($month);
	$date = date('Y-m-d', $time);
	return $date;
}

function auth_w($name, $data=array()){
	if(check_rule($name)){
		return R($name,$data,'Widget');
	}
}

function check_rule($path, $type=1, $mode='url'){
	if(IS_ROOT){
		return true;//管理员允许访问任何页面
	}
	$rule = MODULE_NAME.'/'.$path;
	static $Auth    =   null;
	if (!$Auth) {
		$Auth       =   new \Think\Auth();
	}
	if(!$Auth->check($rule,get_curr_uid(),$type,$mode)){
		return false;
	}
	return true;
}

function errorLog($data,$msg) {
	error_log(PHP_EOL."============== $msg ============".get_client_ip()."============".date('Y-m-d H:i:s').PHP_EOL, 3, './log.txt');
	error_log(var_export($data,true).PHP_EOL, 3, './log.txt');
}

function getColumnName($num) {
	$num = intval($num) + 1;
	if ($num <= 0)
		return false;
	$letterArr = range('A', 'Z');
	$letter = '';
	do {
		$key = ($num - 1) % 26;
		$letter = $letterArr[$key] . $letter;
		$num = floor(($num - $key) / 26);
	} while ( $num > 0 );
	return $letter;
}

function zeroToNull($num){
	if($num == 0){
		return null;
	}else{
		return $num;
	}
}

function getGid(){
	$where = array();
	$where['uid'] = get_curr_uid();
	return M('AuthGroupAccess')->where($where)->getField('group_id');
}



function mysubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	if(function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
		if(false === $slice) {
			$slice = '';
		}
	}else{
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	if(strlen($slice) == strlen($str)){
		return $slice;
	}
	$more = '<a href="#" class="more">...详情</a>';
	return $suffix ? $slice.$more : $slice;
}

