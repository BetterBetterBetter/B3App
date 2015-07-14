<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

Foundry::import( 'admin:/includes/model' );

class q2cmyproductsModel extends EasySocialModel
{
	/**
	 * Retrieves a list of products created by a particular user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$userId		The user's / creator's id.
	 *
	 * @return	Array				A list of products.
	 */
	public function getItems($userId , $limit = 0, $storeid = '')
	{
		$db = Foundry::db();
		$sql = $db->sql();
		$sql = 'SELECT DISTINCT(i.item_id), i.name, i.images, i.featured, i.parent, i.min_quantity, i.max_quantity, i.product_id, i.stock
		 FROM `#__kart_items` as i, `#__kart_store` as s
		 WHERE s.id = i.store_id
		 AND i.state=1
		 AND `owner` = '.$userId;

		if ($storeid)
		{
			$sql .= ' AND i.store_id = ' . $storeid;
		}

		$sql .= " AND i.parent = 'com_quick2cart'";
		$sql .=' order by i.item_id desc';

		if ($limit)
		{
			$sql .=' limit '. $limit;
		}

		$db->setQuery($sql);
		$result = $db->loadAssocList();

		return $result;
	}

	public function getProductsCount($userId, $storeid = '')
	{
		$db = Foundry::db();
		$sql = $db->sql();
		$sql = 'SELECT COUNT(i.item_id)
		 FROM `#__kart_items` as i, `#__kart_store` as s
		 WHERE s.id = i.store_id
		 AND i.state = 1
		 AND `owner` = '.$userId;

		if ($storeid)
		{
			$sql .= ' AND i.store_id = ' . $storeid;
		}

		$sql .= " AND i.parent = 'com_quick2cart'";

		$db->setQuery($sql);
		$result = $db->loadResult();

		return $result;
	}
}
