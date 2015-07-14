<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JPATH_COMPONENT.DS.'controller.php' );

jimport('joomla.application.component.controller');

class quick2cartControllerpayment extends JControllerLegacy
{


	function getHTML() {
		$model= $this->getModel( 'payment');
		$jinput=JFactory::getApplication()->input;
		$pg_plugin = $jinput->get('processor');
		$user = JFactory::getUser();
		$session =JFactory::getSession();
		$order_id = $jinput->get('order');
		$html=$model->getHTML($pg_plugin,$order_id);
		if(!empty($html[0]))
		echo $html[0];
		jexit();
	}
	function confirmpayment(){
		$model= $this->getModel( 'payment');
		$session =JFactory::getSession();
		$jinput=JFactory::getApplication()->input;
		$order_id = $session->get('order_id');

		if(empty($order_id))
		{
			$order_id = $jinput->get('order_id','','STRING');
		}
		if(empty($order_id))  // have to check and fix for this
		{
			$order_id = $jinput->get('orderid','','STRING');
		}
		$pg_plugin = $jinput->get('processor');

		$response=$model->confirmpayment($pg_plugin,$order_id);
	}
	function processpayment()
	{
		$mainframe=JFactory::getApplication();
		$jinput=JFactory::getApplication()->input;
		$session =JFactory::getSession();
		if($session->has('payment_submitpost'))
		{
			$post = $session->get('payment_submitpost');
			$session->clear('payment_submitpost');
		}
		else
		{
			$post = JRequest::get('post');
		}

		$logdata['post'] = JRequest::get('post');
		$logdata['get'] = JRequest::get('get');

		$this->Storelog('paymentLog', $logdata);

		$pg_plugin = $jinput->get('processor');
		$model= $this->getModel('payment');
		$order_id = $jinput->get('orderid','','STRING');

		// temparary
		if(empty($order_id))   // have to check and fix for this
		{
			$order_id = $jinput->get('order_id','','STRING');
		}

		if(empty($post) || empty($pg_plugin) ){
			JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error');
			return;
		}
		/*
		$file = 'payu.txt';

		// The new person to add to the file
		$person = json_encode($post) . "-- $order_id \n <br>";
		// Write the contents to the file,
		// using the FILE_APPEND flag to append the content to the end of the file
		// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
		file_put_contents($file, $person, FILE_APPEND | LOCK_EX);
		*/
		// for adaptive

		/*$testdata='{"transaction":{"0":"NONE","2":"false","1":"Completed"},"log_default_shipping_address_in_transaction":"false","action_type":"PAY","ipn_notification_url":"http:\/\/vidyasagar.tekdi.net\/15aug\/index.php?option=com_quick2cart&task=payment.processpayment&orderid=40&processor=adaptive_paypal","charset":"windows-1252","transaction_type":"Adaptive Payment PAY","notify_version":"UNVERSIONED","cancel_url":"http:\/\/vidyasagar.tekdi.net\/15aug\/index.php\/my-store\/products\/cartcheckout?view=cartcheckout&layout=cancel&processor=adaptive_paypal","verify_sign":"An5ns1Kso7MWUdW4ErQKJJJ4qi4-AbhnqYoO9tIsrix7MD2tSFTmzCUQ","sender_email":"sagar_c1@tekdi.net","fees_payer":"EACHRECEIVER","return_url":"http:\/\/vidyasagar.tekdi.net\/15aug\/index.php\/my-store\/orders?layout=order&orderid=40&processor=adaptive_paypal","reverse_all_parallel_payments_on_error":"false","tracking_id":"OID-00040","pay_key":"AP-8MV36029RY980203T","status":"COMPLETED","test_ipn":"1","payment_request_date":"Wed Aug 27 07:21:59 PDT 2014"}';
		$order_id = 40;
		$pg_plugin = "adaptive_paypal";
		$post = json_decode($testdata,true);

		$person = json_encode($post);*/
//		file_put_contents('Adaprive_component', "\n<br><br>-------------- REQUEST data -------------------<br>\n ".$person, FILE_APPEND | LOCK_EX);

		$response=$model->processpayment($post, $pg_plugin, $order_id);

		// Enque msg
		//$mainframe->enqueueMessage($response['msg']);
		$mainframe->redirect($response['return'], $response['msg']);
	}

	function Storelog($name="com_quick2cart", $logdata)
	{
		jimport('joomla.error.log');
		$options = "{DATE}\t{TIME}\t{USER}\t{DESC}";
		$logdata['JT_CLIENT'] = "com_quick2cart";

		$path = JPATH_ADMINISTRATOR.'/components/com_quick2cart/log/'.$name.'/';
		$my = JFactory::getUser();

		JLog::addLogger(
			array(
				'text_file' => $name.'.log',
				'text_entry_format' => $options ,
				'text_file_path' => $path
			),
			JLog::INFO,
			$logdata['JT_CLIENT']
		);

		$logEntry = new JLogEntry(JText::_('COM_QUICK2CART_PAY_LOG_TRASC_ADDED'), JLog::INFO, $logdata['JT_CLIENT']);
		$logEntry->user= $my->name.'('.$my->id.')';
		$logEntry->desc=json_encode($logdata);

		JLog::add($logEntry);
//		$logs = &JLog::getInstance($logdata['JT_CLIENT'].'_'.$name.'.log',$options,$path);
//    $logs->addEntry(array('user' => $my->name.'('.$my->id.')','desc'=>json_encode($logdata['raw_data'])));

	}
	function test()
	{
		$model= $this->getModel('payment');
		$model->getStorePaypalId(10);
	}

}

