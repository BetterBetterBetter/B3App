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
 * View class for a list of Quick2cart.
 */
class Quick2cartViewShipping extends JViewLegacy
{

	function display($tpl = null)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$app = JFactory::getApplication();
		$this->params = $app->getParams('com_quick2cart');
		$zoneHelper = new zoneHelper;
		// Check whether view is accessible to user
		if (!$zoneHelper->isUserAccessible())
		{
			return;
		}

		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		$option = 'com_quick2cart';
		$nameSpace = 'com_quick2cart.shipping';
		$task = $jinput->get('task');
		$view = $jinput->get('view','');
		$layout = $jinput->get('layout','default');
		// Get other vars
		$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar');

		if ($layout == 'default')
		{
			// Display list of pluigns
			$filter_order		= $mainframe->getUserStateFromRequest($nameSpace.'filter_order',		'filter_order',		'tbl.id',	'cmd');
			$filter_order_Dir	= $mainframe->getUserStateFromRequest($nameSpace.'filter_order_Dir',	'filter_order_Dir',	'',				'word');
			$filter_orderstate	= $mainframe->getUserStateFromRequest($nameSpace.'filter_orderstate',	'filter_orderstate',	'', 'string');
			$filter_name = $mainframe->getUserStateFromRequest($nameSpace.'filter_name',		'filter_name',		'tbl.name',	'cmd');

			$search				= $mainframe->getUserStateFromRequest($nameSpace.'search',			'search',			'',				'string');
			if (strpos($search, '"') !== false) {
				$search = str_replace(array('=', '<'), '', $search);
			}
			$search = JString::strtolower($search);

			$model = $this->getModel('shipping');

			// Get data from the model
			$items = $this->get('Items');
			$total		=  $model->getTotal();
			$pagination =  $model->getPagination();

			if (count($items) == 1)
			{
				// If there is only one plug then redirct to that shipping view
				$extension_id = $items[0]->extension_id;
				$this->form = '';

				if ($extension_id)
				{
					$this->form = $this->getShipPluginForm($extension_id);

					$plugConfigLink = "index.php?option=com_quick2cart&task=shipping.getShipView&extension_id=".$extension_id;

					$CpItemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
					$redirect = JRoute::_($plugConfigLink . '&Itemid='.$itemid, false);
					$app->redirect($redirect);

				}

			}
			// table ordering
			$lists['order_Dir'] = $filter_order_Dir;
			$lists['order'] = $filter_order;
			$lists['filter_name'] = $filter_name;

			// search filter
			$lists['search']= $search;

			$this->assignRef('lists',		$lists);
			$this->assignRef('items',		$items);
			$this->assignRef('pagination',	$pagination);

		}
		elseif ($layout == 'list')
		{
			$this->form = '';
			$extension_id = $jinput->get('extension_id');

			if ($extension_id)
			{
				$this->form = $this->getShipPluginForm($extension_id);
			}
		}


		//JToolBarHelper::title(JText::_('COM_QUICK2CART_SHIPM_SHIPPING_METHODS','quick2cart-logo');
		//JToolBarHelper::title(JText::_('COM_QUICK2CART_SHIPM_SHIPPING_METHODS'));

		parent::display($tpl);
	}


/*function _form($tpl=null)
	{
	   parent::_form($tpl);

		$row = $this->getModel()->getItem();
		$import = JPluginHelper::importPlugin('tjshipping', $row->element);
	}*/
	protected function getShipPluginForm($extension_id)
	{
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		$qtcshiphelper = new qtcshiphelper;
		$plugName = $qtcshiphelper->getPluginDetail($extension_id);
		$import = JPluginHelper::importPlugin('tjshipping', $plugName);
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('TjShip_shipBuildLayout', array($jinput));

		if (!empty($result[0]))
		{
			return $this->form = $result[0];
		}
		return '';
	}


}
