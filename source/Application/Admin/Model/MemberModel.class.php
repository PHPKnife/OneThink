<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class MemberModel extends Model {

    protected $_validate = array(
        array('nickname', '1,16', '昵称长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
        array('nickname', '', '昵称被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
        /* 验证密码 */
        
        array('password', '6,30', '密码长度6-30', self::EXISTS_VALIDATE, 'length'), //密码长度不合法
        
        /* 验证邮箱 */
//         array('email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE), //邮箱格式不正确
//         array('email', '', '邮箱已存在', self::EXISTS_VALIDATE, 'unique'), //邮箱被占用
        
//         /* 验证手机号码 */
        
//         array('mobile', '', '手机号已被占用', self::EXISTS_VALIDATE, 'unique'), //手机号被占用
    );

    public function lists($status = 1, $order = 'uid DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($username, $password){
        /* 检测是否在当前应用注册 */
        $condition = array();
        $condition['nickname'] = $username;
        $user = $this->where($condition)->find();
        if(!$user || 1 != $user['status']) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }else{
            $en_password = encryptPassword($password, $user['salt']);
            if($en_password != $user['password']){
                $this->error = '账号或者密码错误！'; 
                return false;
            }
        }
        $uid = $user['uid'];
        //记录行为
        action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }
    
    public function loginByUid($uid, $password){
        $user = $this->find($uid);
        $en_password = encryptPassword($password, $user['salt']);
        if($en_password != $user['password']){
            return false;
        }else{
            return $uid;
        }
    }
    
    public function getMembers($where = array()){
    	return $this->where($where)->getField('uid,nickname');
    }
    
    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user['uid'],
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => $user['nickname'],
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }

    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->getField('nickname');
    }
    
    public function getMemberInfo($uid){
    	$where = array();
    	$where['uid'] = $uid;
    	return $this->where($where)->find();
    }
    
    public function getMembersByGid($gid){
    	$where = array();
    	$where['group_id'] = $gid;
    	$uids = M('AuthGroupAccess')->where($where)->getField('uid',true);
    	
    	$where = array();
    	$where['uid'] = array('IN', $uids);
    	return $this->getMembers($where);
    }
    
    public function updatePassword($uid, $password){
        $data = array();
        $data['uid'] = $uid;
        $salt = random_string(8);
        $data['salt'] = $salt;
        $data['password'] = encryptPassword($password, $salt);
        return $this->save($data);
    }
 

}
