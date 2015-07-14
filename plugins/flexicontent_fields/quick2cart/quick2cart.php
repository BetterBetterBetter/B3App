<?php
/**
 * @version 1.0 $Id: weblink.php 1627 2013-01-14 06:59:25Z ggppdk $
 * @package Joomla
 * @subpackage FLEXIcontent
 * @subpackage plugin.weblink
 * @copyright (C) 2009 Emmanuel Danan - www.vistamedia.fr
 * @license GNU/GPL v2
 *
 * FLEXIcontent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
defined('_JEXEC') or die('Restricted access');
if (!defined('DS'))
{
	define('DS', '/');
}


//jimport('joomla.plugin.plugin');
jimport('joomla.event.plugin');

$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';
if (! class_exists('comquick2cartHelper'))
{
	// require_once $path;
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

class plgFlexicontent_fieldsQuick2cart extends JPlugin
{
	static $field_types = array('quick2cart');


	// ***********
	// CONSTRUCTOR
	// ***********
	function plgFlexicontent_fieldsQuick2cart (&$subject, $params)
	{
		parent::__construct($subject, $params);
		JPlugin::loadLanguage('plg_flexicontent_fields_quick2cart', JPATH_ADMINISTRATOR);
	}

	// *******************************************
	// DISPLAY methods, item form & frontend views
	// *******************************************

	// Method to create field's HTML display for item form
	function onDisplayField (&$field, &$item)
	{
		// print_r($item->id);die;
		// execute the code only if the field type match the plugin type
		if (! in_array($field->field_type, self::$field_types))
			return;

		$field->label = JText::_($field->label);

		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

		// for copying the the item title to q2c Item Name
		JRequest::setVar("qtc_article_name", $item->title);

		JHtml::_('behavior.modal', 'a.modal');
		$html = '';
		$client = "com_flexicontent";
		$input = JFactory::getApplication()->input;
		$pid = $item->id;

		// CHECK for view override
		$comquick2cartHelper = new comquick2cartHelper();
		$path = $comquick2cartHelper->getViewpath('attributes', '', "ADMIN", "SITE");
		ob_start();
		include $path;
		$field->html = ob_get_contents();
		ob_end_clean();

	}

	// Method to create field's HTML display for frontend views
	function onDisplayFieldValue (&$field, $item, $values = null, $prop = 'display')
	{
		// execute the code only if the field type match the plugin type
		if ($field->field_type != 'quick2cart')
			return;

		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{
			$mainframe = JFactory::getApplication();
			$lang = JFactory::getLanguage();
			$lang->load('com_quick2cart');
			$comquick2cartHelper = new comquick2cartHelper();
			$output = $comquick2cartHelper->getBuynow($item->id, "com_flexicontent");
		}
		$field->{$prop} = $output;
	}

	// **************************************************************
	// METHODS HANDLING before & after saving / deleting field events
	// **************************************************************

	// Method to handle field's values after they are saved into the DB
	function onAfterSaveField (&$field, &$post, &$file, &$item)
	{
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';
		if (class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$input = JFactory::getApplication()->input;
		$post_data = $input->post;

		$item_name = $post_data->get('item_name', '', 'STRING');
		$sku = $post_data->get('sku', '', 'RAW');
		$stock = $post_data->get('stock', '');
		$min_qty = $post_data->get('min_item');
		$max_qty = $post_data->get('max_item');

		// getting store id
		$store_id = $input->get('store_id', '0');
		$pid = $item->id;
		if (! $pid || empty($store_id))
		{
			return;
		}

		$itemstate = $input->get('itemstate', 0, "INTEGER");
		$post_data->set('state', $itemstate);
		$comquick2cartHelper = new comquick2cartHelper();
		$client = $post_data->set('client', 'com_flexicontent');
		$pid = $post_data->set('pid', $pid);

		$comquick2cartHelper = $comquick2cartHelper->saveProduct($post_data);
	}

	// **************************************************************
	// METHODS HANDLING before & after saving / deleting field events
	// **************************************************************

	// Method to handle field's values before they are saved into the DB
	function onBeforeSaveField (&$field, &$post, &$file, &$item)
	{
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';
		if (! class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}
		$postdata = JRequest::get('post');
		if (! $postdata['jform']['id']) // if first time save //@TODO change into
		                              // jinput
			return;
		$input = JFactory::getApplication()->input;
		$post_data = $input->post;

		$item_name = $post_data->get('item_name', '', 'STRING');
		$sku = $post_data->get('sku', '', 'RAW');
		$stock = $post_data->get('itemstock', '');
		$min_qty = $post_data->get('min_item');
		$max_qty = $post_data->get('max_item');
		$itemstate = $input->get('itemstate', 0, "INTEGER");
		$post_data->set('state', $itemstate);
		// getting store id
		$store_id = $input->get('store_id', '0');
		$comquick2cartHelper = new comquick2cartHelper();

		// $comquick2cartHelper=$comquick2cartHelper->saveProduct($item->id,"com_flexicontent",$store_id,$item_name,$post_data,$stock,$min_qty,$max_qty,'',$sku);
	}

	// Method called just before the item is deleted to remove custom item data
	// related to the field
	function onBeforeDeleteField (&$field, &$item)
	{
		$articleId = isset($item->id) ? $item->id : 0;

		if ($articleId)
		{
			$db = JFactory::getDbo();
			$db->setQuery("DELETE FROM #__kart_items WHERE product_id = $articleId AND  parent = 'com_flexicontent'");
			if (! $db->execute())
			{
				$messagetype = 'notice';
				$message = JText::_('QTC_PARAMS_DEL_FAIL') . " - " . $db->stderr();
			}
		}
	}

	// **********************
	// VARIOUS HELPER METHODS
	// **********************
	function cleanurl ($url)
	{
		$prefix = array(
				"http://",
				"https://",
				"ftp://"
		);
		$cleanurl = str_replace($prefix, "", $url);
		return $cleanurl;
	}

}
