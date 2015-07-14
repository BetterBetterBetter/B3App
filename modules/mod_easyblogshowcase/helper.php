<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class modEasyBlogShowcaseHelper
{
	public static function getItems($params)
	{
		$model = EB::model('Blog');

		// Determines if we should display featured or latest entries
		$type = $params->get('showposttype', 'featured');

		// Determines if we should filter by category
		$categoryId = $params->get('catid');

		$result = array();

		if ($categoryId) {
			$categoryId = (int) $categoryId;
		}

		if ($type == 'latest' && !$categoryId) {
			$result = $model->getBlogsBy('', '', 'latest' , $params->get( 'count' ) , EBLOG_FILTER_PUBLISHED, null, true, array(), false, false, false);
		}

		if ($type == 'latest' && $categoryId) {
			$result = $model->getBlogsBy('category', $categoryId , 'latest' , $params->get( 'count' ) , EBLOG_FILTER_PUBLISHED, null, true, array(), false, false, false);
		}

		// If not latest posttype, show featured post.	
		if ($type == 'featured') {
			if ($categoryId == 0) {
				$categoryId = '';
			}
			
			$result = $model->getFeaturedBlog($categoryId);
		}
	
		// If there's nothing to show at all, don't display anything
		if (!$result) {
			return $result;
		}

		$results = EB::formatter('list', $result);

		// Randomize items
		if ($params->get('autoshuffle')) {
			shuffle($results);
		}

		$contentKey	= $params->get('contentfrom', 'content');
		$textcount = $params->get('textlimit', '200');

		$posts = array();

		foreach ($results as $result) {

			$content = $result->getContent();

			// Get the content from the selected source
			if ($contentKey == 'intro') {
				$content = $result->getIntro(true);
			}

			// Truncate the content 
			if (JString::strlen(strip_tags($content)) > $textcount) {
				$content = JString::substr(strip_tags($content), 0, $textcount) . '...';
			} 

			$result->content = $content;

			$posts[] = $result;
		
		}

		return $posts;
	}
}
