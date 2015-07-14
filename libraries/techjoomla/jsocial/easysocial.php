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
class JSocialEasysocial implements JSocial
{

	public function __construct() {
	$this->db=JFactory::getDBO();
		if (!$this->checkExists()) {
			throw new Exception('Easysocial not installed');
		}
		require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
	}

	public function getProfileData(JUser $user) {
		return $user     = Foundry::user( $user->id );
	}

	public function getProfileUrl(JUser $user) {
				$user     = Foundry::user( $user->id );
				return $link=JRoute::_($user->getPermalink());
	}

	public function getAvatar(JUser $user)
	{
		$user     = Foundry::user( $user->id );
		$uimage=$user->getAvatar();
		return $uimage;
	}
	public function getFriends(JUser $user, $accepted=true)
	{
		$sql="select target_id from #__social_friends where actor_id=".$user->id."";
		$this->db->setQuery($sql);
		$tempfriends[]=$this->db->loadColumn();
		$sql="select actor_id from #__social_friends where target_id=".$user->id."";
		$this->db->setQuery($sql);
		$tempfriends[]=$this->db->loadColumn();
		$esfriends_ids=$this->umerge($tempfriends);


		$esfriends=array();
		foreach($esfriends_ids AS $id)
		{
			$user= Foundry::user( $id );
			$esfriends[$id]=new StdClass();
			$esfriends[$id]->id=$id;
			$esfriends[$id]->name=$user->name;
			$esfriends[$id]->avatar=$this->getAvatar($user );
			$esfriends[$id]->profile_link=$this->getProfileUrl($user);
		}

		return $esfriends;



	}

	public function addFriend(JUser $connect_from_user,JUser $connect_to_user)
	{
		$this->db=JFactory::getDBO();
		$sql			= 	"SELECT * FROM #__social_friends WHERE (actor_id=".$connect_from_user->id." AND target_id  = ".$connect_to_user->id.") OR (target_id=".$connect_from_user->id." AND actor_id  = ".$connect_to_user->id.")";
		$this->db->setQuery($sql);
		$once_done = $this->db->loadResult();
		if(!$once_done)
		{
			$insertfrnd = new stdClass();
			$insertfrnd->id				=	'';
			$insertfrnd->actor_id				=	$connect_to_user->id;
			$insertfrnd->target_id 			=	$connect_from_user->id;
			$insertfrnd->state 			=	1;
			$insertfrnd->created			=	date('y-m-d h:i:s');
			$insertfrnd->modified			=	date('y-m-d h:i:s');
			$this->db->insertObject('#__social_friends', $insertfrnd , 'id');
		}


	}

	public function addStream(JUser $options) {}
	public function setStatus(JUser $user, $status, $options) {}
	public function getRegistrationLink($options) {}

	public function checkExists()  {
		return JFile::exists(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');
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

	public function sendNotification(JUser $sender,JUser $receiver,$content="ES Notification",$systemOptions=array()){

		$recipient[] = $receiver->id;
		// If you do not want to send email, $emailOptions should be set to false
		// $emailOptions - An array of options to define in the mail
		// Email template
		$emailOptions 		= false;

		// If you do not want to send system notifications, set this to false.
		// $systemOptions - The internal system notifications
		// System notification template
		$myUser = Foundry::user($receiver->id);

		$cmd=$systemOptions['cmd'];
		unset($systemOptions['cmd']);
		$systemOptions['url']	= JRoute::_($myUser->getPermalink());

		$resp=Foundry::notify( $cmd, $recipient , $emailOptions , $systemOptions );

	}
	public function pushActivity($actor_id,$act_type,$act_subtype,$act_description,$act_link,$act_title,$act_access)
	{

		if($actor_id!=0)
		$myUser = Foundry::user( $actor_id );
		$stream = Foundry::stream();
		$template = $stream->getTemplate();
		$template->setActor( $actor_id, SOCIAL_TYPE_USER );
		$template->setContext( $actor_id, SOCIAL_TYPE_USERS );
		$template->setVerb( 'invite' );
		$template->setType( SOCIAL_STREAM_DISPLAY_MINI );
		if($actor_id!=0)
		{
			$userProfileLink = '<a href="'. $myUser->getPermalink() .'">' . $myUser->getName() . '</a>';
			$title 	 = ($userProfileLink." ".$act_description);
		}
		else
		$title 	 = ("A guest ".$act_description);
		$template->setTitle( $title );
		$template->setAggregate( false );

		$template->setPublicStream( 'core.view' );
		$stream->add( $template );
		return true;
	}

	public function send_SMS($user,$password,$api_id,$text,$to)
	{

	}

	public function umerge($arrays){
		$result = array();
		 foreach($arrays as $array){
		  $array = (array) $array;
		  foreach($array as $value){
		   if(array_search($value,$result)===false)$result[]=$value;
		  }
		 }
		 return $result;
	}
}
