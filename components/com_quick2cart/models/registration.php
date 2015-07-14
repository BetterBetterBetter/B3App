<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table.user' );


class Quick2cartModelregistration extends JModelLegacy
{
	function __construct()
	  {
	 	parent::__construct();
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
	  }


	/* 	Method to store a client record
	 *	@param  array() $data
	 *	@return  boolean true/false
	 *	@since   1.0
	 * */
	function store($data)
	{
		global $mainframe;
		$mainframe  = JFactory::getApplication();
		$jinput		= $mainframe->input;
		$id 		= $jinput->get('cid');
		$session 	= JFactory::getSession();
		$db 		= JFactory::getDBO();

		// send array from ckout model
		$user_email = $data['user_email'];
		$user_name = $data['user_name']; //$data->get('user_name','','RAW');

		$user = JFactory::getUser();

		if (!$user->id)
		{
			$Quick2cartModelregistration = new Quick2cartModelregistration();
			$query = "SELECT id FROM #__users WHERE email = '".$user_email."' or username = '".$user_name."'";
			$this->_db->setQuery($query);
			$userexist = $this->_db->loadResult();
			$userid = "";
			$randpass = "";

			if (!$userexist)
			{
				// Generate the random password & create a new user
				$randpass	= $this->rand_str(6);
				$userid 	= $this->createnewuser($data, $randpass);

			}
			else
			{
				$message=JText::_('USER_EXIST');
				$jinput->set('message',$message);
				return false;
			}

			if ($userid)
			{
				JPluginHelper::importPlugin('user');

				if (!$userexist)
				{
					$Quick2cartModelregistration->SendMailNewUser($data, $randpass);
				}
				$user 	= array();
				$remCk = $jinput->get('remember',false,'BOOLEAN');
				$options = array('remember'=>$remCk);
				// tmp user details
				$user 	= array();
				$user['username'] = $data['user_name'];
				$options['autoregister'] = 0;
				$user['email'] = $user_email;
				$user['password'] = $randpass;
				$mainframe->login(array('username'=>$data['user_name'], 'password'=>$randpass), array('silent'=>true));
				//$mainframe->triggerEvent('onLoginUser', array($user, $options));
			}
		}
		return true;

	}//end store fn

/* 	create user
	 *	@param  array() $data
	 *	@param  $randpass random password
	 *	@return  boolean true/false
	 *	@since   1.0
	 * */
	function createnewuser($data, $randpass)
	{
		global $message;
		jimport('joomla.user.helper');
		$app = JFactory::getApplication();
		$authorize 	= JFactory::getACL();
		$user 		= clone(JFactory::getUser());
		$user->set('username', $data['user_name']);
		$user->set('password1', $randpass);
		$user->set('name', $data['user_name']);
		$user->set('email', $data['user_email']);

		// password encryption
		$salt  = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($user->password1, $salt);
		$user->password = "$crypt:$salt";

		// user group/type
		$user->set('id', '');
		$user->set('usertype', 'Registered');
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$userConfig = JComponentHelper::getParams('com_users');
			// Default to Registered.
			$defaultUserGroup = $userConfig->get('new_usertype', 2);
			$user->set('groups', array($defaultUserGroup));
		}
		else
		{
			$user->set('gid', $authorize->get_group_id( '', 'Registered', 'ARO' ));
		}

		$date = JFactory::getDate();
		$user->set('registerDate', $date->toSQL());

		// true on success, false otherwise
		if (!$user->save())
		{
			echo $message = JText::_('COM_QUICK2CART_UNABLE_TO_CREATE_USER_BZ_OF') . $user->getError();
			return false;
		}
		else
		{
		 	$message =  JText::sprintf('COM_QUICK2CART_CREATED_USER_AND_SEND_ACCOUNT_DETAIL_ON_EMAIL', $user->username);
		}
		$app->enqueueMessage($errMsg);
		return $user->id;
	}


	// Create a random character generator for password
	function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
	   // Length of character list
	   $chars_length = (strlen($chars) - 1);

	   // Start our string
	   $string = $chars{rand(0, $chars_length)};

	   // Generate random string
	   for ($i = 1; $i < $length; $i = strlen($string))
	   {
		   // Grab a random character from our list
		   $r = $chars{rand(0, $chars_length)};

		   // Make sure the same two characters don't appear next to each other
		   if ($r != $string{$i - 1}) $string .=  $r;
	   }

	   // Return the string
	   return $string;
	}


	function SendMailNewUser($data, $randpass)
	{

		$app = JFactory::getApplication();
		$mailfrom=$app->getCfg('mailfrom');
		$fromname=$app->getCfg('fromname');
		$sitename=$app->getCfg('sitename');

		$email=$data['user_email'];
		$subject=JText::_('SA_REGISTRATION_SUBJECT');
		$find1=array('{sitename}');
		$replace1	= array($sitename);
		$subject	= str_replace($find1, $replace1, $subject);

		$message=JText::_('SA_REGISTRATION_USER');
		$find=array('{firstname}','{sitename}','{register_url}','{username}','{password}');
		$replace	= array($data['user_name'],$sitename,JUri::root(),$data['user_name'],$randpass);
		$message	= str_replace($find, $replace, $message);

		JFactory::getMailer()->sendMail($mailfrom, $fromname, $email, $subject, $message);
		$messageadmin=JText::_('SA_REGISTRATION_ADMIN');
		$find2=array('{sitename}','{username}');
		$replace2	= array($sitename,$data['user_name']);
		$messageadmin	= str_replace($find2, $replace2, $messageadmin);

		JFactory::getMailer()->sendMail($mailfrom, $fromname, $mailfrom, $subject, $messageadmin);
		//die;
		return true;
	}


}


