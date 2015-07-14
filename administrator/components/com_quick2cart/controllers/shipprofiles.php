<?php
/**
 * @version     2.2
 * @package     com_quick2cart
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Techjoomla <contact@techjoomla.com> - http://techjoomla.com
 */

// No direct access.
defined('_JEXEC') or die;

// Load Quick2cart Controller for list views
require_once __DIR__ . '/q2clist.php';

/**
 * Shipprofiles list controller class.
 */
class Quick2cartControllerShipprofiles extends  Quick2cartControllerQ2clist
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$lang =  JFactory::getLanguage();
	}
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Shipprofiles', $prefix = 'Quick2cartModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}


}
