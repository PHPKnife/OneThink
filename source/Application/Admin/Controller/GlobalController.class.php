<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
use Think\Model;
use Think\Upload;

/**
 * @date 2014-03-31
 *
 * @author tww <merry2014@vip.qq.com>
 */
class GlobalController extends AdminController{
	private $limit;
	private $jumpPage;
	private $upFileConfig;

	public function _initialize(){
		parent::_initialize();
		$this->upFileConfig = C('PICTURE_UPLOAD');
	}

	public function index($model = '', $return = false){
		$model 	= $this->_checkModel($model);
		if (! empty($model)) {
			$order 	= method_exists($model, 'getOrderFields') ? $model->getOrderFields() : '';
			$list 	= $this->lists($model, $this->getSearchCondition($model), $order, '');
			if($return){
				return $list;
			}else{
				$this->assign('list', $list);
			}	
		}
		$this->display($this->getJumpPage());
		
	}

	public function add($model = ''){
		if(IS_POST){
			$this->doAdd($model);
		}else{		
			$page = $this->getJumpPage();
			if(empty($page) && !is_file(T())){
				$page = 'edit';
			}
			$this->display($page);
			
		}
		
	}

	public function edit($model = ''){
		if(IS_POST){
			$this->doEdit($model);
		}else{
			$id 	= I('get.id', 0);
			$model 	= $this->_checkModel($model);
			$map 	= array($model->getPk() => $id);
			$vo 	= $model->where($map)->find();
			$this->assign('vo', $vo);
			$this->display($this->getJumpPage());
		}
	}

	public function doAdd($model = ''){
		$model 	= $this->_checkModel($model);
		$this->_setFileInfo();	
		$vo = $model->create();	
		if (false === $vo) {
			$this->error($model->getError());
		}
		
		$id = $model->add($vo);
		if ($id) {
			$jumpPage = $this->getJumpPage() ? $this->getJumpPage() : U('index');
			$this->success("操作成功", $jumpPage);
		} else {
			$this->error('数据提交失败！');
		}
	}
	
	public function del(){
		$this->doDel();
	}
	
	public function doDel($model = ''){
		$ids = I('ids', 0);
		if(empty($ids)){
			$this->error('参数错误！');
		}
		
		if (is_numeric($ids)) {
			$id = array($ids);
		} else if (is_array($ids)) {
			$id = $ids;
		} else {
			$this->error('数据错误！');
		}
		$model 		= $this->_checkModel($model);
		$pk_field 	= $model->getPk();
		$data 		= array();
		$data[$pk_field] = array('IN', $id);
		
		$result = $model->where($data)->delete();
		if (false !== $result) {
			$this->success('删除成功！');
		} else {
			$this->error('删除失败！');
		}
	}

	public function doEdit($model = ''){		
		$model 	= $this->_checkModel($model);
		$this->_setFileInfo();
		
		$vo = $model->create('', Model::MODEL_UPDATE);
		if (false === $vo) {
			$this->error($model->getError());
		}

		$id = is_array($vo) ? $vo[$model->getPk()] : $vo->{$model->getPk()};
		$result = $model->save($vo);
		if (false !== $result) {
			$jumpPage = $this->getJumpPage() ? $this->getJumpPage() : U('index');
			$this->success('更新成功！', $jumpPage);
		} else {
			$this->error($model->getError());
		}
	}

	private function _setFileInfo(){
		if(is_up_file()){
			$upfile_infos 	= $this->upload();
			$up_config 		= $this->getUpFileConfig();
			$root_path 		= $up_config['rootPath'];
			foreach ($upfile_infos as $v) {
				$field_name = $v['key'];
				$img_http_url = build_website($root_path . $v['savepath'] . $v['savename']);
				//同名多张图片默认存在一个字段以分隔符隔开。
				if($_POST[$field_name]){
					$_POST[$field_name] .= C('IMAGE_SEPARATOR') . $img_http_url;
				}else{
					$_POST[$field_name] = $img_http_url;
				}			
			}
		}		
	}
	
	
	/**
	 * 通用普通文件上传处理
	 */
	public function upload(){
		$config = $this->getUpFileConfig();
		$upload = new Upload($config);
		$info 	= $upload->upload();
		if ($info) {
			return $info;
		} else {
			$this->error($upload->getError());
		}
	}
	
	public function ajaxUpload(){
		$up_config = $this->getUpFileConfig();
		$upload = new Upload($up_config);
		$upfile_infos 	= $upload->upload();
		if($upfile_infos){
			$root_path 		= $up_config['rootPath'];
			$save_path		= $upfile_infos['Filedata']['savepath'];
			$savename		= $upfile_infos['Filedata']['savename'];
			$img_http_url   = build_website($root_path . $save_path . $savename);
	
			$data = array();
			$data['status'] = true;
			$data['msg'] = $img_http_url;
			$this->ajaxReturn($data);
		}else{
			$data = array();
			$data['status'] = false;
			$data['msg'] = $upload->getError();
			$this->ajaxReturn($data);
		}
	}
	
	public function changeStatus(){
		$ids = I('ids',0);
		$status = I('status',0);
		$model = $this->_checkModel($model);
		$pk = $model->getPk();
		if(method_exists($model, 'getStatusFieldName')){
			$status_field = $model->getStatusFieldName();
		}else{
			$this->error('请在'.$model->getModelName().'模型中添加getStatusFieldName方法!');
		}
				
		$data = array();		
		if(is_array($ids)){
			$data[$pk] = array('IN', $ids);
		}else{
			$data[$pk] = $ids;
		}
		$data[$status_field] = $status;
		$result = $model->save($data);
		if($result){
			$this->success('操作成功！');
		}else{
			$this->error('操作失败！');
		}
		
	}

	public function getSearchCondition($model = ''){
		// 生成查询条件
		$model = $this->_checkModel($model);
		$map = array();
		$likeFields = method_exists($model, 'getLikeFields') ? $model->getLikeFields() : '';
		
		if(method_exists($model, 'getStatusFieldName')){
			$status_field = $model->getStatusFieldName();
			$map[$status_field] = array('EGT', 0);
		}
		
		foreach ($model->getDbFields() as $val) {
			$currentRequest = trim($_REQUEST[$val]);
			if (isset($_REQUEST[$val]) && $currentRequest != '') {
				if (! empty($likeFields) && is_array($likeFields) && in_array($val, $likeFields)) {
					$map[$val] = array('like', '%' . $currentRequest . '%');
				} else {
					$map[$val] = $currentRequest;
				}
			}
		}
		$limit = $this->getLimit();
		if (! empty($limit)) {
			$map['_complex'] = $limit;
		}
		return $map;
	}

	private function _checkModel($model){
		if (empty($model)) {
			$model = CONTROLLER_NAME;
		}
		if (is_string($model)) {
			if (ACTION_NAME == 'index') {
				if (class_exists(CONTROLLER_NAME . 'ViewModel')) {
					return D(CONTROLLER_NAME . 'View');
				}
			}
			return D(CONTROLLER_NAME);
		}
		return $model;
	}

	public function getLimit(){
		return $this->limit;
	}

	public function getJumpPage(){
		return $this->jumpPage;
	}

	public function setLimit($limit){
		$this->limit = $limit;
	}

	public function setJumpPage($jumpPage){
		$this->jumpPage = $jumpPage;
	}
	
	public function getUpFileConfig(){
		return  $this->upFileConfig;
	}
	
	public function setUpFileConfig($upFileConfig){
		$this->upFileConfig = $upFileConfig;
	}
}