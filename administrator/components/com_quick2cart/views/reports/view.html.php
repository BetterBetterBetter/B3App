<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class quick2cartViewReports extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		$mainframe=JFactory::getApplication();
		$jinput = $mainframe->input;
		$option = $jinput->get('option');

		//default layout is default
		$layout = $jinput->get('layout','payouts','STRING');
		$this->setLayout($layout);

		//set toolbar
		$this->_setToolBar();
		$filter_order_Dir=$mainframe->getUserStateFromRequest('com_quick2cart.filter_order_Dir','filter_order_Dir','desc','word');
		$filter_type=$mainframe->getUserStateFromRequest('com_quick2cart.filter_order','filter_order','id','int');

		if($layout=='payouts')
		{
			$payouts=$this->get('Payouts');
			$this->payouts=$payouts;

			$total=$this->get('Total'); // use for pagination
			$this->total=$total;

			$pagination=$this->get('Pagination');
			$this->pagination=$pagination;
		}

		if($layout=='edit_payout')
		{
			$comquick2cartHelper=new comquick2cartHelper;
			$getPayoutFormData=$this->get('PayoutFormData');
			$this->getPayoutFormData=$getPayoutFormData;
			$payee_options=array();
			$payee_options[]=JHtml::_('select.option','0',JText::_('Select payee'));
			if(!empty($getPayoutFormData))
			{
				foreach($getPayoutFormData as $payout){
					$amt = round($payout->total_amount);
					if($amt > 0)
					{
						$username=$comquick2cartHelper->getUserName($payout->user_id);
						$payee_options[]=JHtml::_('select.option',$payout->user_id,$username);
					}
				}
			}
			$this->payee_options=$payee_options;

			$task = $jinput->get('task');
			$this->task = 'reports.' . $task;
			$payout_data = array();

			if ($task == 'edit_pay')
			{
				$payout_data = $this->get('SinglePayoutData');
			}

			$this->assignRef('payout_data', $payout_data);
		}
		$payee_name=$mainframe->getUserStateFromRequest('com_quick2cart', 'payee_name','', 'string' );
		//	$lists['payee_name']=$payee_name;
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_type;
		$this->lists=$lists;
		 // FOR DISPLAY SIDE FILTER
     if(JVERSION>=3.0)
            $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	function _setToolBar()
	{
		global $mainframe, $option;
		$mainframe=JFactory::getApplication();
		$jinput = $mainframe->input;

		$document=JFactory::getDocument();
		$document->addStyleSheet(JUri::base().'components/com_quick2cart/css/quick2cart.css');
		$bar=JToolBar::getInstance('toolbar');

		$layout = $jinput->get('layout');
		if($layout=='payouts')
		{
			// CSV EXPORT
			if(JVERSION >= 3.0)
			{
				JToolBarHelper::custom('csvexport', 'icon-32-save.png', 'icon-32-save.png',JText::_("COM_QUICK2CART_SALES_CSV_EXPORT"), false);
			}
			else
			{
				$button = "<a href='#' onclick=\"javascript:document.getElementById('task').value = 'csvexport';document.getElementById('controller').value = 'reports';document.adminForm.submit();\" ><span class='icon-32-save' title='Export'></span>".JText::_('COM_QUICK2CART_SALES_CSV_EXPORT')."</a>";
				$bar->appendButton( 'Custom', $button);
			}
		//	JToolBarHelper::title(JText::_('COM_QUICK2CART_PAYOUT_REPORTS'),'icon-48-quick2cart.png');
			//JToolBarHelper::addNewX('add');
			JToolBarHelper::addNew('reports.add', 'JTOOLBAR_NEW');
			JToolBarHelper::DeleteList(JText::_('COM_QUICK2CART_DELETE_PAYOUT_CONFIRM'),'delete','JTOOLBAR_DELETE');
			JToolBarHelper::title(JText::_('COM_QUICK2CART_REPORTS'),'icon-48-quick2cart.png');

		}elseif($layout=='edit_payout'){
			JToolBarHelper::title(JText::_('COM_QUICK2CART_EDIT_PAYOUT'),'icon-48-quick2cart.png');
		}

		$task = $jinput->get('task');

		if ($task == "save" || $task == "edit_pay")
		{
			JToolBarHelper::save('reports.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::back(JText::_('COM_QUICK2CART_BACK'), 'index.php?option=com_quick2cart&view=reports&layout=payouts');
		}
		else
		{
			JToolBarHelper::back(JText::_('COM_QUICK2CART_BACK'), 'index.php?option=com_quick2cart&view=reports');
		}
	}
}
