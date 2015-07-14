<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

if (!defined('DS'))
{
	define('DS', '/');
}

$lang = JFactory::getLanguage();
$lang->load('plg_qtcshipping_qtc_shipping_default', JPATH_ADMINISTRATOR);

class plgQtcshippingqtc_shipping_default extends JPlugin
{
	 /**
	 * Gives applicable Shipping charges.
	 *
	 * @param   integer   $amt   cart subtotal (after discounted amount )
	 * @param   object    $vars  object with cartdetail,billing and shipping details.
	 *
	 * @since   2.2
	 * @return   it should return array that contain [charges]=>>shipcharges [DetailMsg]=>after_ship_totalamt
	 * 				or return empty array
	 */
	function qtcshipping($amt, $vars='')
	{
		$shipping_limit=$this->params->get('shipping_limit');
		$return = array();

		if ((float)$amt<$shipping_limit)
		{
			$shipping_per = $this->params->get('shipping_per');
			//$shipping_value = ($shipping_per*$amt)/100;
			$return['charges'] = $shipping_per;
			$return['detailMsg'] = '';
		}

		return $return;

	}
}
