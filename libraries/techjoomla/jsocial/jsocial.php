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

interface JSocial
{
	public function getProfileData(JUser $user);
	public function getProfileUrl(JUser $user);
	public function getAvatar(JUser $user);
	public function getFriends(JUser $user, $accepted=true);
	public function addFriend(JUser $connect_from_user,JUser $connect_to_user);
	public function pushActivity($actor_id,$act_type,$act_subtype,$act_description,$act_link,$act_title,$act_access);
	public function setStatus(JUser $user, $status, $options);
	public function sendNotification(JUser $sender,JUser $receiver,$content="JS Notification",$options=array());
	public function getRegistrationLink($options);
	public function checkExists();
	public function addpoints(JUser $receiver,$options=array());
	public function send_SMS($user,$password,$api_id,$text,$to);

}
