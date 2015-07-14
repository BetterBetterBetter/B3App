<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table.user' );



class Quick2cartModelpayment extends JModelLegacy
{
	function confirmpayment($pg_plugin, $oid)
	{
		$post	= JRequest::get('post');
		$oid = $this->extract_prefix($oid);
		$vars = $this->getPaymentVars($pg_plugin, $oid);
		if(!empty($post) && !empty($vars) )
		{
			/*JPluginHelper::importPlugin('system');
			$dispatcher1 = JDispatcher::getInstance();
			$result=$dispatcher1->trigger('OnBeforeq2cPay',array($vars));//Call the plugin and get the result
			if(!empty($result)){
				$vars=$result[0];
			}*/
			JPluginHelper::importPlugin('payment', $pg_plugin);
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger('onTP_ProcessSubmit', array($post, $vars));
		}
		else{
			JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error');
		}
	}
	function getPaymentVars($pg_plugin, $orderid)
	{
//			JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$comquick2cartHelper = new comquick2cartHelper;
		$params = JComponentHelper::getParams('com_quick2cart');
		$orderItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders');
		$chkoutItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cartcheckout');
		$pass_data = $this->getdetails($orderid);
		$vars = new stdClass;
		$vars->order_id=$pass_data->prefix.$orderid; //append prefix and order_id
		$vars->user_id=$pass_data->user_id;

		$billDetails = array();
		$billDetails['firstname'] = $vars->user_firstname = $pass_data->firstname;
		$billDetails['lastname'] = $vars->user_lastname = $pass_data->lastname;
		$billDetails['add_line1'] = $vars->user_address= $pass_data->address;
		$billDetails['email'] = $vars->user_email = $pass_data->user_email;
		$billDetails['city'] =$vars->user_city= $pass_data->city;
		$billDetails['country_code'] = $vars->user_country= $pass_data->country_code;
		$billDetails['state_code'] = $vars->user_state= $pass_data->state_code;
		$billDetails['zipcode'] = $vars->user_zip= $pass_data->zipcode;
		$billDetails['phone'] = $vars->phone = $pass_data->phone;

		// remove new line
		$remove_character = array("\n", "\r\n", "\r");

		if(!empty($billDetails['add_line1']))
		{
			$billDetails['add_line1'] = str_replace($remove_character ,' ', $billDetails['add_line1']);
		}
		//$billDetails['add_line2'] = str_replace($remove_character ,' ', $billDetails['add_line2']);

		$guest_email = '';
		if(!$pass_data->user_id && $params->get( 'guest' ))
		{
			$guest_email = "&email=".md5($pass_data->user_email);
		}
		$vars->item_name = $pass_data->order_item_name;
		$vars->submiturl = JRoute::_("index.php?option=com_quick2cart&task=payment.confirmpayment&orderid=".($orderid)."&processor={$pg_plugin}");
		$vars->return = JUri::root().substr(JRoute::_("index.php?option=com_quick2cart&view=orders&layout=order".$guest_email."&orderid=".($orderid)."&processor={$pg_plugin}&Itemid=".$orderItemid),strlen(JUri::base(true))+1);
		$vars->cancel_return = JUri::root().substr(JRoute::_("index.php?option=com_quick2cart&view=cartcheckout&layout=cancel&processor={$pg_plugin}&Itemid=".$chkoutItemid),strlen(JUri::base(true))+1);
		$vars->url=$vars->notify_url= JRoute::_(JUri::root()."index.php?option=com_quick2cart&task=payment.processpayment&orderid=".($orderid).$guest_email."&processor=".$pg_plugin);
		$vars->currency_code = $pass_data->currency;
		$vars->comment = $pass_data->customer_note;
		$vars->amount = $pass_data->order_amt;

		if ($pg_plugin == 'paypal')
		{
			$send_payments_to_owner = $params->get('send_payments_to_store_owner', 0);
			$singleStoreCkout = $params->get('singleStoreCkout', 0);
			$commission = $params->get('commission', 0);

			// Lets set the paypal email if admin is not handling transactions
			if($send_payments_to_owner && $singleStoreCkout == 1 && $commission == 0)
			{
				$vars->business = $this->getStorePaypalId($orderid);
			}
		}

		$adaptiveDetails = array();
		$Quick2cartModelpayment = new Quick2cartModelpayment;
		$vars->adaptiveReceiverList = $this->getReceiverList($vars, $pg_plugin, $orderid);

		// For pre fill user \info in payment plug
		$vars->userInfo = $billDetails;
		$vars->client = "com_quick2cart";

		return $vars;
	}


