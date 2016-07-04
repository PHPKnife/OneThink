<?php
include_once './Application/User/Conf/config.php';
function think_ucenter_md5($str, $key = 'ThinkUCenter'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}
echo think_ucenter_md5($_GET['password'], UC_AUTH_KEY);