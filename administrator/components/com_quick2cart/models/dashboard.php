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

jimport('joomla.application.component.model');

/**
 * Dashboard Model for an Q2C.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelDashboard extends JModelLegacy
{
	/**
	 * Method to get title of dashboard
	 *
	 * @param   string  $title    Title of box
	 * @param   string  $content  Content of box
	 * @param   object  $type     type of data
	 *
	 * @return  html  $html  title of dashboard
	 *
	 * @since   2.2
	 */
	public function getbox($title, $content, $type = null)
	{
		$html = '
			<div class="row-fluid">
				<div class="span12"><h5>' . $title . '</h5></div>
			</div>
			<div class="row-fluid">
				<div class="span12">' . $content . '</div>
			</div>';

		return $html;
	}

	/**
	 * Returns overall total income amount
	 *
	 * @return  float  get overall income
	 *
	 * @since   2.2
	 */
	public function getAllOrderIncome()
	{
		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();
		$query = "SELECT FORMAT(SUM(amount), 2)
		 FROM #__kart_orders
		 WHERE (status='C' OR status='S') AND currency='" . $currency . "'
		 AND (processor NOT IN('jomsocialpoints', 'alphapoints') OR extra='points')";

		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		return $result;
	}

	/**
	 * Returns overall total income per month
	 *
	 * @return  float  get total income per month
	 *
	 * @since   2.2
	 */
	public function getMonthIncome()
	{
		$db = JFactory::getDBO();

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		// $backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));

		$curdate    = date('Y-m-d');
		$back_year  = date('Y') - 1;
		$back_month = date('m') + 1;
		$backdate   = $back_year . '-' . $back_month . '-' . '01';

		/* Query echo $query = "SELECT FORMAT(SUM(amount),2) FROM #__kart_orders
		WHERE status ='C' AND cdate between (".$curdate.",".$backdate." )
		GROUP BY YEAR(cdate), MONTH(cdate) order by YEAR(cdate), MONTH(cdate)
		*/
		$query = "SELECT FORMAT( SUM( amount ) , 2 ) AS amount, MONTH( cdate ) AS MONTHSNAME, YEAR( cdate ) AS YEARNM
		FROM `#__kart_orders`
		WHERE DATE(cdate)
		BETWEEN  '" . $backdate . "'
		AND  '" . $curdate . "'
		AND (
		processor NOT
		IN (
		'payment_jomsocialpoints',  'payment_alphapoints'
		)
		OR extra =  'points'
		)
		AND ( STATUS =  'C' OR STATUS =  'S') AND currency='" . $currency . "'
		GROUP BY YEARNM, MONTHSNAME
		ORDER BY YEAR( cdate ) , MONTH( cdate ) ASC";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Returns overall total income per month
	 *
	 * @return  float  get total income per month
	 *
	 * @since   2.2
	 */
	public function getAllmonths()
	{
		$date2      = date('Y-m-d');
		$back_year  = date('Y') - 1;
		$back_month = date('m') + 1;
		$date1      = $back_year . '-' . $back_month . '-' . '01';

		// Convert dates to UNIX timestamp
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		$tmp   = date('mY', $time2);

		$months[] = array("month" => date('F', $time1), "year" => date('Y', $time1));

		while ($time1 < $time2)
		{
			$time1 = strtotime(date('Y-m-d', $time1) . ' +1 month');

			if (date('mY', $time1) != $tmp && ($time1 < $time2))
			{
				$months[] = array(
					"month" => date('F', $time1),
					"year" => date('Y', $time1)
				);
			}
		}

		$months[] = array("month" => date('F', $time2),"year" => date('Y', $time2));

		return $months;
	}

	/**
	 * Function for pie chart
	 *
	 * @return  array  Get data for pie chart
	 *
	 * @since   2.2
	 */
	public function statsforpie()
	{
		$db                  = JFactory::getDBO();
		$session             = JFactory::getSession();

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		$qtc_graph_from_date = $session->get('qtc_graph_from_date');
		$socialads_end_date  = $session->get('socialads_end_date');

		$where   = "AND currency='" . $currency . "'";
		$groupby = '';

		if ($qtc_graph_from_date)
		{
			// For graph
			$where .= " AND DATE(mdate) BETWEEN DATE('" . $qtc_graph_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}
		else
		{
			$day         = date('d');
			$month       = date('m');
			$year        = date('Y');
			$statsforpie = array();

			$backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 30 days'));
			$groupby  = "";
		}

		// Pending order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'P'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		// Confirmed order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'C'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		// Rejected order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'RF'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		// Shipped order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'S'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		return $statsforpie;
	}

	/*
	public function getignoreCount()
	{
	$db=JFactory::getDBO();
	$session = JFactory::getSession();
	$qtc_graph_from_date=$session->get('qtc_graph_from_date');
	$socialads_end_date=$session->get('socialads_end_date');
	$where='';

	if ($qtc_graph_from_date)
	{
	$where="WHERE  DATE(idate) BETWEEN DATE('".$qtc_graph_from_date."') AND DATE('".$socialads_end_date."')";
	}

	$query = "SELECT COUNT(*) as ignorecount,DATE(idate) as idate FROM #__ad_ignore  ".$where." GROUP bY DATE(idate) ORDER BY DATE(idate)";

	$this->_db->setQuery($query);
	$cnt= $this->_db->loadObjectList();
	return $cnt;
	}
	*/

	/**
	 * Returns periodic income based on session data
	 *
	 * @return  INT  periodic income based on session data
	 *
	 * @since   2.2
	 */
	public function getperiodicorderscount()
	{
		$db      = JFactory::getDBO();
		$session = JFactory::getSession();

		$qtc_graph_from_date = $session->get('qtc_graph_from_date');
		$socialads_end_date  = $session->get('socialads_end_date');
		$where               = '';
		$groupby             = '';

		if ($qtc_graph_from_date)
		{
			$where = " AND DATE(mdate) BETWEEN DATE('" . $qtc_graph_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}
		else
		{
			$qtc_graph_from_date = date('Y-m-d');
			$backdate            = date('Y-m-d', strtotime(date('Y-m-d') . ' - 30 days'));
			$where               = " AND DATE(mdate) BETWEEN DATE('" . $backdate . "') AND DATE('" . $qtc_graph_from_date . "')";
			$groupby             = "";
		}

		$query = "SELECT FORMAT(SUM(amount),2) FROM #__kart_orders WHERE (status ='C' OR status ='S')
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		return $result;
	}

	/**
	 * Returns periodic income based on session data
	 *
	 * @return  INT  periodic income based on session data
	 *
	 * @since   2.2
	 */
	public function notShippedDetails()
	{
		$where   = array();
		$where[] = ' o.`status`="C" ';
		$where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$db    = JFactory::getDBO();
		$query = 'SELECT o.id,o.prefix,o.`name`,amount FROM `#__kart_orders` AS o ' . $where . ' ORDER BY o.`mdate` LIMIT 0,7';
		$db->setQuery($query);

		return $result = $db->loadAssocList();
	}

	/**
	 * Returns periodic income based on session data
	 *
	 * @return  INT  periodic income based on session data
	 *
	 * @since   2.2
	 */
	public function getpendingPayouts()
	{
		if (!class_exists('Quick2cartModelPayouts'))
		{
			JLoader::register('Quick2cartModelPayouts', JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/payouts.php');
			JLoader::load('Quick2cartModelPayouts');
		}

		$Quick2cartModelPayouts = new Quick2cartModelPayouts;

		return $Quick2cartModelPayouts->getPayoutFormData();
	}

	/**
	 * Returns orders count
	 *
	 * @return  INT  $ordersCount  orders count
	 *
	 * @since   2.2
	 */
	public function getOrdersCount()
	{
		$db    = JFactory::getDBO();
		$query = "SELECT COUNT(id)
		 FROM #__kart_orders";
		$db->setQuery($query);
		$ordersCount = $db->loadResult();

		return $ordersCount;
	}

	/**
	 * Returns products count
	 *
	 * @return  INT  products count
	 *
	 * @since   2.2
	 */
	public function getProductsCount()
	{
		$db    = JFactory::getDBO();
		$query = "SELECT COUNT(item_id)
		 FROM #__kart_items";

		$db->setQuery($query);
		$productsCount = $db->loadResult();

		return $productsCount;
	}

	/**
	 * Returns stores count
	 *
	 * @return  INT  stores count
	 *
	 * @since   2.2
	 */
	public function getStoresCount()
	{
		$db    = JFactory::getDBO();
		$query = "SELECT COUNT(id)
		 FROM #__kart_store";

		$db->setQuery($query);
		$storesCount = $db->loadResult();

		return $storesCount;
	}
}
