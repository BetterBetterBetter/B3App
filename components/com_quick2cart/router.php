<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */

defined('_JEXEC') or die('Restricted access');

$lang =  JFactory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

function Quick2cartBuildRoute( &$query )
{
	$app      = JFactory::getApplication();
	$segments = array();
	$alias    = '';
	$storeURL_text = JText::_('QTC_VANITY_PAGE');
	// store_id based,
	if (array_key_exists('store_id', $query))
	{
		include_once JPATH_BASE.DS.'components'.DS.'com_quick2cart'.DS.'helpers'.DS.'storeHelper.php';
		$storeHelper = new storeHelper();
		$alias      = $storeHelper->getStoreAlias($query['store_id']) ;
	}
	
	if (isset($query['view']) && isset($query['layout']) && $alias)
	{
		if($query['view'] == 'vendor' && $query['layout'] == 'store' && !empty($alias) && !empty($query['Itemid'])){
			$segments[] = $alias;
			$segments[] = $storeURL_text; //$query['layout'];
			
			unset($query['store_id']);
			unset($query['layout']);
			unset($query['view']);
		}
	}
	if (!empty($query['Itemid'])){
		$menu = $app->getMenu();
		$menuItem = $menu->getItem( $query['Itemid'] );
		
		/* @TODO JUGAD START HERE for Vanity URL to display on createstore page */
		if (isset($query['vanitydisplay']) && isset($query['layout'])){
			unset($query['view']);
			unset($query['layout']);
			unset($query['vanitydisplay']);
		}	/* @TODO JUGAD END HERE */
		
		if (isset($query['view']) ){
			if ( ! isset($menuItem->query['view']) || $menuItem->query['view'] != $query['view'])
			{
				$segments[] = $query['view'];
				//unset($query['view']);
			}
			else
				unset($query['view']);
		}


		/*
		if (isset($query['layout']) ){
			if ( ! isset($menuItem->query['layout']) || $menuItem->query['layout'] != $query['layout'])
			{
				$segments[] = $query['layout'];
				//unset($query['layout']);
			}
			else
				unset($query['layout']);
		} */
	}

	return $segments;
}

function Quick2cartParseRoute($segments)
{
	$site 		= JFactory::getApplication();
	$vars         = array();
	$menu         = $site->getMenu();
	$selectedMenu = $menu->getActive();
	$storeURL_text = JText::_('QTC_VANITY_PAGE');

	// We need to grab the store id first see if the first segment is a store
	$count = count($segments);
	if ( ! empty($count))
	{
		$alias  = $segments[0];
		$storeid = '';
		if ( ! empty($alias))
		{
			// Check if this store exists in the alias
			$storeid = Quick2cartGetStoreId($alias);

			// Joomla converts ':' to '-' when encoding and during decoding,
			// it converts '-' to ':' back for the query string which will break things
			// if the alias has '-'. So we do not have any choice apart from
			// testing both this values until Joomla tries to fix this
			if ( ! $storeid && JString::stristr($alias , ':'))
			{
				$storeid = Quick2cartGetStoreId($alias);//CString::str_ireplace(':', '-', $alias));
			}

		}
		if (!$storeid){
			if(isset($segments[1]) && $segments[1] == $storeURL_text){
				return JError::raiseError(404, JText::_('QTC_STORE_NOT_FOUND'));
			}
		}
		if ($storeid != 0)
		{
			array_shift($segments);
			$vars['store_id'] = $storeid;

			// if empty, we should display the user's profile
			if (empty($segments))
			{
				$vars['view'] = 'vendor';
				$vars['layout'] = 'store';
			}
		}
	}
	
	$count = count($segments);
	
	if ($storeid != 0 && isset($selectedMenu) && $selectedMenu->query['view'] == 'category')
	{
		// We know this is a frontpage view in the menu, try to get the
		// view from the segments instead.
		if ($count > 0)
		{
			$vars['view'] = 'vendor';
			if($segments[0] == $storeURL_text)
				$vars['layout'] = 'store';
			else
				$vars['layout'] = $selectedMenu->query['layout'];
			if ( ! empty($segments[1]))
			{
				$vars['task'] = $segments[1];
			}
		}
	}
	return $vars;
}


function Quick2cartGetStoreId($alias)
{
	$db    = JFactory::getDBO();
	$query = 'SELECT ' . $db->quoteName('id').' FROM '.$db->quoteName('#__kart_store')
					.' WHERE '.$db->quoteName('vanityurl').'='.$db->Quote( $alias );
	$db->setQuery($query);
	$id = $db->loadResult();

	// The alias not found, could be caused by Joomla rewriting - into :
	// Replace the first : into - and search again
	if (empty($id))
	{
		$pattern     = '/([0-9]*)(:)/i';
		$replacement = '$1-';

		// Replace only the first occurance of : into -
		$alias = preg_replace($pattern, $replacement, $alias, 1);

		$query = 'SELECT '.$db->quoteName('id')
					.' FROM '.$db->quoteName('#__kart_store').' WHERE '.$db->quoteName('vanityurl').'='.$db->Quote( $alias );
		$db->setQuery($query);
		$id = $db->loadResult();
	}
	return $id;
}
