<?php
// No direct access to this file
defined('_JEXEC') or die();

// Import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


/**
 * TaxSelect Form Field class for the J2Store component
 */
class JFormFieldQuick2cart extends JFormFieldList
{

	/**
	 * The field type.
	 *
	 * @var string
	 */
	protected $type = 'Quick2cart';

	function getInput ()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

		$fieldName = $this->fieldname;
		JHtml::_('behavior.modal', 'a.modal');
		$html = '';
		$client = "com_content";
		$jinput = JFactory::getApplication()->input;
		// $pid=$jinput->get('id');
		$isAdmin = JFactory::getApplication()->isAdmin();

		if (! $isAdmin)
		{
			$pid = $jinput->get('a_id');
		}
		else
		{
			$pid = $jinput->get('id');
		}

		// CHECK for view override
		$comquick2cartHelper = new comquick2cartHelper();
		$path = $comquick2cartHelper->getViewpath('attributes', '', "ADMIN", "SITE");
		ob_start();
		include $path;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
