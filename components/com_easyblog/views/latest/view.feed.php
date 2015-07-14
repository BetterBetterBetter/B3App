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

class EasyBlogViewLatest extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Ensure that rss is enabled
		if (!$this->config->get('main_rss')) {
			return;
		}

		// Get sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');

		// Get the blogs model
		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('', '', $sort , 0 , EBLOG_FILTER_PUBLISHED, null, true);

		$this->doc->link = EB::_('index.php?option=com_easyblog&view=latest');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_FEEDS_LATEST_TITLE'));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_LATEST_DESC', JURI::root()));

		// If there's no data, skip this altogether
		if (!$posts) {
			return;
		}

		$posts = EB::formatter('list', $posts);

		foreach ($posts as $post) {

			$image = '';

			if ($post->getImage('medium', true, true)) {
				$image = '<img src="' . $post->getImage('medium', true, true) . '" width="250" align="left" />';
			}

			$item = new JFeedItem();
			$item->title = $this->escape($post->title);
			$item->link = $post->getPermalink();
			$item->description = $image . $post->getIntro();
			$item->date = $post->getCreationDate()->toSql();
			$item->category = $post->getPrimaryCategory()->getTitle();
			$item->author = $post->author->getName();
			$item->authorEmail = $this->getRssEmail($post->author);

			$this->doc->addItem($item);
		}

		return;
	}
}
