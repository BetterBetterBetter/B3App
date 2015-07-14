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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewArchive extends EasyBlogView
{
	/**
	 * Displays the default archives view
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Get the archives model
		$model = EB::model('Archive');
		
		// Get a list of posts
		$posts = $model->getPosts();

		// Format the posts
		$posts = EB::formatter('list', $posts);

		// Get the pagination
		$pagination = $model->getPagination();

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('blogs/archives/list');
	}

	/**
	 * Deprecated. Use view=calendar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function calendar($tmpl = null)
	{
		return $this->app->redirect(EB::_('index.php?option=com_easyblog&view=calendar', false));
	}
}