	function getReceiverList($vars, $pg_plugin, $orderid) {

	// GET BUSINESS EMAIL
		$plugin = JPluginHelper::getPlugin( 'payment', $pg_plugin);
		$pluginParams = json_decode( $plugin->params );
		$businessPayEmial = "";
		if (property_exists($pluginParams, 'business')) {
			$businessPayEmial= trim($pluginParams->business);
		}
		else {
			return array();
		}

		$params=JComponentHelper::getParams('com_quick2cart');
		$send_payments_to_owner=$params->get('send_payments_to_owner',0);
		if($pg_plugin == 'adaptive_paypal')
		{
			// Lets set the paypal email if admin is not handling transactions
			//if($send_payments_to_owner)
			{
				$storeHelper = new storeHelper;

				// get payee detail having only paypal mode
				$adaptiveDetails = $storeHelper->getorderItemsStoreInfo($orderid);

				$params = JComponentHelper::getParams('com_quick2cart');
				$commission = $params->get("commission",0);
				//$adminCommisson =  (($vars->amount * $commission)/100 );

				// GET BUSINESS EMAIL
				$plugin = JPluginHelper::getPlugin( 'payment', $pg_plugin);
				$pluginParams = json_decode( $plugin->params );
				$businessPayEmial = "";
				if (property_exists($pluginParams, 'business')) {
					$businessPayEmial= trim($pluginParams->business);
				}
				$receiverList = array();
				$receiverList[0]=array();
				$tamount = 0;   // used to add ship and tax amount to admin
				// admin has his own products
				$receiverList[0]['receiver'] = $businessPayEmial;
				$receiverList[0]['amount'] = $vars->amount;
				$receiverList[0]['primary'] = true;

				if(!empty($adaptiveDetails[$businessPayEmial]))
				{
					// primary account
				//	$tamount = $tamount + $receiverList[0]['amount'];
					unset($adaptiveDetails[$businessPayEmial]);
				}
				else
				{

					//$tamount = $tamount + $receiverList[0]['amount'];
				}

				// add other receivers
				$index =1;

				foreach($adaptiveDetails as $detail)
				{
					$receiverList[$index]['receiver'] = $detail['pay_detail'];
					$receiverList[$index]['amount'] = $detail['commissonCutPrice'];
					$receiverList[$index]['primary'] = false;

					//$tamount = $tamount + $receiverList[$index]['amount'] ;
					$index++;
				}
				return $receiverList;
			}
		}
	}

	function getHTML($pg_plugin, $tid)
	{
		$vars = $this->getPaymentVars($pg_plugin, $tid);

		JPluginHelper::importPlugin('system');
		$dispatcher1 = JDispatcher::getInstance();
		$result=$dispatcher1->trigger('OnBeforeq2cPay',array($vars));//Call the plugin and get the result
		if(!empty($result)){
			$vars=$result[0];
		}
		JPluginHelper::importPlugin('payment', $pg_plugin);
		$dispatcher = JDispatcher::getInstance();
		$html = $dispatcher->trigger('onTP_GetHTML', array($vars));
		return $html;
	}
	function getdetails($tid){
		$query="SELECT user_id,firstname,lastname,address,user_email,city,country_code,state_code,zipcode,phone
				FROM #__kart_users as ou
				where ou.order_id=".$tid." AND ou.address_type='BT'";
		$this->_db->setQuery($query);
		$orderdetails=$this->_db->loadObjectlist();
		$query="SELECT oi.order_item_name
				FROM #__kart_order_item  as oi
				where oi.order_id=".$tid;
		$this->_db->setQuery($query);
		$orderitems=$this->_db->loadResult();
		$orderdetails['0']->order_item_name = $orderitems;
		$query="SELECT o.amount,o.currency,o.customer_note,o.prefix
				FROM #__kart_orders  as o
				where o.id=".$tid;
		$this->_db->setQuery($query);
		$orderamt=$this->_db->loadObjectlist();
		$orderdetails['0']->prefix = $orderamt[0]->prefix;
		$orderdetails['0']->order_amt = $orderamt[0]->amount;
		$orderdetails['0']->currency = $orderamt[0]->currency;
		$orderdetails['0']->customer_note = preg_replace('/\<br(\s*)?\/?\>/i', " ", $orderamt[0]->customer_note);
		return $orderdetails['0'];

	}

