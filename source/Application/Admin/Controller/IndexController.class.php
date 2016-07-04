<?php
namespace Admin\Controller;
// use User\Api\UserApi as UserApi;
class IndexController extends AdminController {

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $this->meta_title = '管理首页';
        $this->display();
    }
    
    public function hs(){
    	
    	$this->display();
    }

}
