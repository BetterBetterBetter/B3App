<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.filesystem.file');

if (!defined('DS'))
{
	define('DS', '/');
}

if (JFile::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
{
	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

	if (!class_exists('comquick2cartHelper'))
	{
		JLoader::register('comquick2cartHelper', $path);
		JLoader::load('comquick2cartHelper');
	}

	// Load assets
	comquick2cartHelper::loadQuicartAssetFiles();

	$doc = JFactory::getDocument();
	$lang = JFactory::getLanguage();
	$lang->load('mod_quick2cart', JPATH_SITE);

	JLoader::import('cart', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models');
	$model = new Quick2cartModelcart;
	$cart = $model->getCartitems();

	// Trigger onBeforeCartModule
	$dispatcher = JDispatcher::getInstance();
	JPluginHelper::importPlugin('system');
	$result = $dispatcher->trigger('onBeforeCartModule');

	if (!empty($result))
	{
		$beforecartmodule = $result[0];
	}

	// Trigger onAfterCartModule
	$dispatcher = JDispatcher::getInstance();
	JPluginHelper::importPlugin('system');
	$result = $dispatcher->trigger('onAfterCartModule');

	if (!empty($result))
	{
		$aftercartdisplay = $result[0];
	}

	// Hide mod when cart is empty.
	if ($params->get('hideOnCartEmpty', 0) == 1)
	{
		if (empty($cart))
		{
			return;
		}
	}

	if (version_compare(JVERSION, '3.0', 'lt'))
	{
		// Define wrapper class
		if (!defined('Q2C_WRAPPER_CLASS'))
		{
			define('Q2C_WRAPPER_CLASS', "q2c-wrapper techjoomla-bootstrap");
		}
	}
	else
	{
		// Define wrapper class
		if (!defined('Q2C_WRAPPER_CLASS'))
		{
			define('Q2C_WRAPPER_CLASS', "q2c-wrapper");
		}

		// Bootstrap tooltip and chosen js
		JHtml::_('bootstrap.tooltip');
		JHtml::_('behavior.multiselect');
	}

	require JModuleHelper::getLayoutPath('mod_quick2cart');
}
