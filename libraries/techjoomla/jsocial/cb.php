<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  JSocial
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Interface to handle Social Extensions
 *
 * @package     Joomla.Libraries
 * @subpackage  JSocial
 * @since       3.1
 */
class JSocialCB implements JSocial
{

	public function __construct() {
		if (!$this->checkExists()) {
			throw new Exception('Community Builder is not Installed');
		}
	}

	public function getProfileData(JUser $user)
	{

	}

	public function getProfileUrl(JUser $user)
	{
		return $link=JUri::root().substr(JRoute::_('index.php?option=com_comprofiler&task=userprofile&user='.$user->id.'&Itemid='.$itemid),strlen(JUri::base(true))+1);

	}

	public function getAvatar(JUser $user)
	{
		$db=JFactory::getDBO();
		$q="SELECT a.id,a.username,a.name, b.avatar, b.avatarapproved
            FROM #__users a, #__comprofiler b
            WHERE a.id=b.user_id AND a.id=".$user->id;
        $db->setQuery($q);
        $cbuser=$db->loadObject();
		$img_path=JUri::root()."images/comprofiler";
		if(isset($cbuser->avatar) && isset($cbuser->avatarapproved))
		{
			if(substr_count($cbuser->avatar, "/") == 0)
			{
				$uimage = $img_path . '/tn' . $cbuser->avatar;
			}
			else
			{
				$uimage = $img_path . DS . $cbuser->avatar;
			}
		}
		else if (isset($cbuser->avatar))
		{//avatar not approved
			$uimage = JUri::root()."/components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png";
		}
		else
		{//no avatar
			$uimage = JUri::root()."/components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png";
		}
		return $uimage;

	}
	public function getFriends(JUser $user, $accepted=true) {}

	public function addFriend(JUser $connect_from_user,JUser $connect_to_user)
	{
				$db=JFactory::getDBO();

		//set frnd cnt to 1
		// make inviter and invitee friends
		$sql			= 	"SELECT * FROM #__comprofiler_members WHERE referenceid=". $connect_from_user->id." AND memberid  = ".$connect_to_user->id;
		$db->setQuery($sql);
		$once_done = $db->loadResult();
		if(!$once_done)
		{

			$insertfrnd = new stdClass();
			$insertfrnd->referenceid		=	$connect_to_user->id;
			$insertfrnd->memberid 			=	$connect_from_user->id;
			$insertfrnd->accepted 			=	1;
			$insertfrnd->pending			=	0;
			$insertfrnd->membersince			=	$dt;
			$db->insertObject('#__comprofiler_members', $insertfrnd);

			$insertfrnds = new stdClass();
			$insertfrnds->referenceid		=	$connect_from_user->id;
			$insertfrnds->memberid 			=	$connect_to_user->id;
			$insertfrnds->accepted			=	1;
			$insertfrnds->pending			=	0;
			$insertfrnds->membersince			=	$dt;
			$db->insertObject('#__comprofiler_members', $insertfrnds);
		}


	}

	public function pushActivity($actor_id, $act_type,$act_subtype='',$act_description='',$act_link='',$act_title='',$act_access)
	{

		//load CB framework
		global $_CB_framework, $mainframe;
		if(defined( 'JPATH_ADMINISTRATOR'))
		{
			if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_comprofiler/plugin.foundation.php'))
			{
				echo 'CB not installed!';
				return false;
			}
			include_once( JPATH_ADMINISTRATOR.'/components/com_comprofiler/plugin.foundation.php' );
		}
		else
		{
			if(!file_exists($mainframe->getCfg('absolute_path').'/administrator/components/com_comprofiler/plugin.foundation.php'))
			{
				echo 'CB not installed!';
				return false;
			}
			include_once( $mainframe->getCfg('absolute_path').'/administrator/components/com_comprofiler/plugin.foundation.php' );
		}

		cbimport('cb.plugins');
		cbimport('cb.html');
		cbimport('cb.database');
		cbimport('language.front');
		cbimport('cb.snoopy');
		cbimport('cb.imgtoolbox');

		global $_CB_framework, $_CB_database, $ueConfig;

		//load cb activity plugin class
		if(!file_exists(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."user".DS."plug_cbactivity".DS."cbactivity.class.php"))
		{
			//echo 'CB Activity plugin not installed!';
			return false;
		}
		require_once(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."user".DS."plug_cbactivity".DS."cbactivity.class.php");

		//push activity
		$linkHTML='<a href="'.$act_link.'">'.$act_title.'</a>';

		$activity=new cbactivityActivity( $_CB_database );
		$activity->set('user_id',$actor_id);
		$activity->set('type',$act_type);
		$activity->set('subtype',$act_subtype);
		$activity->set('title', $act_description.' '.$linkHTML);
		$activity->set('icon','nameplate');
		$activity->set('date',cbactivityClass::getUTCDate() );
		$activity->store();

		return true;
	}

	public function setStatus(JUser $user, $status, $options) {}
	public function getRegistrationLink($options) {}
	public function sendMessage(JUser $user, $recepient) {}

	public function checkExists()
	{
		return JFolder::exists(JPATH_SITE.'/components/com_comprofiler');
	}

	public function sendNotification(JUser $sender,JUser $receiver,$content="JS Notification",$options=array()){

		$recipient[] = $receiver->id;
		// If you do not want to send email, $emailOptions should be set to false
		// $emailOptions - An array of options to define in the mail
		// Email template
		$emailOptions 		= false;

		// If you do not want to send system notifications, set this to false.
		// $systemOptions - The internal system notifications
		// System notification template
		$myUser = Foundry::user($receiver->id);

		$systemOptions['url']	= JRoute::_($myUser->getPermalink());

		$title =  $myUser->getName()." ".$notification_msg;
		Foundry::notify( $options['command'] , $recipient , $emailOptions , $systemOptions );

	}

	/*
	 * @params
	 * $options is array
	 * command for example invites sent
	 * extension for com_invitex
	 * @return array success 0 or 1
	 */
	public function addpoints(JUser $receiver,$options=array())
	{
		if(empty($options['command']) or empty($options['extension']))
		{
				$return['success'] =0;
				$return['message'] ='Command or extension not passed to ';
				return $return;
		}

		$return['success']=Foundry::points()->assign( $options['command'] , $options['extension'] , $receiver->id );
	}

	public function send_SMS($user,$password,$api_id,$text,$to)
	{

	}
}