	function processpayment($post, $pg_plugin, $order_id)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$jinput=JFactory::getApplication()->input;

		$jinput->set('remote',1);
		$sacontroller = new quick2cartController();
		$sacontroller->execute('clearcart');
		$orderItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders');
		$chkoutItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cartcheckout');
		$return_resp=array();

		//Authorise Post Data
		if(!empty($post['plugin_payment_method']) && $post['plugin_payment_method']=='onsite')
			$plugin_payment_method=$post['plugin_payment_method'];

		$order_id = $this->extract_prefix($order_id);
		$vars = $this->getPaymentVars($pg_plugin, $order_id);

		// Payment trigger
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment', $pg_plugin);
		$data = $dispatcher->trigger('onTP_Processpayment', array($post, $vars));//$vars added by aniket
		$data = $data[0];

		$res=@$this->storelog($pg_plugin, $data);
		//get order id
		if(empty($order_id))
		{
			$order_id=$data['order_id'];
		}
		$order_id = $this->extract_prefix($order_id);
		/*start for guest checkout*/
		$query="SELECT ou.user_id,ou.user_email
			FROM #__kart_users as ou
			WHERE ou.address_type='BT' AND ou.order_id = ".$order_id;
		$this->_db->setQuery($query);
		$user_detail=$this->_db->loadObject();
		$params = JComponentHelper::getParams('com_quick2cart');
		$guest_email = "";
		if(!$user_detail->user_id && $params->get( 'guest' ))
		{
			$guest_email = "&email=".md5($user_detail->user_email);
		}
		/*end for guest checkout*/

		$data['processor']=$pg_plugin;
		$data['status']=trim($data['status']);
		$query="SELECT o.amount
				FROM #__kart_orders  as o
				where o.id=".$order_id;
		$this->_db->setQuery($query);
		$order_amount=$this->_db->loadResult();
		$return_resp['status']='0';

		$epsilon = 0.00;
		$epsilon2 = 0.01;

		$return_resp['msg'] = JText::_('COM_QUICK2CART_ORDER_THNX');
		if($data['status']=='C' && ($data['total_paid_amt'] - $order_amount) >= $epsilon)
		{
			// received amount is greater or equal to order amount
			$data['status'] = 'C';
			$return_resp['status']='1';
			$return_resp['msg']=JText::_('COM_QUICK2CART_ORDER_THNX_CONFIRM');
		}
		else if(($order_amount - $data['total_paid_amt'] ) > $epsilon2)
		{
			// received amount les
			$data['status'] = 'E';
			$return_resp['status']='0';
			$comp_pay = "&paybuttonstatus=1";
			$return_resp['msg']=JText::_('COM_QUICK2CART_ORDER_THNX_ERROR');
		}
		else if(empty($data['status'])){
			$data['status'] = 'P';
			$return_resp['status']='0';
			$comp_pay = "&paybuttonstatus=1";
			$return_resp['msg']=JText::_('COM_QUICK2CART_ORDER_THNX');
		}
		if($data['status']!='C' && !empty($data['error']) ){
			$return_resp['msg']=$data['error']['code']." ".$data['error']['desc'];
			$comp_pay = "&paybuttonstatus=1";
			$link = '<a href="#complete-order">'.JText::_('COM_QUICK2CART_ORDER_PROCESS_AGAIN_TEXT').'</a>';
			$comp_pay_msg = '</br>'.JText::sprintf('COM_QUICK2CART_ORDER_PROCESS_AGAIN', $link);
			$return_resp['msg'].= $comp_pay_msg ;
		}

