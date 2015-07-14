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
 * Dashboard form controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerDashboard extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	function getVersion()
	{
		if (!class_exists('comquick2cartHelper'))
		{
			$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';
			JLoader::register('comquick2cartHelper', $path );
			JLoader::load('comquick2cartHelper');
		}

		$helperobj = new comquick2cartHelper;
		echo $latestversion = $helperobj->getVersion();
		jexit();
	}

	function SetsessionForGraph()
	{
		$periodicorderscount = '';
		$fromDate = $_GET['fromDate'];
		$toDate = $_GET['toDate'];
		$periodicorderscount = 0;

		$session = JFactory::getSession();
		$session->set('qtc_graph_from_date', $fromDate);
		$session->set('socialads_end_date', $toDate);

		$model = $this->getModel('dashboard');
		$statsforpie = $model->statsforpie();
		//$ignorecnt = $model->getignoreCount();
		$periodicorderscount = $model->getperiodicorderscount();
		$session->set('statsforpie', $statsforpie);
		//$session->set('ignorecnt', $ignorecnt);
		$session->set('periodicorderscount', $periodicorderscount);

		header('Content-type: application/json');
		echo (json_encode(array("statsforpie" => $statsforpie /*,"ignorecnt" => $ignorecnt*/)));
		jexit();
	}

	function makechart()
	{
		$month_array_name = array(
			JText::_('SA_JAN'),
			JText::_('SA_FEB'),
			JText::_('SA_MAR'),
			JText::_('SA_APR'),
			JText::_('SA_MAY'),
			JText::_('SA_JUN'),
			JText::_('SA_JUL'),
			JText::_('SA_AUG'),
			JText::_('SA_SEP'),
			JText::_('SA_OCT'),
			JText::_('SA_NOV'),
			JText::_('SA_DEC')
		);

		$session = JFactory::getSession();
		$qtc_graph_from_date = '';
		$socialads_end_date = '';

		$qtc_graph_from_date= $session->get('fromDate', '');
		$socialads_end_date = $session->get('socialads_end_date', '');
		$total_days = (strtotime($socialads_end_date) - strtotime($qtc_graph_from_date)) / (60 * 60 * 24);
		$total_days = $total_days + 1;

		$statsforpie = $session->get('statsforpie', '');
		$model = $this->getModel('dashboard');
		$statsforpie = $model->statsforpie();

		$ignorecnt = $session->get('ignorecnt', '');
		$periodicorderscount = $session->get('periodicorderscount');
		$imprs = 0;
		$clicks = 0;
		$max_invite = 100;
		$cmax_invite = 100;
		$yscale = "";
		$titlebar = "";
		$daystring = "";
		$finalstats_date = array();
		$finalstats_clicks = array();
		$finalstats_imprs = array();
		$day_str_final = '';
		$emptylinechart = 0;
		$barchart = '';
		$fromDate = $session->get('qtc_graph_from_date', '');
		$toDate = $session->get('socialads_end_date', '');

		$dateMonthYearArr = array();
		$fromDateSTR = strtotime($fromDate);
		$toDateSTR = strtotime($toDate);
		$pending_orders = $confirmed_orders = $shiped_orders = $refund_orders = 0;

		if (empty($statsforpie[0]) && empty($statsforpie[1]) && empty($statsforpie[2]))
		{
			$barchart = JText::_('NO_STATS');
			$emptylinechart = 1;
		}
		else
		{
			if (!empty($statsforpie[0]))
			{
				$pending_orders = $statsforpie[0][0]->orders;
			}

			if (!empty($statsforpie[1]))
			{
				$confirmed_orders = $statsforpie[1][0]->orders;
				$shiped_orders = $statsforpie[3][0]->orders;
			}

			if (!empty($statsforpie[1]))
			{
				$refund_orders = $statsforpie[2][0]->orders;
			}
		}

		//$barchart='<img src="http://chart.apis.google.com/chart?cht=lc&chtt=+'.$titlebar.'|'.JText::_('NUMICHITSMON').'  	+&chco=0000ff,ff0000&chs=900x310&chbh=a,25&chm='.$chm_str.'&chd=t:'.$imprs.'|'.$clicks.'&chxt=x,y&chxr=0,0,200&chds=0,'.$max_invite.',0,'.$cmax_invite.'&chxl=1:|'.$yscale.'|0:|'. $daystring.'|" />';

		header('Content-type: application/json');
		echo (json_encode(array("pending_orders" => $pending_orders,
			"confirmed_orders" => $confirmed_orders,
			"shiped_orders" => $shiped_orders,
			"refund_orders" => $refund_orders,
			"periodicorderscount" => $periodicorderscount,
			"emptylinechart" => $emptylinechart
		)));
		jexit();
	}
}
