<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die( 'Restricted access' );
//require_once( JPATH_COMPONENT.DS.'views'.DS.'config'.DS.'view.html.php' );

$lang = JFactory::getLanguage();
$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

jimport('joomla.application.component.controller');
class Quick2cartControllerReports extends quick2cartController
{
	//saves new campaign
	//die('reports');
	function save()
	{
		JSession::checkToken()( or jexit('Invalid Token');
		//get model
		$model=$this->getModel('reports');
		$result=$model->savePayout();
		$redirect=JRoute::_('index.php?option=com_quick2cart&view=reports&layout=payouts',false);
		if($result){
			$msg= JText::_('COM_QUICK2CART_PAYOUT_SAVED');
		}
		else{
			$msg= JText::_('COM_QUICK2CART_PAYOUT_ERROR_SAVING');
		}
		$this->setRedirect($redirect,$msg);
	}

	function edit_pay()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('reports');
		$result = $model->editPayout();

		$redirect = JRoute::_('index.php?option=com_quick2cart&view=reports&layout=payouts', false);

		if ($result)
		{
			$msg = JText::_('COM_QUICK2CART_PAYOUT_SAVED');
		}
		else
		{
			$msg = JText::_('COM_QUICK2CART_PAYOUT_ERROR_SAVING');
		}

		$this->setRedirect($redirect, $msg);
	}

	//Added by Sneha
	function  csvexport()
	{
		$model = $this->getModel("reports");
		$CSVData = $model->getCsvexportData();
		$filename = "StoreOwnerPayouts_".date("Y-m-d");
		$csvData = null;
		//$csvData.= "Item_id;Product Name;Store Name;Store Id;Sales Count;Amount;Created By;";

		$headColumn = array();
		$headColumn[0] = JText::_('COM_QUICK2CART_PAYOUTS_ID');
		$headColumn[1] = JText::_('COM_QUICK2CART_PAYOUTS_NAME');// 'Product Name';
		$headColumn[2] = JText::_('COM_QUICK2CART_PAYOUTS_EMAIL');
		$headColumn[3] = JText::_('COM_QUICK2CART_PAYOUTS_TRANS_ID');
		$headColumn[4] = JText::_('COM_QUICK2CART_PAYOUTS_DATE');
		$headColumn[5] = JText::_('COM_QUICK2CART_PAYOUTS_STATUS');
		$headColumn[6] = JText::_('COM_QUICK2CART_PAYOUTS_AMOUNT');

		$csvData .= implode(";",$headColumn);
		$csvData .= "\n";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") .".csv");
		header("Content-disposition: filename=".$filename.".csv");

		if(!empty($CSVData))
		{
			$storeHelper=new storeHelper();
			foreach($CSVData as $data) {
				$csvrow = array();
				$csvrow[0] = '"'.$data['id'].'"';
				$csvrow[1] = '"'.$data['payee_name'].'"';
				$csvrow[2] = '"'.$data['email_id'].'"';
				$csvrow[3] = '"'.$data['transaction_id'].'"';
				if(JVERSION<'1.6.0')
					$date = JHtml::_( 'date', $data['date'], '%Y/%m/%d');
				else
					$date = JHtml::_( 'date', $data['date'], "Y-m-d");
				$csvrow[4] = '"'.$date.'"';
				if($data['status']==1)
					$status = JText::_('COM_QUICK2CART_PAID');
				else
					$status = JText::_('COM_QUICK2CART_NOT_PAID');
				$csvrow[5] = '"'.$status.'"';
				$csvrow[6] = '"'.$data['amount'].'"';
				$csvData .= implode(";",$csvrow);
				$csvData .= "\n";
			}
		}
		ob_clean();
		echo    $csvData."\n";
		jexit();
		$link=JUri::base().substr(JRoute::_('index.php?option=com_quick2cart&view=reports&layout=payouts',false),strlen(JUri::base(true))+1);
		$this->setRedirect( $link);
	}
	/*
	function pp()
	{
			die('new_edit');
	}*/

}?>
