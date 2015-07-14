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

jimport('joomla.application.component.controllerform');

/**
 * Vendor form controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerVendor extends JControllerForm
{
	// @TODO - remove this when jform is used
	function addNew()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=vendor&layout=createstore');
	}

	// @TODO - remove this when jform is used
	function cancel()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=stores');
	}

	// @TODO - remove this when jform is used
	function edit()
	{
		$input = JFactory::getApplication()->input;

		// Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);

		$link = 'index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $cid[0] . '';
		$this->setRedirect($link);
	}

	function save()
	{
		$jinput =JFactory::getApplication()->input;
		$post = $jinput->post;
		$model = $this->getModel('vendor');
		$storeHelper = new storeHelper();
		$storeOwner = $post->get('store_creator_id');
		$result =$storeHelper->saveVendorDetails($post, $storeOwner);

		$msg = $result['msg'];
		$task= $jinput->get('task');

		$btnAction = $post->get('btnAction');

		if ($btnAction == 'vendor.saveAndClose')
		{
			$link=JUri::base()."index.php?option=com_quick2cart&view=stores";
			$this->setRedirect( $link, $msg );
		}
		else
		{
			switch ( $task )
			{
				case 'save':
				$this->setRedirect('index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $result['store_id'], $msg);
				break;
			}
		}
	}

	// @TODO - this function might be needed after 2.2 version
	// Added by Sneha
	function csvexport()
	{
		$model = $this->getModel("vendor");
		$CSVData = $model->getCsvexportData();
		$filename = "SalesPerSellerReport_".date("Y-m-d");
		$csvData = null;
		//$csvData.= "Item_id;Product Name;Store Name;Store Id;Sales Count;Amount;Created By;";

		$headColumn = array();
		$headColumn[0] = JText::_('COM_QUICK2CART_SALESPERSELLER_STORENAME');
		$headColumn[1] = JText::_('COM_QUICK2CART_SALESPERSELLER_VENDORNAME');// 'Product Name';
		$headColumn[2] = JText::_('COM_QUICK2CART_SALESPERSELLER_STATUS');
		$headColumn[3] = JText::_('COM_QUICK2CART_SALESPERSELLER_EMAIL');
		$headColumn[4] = JText::_('COM_QUICK2CART_SALESPERSELLER_PHONE');
		$headColumn[5] = JText::_('COM_QUICK2CART_SALESPERSELLER_SALE');
		$csvData .= implode(";",$headColumn);
		$csvData .= "\n";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") .".csv");
		header("Content-disposition: filename=".$filename.".csv");

		if (!empty($CSVData))
		{
			$storeHelper=new storeHelper();
			foreach($CSVData as $data)
			{
				$csvrow = array();
				$csvrow[0] = '"'.$data['title'].'"';
				$csvrow[1] = '"'.$data['username'].'"';

				if ($data['published'] == 1)
				{
					$status = JText::_('COM_QUICK2CART_PUBLISH');
				}
				else
				{
					$status = JText::_('COM_QUICK2CART_UNPUBLISH');
				}

				$csvrow[2] = '"'.$status.'"';
				$csvrow[3] = '"'.$data['store_email'].'"';
				$csvrow[4] = '"'.$data['phone'].'"';
				$storeHelper=new storeHelper();
				$comquick2cartHelper=new comquick2cartHelper;
				$total_sale=$storeHelper->getTotalSalePerStore($data['id']);

				if ($total_sale)
				{
					$sale = $comquick2cartHelper->getFromattedPrice($total_sale);
				}

				$csvrow[5] = '"'.$sale.'"';
				$csvData .= implode(";",$csvrow);
				$csvData .= "\n";
			}
		}

		ob_clean();
		echo $csvData."\n";
		jexit();

		$link = JUri::base().substr(JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=salespervendor',false),strlen(JUri::base(true))+1);

		$this->setRedirect( $link);
	}
}
