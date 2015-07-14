<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

Foundry::import('admin:/includes/apps/apps');

if (!defined('DS'))
{
	define('DS', '/');
}

/**
 * Quick2cart users product application for EasySocial.
 *
 * @version  Release: <1.0>
 * @since    1.0
 */
class SocialUserAppQ2cMyProducts extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since  1.0
	 * @access  public
	 * @param   array  return all html code of layout.
	 */
	public function __construct ($options = array())
	{
		// Load language file for plugin
		$lang = JFactory::getLanguage();
		$lang->load('plg_app_user_q2cMyProducts', JPATH_ADMINISTRATOR);

		require_once JPATH_SITE . '/components/com_quick2cart/helper.php';

		parent::__construct($options);
	}
}
