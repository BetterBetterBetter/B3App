<?php
/**
 * @version		2.1
 * @package		Example K2 Plugin (K2 plugin)
 * @author    JoomlaWorks - http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('DS'))
{
	define('DS', '/');
}


/**
 * Example K2 Plugin to render YouTube URLs entered in backend K2 forms to video
 * players in the frontend.
 */

// Load the K2 Plugin API
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_k2' . DS . 'lib' . DS . 'k2plugin.php');

// Initiate class to hold plugin events
class plgK2Qtc_k2 extends K2Plugin
{

	// Some params
	var $pluginName = 'qtc_k2';

	var $pluginNameHumanReadable = 'Quick2Cart K2 Plugin';

	function plgK2Qtc_k2 (& $subject, $params)
	{
		parent::__construct($subject, $params);
	}

	function onAfterK2Save ($item, $isNew)
	{
		$pid = $item->id;
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';

		if (! class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$input = JFactory::getApplication()->input;
		$post_data = $input->post;
		$comquick2cartHelper = new comquick2cartHelper();

		$client = $input->post->set('client', 'com_k2');
		$pid = $post_data->set('pid', $pid);

		$comquick2cartHelper = $comquick2cartHelper->saveProduct($post_data);
		//This function trigger to content trigger.
	}

	// Event to display (in the frontend) the YouTube URL as entered in the item
	// form
	function onK2AfterDisplayContent (&$item, &$params, $limitstart)
	{
		// Add Language file.
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		jimport('joomla.filesystem.file');
		if (! JFile::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{
			return true;
		}
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';
		if (! class_exists('comquick2cartHelper'))
		{
			// require_once $path;
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}
		$mainframe = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart');
		$comquick2cartHelper = new comquick2cartHelper();
		$output = $comquick2cartHelper->getBuynow($item->id, 'com_k2');

		return $output;
	}
} // END CLASS

