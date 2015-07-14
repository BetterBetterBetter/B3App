<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAchievements
{
	/*
	 * Allows user to add the author as a friend.
	 *
	 * @param	int	$bloggerid	The blogger id.
	 */
	public function getHTML($bloggerid="")
	{
		$config = EB::config();
		$html = '';

		$easysocial = EB::easysocial();

		if ($config->get('integrations_easysocial_badges') && $easysocial->exists()) {
			$easysocial->init();

			$user = Foundry::user($bloggerid);
			$badges	= $user->getBadges();

			$theme = EB::template();
			$theme->set('badges', $badges);
			$html = $theme->output( 'site/easysocial/achievements' );
		}

		return $html;
	}
}
