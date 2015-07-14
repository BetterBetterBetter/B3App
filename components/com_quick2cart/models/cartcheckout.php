<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class Quick2cartModelcartcheckout extends JModelLegacy
{
	function userdata()
	{
		$params = JComponentHelper::getParams('com_quick2cart');
		$user = JFactory::getUser();
		$userdata = array();
		$query = "SELECT u.*
		FROM #__kart_users as u
		 WHERE  u.user_id = ".$user->id ." order by u.id DESC LIMIT 0 , 2";
		/*$query=" SELECT u.* FROM #__kart_users AS u WHERE u.user_id =".$user->id." AND
		u.id=(select max(id) from #__kart_users)";*/
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		if (!empty($result))
		{
			if ($result[0]->address_type == 'BT')
			{
				$userdata['BT'] = $result[0];
				if ($params->get('shipping')  == '1' && !empty($result[1]))
				{
					$userdata['ST'] = $result[1];
				}
			}
			elseif ($result[1]->address_type == 'BT')
			{
				$userdata['BT'] = $result[1];

				if ($params->get('shipping') == '1')
				{
					$userdata['ST'] = $result[0];
				}
			}
		}
		else
		{
			$row = new stdClass;
			$row->user_email =  $user->email;
			$userdata['BT'] =  $row;
			$userdata['ST'] =  $row;
		}
		return $userdata;
	}

	function getcoupon($c_code = "", $user_id = "", $called = "cart", $order_id = 0)
	{
		$jinput = JFactory::getApplication()->input;

		if ($user_id == "")
			$user_id = JFactory::getUser()->id;
		$db= JFactory::getDBO();
		if (!$c_code)
			$c_code = $jinput->get('coupon_code');
		// MULTIVENDOR ON THEN DONT ALLOW TO APPLY GLOBAL COUPON, store coupon
		$params = JComponentHelper::getParams('com_quick2cart');
		$multivendor_enable = $params->get('multivendor');
		$noGlobalCop='';

		if (!empty($multivendor_enable) & empty($order_id))
		{
			// NO GLOBAL COUPON
			$noGlobalCop=' AND (cop.`store_id` IS NOT NULL  AND cop.`store_id` <> 0) ';
			// NO STORE RELEATED COUPON
			$noGlobalCop .=' AND (cop.`item_id` IS NOT NULL ) ';
		}

	 	$query="SELECT value,val_type,store_id,
		CASE WHEN store_id IS NOT NULL
		THEN CONCAT( item_id ,',',max_use,',', max_per_user)
		ELSE item_id
		END as item_use_per_user
				FROM #__kart_coupon as cop
				WHERE
				published = 1
				AND code=".$db->quote($db->escape($c_code))."
				AND	 ( (CURDATE() BETWEEN from_date AND exp_date)   OR from_date = '0000-00-00 00:00:00')
				AND (max_use  > (SELECT COUNT(api.coupon_code) FROM #__kart_orders as api WHERE api.coupon_code =".$db->quote($db->escape($c_code)).") OR max_use=0)
				AND (max_per_user > (SELECT COUNT(api.coupon_code) FROM #__kart_orders as api WHERE api.coupon_code = ".$db->quote($db->escape($c_code))." AND api.payee_id= ".$user_id.") OR max_per_user=0)
				AND
					CASE WHEN user_id IS NOT NULL THEN user_id LIKE '%|".$user_id."|%'
					ELSE 1
					END
				" . $noGlobalCop;
		$db->setQuery($query);
		$count = $db->loadObjectList();
		if (!empty($count[0]) && strpos($count[0]->item_use_per_user,'|') !== false){	//coupon is product related
			$count[0]->item_id = $this->getCop_item($count[0]->item_use_per_user, $c_code, $user_id, $called, $order_id);
			if (!empty($count[0]->item_id))
				return $count;
			else
				return array();
		}elseif (!empty($count[0]) && empty($count[0]->item_use_per_user)){
			$count[0]->item_id = array();
		}elseif (!empty($count[0]) && !empty($count[0]->store_id) ){	//coupon is store related
			$query ="SELECT i.item_id
			FROM #__kart_items as i
			WHERE i.store_id =".$this->_db->escape($count[0]->store_id)." AND  i.item_id IN (";
			if ($called=="cart"){
				$Quick2cartModelcart =  new Quick2cartModelcart;
				$cartid = $Quick2cartModelcart->getCartId();
				$query .="SELECT kc.item_id
				FROM #__kart_cartitems as kc
				WHERE kc.cart_id =".$cartid;
			}
			else{
				$query .="SELECT oi.item_id
				FROM #__kart_order_item as oi
				WHERE oi.order_id =".$order_id;
			}
			$query .=")";
			$this->_db->setQuery($query);
			$in_cop_store = $this->_db->loadColumn();
			if (!empty($in_cop_store)){
				$count[0]->item_id = $in_cop_store;
				return $count;
			}
			else{
				return array();
			}
		}

		return $count;
	}

	function getCop_item($item_use_per_user, $cop_code, $user_id, $called="cart", $order_id=0){
		$cart_item = array();
		if (isset($item_use_per_user)){
			$item_use_per_user_array = explode (",", $item_use_per_user);
			$countitem_id = substr($item_use_per_user_array[0], 1, -1);
			$cop_itemids = explode ("||", $countitem_id);	//fetch all the item ids from coupon
			if ($called=="cart"){
				$Quick2cartModelcart =  new Quick2cartModelcart;
				$cartid = $Quick2cartModelcart->getCartId();
			}
			foreach ($cop_itemids as $cop_itemid){	//run a loop on all item ids on order item table
				$cart_item_id = $max_use = $max_peruser = $in_cop_store = '';
				if ($called=="cart"){
					$query ="SELECT kc.item_id
				FROM #__kart_cartitems as kc
				WHERE kc.cart_id =".$cartid." AND kc.item_id LIKE '".$cop_itemid."' ";
				}
				else{
					$query ="SELECT oi.item_id
				FROM #__kart_order_item as oi
				WHERE oi.order_id =".$order_id." AND oi.item_id LIKE '".$cop_itemid."' ";
				}
				$this->_db->setQuery($query);
				$cart_item_id = $this->_db->loadResult();

				if ($item_use_per_user_array[1]!=0){
					$query ="SELECT COUNT(oi.params)
					FROM #__kart_order_item as oi
					WHERE oi.params LIKE '%".$this->_db->escape($cop_code)."%'
					AND oi.order_id=(SELECT o.id FROM #__kart_orders as o WHERE o.id= oi.order_id AND o.status='C')";
					$this->_db->setQuery($query);
					$max_use = $this->_db->loadResult();
				}
				if ($item_use_per_user_array[2]!=0){
				$query ="SELECT COUNT(oi.params)
					FROM #__kart_order_item as oi
					WHERE oi.params LIKE '%".$this->_db->escape($cop_code)."%'
					AND oi.order_id=(SELECT o.id FROM #__kart_orders as o WHERE o.id= oi.order_id AND o.status='C' AND o.payee_id =".$user_id.")";
					$this->_db->setQuery($query);
					$max_peruser = $this->_db->loadResult();
				}
				$cart_item_flag = 0;
				if ($cart_item_id && $item_use_per_user_array[1]>$max_use && $item_use_per_user_array[2]>$max_peruser)
					$cart_item_flag = 1;
				else
					$cart_item_flag = 0;

				if ($cart_item_flag){
					$cart_item[] = $cart_item_id;
				}
			}
		}
		return $cart_item;
	}
	function getCountryName($countryId)
	{
		$db = JFactory::getDBO();
		$query="SELECT `country` FROM `#__tj_country` where id=".$countryId;
		$db->setQuery($query);
		$rows = $db->loadResult();
		return $rows;
	}

		function getStateName($stateId)
	{
		$db = JFactory::getDBO();
		$query="SELECT `region` FROM `#__tj_region` where id=".$stateId;
		$db->setQuery($query);
		$rows = $db->loadResult();
		return $rows;
	}

	function shipingaddr($infoid, $uid, $data, $insert_order_id)
	{
		/*$Quick2cartModelcartcheckout = new Quick2cartModelcartcheckout();
		$countryName = '';
		$stateName = '';

		if(!empty($data['country']))
		{
			$countryName = $Quick2cartModelcartcheckout->getCountryName($data['country']);
		}

		if(!empty($data['state']))
		{
			$stateName = $Quick2cartModelcartcheckout->getCountryName($data['state']);
		} */

		$row = new stdClass;
		$row->user_id = $uid;
		$row->user_email = $data['email1'];
		$row->address_type='ST';
		$row->firstname = $data['fnam'];
		if (isset($data['mnam']))
		{
			$row->middlename = $data['mnam'];
		}

		$row->lastname = $data['lnam'];
		//$row->vat_number = $data['fnam'];
		//$row->tax_exempt = $data['fnam'];
		//$row->shopper_group_id = $data['fnam'];
		$row->country_code = !empty($data['country'])?$data['country'] : '';
		$row->address = $data['addr'];

		// in smoe country city, state, zip code is not present - eg HONG CONG
		$row->city=(!empty($data['city']))?$data['city']:'';
		$row->state_code =  !empty($data['state'])?$data['state'] : '';
		$row->zipcode = (!empty($data['zip']))?$data['zip']:'';//$data['zip'];
/*
		$row->city = $data['city'];
		$row->state_code = $data['state'];
		$row->zipcode = $data['zip'];*/
		$row->phone = $data['phon'];
		$row->approved='1';
		$row->order_id = $insert_order_id;

		/*if (!$this->_db->insertObject('#__kart_users',  $row,  'id'))
			{
				echo $this->_db->stderr();
				return false;
			}*/
		// commented BZ: we have to add each oreder address against order_id (no address overwritten)
		 $query = "Select id FROM #__kart_users WHERE address_type='ST' AND user_id=".$uid.' AND `order_id`='.$insert_order_id.' ORDER BY `id` DESC';
		$this->_db->setQuery($query);
		$ship = $this->_db->loadResult();
	 	if (!empty($ship))
	 	{
	 		$row->id = $ship;
			if (!$this->_db->updateObject('#__kart_users', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		else
		{
			if (!$this->_db->insertObject('#__kart_users', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
	}

	function billingaddr($uid, $data1, $insert_order_id)
	{
		$data = $data1->get('bill', array(), 'ARRAY');

		/*$Quick2cartModelcartcheckout = new Quick2cartModelcartcheckout();
		$countryName = '';
		$stateName = '';

		if (!empty($data['country']))
		{
			//$countryName = $Quick2cartModelcartcheckout->getCountryName($data['country']);
			$countryName = $data['country'];
		}

		if (!empty($data['state']))
		{
			$stateName = $Quick2cartModelcartcheckout->getCountryName($data['state']);
		}*/

		$row = new stdClass;
		$row->user_id = $uid;
		$row->user_email = $data['email1'];
		$row->address_type='BT';
		$row->firstname = $data['fnam'];
		if (isset($data['mnam']))
		$row->middlename = $data['mnam'];
		$row->lastname = $data['lnam'];
		if (!empty($data['vat_num']))
			$row->vat_number = $data['vat_num'];
		//$row->tax_exempt = $data['fnam'];
		//$row->shopper_group_id = $data['fnam'];
		$row->country_code = !empty($data['country'])?$data['country'] : '';
		$row->address = $data['addr'];

		// in smoe country city, state, zip code is not present - eg HONG CONG
		$row->city = (!empty($data['city']))?$data['city']:'';
		$row->state_code = !empty($data['state'])?$data['state']: '';
		$row->zipcode = (!empty($data['zip']))?$data['zip']:'';//$data['zip'];
		$row->phone = $data['phon'];
		$row->approved='1';
		$row->order_id = $insert_order_id;

		$query = "Select id FROM #__kart_users WHERE address_type='BT' AND user_id=".$uid .' AND `order_id`='.$insert_order_id.' ORDER BY `id` DESC';
		$this->_db->setQuery($query);
		$bill = $this->_db->loadResult();
	 	if ($bill)
	 	{
	 		$row->id = $bill;
			if (!$this->_db->updateObject('#__kart_users', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		else
		{
			if (!$this->_db->insertObject('#__kart_users', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		// commented BZ: allow to edit  (update details) for same order (1page ckout change)
		/*
		if (!$this->_db->insertObject('#__kart_users', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
			* */

		$params = JComponentHelper::getParams('com_quick2cart');
		if ($params->get('shipping') == '1')
		{
			if (isset($data['ship_chk']))
				$dataship = $data1->get('bill', array(), 'ARRAY');
			else
				$dataship =	$data1->get('ship', array(), 'ARRAY');//change by aniket for task #25656

			$this->shipingaddr($row->user_id, $uid, $dataship, $insert_order_id);
		}
		return $row->user_id;
	}

	function setsession($data)
	{
			$session = JFactory::getSession();
			//$session->set('final_amt', $data['final_amt_pay_inputbox']);
			$session->set('order_id', $data['order_id']);
	}

	function getformated_data($data)
	{
		$amt = 0;
		$detail =array();
		$i=0;
		foreach ($data['val'] as $k=>$val){
			$detail[$i]['val'] = $val;
			$detail[$i]['amt'] = $data['amt'][$k];
			$amt += $data['amt'][$k];
			$i++;
		}
		$result['detail']= json_encode($detail);
		$result['val']= $amt;
		return $result;
	}

/**
 *	This function palce the order
 *	@return
 * 		0  = Unable to insert order
 * 		-1 = Email is already Registered.Please Login
 * 		-2 = session expire (cart empty- if someone on ckout page in one tab and logged out from other tab)
 * */
	function store()
	{
		$user = JFactory::getUser();
		$buildadsession = JFactory::getSession();
		$jinput=JFactory::getApplication()->input;

		$data = $jinput->post;
		$orderId  = $data->get('order_id', '', "RAW");

		// while placing order
		if (empty($orderId))
		{
			$data = $this->recalculateData($data);

			//session expire
			if ($data === -2)
			{
				return -2;
			}
		}

		// GET BILLING AND SHIPPING ADDRESS
		$bill  = $data->get('bill', array(), "ARRAY");
		$ship  = $data->get('ship', array(), "ARRAY");
		$qtc_guest_regis  = $data->get('qtc_guest_regis', '', "STRING");

		if (!$user->id)
		{
			$user->id = 0;
			//Register a new User if Checkout Method is Register
			if (!empty($qtc_guest_regis) && $qtc_guest_regis != "guest")
			{
				$regdata['user_name'] = $bill['email1'];
				$regdata['user_email'] = $bill['email1'];
				JLoader::import('registration', JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'models');

				$Quick2cartModelregistration =  new Quick2cartModelregistration();
				$mesage = $Quick2cartModelregistration->store($regdata);

				if ($mesage)
				{
					$user = JFactory::getUser();
					$userid = $user->id;
				}
				else
				{

					return -1;
				}
			}
		}

		$row = new stdClass;
		$params = JComponentHelper::getParams('com_quick2cart');
		$isAllowedZeroPriceOrder = $params->get('orderWithZeroPrice', 0);

		// Edit from 1page ckout
		if (empty($orderId))
		{
			if (empty($isAllowedZeroPriceOrder))
			{
				// if FINAL orderPRICE <=0 THEN DONT ALLOW FOR ORDER
				//if ($data->get('final_amt_pay_inputbox') <= 0 || $data->get('total_amt_inputbox') <= 0 || !isset($data->get('gateways')))
				if ($data->get('final_amt_pay_inputbox') <= 0)
				{
					return 0;
				}
			}
		}

		// Place order
		$timestamp	= date("Y-m-d H:i:s");
				// Get the IP Address
		if (!empty ($_SERVER ['REMOTE_ADDR']))
		{
			$ip = $_SERVER ['REMOTE_ADDR'];
		}
		else
		{
			$ip = 'unknown';
		}

		$row->payee_id 				= $user->id;
		$row->user_info_id 			= $user->id;
		$row->name 	= $bill['fnam'];
		$row->email = $bill['email1'];

		if (empty($orderId))
		{
			//vm:DONT UPDATE THESE THING WHILE UPDATING ORDER (1-PAGE-CKOUT)
			$row->amount 			= $data->get('final_amt_pay_inputbox');
			$row->original_amount 		= $data->get('total_amt_inputbox');

			$row->order_tax 			= $data->get('orderTax', 0);
			$order_tax_details = $data->get('order_tax_details', '');
			$row->order_tax_details 	= isset($order_tax_details) ? $order_tax_details : json_encode(array());

			$row->order_shipping 		 = $data->get('qtcOrderShipcharges','',"STRING");
			$order_shipping_details 	= $data->get('order_shipping_details','',"STRING");

			//  order level : Save shipping msg for order level shipping method
			$row->order_shipping_details 	= isset($order_shipping_details) ? $order_shipping_details : json_encode(array());
			$row->coupon_code  		= $data->get('cop');
		}

		//	$row->coupon_discount 		= $dis_totalamt;//$cdiscount;
		$comment = $data->get('comment','','RAW');
		$row->customer_note 		= ($comment)?nl2br($comment) : '';
		$updateOrderstatus=0;
		$gtway = $data->get('gateways');

		//	if ZERO ORDER (first time) or on edit
		if (empty($row->amount))
		{
			// if ZERO ORDER and EDIT MODE (FOUND $data['order_id']) THEN DONT CHANGE GATEWAY
			$row->status = 'P';

			// ORDER IS NOT PLACED
			if (empty($orderId))
			{
				// FIRST TIME AND AMOUT =0 THEN USE FREE CKOUT PAYMENT METHOD
				$updateOrderstatus = 1;
				$row->processor = 'FreeCheckout';//$data['gateways']; // vm:what should be place here?
			}
			else
			{	// Order is placed and buyer editing something. Now fetch order price from D.
				// FETCH  ORDER PRICE FRM db
				$orderFinalAMt=(int)($this->getFinalOrderPrice($orderId));
			}
		}
		else
		{
			$row->status  				= 'P';
			// not free product and no geteway then return false
		/*	if (!isset($gtway))
			{
				return 0;
			}
			$row->processor 		= $gtway;*/
		}

		$row->cdate 				= $timestamp;
		$row->mdate 				= $timestamp;
		$row->ip_address 			= $ip;
		$comquick2cartHelper = new comquick2cartHelper;
		$row->currency			= $comquick2cartHelper->getCurrencySession();
		//$row->discount_type 		= $this->discount_type;			@TODO coupon related???
		$row->id 				= '';

		if (!empty($orderId))
		{
			//EDIT ORDER
			$row->id = $insert_order_id = $orderId;

			if (!$this->_db->updateObject('#__kart_orders', $row, 'id'))
			{
				echo $this->_db->stderr();
				return 0;
			}
		}
		else
		{
			if (!$this->_db->insertObject('#__kart_orders', $row, 'id'))
			{
				echo $this->_db->stderr();
				return 0;
			}

			$insert_order_id = $this->_db->insertid();
		}

		// Code to pad zero's to $insert_order_id and append to prefix and update
		JLoader::import('payment', JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'models');
		$Quick2cartModelpayment =  new Quick2cartModelpayment();
		$prefix = $Quick2cartModelpayment->generate_prefix($insert_order_id);

		$row1 = new stdClass;
		$row1->prefix 		= $prefix;
		$row1->id 			= $insert_order_id;

		if (!$this->_db->updateObject('#__kart_orders', $row1, 'id'))
		{
			echo $this->_db->stderr();
			return 0;
		}

		// Get Cart item detail$taxdataf
		$Quick2cartModelcart =  new Quick2cartModelcart;
		$cart_id = $Quick2cartModelcart->getCartId();
		$cart_itemsdata = $Quick2cartModelcart->getCartitems();

		if (empty($orderId))
		{
			$this->addSaveOrderItems($insert_order_id, $cart_itemsdata, $data, $updateOrderstatus);
		}

		// Store billing and shipping detail.
		$this->billingaddr($user->id,$data,$insert_order_id);

		//START Q2C Sample development
		$order_obj = array();
		$order_obj['order'] = $row;
		$order_obj['items'] = $cart_itemsdata;
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin("system");
		$result = $dispatcher->trigger("OnAfterq2cOrder",array($order_obj, $data));
		//END Q2C Sample development


		/*
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$plgresult = $dispatcher->trigger('qtcAfterCheckoutDetailSave',array($row->id, $data));
		*/

		@$comquick2cartHelper->sendordermail($row->id);
		return $insert_order_id;
	}

	function addSaveOrderItems($insert_order_id, $cart_itemsdata, $data, $updateOrderstatus)
  	{
		$productHelper= new productHelper;
		// GET BILLING AND SHIPPING ADDRESS
		$bill  = $data->get('bill', array(), "ARRAY");
		$ship  = $data->get('ship', array(), "ARRAY");
		$comquick2cartHelper = new comquick2cartHelper;
		$data->set('order_id', $insert_order_id); // row_id= last insert id

		$store_info = array();
		$itemsTaxDetail = $data->get('itemsTaxDetail', array(), 'ARRAY');
		$itemShipMethDetail = $data->get('itemShipMethDetail', array(), 'ARRAY');

		foreach ($cart_itemsdata as $cart_items)
		{
			$item_id = $cart_items['item_id'];
			$taxdetail = '';
			$shipdetail = '';

			// Get item tax detail
			if (!empty($itemsTaxDetail[$item_id]))
			{
				// Get current item tax detail
				$taxdetail = $itemsTaxDetail[$item_id];
			}

			// Get item ship detail
			if (!empty($itemShipMethDetail[$item_id]))
			{
				// Get current item tax detail
				$shipdetail = $itemShipMethDetail[$item_id];
			}

			$items = new stdClass;
			$items->order_id = $insert_order_id;
			$items->item_id = $item_id;

			// Getting store id from item_id
			$items->store_id = $comquick2cartHelper->getSoreID($cart_items['item_id']);
			$items->product_attributes = $cart_items['product_attributes'];
			$items->product_attribute_names 	 = $cart_items['options'];
			$items->order_item_name = $cart_items['title'];
			$items->product_quantity = $cart_items['qty'];

			$items->product_item_price = $cart_items['amt'];
			$items->product_attributes_price = $cart_items['opt_amt'];

			// This field store price without cop, tax,shipp etc
			//~ $originalProdPrice = ($items->product_item_price + $items->product_attributes_price ) * $items->product_quantity;
			//~ $items->original_price = isset($cart_items['original_price']) ? $cart_items['original_price'] : $originalProdPrice;
			$items->original_price = $cart_items['original_price'];
			$items->item_tax = !empty($taxdetail['taxAmount']) ? $taxdetail['taxAmount'] : 0;
			$items->item_tax_detail = !empty($taxdetail) ? json_encode($taxdetail) : '';
			$items->item_shipcharges = !empty($shipdetail['totalShipCost']) ? $shipdetail['totalShipCost'] : 0;
			$items->item_shipDetail = !empty($shipdetail) ? json_encode($shipdetail) : '';
			$items->product_final_price = $cart_items['tamt'] + $items->item_tax + $items->item_shipcharges;

			$items->params = $cart_items['params'];
			$items->cdate 				= date("Y-m-d H:i:s");//$cart_items['cdate'];
			$items->mdate 				= date("Y-m-d H:i:s");//$cart_items['mdate'];
			$items->status  				= 'P';

			if (!$this->_db->insertObject('#__kart_order_item', $items, 'order_item_id'))
			{
				echo $this->_db->stderr();
				return 0;
			}

			// Add entry in order_itemattributes
			$query = "Select *
			 FROM #__kart_cartitemattributes
			 WHERE cart_item_id=".(int) $cart_items['id'];   // cart_item_id as id
			$this->_db->setQuery($query);
			$cartresult = $this->_db->loadAssocList();

			if (!empty($cartresult))
			{
				foreach ($cartresult as $key=>$cart_itemopt)
				{
					$items_opt = new stdClass;
					$items_opt->order_item_id 					= $items->order_item_id;
					$items_opt->itemattributeoption_id 		= $cart_itemopt['itemattributeoption_id'];
					$items_opt->orderitemattribute_name		= $cart_itemopt['cartitemattribute_name'];
					$attopprice = $this->getAttrOptionPrice($cart_itemopt['itemattributeoption_id']);
					$items_opt->orderitemattribute_price	= $attopprice;
					$items_opt->orderitemattribute_prefix	= $cart_itemopt['cartitemattribute_prefix'];

					if (!$this->_db->insertObject('#__kart_order_itemattributes', $items_opt, 'orderitemattribute_id'))
					{
						echo $this->_db->stderr();
						return 0;
					}
				}
			}

			$params = JComponentHelper::getParams('com_quick2cart');
			$socialintegration = $params->get('integrate_with', 'none');
			$streamBuyProd = $params->get('streamBuyProd', 0);
			//$libclass = new activityintegrationstream();

			if ( $streamBuyProd && $socialintegration != 'none' )
			{
				// adding msg in stream
				$user = JFactory::getUser();
				$action = 'buyproduct';
				$prodLink = '<a class="" href="'. $comquick2cartHelper->getProductLink($cart_items['item_id']).'">'.$cart_items['title'].'</a>';
				$store_info[$items->store_id] = $comquick2cartHelper->getSoreInfo($items->store_id);
				$storeLink = '<a class="" href="'.JUri::root().substr(JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=store&store_id='.$items->store_id),strlen(JUri::base(true))+1).'">'.$store_info[$items->store_id]['title'].'</a>';
				$originalMsg = JText::sprintf('QTC_ACTIVITY_BUY_PROD', $prodLink,$storeLink);
				$title = '{actor} '.$originalMsg;

				// According to integration create social lib class obj.
				$libclass = $comquick2cartHelper->getQtcSocialLibObj();
				$libclass->pushActivity($user->id, $act_type='', $act_subtype='', $originalMsg, $act_link='', $title='', $act_access='');
			}


			 if (0)  //@VM if social activity FOR  js IS SELECTED THEN ONY SHOW
			{
				// add to JS stream
				if (JFile::exists(JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php'))
				{
					@$comquick2cartHelper->addJSstream($user->id,$user->id,$title,'', $action,0);
					require_once(JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
					$userid=JFactory::getUser()->id;

					if ($userid)
					{
						$userLink='<a class="" href="'.JUri::root().substr(CRoute::_('index.php?option=com_community&view=profile&userid='.$userid),strlen(JUri::base(true))+1).'">'.JFactory::getUser()->name.'</a>';
					}
					else
					{
							$userLink = $bill['email1'];
					}

					//Get connected Users of logged in user
					$jsuser = CFactory::getUser($userid);
					$connections_aa = $jsuser->getFriendIds();
					if (!empty($connections_aa))
					{
						foreach ($connections_aa as $connections){
						$notification_subject = JText::sprintf('QTC_NOTIFIY_BUY_PROD_FRN', $userLink,$prodLink);
						@$comquick2cartHelper->addJSnotify($userid , $connections,$notification_subject,'notif_system_messaging','0','');
						}
					}
					$groupIDs = explode(",",$jsuser->_groups);
					if (empty($groupIDs)){
						$query= "SELECT groupid FROM #__community_groups_members " .
						"WHERE memberid=".$userid;
						$this->_db->setQuery($query);
						$groupIDs = $this->_db->loadColumn();
					}

					if (!empty($groupIDs)){
						foreach ($groupIDs as $groupID){
							if (!empty($groupID)){
								$query= "SELECT name FROM #__community_groups " .
								" WHERE id=".$groupID;
								$this->_db->setQuery($query);
								$groupName = $this->_db->loadResult();
								$query= "SELECT memberid " .
									"FROM #__community_groups_members " .
									"WHERE groupid=".$groupID." AND approved=1 AND memberid<>".$userid."";
								$this->_db->setQuery($query);
								$group_ids = $this->_db->loadColumn();
								if (!empty($group_ids)){
									foreach ($group_ids as $group_id){
									$notification_subject = JText::sprintf('QTC_NOTIFIY_BUY_PROD_GRP', $userLink, $groupName,$prodLink);
									$comquick2cartHelper->addJSnotify($userid , $group_id,$notification_subject,'notif_system_messaging','0','');
									}
								}
							}
						}
					}
				}
			}// end of if (0)

				// REMOVED JS CODE
			/* end add to JS stream*/

		} // ENd of cart item for each

		// For JS notification is sent
		if (!empty($storeLink) && JFile::exists(JPATH_SITE . '/components/com_community/libraries/core.php'))
		{
			require_once(JPATH_SITE . '/components/com_community/libraries/core.php');
			$commented_by_userid=JFactory::getUser()->id;

			if ($commented_by_userid)
			{
				$userLink='<a class="" href="'.JUri::root().substr(CRoute::_('index.php?option=com_community&view=profile&userid=' . $commented_by_userid),strlen(JUri::base(true))+1).'">'.JFactory::getUser()->name.'</a>';
			}
			else
			{
				$userLink = $bill['email1'];
			}

			foreach ($store_info as $store_id=> $storeinfo)
			{
				$storeLink = '<a class="" href="'.JUri::root().substr(JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=store&store_id='.$store_id),strlen(JUri::base(true))+1).'">'.$storeinfo['title'].'</a>';

				$notification_subject = JText::sprintf('QTC_NOTIFIY_BUY_STORE', $userLink,$storeLink);
				@$comquick2cartHelper->addJSnotify($commented_by_userid , $storeinfo['owner'],$notification_subject,'notif_system_messaging','0','');
			}
		}

	}// end of

	/**
	 * Gives country list.
	 *
	 * @since   2.2
	 * @return   countryList
	 */
	function getCountry()
	{
		/*
		$db = JFactory::getDBO();
		$query="SELECT id,`country` FROM `#__tj_country` where com_quick2cart=1  ORDER BY ordering";
		$db->setQuery($query);
		$rows = $db->loadObjectList();*/

		require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
		$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');
		$rows = (array)$tjGeoHelper->getCountryList('com_quick2cart');

		return $rows;
	}

	function getuserState($country_id)
	{
		if (!empty($country_id))
		{
			/*$db = JFactory::getDBO();
			$query="SELECT r.id,r.region FROM #__tj_region AS r LEFT JOIN #__tj_country as c
			ON r.country_id=c.id where c.id=" . $country_id;
			$db->setQuery($query);
			$rows = $db->loadAssocList();*/

			require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
			$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');
			$rows = (array)$tjGeoHelper->getRegionList($country_id, 'com_quick2cart');
			return $rows;
		}
	}
	/*
		Take itemattributeoption_id and fetch price according to currency
		@Param :: integer i$attop_id
		@return:: integer price

	 * */

	function getAttrOptionPrice($attop_id)
	{
		if ($attop_id)
		{
			$db = JFactory::getDBO();
			$comquick2cartHelper = new comquick2cartHelper;
			$currency = $comquick2cartHelper->getCurrencySession();
			$query = "SELECT price FROM `#__kart_option_currency` WHERE itemattributeoption_id= ".(int)$attop_id." AND currency='$currency'";

			$db->setQuery($query);
			$result  = $db->loadResult();

			return $result;
		}

	}

	/**
	 * Gives applicable tax charges.
	 *
	 * @param   integer   $dis_totalamt  cart subtotal (after discounted amount )
	 * @param   object    $vars      object with cartdetail,billing address and shipping adress details.
	 *
	 * @since   2.2
	 * @return   returns the applicatble tax charges
	 */
	public function afterTaxPrice($dis_totalamt, $vars)
	{
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		$params = JComponentHelper::getParams('com_quick2cart');
		$shippingMode = $params->get('shippingMode', 'itemLevel');  // @TODO SET ITEM LEVEL AS DEF

		if ($shippingMode == "orderLeval")
		{
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('qtctax');
			$taxresults = $dispatcher->trigger('addTax',array($dis_totalamt, $vars));

			if (!empty($taxresults[0]['charges']) && is_numeric($taxresults[0]['charges']))
			{
				$firstResult = $taxresults[0];
				$charges = (float)$firstResult['charges'];

				$detail = json_encode($firstResult);
				$post->set("order_shipping_details", $detail);

				return $charges;
			}
		}
		else
		{
			// GET BILLING AND SHIPPING ADDRESS
			$address = new stdClass;
			$address->billing_address = $vars->billing_address;
			$address->shipping_address = $vars->shipping_address;
			$address->ship_chk = $vars->ship_chk;

			$totalTax = 0;
			$taxobject = new stdClass;
			$taxobject->totalAmount = $dis_totalamt;
			$taxobject->cartdetails = $vars->cartItemDetail;
			$taxobject->addressDetails = $address;

			$taxHelper = new taxHelper();
			$taxresults = array();

			JPluginHelper::importPlugin('tjtaxation');
			$dispatcher = JDispatcher::getInstance();
			$taxresults = $dispatcher->trigger('tj_calculateTax',array($taxobject));
			$itemWiseTaxDetail = array();

			if (!empty($taxresults))
			{
				$itemWiseTaxDetail = $taxresults[0];
			}

			if (!empty($itemWiseTaxDetail))
			{
				// To set tax detail against order item row entry
				$post->set('itemsTaxDetail', $itemWiseTaxDetail);

				// Get total of all item taxes
				foreach ($itemWiseTaxDetail as $prodTax)
				{
					if (!empty($prodTax['taxAmount']))
					{
						$totalTax += $prodTax['taxAmount'];
					}
				}

				$post->set('orderTax', $totalTax);
			}

			return $totalTax;
		}
	}

	function updateCop_item($coupon, $cart_item, $called="cart", $order_id=0)
	{
		$db = JFactory::getDBO();
		// $cart_item = coupon items;
		foreach ($cart_item as $cart_item_id)
		{
			if ($called=="cart")
			{
				// Called from cart
				$Quick2cartModelcart =  new Quick2cartModelcart;
				$cartid = $Quick2cartModelcart->getCartId();
				$query = "Select cart_item_id as id ,item_id as item_id, product_final_price as tamt ,product_quantity as qty
			 FROM #__kart_cartitems
			 WHERE item_id='$cart_item_id' AND cart_id =".$cartid." order by `store_id`"  ;
			}
			else{
				// Called from  order
				$query = "Select order_item_id as id , item_id as item_id, product_final_price as tamt ,product_quantity as qty
			 FROM #__kart_order_item
			 WHERE item_id='$cart_item_id' AND order_id =".$order_id." order by `store_id`"  ;
			}
			$db->setQuery($query);
			$cartitems = $db->loadAssocList();

			foreach ($cartitems as $item)
			{
				if ($item['item_id'] == $cart_item_id)
				{
					if ($coupon[0]->val_type == 1)
						$cval = ($coupon[0]->value /100)*$item['tamt'];
					else{
						$cval = $coupon[0]->value;
						$cval = $cval * $item['qty']; /*multiply cop disc with qty*/
					}

					$camt = $item['tamt'] - $cval;

					if ($camt <= 0)
					{
						$camt=0;
					}

					$dis_totalamt = ($camt) ? $camt : $item['tamt'];

					$cart_item = new stdClass;

					$cart_item->item_id = $cart_item_id ;  //as id
					$cart_item->original_price = $item['tamt'];
					$cart_item->product_final_price = $dis_totalamt;
					$comquick2cartHelper = new comquick2cartHelper();
					$db = JFactory::getDBO();

					if ($called=="cart")
					{
						$q="SELECT  `params` FROM  `#__kart_cartitems` WHERE `cart_item_id` =".$item['id'];
						$cart_item->params = $comquick2cartHelper->appendExtraFieldData($coupon[0]->cop_code,$q,'coupon_code');
						$cart_item->cart_item_id = $item['id'];
						$sql =$db->updateObject("#__kart_cartitems", $cart_item, "cart_item_id");
					}
					else{
						$q="SELECT  `params` FROM  `#__kart_order_item` WHERE `order_item_id` =".$item['id'];
						$cart_item->params = $comquick2cartHelper->appendExtraFieldData($coupon[0]->cop_code,$q,'coupon_code');
						$cart_item->order_item_id = $item['id'];
						$sql =$db->updateObject("#__kart_order_item", $cart_item, "order_item_id");
					}
					if (!$sql)
					{
						echo $this->_db->stderr();
						return -1;
					}
				}
			}
		}
	}

	function afterDiscountPrice($totalamt,$c_code,$user_id="",$called="cart",$order_id=0)
	{
		$coupon = $this->getcoupon($c_code,$user_id,$called,$order_id);
		$coupon = $coupon?$coupon : array() ;
		$dis_totalamt=$totalamt;

		if (isset($coupon) && $coupon)// if user entered code is matched with dDb coupon code
		{
			if (!empty($coupon[0]->item_id))
			{
				$coupon[0]->cop_code = $c_code;
				$this->updateCop_item($coupon, $coupon[0]->item_id,$called,$order_id);
			}
			else
			{
				if ($coupon[0]->val_type == 1)
				{
					$cval = ($coupon[0]->value /100)*$totalamt;
				}
				else
				{
					$cval = $coupon[0]->value;
				}

				$camt = $totalamt - $cval;

				if ($camt <= 0)
				{
					$camt=0;
				}

				$dis_totalamt = ($camt>=0) ? $camt : $totalamt;
			}
		}

		return $dis_totalamt;
	}

	 /**
	 * Gives applicable Shipping charges.
	 *
	 * @param   integer   $subtotal  cart subtotal (after discounted amount )
	 * @param   object    $vars      object with cartdetail,billing address and shipping adress details.
	 *
	 * @since   2.2
	 * @return   returns the applicatble shipping charges
	 */
	public function afterShipPrice($subtotal, $vars)
	{
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		$params = JComponentHelper::getParams('com_quick2cart');
		$shippingMode = $params->get('shippingMode', 'itemLevel');  // @TODO SET ITEM LEVEL AS DEF
		//$shippingMode = $params->get('shippingMode', 'orderLeval');

		if ($shippingMode == "orderLeval")
		{
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('qtcshipping');//@TODO:need to check plugim type..
			$shipresults = $dispatcher->trigger('qtcshipping', array($subtotal, $vars));//Call the plugin and get the result

			if (!empty($shipresults[0]['charges']) && is_numeric($shipresults[0]['charges']))
			{
				$firstRes = $shipresults[0];
				$charges = (float)$firstRes['charges'];
				$detail = json_encode($firstRes);
				$post->set("order_shipping_details", $detail);

				return $charges;
			}
		}
		else
		{
			$shipChargesDetail = array();
			$shipChargesDetail['totCharges'] = 0;
			$shipChargesDetail['itemShipMethDetail'] = array();
			$qtcshiphelper = new qtcshiphelper;

			foreach ($vars->cartItemDetail as $key=>$itemDetail)
			{
				$item_id = $itemDetail['item_id'];

				// If item has shipping methods
				if (isset($vars->selectedItemshipMeth[$item_id]))
				{
					$shipMethIdForItem = $vars->selectedItemshipMeth[$item_id];
					$itemShipMethDetail = $vars->itemsShipMethRateDetail[$shipMethIdForItem];

					$shipMethod = array();
					$shipMethod['client'] = $itemShipMethDetail['client'];
					$shipMethod['methodId'] = $itemShipMethDetail['methodId'];

					$address = new stdClass;
					$address->billing_address = $vars->billing_address;
					$address->shipping_address = $vars->shipping_address;
					$address->ship_chk = $vars->ship_chk;


					//	(Recalculate) Get selected shipping method detail.
					$shipChargesDetail['itemShipMethDetail'][$item_id] = $shippingChargeDetail = $qtcshiphelper->getItemsShipMethods($item_id, $address, $itemDetail, $shipMethod);

					$vars->cartItemDetail[$key]['itemShipCharges'] = !empty($shippingChargeDetail['totalShipCost'])? $shippingChargeDetail['totalShipCost'] : $itemShipMethDetail['totalShipCost'];

					$shipChargesDetail['totCharges'] += $vars->cartItemDetail[$key]['itemShipCharges'];
				}
			}

			$qtcOrderShipcharges = 0;

			if (!empty($shipChargesDetail))
			{
				// To add against item row entry
				$post->set('itemShipMethDetail', $shipChargesDetail['itemShipMethDetail']);

				if ($shipChargesDetail['totCharges'])
				{
					$qtcOrderShipcharges = $shipChargesDetail['totCharges'];
					//$shipval = $comquick2cartHelper->calamt($shipval, $shipChargesDetail['totCharges']);
				}
			}

			return $qtcOrderShipcharges;
		}
	}

	function addtax($dis_totalamt,$tax)
	{
		return $dis_totalamt+$tax;
	}


	/* 	keyname contain one of state,county,city
	 * */
	function getShippingPrice($keyname,$keyvalue)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$shipid=$comquick2cartHelper->getShippingManagerId($keyname,$keyvalue);

		if (!empty($shipid))
		{
		  $shipval=$comquick2cartHelper->getShipCurrencyPrice($shipid);
		  return $shipval;
		}
		return 0;
	}

	/**
	 * data should in format
	 * stdClass Object ([totalprice] => 174.375 [country] => Bangladesh [state] => Dhaka [city] => xyz)
	 *
	 * */
	/*function getFinalShipPrice($data)
	{
		$shipcharges=0;
		$totalamt=$data->totalprice;
		$comquick2cartHelper = new comquick2cartHelper;
		// use onBeforShipManagerApply
		$finalshipdata=$comquick2cartHelper->applyShipManegertrigger($totalamt,$shipcharges,"onBeforShipManagerApply");
		$totalamt = $comquick2cartHelper->calamt($finalshipdata['totalamt'],$finalshipdata['charges']);
		$ship_for='';
		if ($data->country && $data->region && $data->city)
		{
			$shipcharges=$this->getShippingPrice('city',$data->city);
			$ship_for=(empty($shipcharges)) ? "":"city";
			if (empty($shipcharges))
			{
				$shipcharges=$this->getShippingPrice('region',$data->region);
				$ship_for=(empty($shipcharges)) ? "":"region";
				if (empty($shipcharges))
				{
					$shipcharges=$this->getShippingPrice('country',$data->country);
					$ship_for=(empty($shipcharges)) ? "":"country";
				}
			}
		}
		$finalshipdata=$comquick2cartHelper->applyShipManegertrigger($totalamt,$shipcharges,"onAfterShipManagerApply");

		$finalshipdata['totalamt']=$comquick2cartHelper->calamt($finalshipdata['totalamt'],$finalshipdata['charges']);
		$finalshipdata['shipkey']=$ship_for;
		return $finalshipdata;
	}*/


	/**		This function accept post data (array) from cartcheckout view and
	 * 		add/ modify price releated thing to recalculated value
	 **/
	function recalculateData($data)
	{
		$params = JComponentHelper::getParams('com_quick2cart');
		$isShippingEnabled = $params->get('shipping', 0);
		$isTaxationEnabled = $params->get('enableTaxtion', 0);
		$jinput = JFactory::getApplication()->input;
		$postdata = $jinput->post;

		$couponschk = $data->get('couponschk', '', "ARRAY");
		$comquick2cartHelper = new comquick2cartHelper;
		$productHelper = new productHelper;
		$orderId  = $data->get('order_id', '', "RAW");

		$address = new stdClass;
		// GET BILLING AND SHIPPING ADDRESS
		$address->billing_address = $data->get('bill', array(), "ARRAY");
		$address->ship_chk = $data->get('ship_chk', 0);

		$address->shipping_address = $data->get('ship', array(), "ARRAY");

		// Calculate coupon discount amount
		if (empty($orderId))
		{
			// For multivendor is ON. Calculate discount amount. (For peoduct specific cop)
			if (!empty($couponschk))
			{
				$session = JFactory::getSession();
				$cop = $session->get('coupon');

				if (empty($cop))  // why ?
					$cop = $couponschk;

				foreach ($cop as $copn)
				{
					if (!empty($copn['item_id']))
					{
						$dis_totalamt = $this->afterDiscountPrice($totalamt = 0, $copn['code']);
					}
				}
			}
		}

		$Quick2cartModelcart =  new Quick2cartModelcart();

		if (empty($orderId))
		{
			$cartitems	 = 	$Quick2cartModelcart->getCartitems();
			/**
			       [id] => 249
            [cart_id] => 64
            [store_id] => 1
            [title] => Hand Gloves
            [user_info_id] =>
            [cdate] => 2014-06-09 18:46:43
            [mdate] => 2014-06-09 18:46:43
            [qty] => 1
            [options] =>
            [item_id] => 69
            [product_item_price] => 10.00
            [product_attributes_price] => 0
            [product_final_price] => 10.00
            [original_price] => 0.00
            [params] =>
            [currency] => USD
            [product_attributes] =>
            [seller_id] => 884
            [amt] => 10.00
            [opt_amt] => 0
            [tamt] => 10
       )
			 * */
		}
		else
		{	 // FOR EDIT ORDER :: FETCH ITEMS FROM
			//	$orderWholeDetail = $comquick2cartHelper->getOrderInfo($orderId);
			//		$cartitems = $orderWholeDetail['items'];
		}

		// add price releated thing from database instead post data
		$totalamt=0;
		if (empty($cartitems))
		{
			return -2;
		}

		// Calculate subtoal of cart
		foreach ($cartitems as $key=>$rec)
		{
			$index="";
			$index="cart_total_price_inputbox".$rec['id'];
			$data->set($index, $rec['tamt']);
			$totalamt+= $rec['tamt'];
		}

		$data->set('total_amt_inputbox', $totalamt);	 // original amount
		//  step 1. calculate  amt after discount = amount after reducing discount
		$dis_totalamt = $totalamt;

		$cop = $postdata->get('cop', '', 'RAW');

		if (!empty($cop))
		{
			$dis_totalamt = $this->afterDiscountPrice($totalamt, $cop);

			if (!empty($dis_totalamt))
			{
				$data->set('net_amt_pay_inputbox', $dis_totalamt);
			}
		}

		// Make object to send data to taxation and shipping
		$vars = new stdClass;
		$vars->cartItemDetail = $cartitems;
		$vars->billing_address = $data->get('bill', array(), "ARRAY");
		$vars->shipping_address = $data->get('ship', array(), "ARRAY");
		$vars->ship_chk = $data->get('ship_chk', 0);

		// 2. Apply shipping
		//$shipval= $dis_totalamt;
		$qtcOrderShipcharges = 0 ;
		$data->set('itemShipMethDetail', array());

		if ($isShippingEnabled)
		{
			// Getting item wise shipping method id according to user selection
			$vars->selectedItemshipMeth = $postdata->get('itemshipMeth', array(), 'ARRAY');

			// Get all shipping method (here array key is method id)
			$vars->itemsShipMethRateDetail = $postdata->get('itemshipMethDetails', array(), 'ARRAY');

			// Get revalidate shipping detail
			$qtcOrderShipcharges = $this->afterShipPrice($dis_totalamt, $vars);
		}

		// Shipping charges
		$data->set('qtcOrderShipcharges', $qtcOrderShipcharges);

		// ================
		//step 2 calculate tax
		$totalTax = 0;
		$taxdetails=array();
		$data->set('itemsTaxDetail', array());
		$data->set('orderTax', 0);
		//$taxresults = $dis_totalamt;

		// Taxation is enabled
		if ($isTaxationEnabled)
		{
			$totalTax	= $this->afterTaxPrice($dis_totalamt, $vars);

			if (!empty($totalTax))
			{
				$data->set('orderTax', $totalTax);
			}
		}

		$final_amt_pay_inputbox = $dis_totalamt + $totalTax + $qtcOrderShipcharges;
		$data->set('final_amt_pay_inputbox', $final_amt_pay_inputbox);

		return $data;
	}

	function checkbillMailExists($mail)
	{
		$mailexist = 0;
		$query = "SELECT id FROM #__users where email  LIKE '".$mail."'";
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		if ($result){
			$mailexist = 1;
		}
		else
		{
			$mailexist = 0;
		}

		return $mailexist;
	}

	/** This function gives plugin name from plugin parameter
	*/
	function getPluginName($plgname)
	{
		$plugin = JPluginHelper::getPlugin('payment',  $plgname);
		@$params=json_decode($plugin->params);
		return @$params->plugin_name;
	}
	function getFinalOrderPrice($orderid)
	{
		$db = JFactory::getDBO();
			if (!empty($orderid))
			{
				$query = "SELECT `amount` FROM `#__kart_orders` where `id`=".$orderid;
				$db->setQuery($query);
				return $db->loadResult();
			}

	}

	/**
	 * amol changes
	 * Get details of checkout cart items
	 */
	function getCheckoutCartItemsDetails()
	{
		// GETTING CART ITEMS
		JLoader::import('cart', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models');
		$cartmodel = new Quick2cartModelcart;
		$cart = $cartmodel->getCartitems();

		foreach( $cart as $key=>$rec )
		{
			JLoader::import('product', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models');
			$quick2cartModelProduct = new quick2cartModelProduct;
			$cart[$key]['item_images'] = $quick2cartModelProduct->getProdutImages($rec['item_id']);

			$productHelper = new productHelper();
			// Get cart items attribute details
			$cart[$key]['prodAttributeDetails'] = $productHelper->getItemCompleteAttrDetail($rec['item_id']);

			$product_attributes = rtrim($cart[$key]['product_attributes'], ",");

			if(!empty($product_attributes))
			{
				// Get Cart Item attribute seleted value
				if($cart[$key]['product_attributes'])
				{
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					$query->select("`cartitemattribute_id`, `itemattributeoption_id`, `cartitemattribute_name`");
					$query->from('#__kart_cartitemattributes');
					$query->where("itemattributeoption_id IN(" . $product_attributes .")");
					$query->where(" cart_item_id = ".$cart[$key]['id']);
					$db->setQuery($query);
					$cart[$key]['product_attributes_values'] = $db->loadObjectList('itemattributeoption_id');
				}
			}

		}

		return $cart;
	}

	/* amol changes
	 * Function: updatecart updates the cart and also calculates the tax and
	 * shipping charges Parameters: none Returns: json:
	 */
/*  As not required (pls refere update_cart_item function from same controller file)
 * 	function update_cart_item()
	{

		$input = JFactory::getApplication()->input;;
		$post = $input->post;

		$cart_id =  $post->get('cart_id', '', 'INT');
		$item_id =  $post->get('item_id', '', 'INT');

		// Get cartitemattribute_ids to update
		$query = $this->_db->getQuery(true);
		$query->select("`cartitemattribute_id`");
		$query->from('#__kart_cartitemattributes');
		$query->where(" cart_item_id = ".$cart_id);
		$this->_db->setQuery($query);
		$cartitemattribute_ids = $this->_db->loadColumn();

		// Get parsed form data
		parse_str($post->get('formData', '' ,'STRING'), $formData);

		//print_r($formData);die;

		$itemattributeoption_ids = array();
		$product_attribute_names = array();

		// Update item cart attribute
		foreach($cartitemattribute_ids as $cartitemattribute_id)
		{
			try
			{
				$obj = new stdclass;
				$obj->cartitemattribute_id = $cartitemattribute_id;

				//~  Now there are two attribute type
				 //~ * 1=> select & 2=> text
				 //~ * So get the value from one of them
//~
				 //~ e.g post data
				 //~ [qtcTextboxField_45] => AA
				 //~ [attri_option_46] => 67
				 //~ [attri_option_47] => 59


				// For TextboxField field
				if( !empty( $formData['qtcTextboxField_'.$cartitemattribute_id]) )
				{
					$obj->cartitemattribute_name = $formData['qtcTextboxField_'.$cartitemattribute_id];

					$query = $this->_db->getQuery(true);
					$query->select("ci.itemattributeoption_id, ao.itemattributeoption_name");
					$query->from($this->_db->quoteName('#__kart_cartitemattributes', 'ci'));
					$query->join("LEFT", $this->_db->quoteName("#__kart_itemattributeoptions" , "ao") ." ON (" . $this->_db->quoteName('ci.itemattributeoption_id') . " = " . $this->_db->quoteName('ao.itemattributeoption_id') . ")" );
					$query->where("ci.cartitemattribute_id = " . $cartitemattribute_id);
					$this->_db->setQuery($query);

					$result = $this->_db->loadObject();
					//print_r($result);

					$itemattributeoption_ids[] = $result->itemattributeoption_id;
					$product_attribute_names[] = $result-> itemattributeoption_name . ": ". $obj->cartitemattribute_name;

				}
				else
				{
					$obj->itemattributeoption_id = $formData['attri_option_'.$cartitemattribute_id];
					$itemattributeoption_ids[] = $obj->itemattributeoption_id;

					// Get the details to update itemattributeoptions
					$query = $this->_db->getQuery(true);
					$query->select("`itemattributeoption_name`, `itemattributeoption_price`, `itemattributeoption_prefix`");
					$query->from('#__kart_itemattributeoptions');
					$query->where(" itemattributeoption_id = " . $obj->itemattributeoption_id);
					$this->_db->setQuery($query);
					$itemattributeoption = $this->_db->loadObject();

					// Get itemattributeoptions
					$obj->cartitemattribute_name = $itemattributeoption->itemattributeoption_name;
					$obj->cartitemattribute_price = $itemattributeoption->itemattributeoption_price;
					$obj->cartitemattribute_prefix = $itemattributeoption->itemattributeoption_prefix;

					// Get the attribute name & value to update product_attribute_names in cart_items table
					$query = $this->_db->getQuery(true);
					$query->select("ci.itemattributeoption_id, ao.itemattributeoption_name, ai.itemattribute_name");
					$query->from($this->_db->quoteName('#__kart_cartitemattributes', 'ci'));

					$query->join("LEFT", $this->_db->quoteName("#__kart_itemattributeoptions" , "ao") ." ON (" . $this->_db->quoteName('ci.itemattributeoption_id') . " = " . $this->_db->quoteName('ao.itemattributeoption_id') . ")" );

					$query->join("LEFT", $this->_db->quoteName("#__kart_itemattributes" , "ai") ." ON (" . $this->_db->quoteName('ao.itemattribute_id') . " = " . $this->_db->quoteName('ai.itemattribute_id') . ")" );

					$query->where("ci.cartitemattribute_id = " . $cartitemattribute_id);
					$this->_db->setQuery($query);
					$result = $this->_db->loadObject();

					$product_attribute_names[] = $result-> itemattribute_name . ": ". $obj->cartitemattribute_name;

				}

				// Update Table
				$this->_db->updateObject("#__kart_cartitemattributes", $obj, "cartitemattribute_id");

			}
			catch(Exception $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
		}

		try
		{
			// Update cart Item table
			$cartItem =  new stdclass;
			$cartItem->cart_item_id = $cart_id;
			$cartItem->item_id = $item_id;

			// One ',' is extra in existing structure of Q@C
			$cartItem->product_attributes = implode(',', $itemattributeoption_ids) . ',';
			$cartItem->product_attribute_names = implode(',', $product_attribute_names) . ',';
			$cartItem->mdate = date("Y-m-d H:i:s");
			$this->_db->updateObject('#__kart_cartitems', $cartItem, "cart_item_id");

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		return true;

	}
*/

}
