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

jimport('joomla.form.formfield');

/**
 * This Class provide store list which is require while creating tax profile etc (store must have created tax rate ).
 *
 * @package    Quick2Cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class JFormFieldTaxprofilestorelist extends JFormField
{
	var	$type = 'taxprofilestorelist';

	/**
	 * Fetch Element view.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getInput()
	{
		return $this->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}

	/**
	 * Fetch custom Element view.
	 *
	 * @param   string  $name          Field Name.
	 * @param   mixed   $value         Field value.
	 * @param   mixed   $node          Field node.
	 * @param   mixed   $control_name  Field control_name/Id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function fetchElement($name, $value, $node, $control_name)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		// While edit: get profile store id
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$id = $jinput->get('id');
		$defaultStore_id = 0;

		// Load tax helper.
		$path = JPATH_SITE . DS . "components" . DS . "com_quick2cart" . DS . 'helpers' . DS . "taxHelper.php";

		if (!class_exists('taxHelper'))
		{
			JLoader::register('taxHelper', $path);
			JLoader::load('taxHelper');
		}

		$taxHelper = new taxHelper;

		if ($id)
		{
			 $defaultStore_id = $taxHelper->getTaxProfileStoreId($id);
		}

		$storeList = $taxHelper->getStoreListForTaxprofile();

		$options = array();

		foreach ($storeList as $store)
		{
			$storename = ucfirst($store['title']);
			$options[] = JHtml::_('select.option', $store['store_id'], $storename);
		}

		$fieldName = $name;

		return JHtml::_('select.genericlist',  $options, $fieldName, 'class="inputbox required"  size="1"  ', 'value', 'text', $defaultStore_id, $control_name);
	}

	/* function fetchTooltip($label, $description, &$node, $control_name, $name){
		return NULL;
	}*/
}
