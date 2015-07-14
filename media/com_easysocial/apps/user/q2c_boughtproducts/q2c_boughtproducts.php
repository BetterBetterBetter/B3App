<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Unauthorized Access');

Foundry::import('admin:/includes/apps/apps');

if (!defined('DS'))
{
	define('DS', '/');
}


/**
 * Quick2cart bought product list application for EasySocial.
 *
 * @version  Release: <1.0>
 * @since    1.0
 */

class SocialUserAppQ2c_boughtProducts extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct ()
	{
		// Load language file for plugin
		$lang = JFactory::getLanguage();
		$lang->load('plg_app_user_q2c_boughtproducts', JPATH_ADMINISTRATOR);

		parent::__construct();
	}
}