		$this->updateOrder($data, $pg_plugin);
		$comquick2cartHelper->updatestatus($order_id, $data['status']);
		$return_resp['return'] = JUri::root().substr(JRoute::_("index.php?option=com_quick2cart&view=orders&layout=order".$guest_email."&orderid=".($order_id)."&processor={$pg_plugin}".$comp_pay."&Itemid=".$orderItemid,false),strlen(JUri::base(true))+1);
		return $return_resp;
	}

	function storelog($name, $data)
	{
		$data1=array();
		$data1['raw_data']=$data['raw_data'];
		$data1['JT_CLIENT']="com_quick2cart";

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment', $name);
		$data = $dispatcher->trigger('onTP_Storelog', array($data1));

	}
	/**
	 * This function update order info. Update mdate,transaction_id. [DOEN'T CHAGE ORDER STATUS HERE]
	 *
	 * @param   integer  $storeId  store id.
	 * @param   array  $pg_plugin  processed plugin name.
	 *
	 */
	function updateOrder($data, $pg_plugin)
	{
			$db= JFactory::getDBO();
			$res = new stdClass();
			$eoid=$this->extract_prefix($data['order_id']); // $eoid means extracted order id
			$res->id = $eoid;
			$res->mdate 			= date("Y-m-d H:i:s");
			$res->transaction_id 	= $data['transaction_id'];
//			$res->status 	  		= $data['status']; /*changed by dipti since there is already a update status function*/
			$res->processor 		= $data['processor'];
//			$res->payee_id			= $data['buyer_email'];
			//appending raw data to orders's extra field data
			$comquick2cartHelper = new comquick2cartHelper;
			$q="SELECT  `extra` FROM  `#__kart_orders` WHERE `id` =".$eoid;
			$res->extra = $comquick2cartHelper->appendExtraFieldData($data['raw_data'], $q);
			if(!$db->updateObject( '#__kart_orders', $res, 'id' ))
			{
				//return false;
			}

			// Add payout entry
			$payout_id = $this->addPayoutEntry($eoid, $res->transaction_id, $data['status'], $pg_plugin);

	}
	/**
	 * $res is an object
	 * */
	function addPayoutEntry($order_id, $txnid, $status, $pg_plugin)
	{
		// GET BUSINESS EMAIL
		$plugin = JPluginHelper::getPlugin( 'payment', $pg_plugin);
		$pluginParams = json_decode($plugin->params);
		$businessPayEmial = "";
		if (property_exists($pluginParams, 'business')) {
			$businessPayEmial= trim($pluginParams->business);
		}
		else {
			return array();
		}
		$params = JComponentHelper::getParams('com_quick2cart');
		$send_payments_to_owner = $params->get('send_payments_to_owner',0);

		if($pg_plugin == 'adaptive_paypal')
		{
			// Lets set the paypal email if admin is not handling transactions
			//if($send_payments_to_owner)
			{
				$comquick2cartHelper = new comquick2cartHelper;
				$storeHelper = new storeHelper;
				$adaptiveDetails = $storeHelper->getorderItemsStoreInfo($order_id);
				$Quick2cartModelReports = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/models/reports.php",'Quick2cartModelReports') ;

 				$reportStatus = ($status=='C') ? 1: 0;

				foreach($adaptiveDetails as $userReport)  {

					$Quick2cartModelpayment = new Quick2cartModelpayment;
					$payDetail = $Quick2cartModelpayment->getPayoutId($txnid, $userReport['owner']);
					if(!empty($payDetail) && $payDetail['status'] == $reportStatus) {
						// payout already present mean $payDetail will not empty AND STATUS is same then dont process.
						// for new payout,thisl will not process

						break;
					}
					$post = array();
					$post['id'] = empty($payDetail['id'])? '': $payDetail['id'];
					$post['user_id'] = $userReport['owner'];
					$post['payee_name'] = $comquick2cartHelper->getUserName($post['user_id']);
					$post['paypal_email'] = $userReport['store_email'];
					$post['transaction_id'] = $txnid;
					$post['payment_amount'] = $userReport['commissonCutPrice'];
					$post['payout_date'] = date('Y-m-d');
					$post['status'] = $reportStatus;
					$post['payment_comment']= "adaptive pay";
					$Quick2cartModelReports->savePayout($post);
				}
			}
		}

	}
	function getPayoutId($transactionID, $userid){
		$db=JFactory::getDBO();
	 	$query="SELECT `id`,`status`
		FROM `#__kart_payouts`
		WHERE `transaction_id`='".$transactionID . "' AND `user_id`=".$userid;
		$db->setQuery($query);
		return $db->loadAssoc();
	}

	/*
	 * i/p : $oid eg. QTC-ZLO36-00078
	 * o/p : $prefix eg. 78
	 * */
