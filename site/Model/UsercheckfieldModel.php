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

jimport('Joomla.mail.helper');

class UsercheckfieldModel {

    private $_error = '';

    public function getLastErrorMsg() {
        return $this->_error;
    }

	public function int($val) {
        return intval($val);
    }

    public function string($val) {
        return trim($val) != '';
    }

    public function email($val) {
		$domains = \JComponentHelper::getParams('com_users')->get('domains');
		if ($domains) {
			$emailDomain = explode('@', $val);
			$emailDomain = $emailDomain[1];
			$emailParts  = array_reverse(explode('.', $emailDomain));
			$emailCount  = \count($emailParts);
			$allowed     = true;

			foreach ($domains as $domain) {
				$domainParts = array_reverse(explode('.', $domain->name));
				$status      = 0;

				// Don't run if the email has less segments than the rule.
				if ($emailCount < \count($domainParts)) {
					continue;
				}

				foreach ($emailParts as $key => $emailPart) {
					if (!isset($domainParts[$key]) || $domainParts[$key] == $emailPart || $domainParts[$key] == '*') {
						$status++;
					}
				}

				// All segments match, check whether to allow the domain or not.
				if ($status === $emailCount) {
					if ($domain->rule == 0) {
						$allowed = false;
					} else {
						$allowed = true;
					}
				}
			}

			// If domain is not allowed, fail validation. Otherwise continue.
			if (!$allowed) {
				throw new \UnexpectedValueException(\JText::sprintf('JGLOBAL_EMAIL_DOMAIN_NOT_ALLOWED', $emailDomain));
			}
		}
		
        return trim($val) != '' && \JMailHelper::isEmailAddress($val);
    }

    public function password($value){
		$params = \JComponentHelper::getParams('com_users');

		if (!empty($params)){
			$minimumLength = $params->get('minimum_length');
			$minimumIntegers = $params->get('minimum_integers');
			$minimumSymbols = $params->get('minimum_symbols');
			$minimumUppercase = $params->get('minimum_uppercase');
		}

		$valueLength = strlen($value);
		$valueTrim = trim($value);
		$validPassword = true;

		if ($valueLength > 4096){
			$this->_error = \JText::_('JFIELD_PASSWORD_TOO_LONG');
			$validPassword = false;
		}

		if (strlen($valueTrim) != $valueLength){
			$this->_error = \JText::_('JFIELD_PASSWORD_SPACES_IN_PASSWORD');
			$validPassword = false;
		}

		if (!empty($minimumIntegers)){
			$nInts = preg_match_all('/[0-9]/', $value, $imatch);
			if ($nInts < $minimumIntegers){
				$this->_error = \JText::plural('JFIELD_PASSWORD_NOT_ENOUGH_INTEGERS_N', $minimumIntegers);
				$validPassword = false;
			}
		}

		if (!empty($minimumSymbols)){
			$nsymbols = preg_match_all('[\W]', $value, $smatch);
			if ($nsymbols < $minimumSymbols){
				$this->_error = \JText::plural('JFIELD_PASSWORD_NOT_ENOUGH_SYMBOLS_N', $minimumSymbols);
				$validPassword = false;
			}
		}

		if (!empty($minimumUppercase)){
			$nUppercase = preg_match_all('/[A-Z]/', $value, $umatch);
			if ($nUppercase < $minimumUppercase){
				$this->_error = \JText::plural('JFIELD_PASSWORD_NOT_ENOUGH_UPPERCASE_LETTERS_N', $minimumUppercase);
				$validPassword = false;
			}
		}

		if (!empty($minimumLength)){
			if (strlen((string) $value) < $minimumLength){
				$this->_error = \JText::plural('JFIELD_PASSWORD_TOO_SHORT_N', $minimumLength);
				$validPassword = false;
			}
		}

		if (empty($valueTrim)){
			$this->_error = \JText::_('JSHOP_REGWARN_PASSWORD');
			$validPassword = false;
		}

		return $validPassword;
	}
}