<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  JSocial
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
jimport('joomla.filesystem.file');
/**
 * Interface to handle Social Extensions
 *
 * @package     Joomla.Libraries
 * @subpackage  JSocial
 * @since       3.1
 */
class JSocialJomwall implements JSocial
{

	public function __construct() {
		if (!$this->checkExists()) {
			throw new Exception('Jomwall is not Installed');
		}
	}

	public function getProfileData(JUser $user) {

	}

	public function getProfileUrl(JUser $user) {
		$awduser=new AwdwallHelperUser();
		$Itemid=$awduser->getComItemId();
		$link=JRoute::_('index.php?option=com_awdwall&view=awdwall&layout=mywall&wuid='.$user->id.'&Itemid='.$Itemid);
	}

	public function getAvatar(JUser $user) {

		$awduser=new AwdwallHelperUser();
		$uimage=$awduser->getAvatar($user->id);
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

		/*load jomwall core*/
		if(!class_exists('AwdwallHelperUser')){
			require_once(JPATH_SITE.DS.'components'.DS.'com_awdwall'.DS.'helpers'.DS.'user.php');
		}
		$linkHTML='<a href="'.$act_link.'">'.$act_title.'</a>';
		$comment=$act_description.' '.$linkHTML;
		$attachment=$act_link;
		$type='text';
		$imgpath=NULL;
		$params=array();

		AwdwallHelperUser::addtostream($comment,$attachment,$type,$actor_id,$imgpath,$params);

		return true;
	}
	public function setStatus(JUser $user, $status, $options) {}
	public function getRegistrationLink($options) {}
	public function sendMessage(JUser $user, $recepient) {}

	public function checkExists()
	{
		return JFolder::exists(JPATH_SITE.DS.'components'.DS.'com_awdwall'.DS.'helpers'.DS.'user.php');
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

	public function sendNotification(JUser $sender,JUser $receiver,$content="JS Notification",$options=array())
	{

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
		Foundry::notify( 'notify_invite.create' , $recipient , $emailOptions , $systemOptions );

	}

	public function send_SMS($user,$password,$api_id,$text,$to)
	{

	}
}