function extract_prefix($prefix_orderid){
	$params = JComponentHelper::getParams('com_quick2cart');
	$separator=(string)$params->get('separator');
	$prefix_array = explode($separator, $prefix_orderid);
	if(count($prefix_array)==1){
		return $prefix_array[0];
	}
	else{
		$use_random_orderid=(int)$params->get('random_orderid');
		if($use_random_orderid){
			$order_id = $prefix_array[2];
		}else{
			$order_id = $prefix_array[1];
		}
		$order_id = ltrim($order_id,"0");
		/* @TODO trim the padded zero's from order id*/
		return $order_id;
	}
}

	/*
	 * i/p : $oid eg. 78
	 * o/p : $prefix eg.QTC-ZLO36-000
	 * */
	function generate_prefix($oid){
		$params = JComponentHelper::getParams('com_quick2cart');
		/*##############################################################*/
		// Lets make a random char for this order
		//take order prefix set by admin
		$order_prefix=(string)$params->get('order_prefix');
		$order_prefix=substr($order_prefix,0,5);//string length should not be more than 5
		//take separator set by admin
		$separator=(string)$params->get('separator');
		$prefix=$order_prefix.$separator;
		//check if we have to add random number to order id
		$use_random_orderid=(int)$params->get('random_orderid');
		if($use_random_orderid)
		{
			$random_numer=$this->_random(5);
			$prefix.=$random_numer.$separator;
			//this length shud be such that it matches the column lenth of primary key
			//it is used to add pading
			$len=(23-5-2-5);//order_id_column_field_length - prefix_length - no_of_underscores - length_of_random number
		}else{
			//this length shud be such that it matches the column lenth of primary key
			//it is used to add pading
			$len=(23-5-2);//order_id_column_field_length - prefix_length - no_of_underscores
		}
		/*##############################################################*/

		$maxlen=23-strlen($prefix)-strlen($oid);
		$padding_count=(int)$params->get('padding_count');
		//use padding length set by admin only if it is les than allowed(calculate) length
		if($padding_count>$maxlen){
			$padding_count=$maxlen;
		}
		$append='';
		if(strlen((string)$oid)<=$len)
		{
			for($z=0;$z<$padding_count;$z++){
				$append.='0';
			}
			//$append=$append.$oid;
		}
		$prefix .= $append;

	return $prefix;
	}


	function _random( $length = 5 )
	{
		$salt = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$random = '';

		$stat = @stat(__FILE__);
		if(empty($stat) || !is_array($stat)) $stat = array(php_uname());

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i ++) {
			$random .= $salt[mt_rand(0, $len -1)];
		}

		return $random;
	}

	/** VM: This function update order gateway on change of gateway*/
	function updateOrderGateway($selectedGateway, $order_id)
	{
		$db   =  JFactory::getDBO();
		$row  =  new stdClass;
		$row->id = $order_id;
		$row->processor = $selectedGateway;
		if(!$this->_db->updateObject('#__kart_orders', $row, 'id'))
		{
			echo $this->_db->stderr();
			return 0;
		}
		return 1;
	}

	/**
	 * Method gives first stores paypal email .
	 *
	 * @param    integer  $order_id  order id .
	 *
	 * @since    2.2
	 * @return   string paypal email.
	 */
	function getStorePaypalId($order_id)
	{
		if ($order_id)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			 // Check in tax related table
			$query->select('s.`pay_detail`');
			$query->from("#__kart_order_item AS i");
			$query->join('LEFT','`#__kart_orders` AS o ON i.order_id=o.id');
			$query->join('LEFT','`#__kart_store` AS s ON s.id = i.store_id');
			$query->where('o.id=' . $order_id);

			try
			{
				$db->setQuery($query);
				$detail = $db->loadAssocList();

				if (!empty($detail[0]['pay_detail']))
				{
					return $detail[0]['pay_detail'];
				}
			}
			catch (Exception $e)
			{
				$this->setMessage(JText::_('JLIB_DATABASE_ERROR_ANCESTOR_NODES_LOWER_STATE'), 'error');
				return false;
			}
		}
		return '';
	}

}

