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

jimport('joomla.html.parameter.element');
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Gives HTML element as header
 */
class JFormFieldHeader extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	var $type='Header';

	function getInput()
	{
		$document = JFactory::getDocument();

		$document->addStyleSheet(JUri::base() . 'components/com_quick2cart/assets/css/quick2cart.css');

		$return='
		<div class="q2cHeaderOuterDiv">
			<div class="q2cHeaderInnerDiv">
				' . JText::_($this->value) . '
			</div>
		</div>';

		return $return;
	}
}
