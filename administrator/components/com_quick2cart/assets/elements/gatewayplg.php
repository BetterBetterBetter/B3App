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
 * Supports an HTML select list of gateways
 */
class JFormFieldGatewayplg extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	var	$type = 'Gatewayplg';

	function getInput()
	{
		return $this->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}

	function fetchElement($name, $value, $node, $control_name)
	{
		$db = JFactory::getDBO();

		$condition = array(0 => '\'payment\'');

		$conditionType = join(',', $condition);

		$query = "SELECT extension_id as id, name, element, enabled as published
		 FROM #__extensions
		 WHERE folder in (" . $conditionType . ") AND enabled=1";

		$db->setQuery($query);
		$gatewayplugin = $db->loadobjectList();

		$options = array();

		foreach ($gatewayplugin as $gateway)
		{
			$gatewayname = ucfirst(str_replace('plugpayment', '', $gateway->element));
			$options[] = JHtml::_('select.option',$gateway->element, $gatewayname);
		}

		$fieldName = $name;

		$html = JHtml::_('select.genericlist', $options, $fieldName, 'class="inputbox required"  multiple="multiple" size="5"', 'value', 'text', $value, $control_name . $name);

		if (JVERSION < '3.0')
		{
			$class = "q2c-elements-gateways-link";
		}
		else
		{
			$class = "";
		}
		
		// Show link for payment plugins.
		$html .= '<a
			href="index.php?option=com_plugins&view=plugins&filter_folder=payment&filter_enabled="
			target="_blank"
			class="btn btn-small btn-primary ' . $class . '">'
				. JText::_('COM_QUICK2CART_SETTINGS_SETUP_PAYMENT_PLUGINS') .
			'</a>';

		return $html;
	}
}
