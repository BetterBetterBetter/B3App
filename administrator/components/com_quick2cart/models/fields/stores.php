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

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldStores extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'stores';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getOptions()
	{
		require_once (JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php');
		$comquick2cartHelper = new comquick2cartHelper;

		// Get all stores.
		$stores = $comquick2cartHelper->getAllStoreDetails();

		$options   = array();
		//$options[] = JHtml::_('select.option', '', JText::_('QTC_SELET_STORE'));

		foreach ($stores as $key=>$value)
		{
			$options[] = JHtml::_('select.option', $key, $value['title']);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
