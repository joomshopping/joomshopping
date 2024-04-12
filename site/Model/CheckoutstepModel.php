<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class CheckoutStepModel  extends BaseModel{
    
    public function getNextStep($step){
		$jshopConfig = \JSFactory::getConfig();
		
		if ($step==2){
			if ($jshopConfig->without_shipping && $jshopConfig->without_payment){
				$next = 5;
				return $next;
			}        
			if ($jshopConfig->without_payment){
				$next = 4;
				return $next;
			}

			if ($jshopConfig->step_4_3){
				if ($jshopConfig->without_shipping){
					$next = 3;
					return $next;
				}
				$next = 4;
				return $next;
			}else{
				$next = 3;
				return $next;
			}
		}
		if ($step==3){
			if ($jshopConfig->without_shipping) {
				$next = 5;
				return $next;				
			}
			
			if ($jshopConfig->step_4_3){
				$next = 5;
				return $next;
			}else{
				$next = 4;
				return $next;
			}
		}
		if ($step==4){
			if ($jshopConfig->step_4_3 && !$jshopConfig->without_payment){				
				$next = 3;
				return $next;
			}else{
				$next = 5;
				return $next;
			}
		}
		
		
	}
	
	public function getCheckoutUrl($step, $defaultItemId = 0, $redirect = 1){
		$jshopConfig = \JSFactory::getConfig();
		if (preg_match('/^(\d)+$/', $step)){			
			$task = 'step'.$step;
		}else{
			$task = $step;
		}
		$url = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task='.$task, $defaultItemId, $redirect, $jshopConfig->use_ssl);
		return $url;
	}
        
}