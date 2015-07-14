<?php
/*
 * @package Quick2Cart sample development System plugin
 * @copyright Copyright (C) 2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.application.application');

if (!defined('DS'))
{
	define('DS', '/');
}

//require_once(JPATH_SITE .DS. 'components'.DS.'com_quick2cart'.DS.'helper.php');
class plgSystemQtc_sample_development extends JPlugin
{
	public function Onq2cOrderUpdate($order)
	{
		$affiliate_status = trim($this->params->get('affiliate_status'));
		if (!empty($affiliate_status))
		{
			if ($order->status == 'C')
			{
				//for idev affiliate
				//http://techjoomla.com/affiliates/sale.php?profile=72198&idev_saleamt=XXX&idev_ordernum=XXX
				//for post affiliate
				//http://demo.qualityunit.com/pax4/scripts/sale.php?AccountId=default1&TotalCost=120.50&OrderID=ORD_12345XYZ&ProductID=test_product
				//http://www.yoursite.com/affiliate/scripts/sale.php?TotalCost=120.50&OrderID=ORD_12345XYZ&ProductID=test+product

				$baseurl = $this->params->get('baseurl');

				if ($baseurl)
				{
					$affiliate = $this->params->get('affiliate');

					switch ($affiliate)
					{
						case 'iDevAffi':
							//  Here you have to add base url as a idevaffiliate path eg http://YOUR_SITE_BASE_PATH/IDEVAFFILIATE_DIR/
							/*
							// START IDEVAFFILIATE TRACKING
							// ----------------------------
							$idev_saleamount = $order['details']['BT']->order_subtotal;
							$idev_ordernum = $order['details']['BT']->order_number;
							$idev_coupon_discount = $order['details']['BT']->coupon_discount;
							$idev_coupon_code = $order['details']['BT']->coupon_code;
							echo "<img border=\"0\" src=$idev_saleamount = $idev_saleamount - $idev_coupon_discount;
							\"http://durgesh.tekdi.net/idevaffiliate/sale.php?profile=90&idev_saleamt=$idev_saleamount&idev_ordernum=$idev_ordernum&coupon_code=$idev_coupon_code\" width=\"1\" height=\"1\">";
							// ----------------------------
							// END IDEVAFFILIATE TRACKING
							* */
							$affiliate_link = $baseurl . "/sale.php?idev_saleamt=" . $order->amount . "&idev_ordernum=" . $order->id;
							//	$affiliate_link = $baseurl."/index.php?option=com_quick2cart&task=test&idev_saleamt=".$order->amount."&idev_ordernum=".$order->id;
							break;
						case 'PostAffiPro':
							$affiliate_link = $baseurl . "/affiliate/scripts/sale.php?TotalCost=" . $order->amount . "&OrderID=" . $order->id;
							//				$affiliate_link =$baseurl."/index.php?option=com_quick2cart&task=test&idev_saleamt=".$order->amount."&idev_ordernum=".$order->id;
							break;
					}
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $affiliate_link);
					//    curl_setopt($ch, CURLOPT_FILE, $fp);

					$data = curl_exec($ch);
					// Check if any error occured
					if (curl_errno($ch))
					{
						$curlmsg = 'Curl error: ' . curl_error($ch);
					}
					else
					{
						$curlmsg = '1'; //'Operation completed of download';
					}
					curl_close($ch);
				}
			}
		}
		//		die('jajua');
	}

	public function OnBeforeq2cOrderUpdateEmail($order, $email_subject, $email_body)
	{
	}
	public function OnBeforeq2cPay($order_vars)
	{
		/*	$usd_exchange_status =trim( $this->params->get('usd_exchange_status'));
		if(!empty($usd_exchange_status))
		{
		$comquick2cartHelper = new comquick2cartHelper();
		$currency = $comquick2cartHelper->getCurrencySession();
		if($currency){
		switch($currency){
		case 'USD':
		$convert_val = $this->params->get("usd_exchange");
		if($convert_val){
		$order_vars->amount = $order_vars->amount * $convert_val;
		}
		break;
		}
		}
		}
		return $order_vars;*/
	}
	/** $param :: $order (array) contain oder and order item information
	 * $param :: $data (array) contain oder contain checkout post detail after
	 * */
	public function OnAfterq2cOrder($order, $data)
	{

	}
	public function Onq2cOrderDelete($order_id)
	{

	}
	public function OnBeforeq2cAdd2Cart($cartId, $item)
	{

	}
	public function OnAfterq2cRemovefromCart($item)
	{

	}
	/* for Checkout view*/
	public function OnBeforeq2cCheckoutCartDisplay()
	{
		//  return "OnBeforeq2cCartDisplay";
	}
	public function OnAfterq2cCheckoutCartDisplay()
	{
		//return "OnAfterq2cCartDisplay";
	}
	/* For module  */
	public function onBeforeCartModule()
	{

	}
	public function onAfterCartModule()
	{
		//return "<div class=\"ship_msgfree\">Shipping is FREE on a purchase of Rs. 300/- and more !</div> ";
	}
	/* For cart pop up*/
	public function onBeforeCartDisplay()
	{
		//return "onBeforeCartDisplay";

	}
	public function onAfterCartDisplay()
	{
		//return "<div class=\"ship_msgfree\">Shipping is FREE on a purchase of Rs. 300/- and more !</div> ";
	}

	public function onProductDisplaySocialOptions($cont_id, $element, $title, $route)
	{
		$likehtml = '';
		$jlike    = $this->params->get('jlike');
		if ($jlike == 1)
		{
			jimport('joomla.filesystem.file');
			if (JFile::exists(JPATH_SITE . DS . 'components' . DS . 'com_jlike' . DS . 'jlike.php'))
			{
				JRequest::setVar('data', json_encode(array(
					'cont_id' => $cont_id,
					'element' => $element,
					'title' => $title,
					'url' => $route
				)));
				//Require the jlike helper
				require_once(JPATH_SITE . '/' . 'components/com_jlike/helper.php');
				$jlikehelperObj = new comjlikeHelper();
				$likehtml       = $jlikehelperObj->showlike();
			}
		}
		return $likehtml;
	}

	public function qtcOnBeforeCreateStore($store_id)
	{
	}

	/** for future use*/
	public function qtcOnBeforeSaveStore($post)
	{
	}

	public function qtcOnAfterSaveStore($post, $storeid)
	{
	}

	/** corrently used for only pickup date
	 * @param : cartdata array - cart details
	 *
	 * @return array()
	 {
	 *	['tabName'] = "" , // tab name
	 *	['nextstepid'] = ""				// show tab
	 * ['tabPlace']  =''  // require in future
	 * ['html to return']
	 * }
	 * */

	public function qtcaddTabOnCheckoutPage($cartData)
	{
		//load language file
		//$lang = JFactory::getLanguage();
		//$lang->load('plg_qtc_sample_development',
	}

	/**
	 * @param : orderid  - order id
	 * @param : post  - Checkout page post data
	 * @return : return modified post data
	 * */
	public function qtcAfterCheckoutDetailSave($orderid, $post)
	{
	}

	/** corrently used for only pickup date
	 * @param : orderid  -
	 * @param : orderInfo  - order info details
	 * @param : orderitems  - orderItem info details
	 *
	 * @return array()
	 {
	 * ['tabPlace']  =''  // require in future
	 * ['html to return']
	 * }
	 * */
	public function addHtmlOnOrderDetailPage($orderid = 0, $orderInfo = array(), $orderitems = array())
	{
	}

	/** v1.1 This trigger will be called after saving product
	 * @param : item_id  - Quick2cart's unique product id
	 * @param : att_detail  - product attribute details order id
	 * @param : sku  - unique product code
	 * @param : client- called client eg. com_content,com_quick2cart etc
	 * */
	public function OnAfterq2cProductSave($item_id, $att_detail, $sku, $client)
	{
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   object   $itempost  Object of post.
	 * @param   string   $action    action while insert or update object.
	 *
	 * @return  boolean
	 *
	 * @since   2.2.2
	 */
	public function beforeSavingProductBasicDetail($itempost, $action)
	{
	}

	/**
	 * This trigger will be called before saving single attribute.
	 *
	 * @param   Array  $att_detail  Attribute detail array along with option.
	 *
	 * @return  Array  Modified Attribute details.
	 *
	 * @since   2.2.4
	 */
	public function OnBeforeq2cAttributeSave($att_detail)
	{
	/*$att_detail contails
			Array
			(
				[attri_name] => color
				[attri_id] => 17
				[fieldType] => Select
				[iscompulsary_attr] => on
				[attri_opt] => Array
					(
						[0] => Array
							(
								[id] => 25
								[name] => Yellow
								[prefix] => +
								[currency] => Array
									(
										[USD] => 0.00
									)

								[order] => 1
							)

						[1] => Array
							(
								[id] => 26
								[name] => red
								[prefix] => +
								[currency] => Array
									(
										[USD] => 0.00
									)

								[order] => 2
							)


					)

				[item_id] => 15
			)
		*/
		//	return $att_detail;
	}

	/**
	 * This trigger is called while changing the steps from checkout page
	 *
	 * @since   2.2.4
	 */
	public function OnAfterQ2cStepChange()
	{

	}
} //end class
