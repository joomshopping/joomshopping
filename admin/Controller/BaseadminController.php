<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die();

class BaseadminController extends BaseController{
    
    protected $nameModel = '';
    protected $nameController = '';
    protected $urlEditParamId = 'id';
    protected $modelSaveItemFileName = '';
    protected $checkToken = array('save' => 0, 'remove' => 0);

    public function __construct($config = array(), \Joomla\CMS\MVC\Factory\MVCFactoryInterface $factory = null, $app = null, $input = null){
		parent::__construct($config, $factory, $app, $input);
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('save2new', 'save');
        $this->registerTask('orderup', 'order');
        $this->registerTask('orderdown', 'order');
        $this->nameController = $this->getNameController();
        $this->nameModel = $this->getNameModel();
        $this->init();
    }

    public function init(){        
    }
    
    public function getNameController(){
		if (empty($this->nameController)){
			$r = null;
			preg_match('/Controller\\\(.*)Controller/i', get_class($this), $r);
			$this->nameController = strtolower($r[1]);
		}
		return $this->nameController;
	}
    
    public function getNameModel(){
		if (empty($this->nameModel)){
			$r = null;
			preg_match('/Controller\\\(.*)Controller$/i', get_class($this), $r);
			$this->nameModel = strtolower($r[1])."Model";
		}
		return $this->nameModel;
	}
    
    public function getAdminModel(){
        return \JSFactory::getModel($this->nameModel);
    }
    
    public function getUrlListItems(){
        return "index.php?option=com_jshopping&controller=".$this->getNameController();
    }
    
    public function getUrlEditItem($id = 0){
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&task=edit&".$this->urlEditParamId."=".$id;
    }

    public function home(){
        \JFactory::getApplication()->redirect('index.php?option=com_jshopping');
    }
    
    public function save(){
        if ($this->checkToken['save']){
            \JSession::checkToken() or die('Invalid Token');
        }
        $app = \JFactory::getApplication();
        $model = $this->getAdminModel();
        $post = $model->getPrepareDataSave($this->input);
        if (isset($post['f-id'])){
            $post['id'] = $post['f-id'];
            unset($post['f-id']);
        }
        if ($this->modelSaveItemFileName){
            $table = $model->save($post, $_FILES[$this->modelSaveItemFileName]);
        }else{
            $table = $model->save($post);
        }        
        if (!$table){            
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect($this->getUrlEditItem($post[$this->urlEditParamId]));
            return 0;
        }
        $messageSaveOk = $this->getMessageSaveOk($post);
        if ($messageSaveOk != ''){
            $app->enqueueMessage($messageSaveOk, 'message');
        }        
        $table_key = $table->getKeyName();        
		if ($this->getTask()=='apply'){
            $this->setRedirect($this->getUrlEditItem($table->$table_key));
        } elseif ($this->getTask()=='save2new') {
            $this->setRedirect($this->getUrlEditItem());
        }else{
            $this->setRedirect($this->getUrlListItems());
        }
	}
    
    public function remove(){
        if ($this->checkToken['remove']){
            \JSession::checkToken() or die('Invalid Token');
        }
		$cid = $this->input->get('cid', array(), 'array');
		$this->getAdminModel()->deleteList($cid);
		$this->setRedirect($this->getUrlListItems());
	}
    
    public function publish(){
        $cid = $this->input->get('cid', array(), 'array');
        $this->getAdminModel()->publish($cid, 1);
		$this->setRedirect($this->getUrlListItems());
    }
    
    public function unpublish(){
        $cid = $this->input->get('cid', array(), 'array');
        $this->getAdminModel()->publish($cid, 0);
		$this->setRedirect($this->getUrlListItems());
    }
    
    public function order(){
        $id = $this->input->getInt("id");
        $move = $this->input->getInt("move");
        if (!$id){
            $ids = $this->input->get('cid', array(), 'array');
            $id = (int)$ids[0];
        }
        if ($this->getTask() == 'orderup'){
            $move = -1;
        }
        if ($this->getTask() == 'orderdown'){
            $move = 1;
        }
        $this->getAdminModel()->order($id, $move, $this->getOrderWhere());
		$this->setRedirect($this->getUrlListItems());
    }
    
    public function saveorder(){
        $cid = $this->input->get('cid', array(), 'array');
        $order = $this->input->get('order', array(), 'array');
        $this->getAdminModel()->saveorder($cid, $order, $this->getSaveOrderWhere());
        if ($this->input->getInt('ajax')) {
            $this->app->close();
        } else {
            $this->setRedirect($this->getUrlListItems());
        }
    }
    
    protected function getOrderWhere(){
        return '';
    }
    
    protected function getSaveOrderWhere(){
        return '';
    }
    
    protected function getMessageSaveOk($post){
        return '';
    }
}