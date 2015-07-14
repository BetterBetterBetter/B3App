<?php

/**
* @version    SVN: <svn_id>
* @package    Quick2cart
* @author     Techjoomla <extensions@techjoomla.com>
* @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
* @license    GNU General Public License version 2 or later.
*/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');


/**
* View to edit
*/
class Quick2cartViewTaxrateform extends JViewLegacy
{

	protected $state;
	protected $item;
	protected $form;
	protected $params;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$zoneHelper = new zoneHelper;
		// Check whether view is accessible to user
		if (!$zoneHelper->isUserAccessible())
		{
			return;
		}
		$app	= JFactory::getApplication();
		$user		= JFactory::getUser();

		$this->state = $this->get('State');
		$this->item = $this->get('Data');
		$this->params = $app->getParams('com_quick2cart');
		$this->form		= $this->get('Form');

		if (!empty($this->item->zone_id))
		{
			$zoneDetail = $zoneHelper->getZoneDetail($this->item->zone_id);
			// Check whether user is authorized for this zone ?
			if (!empty($zoneDetail['store_id']))
			{
				$status = $comquick2cartHelper->store_authorize('taxrateform_default', $zoneDetail['store_id']);

				if (!$status)
				{
					$zoneHelper->showUnauthorizedMsg();
					return false;
				}

			}
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_QUICK2CART_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

}
