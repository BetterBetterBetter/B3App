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

$lang = JFactory::getLanguage();
//$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

class Quick2cartControllerVendor extends quick2cartController
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$comquick2cartHelper = new comquick2cartHelper;

		$this->my_stores_itemid    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=stores&layout=my');
		$this->create_store_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');

		parent::__construct($config);
	}

	// + manoj
	function addNew()
	{
		$link = JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&Itemid=' . $this->create_store_itemid, false);

		$this->setRedirect($link);
	}

	// + manoj
	function edit()
	{
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);

		$link = JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $cid[0] . '&Itemid=' . $this->create_store_itemid, false);

		$this->setRedirect($link);
	}

	function save()
	{
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		$btnAction = $post->get('btnAction');
		$storeHelper = new storeHelper();
		$result = $storeHelper->saveVendorDetails($post);

		$qtcadminCall = $jinput->get('qtcadminCall');

		if ($btnAction == 'vendor.saveAndClose')
		{
			$link = JRoute::_('index.php?option=com_quick2cart&view=stores&layout=my&Itemid=' . $this->my_stores_itemid, false);
		}
		else
		{
			$link = JUri::base() . 'index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id='. $result['store_id'];
		}

		if (!empty($qtcadminCall))
		{
			$link = JUri::root() . 'administrator/index.php?option=com_quick2cart';
		}

		$this->setRedirect($link, $result['msg']);
	}

	function cancel()
	{
		$link = JRoute::_('index.php?option=com_quick2cart&view=stores&layout=my&Itemid=' . $this->my_stores_itemid, false);

		$this->setRedirect($link);
	}

	function refreshVendorDashboard()
	{
		$jinput=JFactory::getApplication()->input;
		$comquick2cartHelper = new comquick2cartHelper;
		$storeid=$jinput->get('storeid', '0', 'INT');
		$periodicorderscount='';

		$fromDate = $jinput->get('fromDate', '', 'STRING');

		if ($fromDate)
		{
			$fromDate = date('Y-m-d H:i:s', strtotime($fromDate));
		}

		$toDate = $jinput->get('toDate', '', 'STRING');

		if ($fromDate)
		{
			$toDate = date('Y-m-d H:i:s', strtotime($toDate));
		}

		$app = JFactory::getApplication();
		$app->setUserState('from', $fromDate);
		$app->setUserState('to', $toDate);

		//$session = JFactory::getSession();
		//$session->set('q2c_from_date', $fromDate);
		//$session->set('q2c_end_date', $toDate);
		/*
		$model=$this->getModel("vendor");
		$idata=$model->getPeriodicIncomeGrapthData($storeid,$fromDate,$toDate);

		// get formatted data for line chart
		$chartdata=$comquick2cartHelper->getLineChartFormattedData($idata);
		$getPeriodicIncome=$model->getPeriodicIncome($storeid,$fromDate,$toDate);
		//print"<pre>"; print_r($getPeriodicIncome);

		$response=array();
		$response['chartdat']=$chartdata;
		$response['qty']=$getPeriodicIncome['qty'];
		$response['revenue']=$getPeriodicIncome['amount'];
		$response['totorders']=$getPeriodicIncome['totorders'];

		echo $responsedata=json_encode($response);*/
		echo 1;
		jexit();
		//$periodicorderscount=0;
	}

	function refreshStoreView()
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$jinput=JFactory::getApplication()->input;
		$post	= $jinput->post;
		$store_id=$jinput->get('store_id');
		$current_store = $post->get('current_store');

		if(!empty($current_store))
				$mainframe->setUserState('current_store', $current_store);
 		$mainframe->setUserState('store_cat', $current_store);
		$this->setRedirect( JUri::base().'index.php?option=com_quick2cart&view=vendor&layout=vendor&layout=store&store_id='.$store_id);
	}

	/**This fuction will send customer email to store owner
	 * */
	function contactUsEmail()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
		$jinput=JFactory::getApplication()->input;
		$storeid=$jinput->get('store_id');
		$post = $jinput->post;

		$model=$this->getModel('vendor');
		$model->sendcontactUsEmail($post);
		$msgsent=JText::_('QTC_MSG_SENT') ;
		//$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=category", $msgsent );
		$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=vendor&layout=contactus&store_id=".$storeid.'&tmpl=component', $msgsent );
	}

	function ckUniqueVanityURL()
	{
		$jinput=JFactory::getApplication()->input;
		$vanityURL=$jinput->get('vanityURL','','RAW');
		$model=$this->getModel('vendor');
		$status=$model->ckUniqueVanityURL($vanityURL);
		if(!empty($status))
		{
			// present vanity URL
			echo 1;
		}
		else
		{
			echo 0;
		}

		jexit();
	}
}
