<?php
namespace Admin\Controller;
use Admin\Controller\GlobalController;
/**
 * @date 2015-3-30
 * @author tww <merry2014@vip.qq.com>
 */
class MemberController extends  GlobalController{
	public function assignMembers(){
		$members = D('Member')->getMembers();
		$this->assign('members', $members);
	}
	
	public function members($gid = 0){
		if($gid){
			$members = D('Member')->getMembers();
		}else{
			$members = D('Member')->getMembersByGid($gid);
		}
		$this->assign('members', $members);
		$this->display('Member/members');
	}
	
}