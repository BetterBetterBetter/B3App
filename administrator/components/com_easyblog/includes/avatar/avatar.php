<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAvatar extends EasyBlog
{
	public function getAvatarURL($profile)
	{
		// Get the avatar type
		$type = $this->config->get('layout_avatarIntegration');

		$class = 'EasyBlogAvatar' . ucfirst($type);

		if (!$this->loadLibrary($type)) {
			return false;
		}

		// Try to get the avatar
		$obj = new $class();
		$link = $obj->getAvatar($profile);

		// If we get a failed status, just render default avatar
		if ($link === false) {
			$this->loadLibrary('default');
			$obj = new EasyBlogAvatarDefault();
			$link = $obj->getAvatar($profile);
		}

		return $link;
	}

	private function loadLibrary($type)
	{
		$file = __DIR__ . '/adapters/' . strtolower($type) . '/client.php';

		require_once($file);

		return true;
	}

	public function setError($error)
	{
		$this->error = $error;
	}

	public function getError()
	{
		return $this->error;
	}

	/**
	 * Uploads a user avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function upload($fileData)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Check if the user is allowed to upload avatar
		$acl = EB::acl();

		// Ensure that the user really has access to upload avatar
		if (!$acl->get('upload_avatar')) {
			$this->setError('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR');
			return false;
		}

		// Get the current user
		$my  = JFactory::getUser();
		$app = JFactory::getApplication();
		$config = EB::config();

		$path = $config->get('main_avatarpath');
		$path = rtrim($path, '/');

		$relativePath = $path;
		$absolutePath = JPATH_ROOT . '/' . $path;

		// If the absolute path does not exist, create it first
		if (!JFolder::exists($absolutePath)) {
			JFolder::create($absolutePath);

			// Copy the index.html file over to this new folder
			JFile::copy(JPATH_ROOT . '/components/com_easyblog/index.html', $absolutePath . '/index.html');
		}

		// The file data should have a name
		if (!isset($fileData['name'])) {
			return false;
		}

		// Generate a better name for the file
		$fileData['name'] = $my->id . '_' . JFile::makeSafe($fileData['name']);

		// Get the relative path
		$relativeFile = $relativePath . '/' . $fileData['name'];

		// Get the absolute file path
		$absoluteFile = $absolutePath . '/' . $fileData['name'];

		// Test if the file is upload-able
		$message = '';

		if (!EB::image()->canUpload($fileData, $message)) {

			$this->setError($message);
			return false;
		}

		// Determines if the web server is generating errors
		if ($fileData['error'] != 0) {
			$this->setError($fileData['error']);
			return false;
		}

		// We need to delete the old avatar
		$profile = EB::user($my->id);

		// Get the old avatar
		$oldAvatar = $profile->avatar;
		$isNew = false;

		// Delete the old avatar first
		if ($oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png') {
			$session	= JFactory::getSession();
			$sessionId	= $session->getToken();

			$oldAvatarPath = $absolutePath . '/' . $oldAvatar;

			if (JFile::exists($oldAvatarPath)) {
				JFile::delete($oldAvatarPath);
			}
		} else {
			$isNew = true;
		}

		$width = EBLOG_AVATAR_LARGE_WIDTH;
		$height = EBLOG_AVATAR_LARGE_HEIGHT;

		$image = EB::simpleimage();
		$image->load($fileData['tmp_name']);
		$image->resizeToFill($width, $height);
		$image->save($absoluteFile, $image->type);

		if ($isNew && $config->get('main_jomsocial_userpoint')) {
			EB::jomsocial()->assignPoints('com_easyblog.avatar.upload', $my->id);
		}

		return $fileData['name'];
	}
}
