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
class JSocialJomsocial implements JSocial
{

	public function __construct()
	{
		if (!$this->checkExists()) {

			throw new Exception('Jomsocial not installed');
		}
		$this->mainframe=JFactory::getApplication();
		require_once JPATH_SITE . '/components/com_community/libraries/core.php';
	}

	public function checkExists()
	{
		return JFile::exists(JPATH_SITE.'/components/com_community/libraries/core.php');
	}

	public function getProfileData(JUser $user) {
		return CFactory::getUser($user->id);
	}


	public function getProfileUrl(JUser $user) {
		return $link=JUri::root().substr(CRoute::_('index.php?option=com_community&view=profile&userid='.$user->id),strlen(JUri::base(true))+1);
	}

	public function getAvatar(JUser $user)
	{
		$uimage='';
		$cuser=CFactory::getUser($user->id);
		$uimage=$cuser->getThumbAvatar();

		if(!$this->mainframe->isSite())
		{
			$uimage=str_replace('administrator/','',$uimage);
		}

		return $uimage;

	}
	public function getFriends(JUser $user, $accepted=true) {

		$friendsModel	= CFactory::getModel( 'Friends' );
		$friends		= $friendsModel->getFriends( $user->id , 'name' , false);
		$newfriends=array();
		if(!empty($friends))
		{
			$index=0;
			foreach( $friends as $friend )
			{
				$newfriends[$index]=new StdClass();
				$newfriends[$index]->id=$friend->id;
				$newfriends[$index]->name=$friend->getDisplayName($friend->id);
				$newfriends[$index]->avatar=$friend->getThumbAvatar($friend->id);
				$index++;
			}
		}

		return $newfriends;

	}

	public function addFriend(JUser $connect_from_user,JUser $connect_to_user)
	{
		$db			= 	JFactory::getDBO();
		$sql = 	"SELECT * FROM #__community_connection WHERE connect_from=$connect_to_user->id AND connect_to  = $connect_from_user->id ";
		$db->setQuery($sql);
		$once_done = $db->loadResult();

		if(!$once_done)
		{
			$insertfrnd = new stdClass();
			$insertfrnd->connection_id				=	null;
			$insertfrnd->connect_from				=	$connect_from_user->id;
			$insertfrnd->connect_to 			=	$connect_to_user->id;
			$insertfrnd->status 			=	1;
			$insertfrnd->group			=	0;
			$db->insertObject('#__community_connection', $insertfrnd , 'connection_id');

			$insertfrnds = new stdClass();
			$insertfrnds->connection_id				=	null;
			$insertfrnds->connect_from				=	$connect_to_user->id;
			$insertfrnds->connect_to 			=	$connect_from_user->id;
			$insertfrnds->status 			=	1;
			$insertfrnds->group			=	0;
			$db->insertObject('#__community_connection', $insertfrnds , 'connection_id');
		}

		//increase friend count of inviter and invitee
		$query="UPDATE `#__community_users`
		SET `friendcount`=`friendcount`+1
		WHERE `userid`= '".$connect_to_user->id."'";
		$db->setQuery($query);
		$db->execute();

		$query="UPDATE `#__community_users`
		SET `friendcount`=`friendcount`+1
		WHERE `userid`= '".$connect_from_user->id."'";
		$db->setQuery($query);
		$db->execute();
	}

	public function pushActivity($actor_id,$act_type,$act_subtype,$act_description,$act_link,$act_title,$act_access)
	{
		/*load Jomsocial core*/
		$linkHTML='';
		//push activity
		if($act_title and $act_link)
		$linkHTML='<a href="'.$act_link.'">'.$act_title.'</a>';
		$act=new stdClass();
		$act->cmd='wall.write';
		$act->actor=$actor_id;
		$act->target=0; // no target
		$act->title='{actor} ' .$act_description.' '.$linkHTML;
		$act->content='';
		$act->app='wall';
		$act->cid=0;
		$act->access=$act_access;
		CFactory::load('libraries','activities');
		if (defined('CActivities::COMMENT_SELF')) {
			$act->comment_id = CActivities::COMMENT_SELF;
			$act->comment_type = 'profile.location';
		}
		if (defined('CActivities::LIKE_SELF')) {
				$act->like_id = CActivities::LIKE_SELF;
				$act->like_type = 'profile.location';
		}

		$res=CActivityStream::add($act);
		return true;
	}

	public function setStatus(JUser $user, $status, $options) {
	}
	public function getRegistrationLink($options) {}

	public function sendNotification(JUser $sender,JUser $receiver,$content="JS Notification",$options=array())
	{

		CFactory::load( 'libraries' , 'userpoints' );
		CFactory::load( 'libraries' , 'notification' );
		CUserPoints::assignPoint('inbox.message.send');
		// Add notification
		$params			= new CParameter( '' );
		$params->set('url' , 'index.php?option=com_community&view=inbox&task=read&msgid='. $msgid );

		$params->set( 'message' , $data['body'] );
		$params->set( 'title'	, $data['subject'] );
		$my=CFactory::getUser();
		CNotificationLibrary::add( 'etype_inbox_create_message' , $my->id , $data[ 'to' ] , JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE', $my->getDisplayName()) , '' , 'inbox.sent' , $params );

		$model = CFactory::getModel('Notification');
		$model->add($receiver->id , $sender->id,$content,$options['cmd'], $options['type'], $options['params']);
	}


	public function addpoints(JUser $receiver,$options=array())
	{
		CFactory::load( 'libraries' , 'userpoints' );
		CFactory::load( 'libraries' , 'notification' );
		CuserPoints::assignPoint($options['command'],$receiver->id);
	}
	public function send_SMS($user,$password,$api_id,$text,$to)
	{

	}

}
