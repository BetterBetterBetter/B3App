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
class JSocialJoomla implements JSocial
{

	var $gravatar = true;

	var $gravatar_surl = 'https://secure.gravatar.com/avatar/';

	var $gravatar_url = 'http://www.gravatar.com/avatar/';

	var $gravatar_size = 200;

	var $gravatar_default = '';

	var $gravatar_rating = 'g';

	var $gravatar_secure = false;

	public function __construct() {
		require_once JPATH_SITE . '/components/com_users/helpers/route.php';
	}



	public function getProfileData(JUser $user){}
	public function getProfileUrl(JUser $user){}
	public function getAvatar(JUser $user){}
	public function getFriends(JUser $user, $accepted=true){}
	public function addFriend(JUser $connect_from_user,JUser $connect_to_user){}
	public function pushActivity($actor_id,$act_type,$act_subtype,$act_description,$act_link,$act_title,$act_access){}
	public function setStatus(JUser $user, $status, $options){}
	public function sendNotification(JUser $sender,JUser $receiver,$content="JS Notification",$options=array()){}
	public function getRegistrationLink($options){}
	public function checkExists(){}
	public function addpoints(JUser $receiver,$options=array()){}
	public function send_SMS($user,$password,$api_id,$text,$to)
	{
		$baseurl ="https://api.clickatell.com";
		//test trext and phone number....should be pass from plugin
		$text = urlencode($text);
		//$to = 919970000526;
		// auth call
		$url = $baseurl."/http/auth?user=".$user."&password=".$password."&api_id=".$api_id;
		// do auth call
		$ret = file($url);
		// explode our response. return string is on first line of the data returned
		$sess = explode(":",$ret[0]);

		if ($sess[0] == "OK")
		{
			$sess_id = trim($sess[1]); // remove any whitespace
			$url = $baseurl."/http/sendmsg?session_id=".$sess_id."&to=".$to."&text=".$text."&callback=6";
			// do sendmsg call
			$ret = file($url);
			$send = explode(":",$ret[0]);
			//print_r($send);die;
		}
		else
		{
			echo "Authentication failure: ". $ret[0];
		}

		if ($send[0] == "ID")
		{
			$return[0] = 1;
			$return[1] = $send[1];
			return $return;
		}
		else
		{
			$return[0] = -1;
			$return[1] = $send[0].$send[1];
			return $return;
		}
	}

}
