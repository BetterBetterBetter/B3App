<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JPATH_COMPONENT.DS.'controller.php' );

jimport('joomla.application.component.controller');

class quick2cartControllerregistration extends JControllerLegacy
{

	function __construct()
	{
		parent::__construct();

	}

	function save()
	{
		$jinput=JFactory::getApplication()->input;
		$id = $jinput->get('cid');
        $model = $this->getModel('registration');
        $session = JFactory::getSession();
        //get data from request

		$post	= $jinput->get('post');
		$socialadsbackurl=$session->get('socialadsbackurl');
 				// let the model save it
 		$result = $model->store($post);
        if ($result)
        {
            $message = JText::_( 'REGIS_USER_CREATE_MSG' );
            $itemid=$jinput->get('Itemid');
						$user = JFactory::getuser();
						$cart = $session->get('cart_temp');
						$session->set('cart' . $user->id,$cart);
							$session->clear('cart_temp');
						$cart1 = $session->get('cart' . $user->id);
						$this->setRedirect($socialadsbackurl, $message);
        }
        else
        {
         	$message = $jinput->get('message','','STRING');
		    $itemid=$jinput->get('Itemid');
		    $this->setRedirect('index.php?option=com_quick2cart&view=registration&Itemid=' . $itemid, $message);
        }
	}
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$jinput=JFactory::getApplication()->input;
		$itemid=$jinput->get('Itemid');
		$this->setRedirect( 'index.php', $msg );
	}

	function login()
	{
		$jinput=JFactory::getApplication()->input;
		$pass = $jinput->get('qtc_password');
		$username = $jinput->get('login_user_name');
		$itemid = $jinput->get('Itemid');
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$status=$mainframe->login(array('username'=>$username, 'password'=>$pass), array('silent'=>true));
		if($status)
		{
			$mainframe->redirect(JRoute::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $Itemid,false));
		}
		else
		{
			$massage= JText::_( 'Q2C_LOGIN_FAIL' );
			$mainframe->redirect(JRoute::_('index.php?option=com_quick2cart&view=registration&Itemid=' . $itemid,$massage,'alert'));
		}
	} // end of login function

	function guest_checkout()
	{
		$jinput=JFactory::getApplication()->input;
		$itemid = $jinput->get('Itemid');
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$mainframe->redirect(JRoute::_('index.php?option=com_quick2cart&view=cartcheckout&guestckout=1&Itemid=' . $itemid,false));
	}
	//  for one page checkout As it is copied from sagar file
	function login_validate()
	{
		$input=JFactory::getApplication()->input;
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$itemid = $input->get('Itemid');
		$redirect_url = JRoute::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemid,false);

		$json = array();

		if ($user->id)
		{
			$json['redirect'] = $redirect_url;
		}


		if (!$json)
		{
			require_once (JPATH_SITE.'/components/com_quick2cart/helpers/user.php');
			$userHelper = new Quick2cartHelperUser;

			// Now login the user
			if (!$userHelper->login(array('username' => $input->get('email', '', 'STRING'), 'password' => $input->get('password', '', 'STRING'))))
			{
				// If not logged in then show error msg.
				$json['error']['warning'] = JText::_('COM_QUICK2CART_ERROR_LOGIN');
			}

		}
			$json['redirect'] = $redirect_url;

		echo json_encode($json);
		$app->close();
		//jexit();
	}

}

