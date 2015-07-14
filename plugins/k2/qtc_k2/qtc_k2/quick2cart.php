<?php

// No direct access to this file
defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_k2/elements/base.php';

/**
 * TaxSelect Form Field class for the J2Store component
 */
class K2ElementQuick2cart extends K2Element
{

	function fetchElement ($name, $value, $node, $control_name)
	{
		$input = JFactory::getApplication()->input;
		$option = $input->get('option', '');

		if ($option != 'com_k2')
		{
			return;
		}

		jimport('joomla.filesystem.file');

		if (! JFile::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{
			return true;
		}
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		JHtml::_('behavior.modal', 'a.modal');
		$html = '';
		$client = "com_k2";
		$pid = JRequest::getInt('cid');
		// if($pid) {
		/* prefill k2 title */
		$db = JFactory::getDBO();
		$q = "SELECT `title` FROM `#__k2_items` WHERE `id` =" . (int) $pid;
		$db->setQuery($q);
		$k2item = $db->loadResult();
		$jinput = JFactory::getApplication()->input;
		$jinput->set('qtc_article_name', $k2item);
		/* prefill k2 title */

		if (!class_exists('comquick2cartHelper')) // load helper file if not exist
		{
			// Require_once $path;
			$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$comquick2cartHelper = new comquick2cartHelper();
		$path = $comquick2cartHelper->getViewpath('attributes', '', "ADMIN", "SITE");
		ob_start();
		include $path;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}

class JFormFieldQuick2cart extends K2ElementQuick2cart
{

	var $type = 'Quick2cart';
}

class JElementQuick2cart extends K2ElementQuick2cart
{
	var $_name = 'Quick2cart';
}
