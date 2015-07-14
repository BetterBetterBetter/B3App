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

require_once(__DIR__ . '/model.php');

class EasyBlogModelMenu extends EasyBlogAdminModel
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Retrieves all menus associated with EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssociatedMenus()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog%');

		$language = false;

		if (!$this->app->isAdmin()) {
        	$language = JFactory::getApplication()->getLanguageFilter();
        	$languageTag = JFactory::getLanguage()->getTag();
    	}

        if ($language) {

        	$query[] = 'AND';
        	$query[] = '(';
        	$query[] = $db->qn('language') . '=' . $db->Quote($languageTag);
        	$query[] = 'OR';
        	$query[] = $db->qn('language') . '=' . $db->Quote('*');
        	$query[] = ')';
        }

        $query = implode(' ', $query);

        $db->setQuery($query);

        $result = $db->loadObjectList();

        return $result;
	}

	/**
	 * Retrieves the link property of a given menu id
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenuLink($itemId)
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT ' . $db->qn('link') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE ' . $db->qn('id') . '=' . $db->Quote($itemId);
		$query = implode(' ', $query);

		$db->setQuery($query);

		$link = $db->loadResult();

		return $link;
	}

	/**
	 * Retrieve menu items associated with a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByPostId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=entry&id=' . $id . '%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=entry&id=' . (int) $id . '%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByAllCategory()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=categories');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=categories&limit%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByCategoryId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $id . '&limit%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=categories&layout=listings&id=' . (int) $id);
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByBloggerId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $id . '%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . (int) $id . '%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with team blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByTagId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id . '&limit%');
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . (int) $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=tags&layout=tag&id=' . (int) $id . '&limit%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with team blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenus($view, $layout = null)
	{
		$db = EB::db();

		$layout = is_null($layout) ? '' : '&layout=' . $layout;

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=' . $view . $layout);
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve menu items associated with team blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenusByTeamId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE';
		$query[] = '(';
		$query[] = $db->qn('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $id);
		$query[] = 'OR';
		$query[] = $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $id . '&limit%');
		$query[] = ')';
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = EBR::getLanguageQuery();
		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}
}
