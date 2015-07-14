<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class productHelper
{
	/**
	 * This function gives all featured prod
	 * @param $store_id  INTEGER -- Store id from which we hv to select featured prod
	 * @param $prod_cat INTEGET -- category from which we hv to select
	 *
	 * @return ARRAY - array if all products details
	 * */
	function getAllFeturedProducts($store_id = '', $prod_cat = '', $limit = '4')
	{
		$where   = array();
		$where[] = ' i.featured=1 ';
		$where[] = ' i.state=1 ';

		if (!empty($store_id))
		{
			$where[] = ' i.`store_id`=\'' . $store_id . '\' ';
		}
		if (!empty($prod_cat))
		{
			$where[] = ' i.`category`=\'' . $prod_cat . '\' ';
		}
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$db    = JFactory::getDBO();
		$query = "SELECT i.item_id,i.name,i.images,i.`store_id`,i.slab,i.`min_quantity`,i.`max_quantity`,i.`parent`,i.`product_id`,i.stock FROM #__kart_items  AS i" . $where . ' LIMIT 0 , ' . $limit;
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}

	/**
	 * This function gives all featured prod
	 * @param $store_id  INTEGER -- Store id from which we hv to select
	 * @param $prod_cat INTEGET -- category from which we hv to select
	 *
	 * @return ARRAY - array if all products details
	 * */
	function getTopSellerProducts($store_id = '', $prod_cat = '', $limit = 5)
	{
		$where = array();

		//  Payment completed, and shipped are considered
		$where[] = " o.`status` IN('C', 'S')";

		if (!empty($store_id))
		{
			$where[] = ' i.store_id=\'' . $store_id . '\' ';
		}

		if (!empty($prod_cat))
		{
			$where[] = ' i.`category`=\'' . $prod_cat . '\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$db    = JFactory::getDBO();
		$query = 'SELECT i.item_id,i.name,i.images,i.`store_id`,i.slab,i.`min_quantity`,i.`max_quantity`,i.`featured`,i.`parent`,i.`product_id` , SUM( oi.`product_quantity` ) AS qty,i.stock
		FROM  `#__kart_order_item` AS oi
		INNER JOIN  `#__kart_items` AS i ON oi.`item_id` = i.`item_id`
		INNER JOIN  `#__kart_orders` AS o ON oi.`order_id` = o.`id`
		' . $where . '
		GROUP BY  `item_id`
		ORDER BY qty DESC
		LIMIT 0 , ' . $limit;

		$db->setQuery($query);

		return $data = $db->loadAssocList();
	}

	/**
	 * This function gives all featured prod
	 * @return ARRAY - array if all products details
	 * */

	function getTopSellerStore($limit = '')
	{

		if (empty($limit))
			$limit = 2;
		$where   = array();
		$where[] = " o.`status` =  'C'";
		$where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$db      = JFactory::getDBO();

		$query = 'SELECT SUM( oi.`product_quantity` ) AS qty, s.id,s.store_avatar,s.title, s.live
		FROM `#__kart_order_item` AS oi
		INNER JOIN `#__kart_store` AS s ON oi.`store_id` = s.`id`
		INNER JOIN `#__kart_orders` AS o ON oi.`order_id` = o.`id`
		 ' . $where . "
		GROUP BY s.`id`
		ORDER BY qty DESC
		LIMIT 0 , $limit";
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}


	/**
	 * This function Products from same category
	 * @param $cat  INTEGER -- category id from which we hv to select
	 * @param $item_id  INTEGER -- item_id  from which WHICH IS NOT TO SELECT
	 * */

	function getSimilarProdFromCat($cat, $item_id)
	{
		$where = array();
		if (empty($cat))
		{
			return;
		}
		if (!empty($item_id))
		{
			$where[] = ' `item_id`!=' . $item_id;
		}
		$where[] = ' `category`=\'' . $cat . '\' ';
		$where[] = " `state` =  '1'";


		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$db    = JFactory::getDBO();
		$query = "SELECT i.item_id,i.name,i.images,i.`store_id`,i.slab,i.`min_quantity`,i.`max_quantity`,i.`featured`,i.`parent`,i.`product_id`,i.`state`,i.stock FROM #__kart_items as i " . $where . ' LIMIT 0 , 5';
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}

	/**
	 * This function Products from same store
	 *  @param $store_id  INTEGER -- store_id  from which we hv to select
	 *  @param $item_id  INTEGER -- item_id  from which WHICH IS NOT TO SELECT
	 * */

	function prodFromSameStore($store_id, $item_id)
	{
		$where = array();

		if (empty($store_id))
		{
			return;
		}
		$where[] = " `state` =  '1'";
		$where[] = ' `store_id`=\'' . $store_id . '\' ';
		if (!empty($item_id))
		{
			$where[] = ' `item_id`!=' . $item_id;
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$db    = JFactory::getDBO();
		$query = "SELECT i.item_id,i.name,i.images,i.`store_id`,i.`min_quantity`,i.slab,i.`max_quantity`,i.`featured`,i.`parent`,i.`product_id`,i.`state`,i.`stock`  FROM #__kart_items as i " . $where . ' LIMIT 0 ,2';
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}


	/**
	 * This function People who bought this also bought
	 *  @param $store_id  INTEGER -- store_id  from which we hv to select
	 *  @param $item_id  INTEGER -- item_id  from which WHICH IS NOT TO SELECT
	 * */

	/*SELECT `order_id` , count( `order_item_id` ) AS qtc
	FROM #__kart_order_item
	GROUP BY `order_id`
	LIMIT 0 , 30*/
	function peopleAlsoBought($item_id, $datacount = 2)
	{
		$where = array();

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$db    = JFactory::getDBO();
		$query = 'SELECT oi.`order_id` FROM `#__kart_order_item` AS oi
			WHERE oi.`item_id` =' . $item_id;
		$db->setQuery($query);
		$data     = $db->loadColumn();
		//   MAINTAIN LIST OF item_id
		$item_rec = array();

		//  MAINTAIN item_id details
		$item_details = array();
		foreach ($data as $orderid)
		{
			$query = 'SELECT i.item_id,i.name,i.images ,i.`store_id`,i.slab,i.`min_quantity`,i.`max_quantity`,i.`parent`,i.`product_id` ,i.`state`,i.`featured`,i.`stock`
			FROM `#__kart_order_item` AS oi
			INNER JOIN `#__kart_items` AS i
			ON oi.item_id=i.item_id
			WHERE oi.order_id=' . $orderid . ' AND oi.`item_id` !=' . $item_id . ' AND i.state=1';
			$db->setQuery($query);
			$otherProducts = $db->loadAssocList();

			//  If fetched data (order contain) more than one prod
			if (!empty($otherProducts))
			{
				foreach ($otherProducts as $detail)
				{
					// 		IF NOT PRESENT THEN ADD
					if (!in_array($detail['item_id'], $item_rec))
					{
						$item_rec[]     = $detail['item_id'];
						$item_details[] = $detail;
					}
				}
				//  CHECKING COUNT TO  SHOW
				if (count($item_details) >= $datacount)
				{
					break;
				}
			}
		}

		return $item_details;

	}

	/**
	 * This function gives Pepole who bought this prouduct
	 * @return ARRAY - array if all products details
	 * */
	function peopleWhoBought($item_id, $datacount = 2)
	{
		$db               = JFactory::getDBO();
		$params           = JComponentHelper::getParams('com_quick2cart');
		$who_bought_limit = $params->get('who_bought_limit', 2);
		$query            = 'SELECT o.`user_info_id` as id ,o.`email`,o.name FROM `#__kart_order_item` as oi ,`#__kart_orders` as o WHERE oi.`order_id`= o.id AND oi.`status`= "c" AND oi.`item_id` =' . $item_id . ' ORDER BY o.`id` DESC LIMIT 0 ,' . $who_bought_limit;
		$db->setQuery($query);
		$data = $db->loadObjectlist();
		return $data;

	}

	/**
	 * This function gives latest store
	 * @return ARRAY - array if all products details
	 * */
	function getLatestStore($limit = 2)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT  s.id,s.store_avatar,s.title, s.live from `#__kart_store` as s
		ORDER BY `cdate` DESC
		LIMIT 0 , ' . $limit;
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}

	function getNewlyAdded_products($limit = '2')
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT i.item_id,i.name,i.images,i.`store_id`,i.slab,i.`min_quantity`,i.`max_quantity`,i.`featured`,i.`parent`,i.`product_id`  ,i.`stock` from `#__kart_items` AS i
		WHERE i.state=1
		ORDER BY `cdate` DESC
		LIMIT 0 , ' . $limit;
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}

	function getRecentlyBoughtproducts($limit = '2')
	{
		$db    = JFactory::getDBO();
		/*print 	$query = 'SELECT i.item_id,i.name,i.images,i.`store_id`,i.slab,i.`min_quantity`,i.`max_quantity`,i.`parent`,i.`product_id`
		FROM `#__kart_items` AS i
		INNER JOIN `#__kart_order_item` AS oi
		ON oi.item_id=i.item_id
		WHERE i.state=1
		ORDER BY oi.`cdate` DESC
		LIMIT 0 , '.$limit;*/
		$query = 'SELECT DISTINCT(oi.item_id),i.name,i.images,i.featured,i.`stock`
								FROM `#__kart_order_item` AS oi , `#__kart_items` AS i
								WHERE i.state=1
								 AND   oi.item_id=i.item_id
								GROUP BY oi.item_id
								ORDER BY oi.`cdate` DESC
								LIMIT 0 , ' . $limit;

		$db->setQuery($query);
		return $db->loadAssocList();
	}

	function getUserRecentlyBoughtproducts($uid, $limit = '2')
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT DISTINCT(oi.item_id),i.name,i.images,i.featured, i.stock
					FROM `#__kart_order_item` AS oi , `#__kart_items` AS i, `#__kart_orders` as o
					WHERE i.state = 1
					AND oi.order_id = o.id
					AND oi.item_id = i.item_id
					AND o.user_info_id = "' . $uid . '"
					GROUP BY oi.item_id
					ORDER BY oi.order_id DESC
					LIMIT 0 , ' . $limit;
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	/**
	 * This function gives user stores
	 * userid  INT user id
	 * limit INT number of stores
	 * @return ARRAY - array
	 * */
	function getUserStores($userid, $limit = '2')
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT id,title,store_avatar
		From `#__kart_store`
		WHERE owner=' . (int) $userid . '
		ORDER BY `cdate` DESC
		LIMIT 0 , ' . (int) $limit;
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}
	/**
	 * This function gives products of user id
	 * userid  INT user id
	 * limit INT number of products
	 * @return ARRAY - array
	 * */
	function getUserProducts($userid, $limit = '2', $client = "com_quick2cart")
	{
		//  @TODO : SHOW MODULES INDEPENDANT OF ccks.
		$db    = JFactory::getDBO();
		$query = 'SELECT i.item_id,name,i.images,i.store_id,i.state,i.featured,i.`stock`
		From `#__kart_items` as i
		INNER JOIN `#__kart_store` AS s ON i.`store_id` = s.`id`
		WHERE s.owner=' . (int) $userid . '
		ORDER BY i.cdate DESC
		LIMIT 0 , ' . (int) $limit;
		$db->setQuery($query);
		return $data = $db->loadAssocList();
	}

	/**
	 * This function attibute optios with all  prices
	 * @param :: integer attributeOptions_id
	 * @return :
	 * */
	function delWholeAttributeOptionPrices($itemattributeoption_id)
	{
		if (!empty($itemattributeoption_id))
		{
			$db    = JFactory::getDBO();
			$query = "DELETE FROM `#__kart_option_currency`  WHERE itemattributeoption_id=" . (int) $itemattributeoption_id;
			$db->setQuery($query);
			return $db->execute();
		}
	}

	/**
	 * This function delete option with all  prices
	 * @param :: integer itemattributeoption_id
	 * @return :
	 * */
	function delWholeAttributeOption($itemattributeoption_id)
	{
		$db = JFactory::getDBO();
		if (!empty($itemattributeoption_id))
		{
			$productHelper             = new productHelper;
			// LOAD ATTRIBUTE MODEL FILE
			$quick2cartModelAttributes = $productHelper->loadAttribModel();
			//  DELETE OPTION PRICES
			$productHelper->delWholeAttributeOptionPrices($itemattributeoption_id);
			//  DELETE OPTION
			$query = "DELETE FROM #__kart_itemattributeoptions  WHERE itemattributeoption_id=" . (int) $itemattributeoption_id;
			$db->setQuery($query);
			return $db->execute();
		}
	}

	/**
	 * This function delete whole attibute
	 * @param :: integer attributeOptions_id
	 * @return :
	 **/
	function delWholeAttribute($itemattribute_id)
	{
		$db                        = JFactory::getDBO();
		$productHelper             = new productHelper;
		//  LOAD ATTRIBUTE MODEL FILE
		$quick2cartModelAttributes = $productHelper->loadAttribModel();
		//  GET OPTIONS
		$options                   = $quick2cartModelAttributes->getAttributeoption($itemattribute_id);
		foreach ($options as $op)
		{
			//  DEL OPTIONS
			$productHelper->delWholeAttributeOption($op->itemattributeoption_id);
		}

		//  DEL ATTRIBUTE
		$query = "DELETE FROM `#__kart_itemattributes`  WHERE itemattribute_id=" . (int) $itemattribute_id;
		$db->setQuery($query);
		return $db->execute();
	}

	/**
	 * This function delete whole product (its attribute, attribute option and prices )
	 * @param :: integer item_id
	 * @return :
	 * */
	function deleteWholeProduct($item_id)
	{
		$status = $this->isAllowedToDelProduct($item_id);

		if ($status === false)
		{
			return false;
		}
		$productHelper = new productHelper;

		//  LOAD ATTRIBUTE MODEL FILE
		$quick2cartModelAttributes = $productHelper->loadAttribModel();

		//  TASK1: FETCH ALL ATTRIBUTE ID
		$attributes = $quick2cartModelAttributes->getItemAttributes($item_id);

		if (!empty($attributes))
		{
			//  IF ATTRIBURE
			foreach ($attributes as $att)
			{
				$productHelper->delWholeAttribute($att->itemattribute_id);
			}
		}
		//  AT LAST, DELETE PRODUCT
		$productHelper->delProdBasePrices($item_id);
		return $status = $quick2cartModelAttributes->deleteItem($item_id);
	}
	/**
	 * This function attibute optios with all  prices
	 * @param :: integer attributeOptions_id
	 * @return :
	 * */
	function delProdBasePrices($item_id)
	{
		if (!empty($item_id))
		{
			$db    = JFactory::getDBO();
			$query = "DELETE FROM `#__kart_base_currency`  WHERE `item_id`=" . (int) $item_id;
			$db->setQuery($query);
			return $db->execute();
		}
	}
	function loadAttribModel()
	{
		if (!class_exists('quick2cartModelAttributes'))
		{

			JLoader::register('quick2cartModelAttributes', JPATH_SITE . '/components' . '/com_quick2cart' . '/models' . '/attributes.php');
			JLoader::load('quick2cartModelAttributes');
		}
		return $quick2cartModelAttributes = new quick2cartModelAttributes();
	}

	/**
	 * This function deleted product extra images on EDIT OR DELETE product
	 * @param :: $item_id id of product
	 * $image_path :: JSON FORMATTED image paths
	 *
	 * */
	function deleteNotReqProdImages($item_id, $image_path)
	{
		if (!empty($item_id))
		{
			$db    = JFactory::getDBO();
			$query = 'SELECT `images` from `#__kart_items` where `item_id`=' . $item_id;
			$db->setQuery($query);
			$dbimages      = $db->loadResult();
			$productHelper = new productHelper;

			//  IF NOT EMPTY
			if (!empty($dbimages))
			{
				$dbImg = array();
				$dbImg = json_decode($dbimages, true);

				$image_path = json_decode($image_path);
				if (empty($image_path))
				{
					//  DELETE ALL IMAGES IF IIMAGE_PATH PARAM IS EMPTY
					$productHelper = new productHelper;
					$productHelper->deletImg($dbImg);
				}
				else
				{
					//  FIND OUT EXTRA IMAGE
					$delImg = array();
					foreach ($dbImg as $img)
					{

						if (is_array($image_path) && !in_array($img, $image_path))
						{
							$delImg[]=$img;
						}

						$productHelper->deletImg($delImg);
					}
				}
			}
		}
	}

	function deletImg($imgarray)
	{
		$productHelper = new productHelper;
		require_once(JPATH_SITE . DS . 'components/com_quick2cart/helpers/media.php');
		$media = new qtc_mediaHelper();
		if (!empty($imgarray))
		{
			foreach ($imgarray as $img)
			{
				$file_name_without_extension = $media->get_media_file_name_without_extension($img);
				$media_extension             = $media->get_media_extension($img);
				$imgpath_withoutExtention    = JPATH_SITE . DS . 'images/quick2cart' . DS . $file_name_without_extension;
				//  DELETE ORIGINAL IMG
				$imgfilepath                 = JPATH_SITE . DS . 'images/quick2cart' . DS . $img;
				$productHelper->deleteFile($imgfilepath);

				//  DELETE SMALL, M ,L IMAGES
				$imgfilepath = $imgpath_withoutExtention . '_S.' . $media_extension;
				$productHelper->deleteFile($imgfilepath);
				$imgfilepath = $imgpath_withoutExtention . '_M.' . $media_extension;
				$productHelper->deleteFile($imgfilepath);
				$imgfilepath = $imgpath_withoutExtention . '_L.' . $media_extension;
				$productHelper->deleteFile($imgfilepath);

			}
		}
	}
	/** THIS FUNCTION DELETE any file
	 * @param ::dfinepath path of file
	 * */
	function deleteFile($dfinepath)
	{
		if (JFile::exists($dfinepath))
		{
			$status2 = JFile::delete($dfinepath);
		}
	}
	/*
	 * This function return attribute list of perticular item
	 * */

	function getAttributeList($item_id)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT `itemattribute_id` FROM `#__kart_itemattributes`  WHERE `item_id`=" . (int) $item_id;
		$db->setQuery($query);
		return $db->loadColumn();
	}
	function getAttributeOptionsList($att_id)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT `itemattributeoption_id` FROM `#__kart_itemattributeoptions`  WHERE `itemattribute_id`=" . (int) $att_id;
		$db->setQuery($query);
		return $db->loadColumn();
	}

	/*	THIS FUNCTION DELETE db attributes which is not present used
	 * attIdList :: post att_ids
	 * item_id :: product id
	 * */
	function deleteExtaAttribute($item_id, $attIdList)
	{
		$productHelper = new productHelper;
		$DbAtt_list    = $productHelper->getAttributeList($item_id);
		//  ITEM HAS ATTRIBUTES
		if (!empty($DbAtt_list))
		{
			if (empty($attIdList))
			//  IF REMOVED ALL ATTRI THEN DEL ALL ATTRIB
			{
				foreach ($DbAtt_list as $dbid)
				{
					$productHelper->delWholeAttribute($dbid);
				}
			}
			else
			{
				foreach ($DbAtt_list as $dbid)
				{
					if (!in_array($dbid, $attIdList))
					{
						$productHelper->delWholeAttribute($dbid);
					}
				}
			}
		}
	}
	/*	THIS FUNCTION DELETE db attributes OPTION which is not presently we not going to used/ delted
	 *
	 */
	function deleteExtraAttributeOptions($att_id, $optionids)
	{
		$productHelper    = new productHelper;
		$DbAttoption_list = $productHelper->getAttributeOptionsList($att_id);

		//  ATTRIBUTES HS OPTIONS
		if (!empty($DbAttoption_list))
		{
			//  CURRENTLY NO OPIONS
			if (empty($optionids))
			//  IF REMOVED ALL OPTION THEN DELETE WHOLE ATTRIBUTE
			{
				$productHelper->delWholeAttribute($att_id);
			}
			else
			{

				foreach ($DbAttoption_list as $dbid)
				{
					if (!in_array($dbid, $optionids))
					{
						$productHelper->delWholeAttributeOption($dbid);
					}
				}
			}
		}
	}

	/*
	function pushToEasySocialActivity($actor_id,$act_type='',$act_subtype='',$act_description='',$act_link='',$act_title='',$act_access='')
	{
	require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
	$myUser = Foundry::user( $actor_id );
	$stream = Foundry::stream();
	$template = $stream->getTemplate();
	$template->setActor( $actor_id, SOCIAL_TYPE_USER );
	$template->setContext( $actor_id, SOCIAL_TYPE_USERS );
	$template->setVerb( 'invite' );
	$template->setType( SOCIAL_STREAM_DISPLAY_MINI );

	$userProfileLink = '<a href="'. $myUser->getPermalink() .'">' . $myUser->getName() . '</a>';
	$title      = ($userProfileLink." ".$act_description);

	$template->setTitle( $title );
	$template->setAggregate( false );

	$template->setPublicStream( 'core.view' );
	$stream->add( $template );
	return true;
	}
	*/
	function getAttributeName($itemattribute_id)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT `itemattribute_name` FROM #__kart_itemattributes WHERE itemattribute_id=" . $itemattribute_id;
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	/*
	 * @param item_id
	 * */
	function getProdPriceWithDefltAttributePrice($item_id)
	{
		$return               = array();
		$return['itemdetail'] = array();
		$helperobj            = new comquick2cartHelper;
		$curr                 = $helperobj->getCurrencySession();

		$path = JPATH_SITE . DS . 'components/com_quick2cart/models/attributes.php';
		if (!class_exists('quick2cartModelAttributes'))
		{
			JLoader::register('quick2cartModelAttributes', $path);
			JLoader::load('quick2cartModelAttributes');
		}
		$quick2cartModelAttributes = new quick2cartModelAttributes();

		//  GETTING ITEM_DETAILS
		$item_details = $quick2cartModelAttributes->getCurrenciesvalue('0', $curr, 'com_quick2cart', $item_id);
		if (!empty($item_details[0]))
		{
			$return['itemdetail'] = $item_details[0];
		}

		//  GETTING ATTRIBUTE DETAILS
		$attribure_option_ids = array();
		$tot_att_price        = 0;
		$allAttr              = $quick2cartModelAttributes->getItemAttributes($item_id);
		if (!empty($allAttr))
		{
			foreach ($allAttr as $attr) //  ATTRIBUTES
			{
				//  if cumpulsory then only consider price ( i.e attribute_compulsary=0)
				if (!empty($attr->attribute_compulsary))
				{
					$attr_details = $helperobj->getAttributeDetails($attr->itemattribute_id);
					foreach ($attr_details as $options)
					{
						if ($options->ordering == 1 && !empty($options->$curr)) //  consider first order option
						{
							$attribure_option_ids[] = $options->itemattributeoption_id;
							$tot_att_price += $options->$curr;
							break;
						}
						//  consider prefix also
					}
				}
			}
		}
		$attrDetails                  = array();
		$attrDetails['tot_att_price'] = $tot_att_price;
		$attrDetails['attrOptionIds'] = $attribure_option_ids;

		$return['attrDetail'] = $attrDetails;
		return $return;
	}
	/*
	 * $data['itemattribute_id'] = $attribute->itemattribute_id;
	 $data['fieldType'] = $attribute->attributeFieldType;
	 $data['parent'] = $parent;
	 $data['product_id'] = $product_id;
	 $data['attribute_compulsary'] = $attribute->attribute_compulsary;
	 * */
	function getAttrFieldTypeHtml($data)
	{
		$parent = '';

		if (isset($data['parent']))
		{
			$parent = $data['parent'];
		}

		$product_id          = $data['product_id'];
		$comquick2cartHelper = new comquick2cartHelper;

		// $atri_options = comquick2cartHelper::getAttributeOption($attribute->itemattribute_id);//  commented during multicurrency
		$atri_options = $comquick2cartHelper->getAttributeOptionCurrPrice($data['itemattribute_id']);
		$select_opt   = array();
		$userData     = array();
		$userData[]   = 'Textbox';

		if (!$data['attribute_compulsary'] && !in_array($data['fieldType'], $userData))
		//  if checked
		{
			$select_opt[] = JHtml::_('select.option', "", "");
		}

		$returnHtml = '';

		foreach ($atri_options as $atri_option)
		{
			$attOp_price = (int) $atri_option->itemattributeoption_currency_price;

			// IF 0 ATT PRIVE THEN DONT ADD +0 USD
			if (!empty($attOp_price))
			{
				$priceText = $comquick2cartHelper->getFromattedPrice($atri_option->itemattributeoption_currency_price, NULL, 0);
				$opt_str   = $atri_option->itemattributeoption_name . ": " . $atri_option->itemattributeoption_prefix . " " . $priceText;
			}
			else
			{
				//  If no price than dont append like  +00.0 USD
				$opt_str = $atri_option->itemattributeoption_name;
			}

			//  Generate op according to datatype

			if (in_array($data['fieldType'], $userData))
			{
				$returnHtml = "<input type='text' name='qtcUserField_" . $atri_option->itemattributeoption_id . "' class='input input-small " . $parent . '-' . $product_id . '_UserField' . "' >";
			}
			else
			{
				/* Amol change*/
				$default_value = '';

				if (isset($data['default_value']))
				{
					$default_value = $data['default_value'];
				}

				$field_name = 'attri_option';

				if (isset($data['field_name']))
				{
					$field_name = $data['field_name'];
				}

				//  user data
				$select_opt[] = JHtml::_('select.option', $atri_option->itemattributeoption_id, $opt_str);
				$returnHtml   = JHtml::_('select.genericlist', $select_opt, $field_name, "  class='q2c_AttoptionsMaxWidth {$parent}-{$product_id}_options'", 'value', 'text', $default_value, false);
			}
		}

		return $returnHtml;
		/*    if (!in_array($data['fieldType'],$userData)) {
		echo JHtml::_('select.genericlist', $select_opt, 'attri_option', "  class='span2 {$parent}-{$product_id}_options'", 'value', 'text', '',false);
		}*/

	}
	function getAttributeFieldsOption()
	{
		$fields[] = JHtml::_('select.option', 'Select', JText::_('QTC_ADDATTRI_SELECT_FIELD'));
		$fields[] = JHtml::_('select.option', 'Textbox', JText::_('QTC_ADDATTRI_TEXT_FIELD'));
		// $fields[] = JHtml::_('select.option','Checkbox', JText::_('QTC_ADDATTRI_CHECKBOX_FIELD'));
		return $fields;
	}
	/**
	 * This function save product media details
	 * @param $media_detail array  - Media detail
	 * */
	function saveProdMediaDetails($media_detail, $item_id, $deleteOldRec = 1)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$db = JFactory::getDBO();

		//   get privious  file ids
		$query = 'SELECT  `file_id` FROM `#__kart_itemfiles` where `item_id`=' . $item_id;
		$db->setQuery($query);
		$oldMedia = $db->loadColumn();

		$comquick2cartHelper = new comquick2cartHelper;
		$params              = JComponentHelper::getParams('com_quick2cart');
		$destinationPath     = $params->get('eProdUploadDir', 'media/com_quick2cart/productfiles');
		$eProdExpFormat      = $params->get('eProdExpFormat', 'epMonthExp');
		$eProdUExpiryMode    = $params->get('eProdUExpiryMode', 'epMaxDownload'); //  down count/ date limit / both

		foreach ($media_detail as $media)
		{
			if (empty($media['mediaFilePath']))
			{
				continue;
			}

			$file                    = new stdClass();
			$file->file_id           = $media['file_id'];
			$file->file_display_name = $media['name'];
			$file->item_id           = $item_id;
			$file->state             = isset($media['status']) ? true : false;
			$file->purchase_required = isset($media['purchaseReq']) ? true : false;

			//  use download count as well as data expiration mode
			// if ($eProdUExpiryMode == "epboth")
			{
				// @TODO : if date expiry mode, keep this value as default according to config
				$file->download_limit = !empty($media['downCount']) ? $media['downCount'] : -1;
				$file->expiry_mode    = ($eProdExpFormat == 'epMonthExp') ? 1 : 0;

				// @TODO : if download limit mode, keep this value as default according to config
				$file->expiry_in = !empty($media['expirary']) ? $media['expirary'] : 2;
			}
			/*elseif ($eProdUExpiryMode == "epDateExpiry")
			{
			//  use data expiration ( not download count )
			//  null - field not considered, -1 - unlimited , other for req count
			// 	$file->download_limit = -1;  //  for edit and changed config  ( NULL DOESN'T INSERT IN DB BZ DATATYPE MISMATCH)
			//  IN DAY  OR MONTHS
			$file->expiry_mode = ($eProdExpFormat == 'epMonthExp') ? 1:0;
			$file->expiry_in = $media['expirary'];
			}
			elseif ($eProdUExpiryMode == "epMaxDownload")
			{
			$file->download_limit = $media['downCount'];
			// $file->expiry_mode = -1; //  -1 for dont consider field
			// $file->expiry_in = 0;
			}*/

			if (empty($media['file_id']))
			{
				$file->cdate = date('Y-m-d');
			}
			else
			{
				//  if media editing then
				$medKey = array_search($media['file_id'], $oldMedia);
				if (isset($medKey))
				{
					//  present data (not deleted media file)
					unset($oldMedia[$medKey]);
				}
			}
			$file->mdate = date('Y-m-d H:i:s');

			//  for new file, check for file save path
			if (empty($media['file_id']) && !empty($media['mediaFilePath']))
			{
				//  get temparary uploaded path
				$tempSource   = JPATH_SITE . '/tmp/' . $media['mediaFilePath'];
				$uploadedFile = explode(DS, $tempSource);

				$filNameIndex = count($uploadedFile) - 1;
				$fileName     = $uploadedFile[$filNameIndex];

				//  new destination path
				$newDestination = JPATH_ROOT . DS . $destinationPath;

				//  if folder path not exist
				if (!JFolder::exists($newDestination))
				{
					$status = JFolder::create($newDestination);
				}

				//  move a file from temporary location to newlocation
				$newDestination = $newDestination . DS . $fileName;
				$uploadSuccess  = JFile::move($tempSource, $newDestination);
			}

			$file->filePath = $media['mediaFilePath'];
			$action         = empty($media['file_id']) ? 'insertObject' : 'updateObject';

			if (!$db->$action('#__kart_itemfiles', $file, 'file_id'))
			{
				echo $db->stderr();
				return 0;
			}
		}
		$productHelper = new productHelper;
		if ($deleteOldRec == 1)
		{
			$productHelper->deleteProductMediaFile($oldMedia);
		}

		return 1;

	}
	/**
	 * 	This function delete media file
	 * 	@praram array media id's and filePath array
	 * */
	function deleteProductMediaFile($mediaIds)
	{
		$productHelper = new productHelper;
		//  remove empty elements
		// $mediaIds = array_filter($mediaIds,'strlen');
		// $ids = implode(',',$mediaIds);
		$db            = JFactory::getDBO();
		$params        = JComponentHelper::getParams('com_quick2cart');
		$delFromDir    = JPATH_ROOT . DS . $params->get('eProdUploadDir', 'media/com_quick2cart/productfiles');

		foreach ($mediaIds as $media)
		{
			//  get file name
			$query = 'SELECT  `filePath` FROM `#__kart_itemfiles` where `file_id`=' . $media;
			$db->setQuery($query);
			$filename = $db->loadResult();

			//  delete file record
			$query = 'DELETE FROM `#__kart_itemfiles`  WHERE `file_id` = ' . $media;
			$db->setQuery($query);
			$status = $db->execute();

			//  if file rec is deleted and file exist then delete physical media file
			if ($status && !empty($filename))
			{
				$productHelper->deleteFile($delFromDir . DS . $filename);
			}
		}
		return 1;
	}

	/** This function give media files detail
	 *	@param $item_id integer if provoide item's media files
	 * 	@param $file_id give media detail for given file
	 * 	@param $state state = '' means fetched published as well as unpublished recorder
	 *
	 * 	@return array media detail array
	 * */
	function getMediaDetail($item_id = 0, $file_id = '', $state = '')
	{
		$db    = JFactory::getDBO();
		$where = array();

		//  state = '' means published as well as unpublished recorder
		if ($state != '')
		{
			$where[] = " `state`=" . $state;
		}

		if (!empty($item_id))
		{
			$where[] = " `item_id`=" . $item_id;
		}

		if ($file_id != '')
		{
			$where[] = " `file_id`=" . $file_id;
		}
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query = "SELECT `file_id`,`file_display_name`,`item_id`,`purchase_required`,`state`,`download_limit`,`filePath`,`expiry_mode`,`expiry_in`,`cdate` FROM `#__kart_itemfiles`" . $where;
		$db->setQuery($query);
		return $res = $db->loadAssocList();

	}
	/** This function gives physical file name and display name from db */
	function getFileName($file_id)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT `filePath`,`file_display_name` FROM `#__kart_itemfiles` WHERE `file_id`=' . $file_id;
		$db->setQuery($query);
		return $db->loadAssoc();
	}

	function getFilePathToDownload($file_id)
	{
		$productHelper = new productHelper;

		//  GET FILE NAME
		$fileDetail = $productHelper->getFileName($file_id);
		$filePath   = $fileDetail['filePath'];

		$params     = JComponentHelper::getParams('com_quick2cart');
		$folderPath = $params->get('eProdUploadDir', 'media/com_quick2cart/productfiles');
		return JPATH_SITE . '/' . $folderPath . '/' . $filePath;

	}

	/** This function show download link of product
	 * $linkData = array();
	 $linkData['linkName'] = file_display_name'
	 $linkData['href'] = '#';
	 $linkData['event'] = 'onClick';
	 $linkData['functionName'] = 'qtcDownFile';
	 $linkData['fnParam'] = '';
	 * */
	function showMediaDownloadLink($linkData)
	{
		$linkData['href'];
		$event = $linkData['event'] . '= ' . $linkData['functionName'] . '(' . $linkData['fnParam'] . ')';
		$link  = "<a href=\"" . $linkData['href'] . "\" $event target='_blank' title=\"" . JText::_("COM_QUICK2CART_PROD_PG_DOWNLOADLINK_TITLE") . "\">" . ucfirst($linkData['linkName']) . "</a> ";
		return $link;
	}

	/**
	 * This function provide href field contains. Specified where to go(which task), onclick of download link
	 *
	 * @param   INT  $item_id integer    item_id of prod
	 * @param   INT  $purchase_required  integer if set if 1, return all files else only free file are
	 *
	 * @return  VOID
	 */
	function getMediaDownloadLinkHref($fileid, $extraUrlPrams = '')
	{
		$helperobj = new comquick2cartHelper;

		//  If url extra param is present
		if (!empty($extraUrlPrams))
		{
			$extraUrlPrams = '&' . $extraUrlPrams;
		}

		$storecp_Itemid = $helperobj->getitemid('index.php?option=com_quick2cart&view=downloads');
		$link           = JUri::root() . substr(JRoute::_('index.php?option=com_quick2cart&task=product.downStart&fid=' .
		 $fileid . $extraUrlPrams . '&Itemid=' . $storecp_Itemid), strlen(JUri::base(true)) + 1);

		return $link;
	}

	/**
	 * This function gives  product files
	 *
	 * @param   INT  $item_id            item_id of prod
	 * @param   INT  $purchase_required  if set if 1, return all files else only free file are
	 *
	 * @return  VOID
	 */
	public function getProdmediaFiles($item_id, $purchase_required = 0)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT `file_id`,`file_display_name`,`filePath`
				FROM `#__kart_itemfiles` WHERE `item_id`=' . $item_id . ' AND `purchase_required`=' . $purchase_required;
		$db->setQuery($query);

		return $res = $db->loadAssocList();
	}

	/**
	 * On order confirm, add product media file in orderitemFIlesTable
	 *
	 * @param   INT  $order_id  order_id
	 *
	 * @return  VOID
	 */
	public function addEntryInOrderItemFiles($order_id)
	{
		$productHelper       = new productHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		$params = JComponentHelper::getParams('com_quick2cart');

		$eProdExpFormat   = $params->get('eProdExpFormat', 'epMonthExp');

		// Down count/ date limit / both
		$eProdUExpiryMode = $params->get('eProdUExpiryMode', 'epMaxDownload');

		$db = JFactory::getDBO();
		$q  = "SELECT `item_id`,`order_item_id`,`product_quantity`
				FROM  `#__kart_order_item`
				WHERE `order_id` =" . (int) $order_id;
		$db->setQuery($q);
		$result = $db->loadAssocList();

		foreach ($result as $res)
		{
			$item_id     = $res['item_id'];
			$mediaDetail = $productHelper->getMediaDetail($item_id);

			//  Copy all media
			foreach ($mediaDetail as $media)
			{
				$pfile                  = new stdClass;
				$pfile->product_file_id = $media['file_id'];
				$pfile->order_item_id   = $res['order_item_id'];
				$pfile->expirary_date   = '';
				$que                    = 'SELECT of.id FROM `#__kart_orderItemFiles` AS of
					where of.order_item_id=' . $pfile->order_item_id . ' AND of.`product_file_id`=' . $pfile->product_file_id . ' ORDER BY of.id';
				$db->setQuery($que);
				$fileid = $db->loadResult();

				if (!empty($fileid))
				{
					//  Actually will not come inside. still for worse case
					$action    = "updateObject";
					$pfile->id = $fileid;
				}
				else
				{
					$action       = "insertObject";
					$pfile->cdate = date('Y-m-d H:i:s');
				}

				//  if purchase require not req
				if ($media['purchase_required'] == 0)
				{
					$pfile->download_limit = -1;
				}
				else
				{
					//  DATE EXPIRATION
					if ($eProdUExpiryMode == 'epDateExpiry' || $eProdUExpiryMode == 'epboth')
					{
						//  expirary in months
						if ($media['expiry_mode'] == 1)
						{
							//  months
							$exdate = $comquick2cartHelper->add_date($pfile->cdate, 0, $media['expiry_in']);
						}
						else
						{
							//  in days
							$exdate = $comquick2cartHelper->add_date($pfile->cdate, $media['expiry_in']);
						}

						$pfile->expirary_date = $exdate;
					}

					if ($eProdUExpiryMode == 'epMaxDownload' || $eProdUExpiryMode == 'epboth')
					{
						if ($media['download_limit'] > 0)
						{
							//  not for unlimited
							$pfile->download_limit = $media['download_limit'] * $res['product_quantity'];
						}
						else
						{
							$pfile->download_limit = $media['download_limit'];
						}
					}
				}

				$pfile->expiration_mode = $eProdUExpiryMode;

				if (!$db->$action('#__kart_orderItemFiles', $pfile, 'id'))
				{
					echo JText::_("COM_QUICK2CART_ERROR_WHILE_ASSIGNING_MEIDA_FILE_TO_ORDER") . $db->stderr();

					return false;
				}
			}
		}
	}

	/* Code added by sanjivani*/

	/**
	 * After Order Confirm Add point to buyer
	 * count of order item X point allocate to action perform
	 *
	 * @param   INT  $oid  oid
	 *
	 * @return  VOID
	 */
	public function addPoint($oid)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT sum( `product_quantity` )  as count FROM `#__kart_order_item` WHERE `order_id`="' . $oid . '"';
		$db->setQuery($query);
		$count_item = $db->loadResult();

		//  Add point to Community extension when product added into Quick2cart
		$params         = JComponentHelper::getParams('com_quick2cart');
		$point_system   = $params->get('point_system');
		$integrate_with = $params->get('integrate_with');
		$user           = JFactory::getUser();

		if ($point_system === '1')
		{
			//  According to integration create social lib class obj.
			$comquick2cartHelper  = new comquick2cartHelper;
			$libclass             = $comquick2cartHelper->getQtcSocialLibObj();
			$options['extension'] = 'com_quick2cart';

			if ($integrate_with === 'JomSocial')
			{
				$options['command'] = 'BuyProduct';

				for ($i = 0; $i < $count_item; $i++)
				{
					$libclass->addpoints($user, $options);
				}
			}
			elseif ($integrate_with === 'EasySocial')
			{
				$options['command'] = 'buy_product';

				for ($i = 0; $i < $count_item; $i++)
				{
					$libclass->addpoints($user, $options);
				}
			}
		}
	}

	/**
	 * This function check whether order item has any media or not
	 *
	 * @param   INT  $primaryKey  primaryKey
	 *
	 * @return  VOID
	 */
	public function updateFileDownloadCount($primaryKey)
	{
		$pfile                  = new stdClass;
		$pfile->id              = $primaryKey;
		$pfile->product_file_id = $media['file_id'];

		$db    = JFactory::getDBO();
		$query = 'UPDATE  `#__kart_orderItemFiles` SET `download_count` = (`download_count` + 1) WHERE `id`=' . $primaryKey;

		//  . ' AND download_count != -1';
		$db->setQuery($query);
		$db->execute();

		return;
	}

	/**
	 * This function check whether order item has any media or not
	 *
	 * @param   INT  $order_item_id  integer
	 *
	 * @return  array of all mediafile ids
	 */
	public function isMediaForPresent($order_item_id)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT `id` FROM `#__kart_orderItemFiles` WHERE `order_item_id`=' . $order_item_id;
		$db->setQuery($query);

		return $res = $db->loadAssocList();
	}

	/**
	 * mediaFileAuthorise
	 *
	 * @param   STRING  $file_id        file_id
	 * @param   STRING  $strorecall     strorecall
	 * @param   STRING  $guest_email    guest_email
	 * @param   STRING  $order_item_id  order_item_id
	 *
	 * @return  html
	 */
	public function mediaFileAuthorise($file_id, $strorecall, $guest_email, $order_item_id)
	{
		// Get store id associatd with file id
		$db    = JFactory::getDBO();
		$query = "SELECT i.`store_id`,`file_id`,`file_display_name`,`filePath`,`purchase_required` FROM `#__kart_itemfiles` AS f
		LEFT JOIN `#__kart_items` AS i ON f.item_id=i.item_id
		where f.`file_id`=" . $file_id;
		$db->setQuery($query);
		$fileDetail = $db->loadAssoc();

		$comquick2cartHelper    = new comquick2cartHelper;
		$ret['validDownload']   = 0;
		$ret['orderItemFileId'] = 0;

		// FOR STORE AUTHORIZED PERSONS
		if (!empty($strorecall))
		{
			$ret['validDownload'] = $comquick2cartHelper->store_authorize('product_default', $fileDetail['store_id']);

			return $ret;
		}

		// Vm: for authorization we are only user detail from orderfiles table
		elseif (empty($order_item_id) && $fileDetail['purchase_required'] == 0)
		{
			// Called from product detail page
			// For free media
			$ret['validDownload'] = 1;

			return $ret;
		}
		else
		{
			// Is authorized guest chekout
			if (!empty($guest_email))
			{
				$orderid         = $comquick2cartHelper->getOrderId($order_item_id);
				$guest_email_chk = $comquick2cartHelper->checkmailhash($orderid, $guest_email);

				if (empty($guest_email_chk))
				{
					//  Not matched
					$ret['validDownload'] = 0;

					return $ret;
				}
			}

			// Is expired date/download conunt or not
			$productHelper = new productHelper;
			$ret           = $productHelper->validDownload($file_id, $order_item_id);

			return $ret;
		}
	}

	/**
	 * This functionw check whether download is expired date/download conunt or not
	 *
	 * @param   STRING  $file_id        file_id
	 * @param   STRING  $order_item_id  order_item_id
	 *
	 * @return  html
	 */
	public function validDownload($file_id, $order_item_id)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT of.* FROM `#__kart_orderItemFiles` AS of
		where of.order_item_id=' . $order_item_id . ' AND of.`product_file_id`=' . $file_id . ' ORDER BY id';
		$db->setQuery($query);
		$fileDetail             = $db->loadAssoc();
		$ret['validDownload']   = 0;
		$ret['orderItemFileId'] = 0;
		$params                 = JComponentHelper::getParams('com_quick2cart');

		//  Down count/ date limit / both
		$eProdUExpiryMode       = $params->get('eProdUExpiryMode', 'epMaxDownload');

		if (!empty($fileDetail))
		{
			//  ALL DOWNLOAD COUNT done. NULL for while placing order, only expiry date is considered
			//  DATE EXPIRATION
			if ($eProdUExpiryMode == 'epDateExpiry' || $eProdUExpiryMode == 'epboth')
			{
				if ($fileDetail['download_limit'] != null && $fileDetail['download_limit'] != -1
					&& $fileDetail['download_count'] >= $fileDetail['download_limit'])
				{
					$ret['validDownload'] = 0;

					return $ret;
				}
			}

			if ($eProdUExpiryMode == 'epMaxDownload' || $eProdUExpiryMode == 'epboth')
			{
				// Check of date expirary
				if (!empty($fileDetail['expirary_date'])
					&& $fileDetail['expirary_date'] != "0000-00-00 00:00:00"
					&& date('Y-m-d H:i:s') > $fileDetail['expirary_date'])
				{
					$ret['validDownload'] = 0;

					return $ret;
				}
			}

			$ret['validDownload']   = 1;
			$ret['orderItemFileId'] = $fileDetail['id'];
		}

		return $ret;
	}

	/**
	 * download the file
	 *
	 * @param   STRING  $file             - file path eg /var/www/j30/media/com_quick2cart/qtc_pack.zip
	 * @param   STRING  $filename_direct  - for direct download it will be file path like http://
	 * localhost/j30/media/com_quick2cart/qtc_pack.zip  -- for FUTURE SCOPE
	 * @param   STRING  $extern           - for direct download it will be file path like http://
	 * @param   STRING  $exitHere         - for direct download it will be file path like http://
	 *
	 * @return  html
	 */
	public function download($file, $filename_direct = '', $extern = '', $exitHere = 1)
	{
		$productHelper = new productHelper;
		global $jlistConfig, $mainframe;
		$app = JFactory::getApplication();

		$view_types = array();

		//  ALLOWED  FILE EXTENTION
		$view_types = explode(',', $jlistConfig['file.types.view']);

		clearstatcache();

		//  Existiert file - wenn nicht error
		if (!$extern)
		{
			if (!file_exists($file))
			{
				return 2;
			}
			else
			{
				$len = filesize($file);
			}
		}
		else
		{
			$len = urlfilesize($file);
		}

		// If url go to other website - open it in a new browser window

		/*   if ($extern_site){
		echo "<script>document.location.href='$file';</script>\n";
		exit;
		}*/

		// If set the option for direct link to the file
		// If (0 || !$jlistConfig['use.php.script.for.download']){
		if (0)
		{
			if (empty($filename_direct))
			{
				$app->redirect($file);
			}
			else
			{
				$app->redirect($filename_direct);
			}
		}
		else
		{
			$filename       = basename($file);
			$file_extension = strtolower(substr(strrchr($filename, "."), 1));
			$ctype          = $productHelper->datei_mime($file_extension);

			ob_end_clean();

			//  Needed for MS IE - otherwise content disposition is not used?
			if (ini_get('zlib.output_compression'))
			{
				ini_set('zlib.output_compression', 'Off');
			}

			header("Cache-Control: public, must-revalidate");
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			header("Expires: 0");
			header("Content-Description: File Transfer");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			header("Content-Type: " . $ctype);
			header("Content-Length: " . (string) $len);

			//  If valid extention
			//  If (!in_array($file_extension, $view_types)){

			header('Content-Disposition: attachment; filename="' . $filename . '"');

			/* } else {
			 view file in browser
			header('Content-Disposition: inline; filename="'.$filename.'"');
			}*/

			header("Content-Transfer-Encoding: binary\n");

			//  redirect to category when it is set the time

			/* if (intval($jlistConfig['redirect.after.download']) > 0){
			header( "refresh:".$jlistConfig['redirect.after.download']."; url=".$redirect_to );
			}*/

			//  set_time_limit doesn't work in safe mode
			if (!ini_get('safe_mode'))
			{
				@set_time_limit(0);
			}

			@readfile($file);
		}

		if ($exitHere == 1)
		{
			exit;
		}
	}

	/**
	 * datei_mime
	 *
	 * @param   STRING  $filetype  filetype
	 *
	 * @return  html
	 */
	public function datei_mime($filetype)
	{
		switch ($filetype)
		{
			case "ez":
				$mime = "application/andrew-inset";
				break;
			case "hqx":
				$mime = "application/mac-binhex40";
				break;
			case "cpt":
				$mime = "application/mac-compactpro";
				break;
			case "doc":
				$mime = "application/msword";
				break;
			case "bin":
				$mime = "application/octet-stream";
				break;
			case "dms":
				$mime = "application/octet-stream";
				break;
			case "lha":
				$mime = "application/octet-stream";
				break;
			case "lzh":
				$mime = "application/octet-stream";
				break;
			case "exe":
				$mime = "application/octet-stream";
				break;
			case "class":
				$mime = "application/octet-stream";
				break;
			case "dll":
				$mime = "application/octet-stream";
				break;
			case "oda":
				$mime = "application/oda";
				break;
			case "pdf":
				$mime = "application/pdf";
				break;
			case "ai":
				$mime = "application/postscript";
				break;
			case "eps":
				$mime = "application/postscript";
				break;
			case "ps":
				$mime = "application/postscript";
				break;
			case "xls":
				$mime = "application/vnd.ms-excel";
				break;
			case "ppt":
				$mime = "application/vnd.ms-powerpoint";
				break;
			case "wbxml":
				$mime = "application/vnd.wap.wbxml";
				break;
			case "wmlc":
				$mime = "application/vnd.wap.wmlc";
				break;
			case "wmlsc":
				$mime = "application/vnd.wap.wmlscriptc";
				break;
			case "vcd":
				$mime = "application/x-cdlink";
				break;
			case "pgn":
				$mime = "application/x-chess-pgn";
				break;
			case "csh":
				$mime = "application/x-csh";
				break;
			case "dvi":
				$mime = "application/x-dvi";
				break;
			case "spl":
				$mime = "application/x-futuresplash";
				break;
			case "gtar":
				$mime = "application/x-gtar";
				break;
			case "hdf":
				$mime = "application/x-hdf";
				break;
			case "js":
				$mime = "application/x-javascript";
				break;
			case "nc":
				$mime = "application/x-netcdf";
				break;
			case "cdf":
				$mime = "application/x-netcdf";
				break;
			case "swf":
				$mime = "application/x-shockwave-flash";
				break;
			case "tar":
				$mime = "application/x-tar";
				break;
			case "tcl":
				$mime = "application/x-tcl";
				break;
			case "tex":
				$mime = "application/x-tex";
				break;
			case "texinfo":
				$mime = "application/x-texinfo";
				break;
			case "texi":
				$mime = "application/x-texinfo";
				break;
			case "t":
				$mime = "application/x-troff";
				break;
			case "tr":
				$mime = "application/x-troff";
				break;
			case "roff":
				$mime = "application/x-troff";
				break;
			case "man":
				$mime = "application/x-troff-man";
				break;
			case "me":
				$mime = "application/x-troff-me";
				break;
			case "ms":
				$mime = "application/x-troff-ms";
				break;
			case "ustar":
				$mime = "application/x-ustar";
				break;
			case "src":
				$mime = "application/x-wais-source";
				break;
			case "zip":
				$mime = "application/x-zip";
				break;
			case "au":
				$mime = "audio/basic";
				break;
			case "snd":
				$mime = "audio/basic";
				break;
			case "mid":
				$mime = "audio/midi";
				break;
			case "midi":
				$mime = "audio/midi";
				break;
			case "kar":
				$mime = "audio/midi";
				break;
			case "mpga":
				$mime = "audio/mpeg";
				break;
			case "mp2":
				$mime = "audio/mpeg";
				break;
			case "mp3":
				$mime = "audio/mpeg";
				break;
			case "aif":
				$mime = "audio/x-aiff";
				break;
			case "aiff":
				$mime = "audio/x-aiff";
				break;
			case "aifc":
				$mime = "audio/x-aiff";
				break;
			case "m3u":
				$mime = "audio/x-mpegurl";
				break;
			case "ram":
				$mime = "audio/x-pn-realaudio";
				break;
			case "rm":
				$mime = "audio/x-pn-realaudio";
				break;
			case "rpm":
				$mime = "audio/x-pn-realaudio-plugin";
				break;
			case "ra":
				$mime = "audio/x-realaudio";
				break;
			case "wav":
				$mime = "audio/x-wav";
				break;
			case "pdb":
				$mime = "chemical/x-pdb";
				break;
			case "xyz":
				$mime = "chemical/x-xyz";
				break;
			case "bmp":
				$mime = "image/bmp";
				break;
			case "gif":
				$mime = "image/gif";
				break;
			case "ief":
				$mime = "image/ief";
				break;
			case "jpeg":
				$mime = "image/jpeg";
				break;
			case "jpg":
				$mime = "image/jpeg";
				break;
			case "jpe":
				$mime = "image/jpeg";
				break;
			case "png":
				$mime = "image/png";
				break;
			case "tiff":
				$mime = "image/tiff";
				break;
			case "tif":
				$mime = "image/tiff";
				break;
			case "wbmp":
				$mime = "image/vnd.wap.wbmp";
				break;
			case "ras":
				$mime = "image/x-cmu-raster";
				break;
			case "pnm":
				$mime = "image/x-portable-anymap";
				break;
			case "pbm":
				$mime = "image/x-portable-bitmap";
				break;
			case "pgm":
				$mime = "image/x-portable-graymap";
				break;
			case "ppm":
				$mime = "image/x-portable-pixmap";
				break;
			case "rgb":
				$mime = "image/x-rgb";
				break;
			case "xbm":
				$mime = "image/x-xbitmap";
				break;
			case "xpm":
				$mime = "image/x-xpixmap";
				break;
			case "xwd":
				$mime = "image/x-xwindowdump";
				break;
			case "msh":
				$mime = "model/mesh";
				break;
			case "mesh":
				$mime = "model/mesh";
				break;
			case "silo":
				$mime = "model/mesh";
				break;
			case "wrl":
				$mime = "model/vrml";
				break;
			case "vrml":
				$mime = "model/vrml";
				break;
			case "css":
				$mime = "text/css";
				break;
			case "asc":
				$mime = "text/plain";
				break;
			case "txt":
				$mime = "text/plain";
				break;
			case "gpg":
				$mime = "text/plain";
				break;
			case "rtx":
				$mime = "text/richtext";
				break;
			case "rtf":
				$mime = "text/rtf";
				break;
			case "wml":
				$mime = "text/vnd.wap.wml";
				break;
			case "wmls":
				$mime = "text/vnd.wap.wmlscript";
				break;
			case "etx":
				$mime = "text/x-setext";
				break;
			case "xsl":
				$mime = "text/xml";
				break;
			case "flv":
				$mime = "video/x-flv";
				break;
			case "mpeg":
				$mime = "video/mpeg";
				break;
			case "mpg":
				$mime = "video/mpeg";
				break;
			case "mpe":
				$mime = "video/mpeg";
				break;
			case "qt":
				$mime = "video/quicktime";
				break;
			case "mov":
				$mime = "video/quicktime";
				break;
			case "mxu":
				$mime = "video/vnd.mpegurl";
				break;
			case "avi":
				$mime = "video/x-msvideo";
				break;
			case "movie":
				$mime = "video/x-sgi-movie";
				break;
			case "asf":
				$mime = "video/x-ms-asf";
				break;
			case "asx":
				$mime = "video/x-ms-asf";
				break;
			case "wm":
				$mime = "video/x-ms-wm";
				break;
			case "wmv":
				$mime = "video/x-ms-wmv";
				break;
			case "wvx":
				$mime = "video/x-ms-wvx";
				break;
			case "ice":
				$mime = "x-conference/x-cooltalk";
				break;
			case "rar":
				$mime = "application/x-rar";
				break;
			default:
				$mime = "application/octet-stream";
				break;
		}

		return $mime;
	}

	/**
	 * Collecting data & called jLike trigger
	 *
	 * @param   STRING   $product_url  product_url
	 * @param   INTEGER  $id           id
	 * @param   STRING   $title        STRING
	 *
	 * @return  html
	 */
	public function DisplayjlikeButton($product_url, $id, $title)
	{
		$jlikeparams               = array();
		$jlikeparams['url']        = $product_url;
		$jlikeparams['product_id'] = $id;
		$jlikeparams['title']      = $title;

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('content', 'jlike_quick2cart');
		$grt_response = $dispatcher->trigger('onBeforeDisplaylike', array(
						'com_quick2cart.productpage',
						$jlikeparams
						)
					);

		if (!empty($grt_response['0']))
		{
			return $grt_response['0'];
		}
		else
		{
			return '';
		}
	}

	/**
	 * Method to get getMultipleCurrFields.
	 *
	 * @param   string  $name             name
	 * @param   string  $currFieldValues  currFieldValues
	 * @param   string  $currFieldClass   currFieldClass
	 *
	 * @since   2.2
	 *
	 * @return   null
	 */
	public function getMultipleCurrFields($name, $currFieldValues = array(), $currFieldClass = "")
	{
		$html             = '';
		$entered_numerics = "'" . JText::_('QTC_ENTER_NUMERICS') . "'";
		$comParams        = JComponentHelper::getParams('com_quick2cart');

		// Get Currencies
		$currencies = $comParams->get('addcurrency');
		$curr       = explode(',', $currencies);

		// Get Curr sysbols
		$currencies_sym = $comParams->get('addcurrency_sym');

		if (!empty($currencies_sym))
		{
			$curr_syms = explode(',', $currencies_sym);
		}

		//  Key contain 0,1,2...value contain INR...
		foreach ($curr as $currKey => $value)
		{
			$storevalue = "";

			if (!empty($curr_syms[$currKey]))
			{
				$currtext = $curr_syms[$currKey];
			}
			else
			{
				$currtext = $value;
			}

			$currentFieldValue = ((isset($currFieldValues[$value])) ? number_format($currFieldValues[$value], 2) : '');
			$html .= '<div class="input-append curr_margin " >' .
						'<input type="text" name="' . $name . '[' . $value . ']"
							size="" id="" value="' . $currentFieldValue . '"
							class=" input-mini ' . $currFieldClass . '">' .
						'<span class="add-on ">' . $currtext . '</span>' .
					'</div>';
		}

		return $html;
	}

	/**
	 * This method give shipping related fields details.
	 *
	 * @param   INTEGER  $item_id  product's item id.
	 *
	 * @since   1.0
	 *
	 * @return  Shipping related fields.
	 */
	public function getProductsShipRelFields($item_id)
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("i.`item_id`,i.`item_length`,i.`item_width`,i.`item_height`");
		$query->select("i.`item_length_class_id`,i.`item_weight`,i.`item_weight_class_id`,i.`shipProfileId`");
		$query->from('#__kart_items AS i');
		$query->where('i.item_id=' . $item_id);
		$db->setQuery($query);

		return $db->loadAssoc();
	}

	/**
	 * Method cCheck whether product is allowed to delete or not.  If not the enqueue error message accordingly..
	 *
	 * @param   string  $item_id  item_id.
	 *
	 * @since   2.2
	 *
	 * @return   boolean true or false.
	 */
	public function isAllowedToDelProduct($item_id)
	{
		// 1. Check order with this item id
		$app   = JFactory::getApplication();
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Check in tax related table
		$query->select('oi.order_id');
		$query->from('#__kart_order_item AS oi');
		$query->where('oi.item_id	=' . $item_id);
		$db->setQuery($query);
		$entryList = $db->loadColumn();

		if (!empty($entryList))
		{
			// Order list
			$entryListStr = '(' . JText::_('COM_QUICK2CART_IDS') . implode(',', $entryList) . ')';

			$errMsg = JText::sprintf('COM_QUICK2CART_DEL_PROD_FOUND_ORDER_AGAINST', $item_id, $entryListStr);
			$app->enqueueMessage($errMsg, 'error');

			return false;
		}

		//  @TODO have to check for shipping related and etc table.
		return true;
	}

	/**
	 * Method Check whether product is allowd to buy or not according to the options eg "allowed out of stock" option..
	 *
	 * @param   string  $itemdetail  itemdetail.
	 *
	 * @since   2.2
	 *
	 * @return   boolean true or false.
	 */
	public function isInStockProduct($itemdetail)
	{
		$params               = JFactory::getApplication()->getParams('com_quick2cart');
		$usestock             = $params->get('usestock');
		$outofstock_allowship = $params->get('outofstock_allowship');

		$stock            = $itemdetail->stock;
		$min_qty          = (!empty($itemdetail->min_quantity)) ? $itemdetail->min_quantity : 1;
		$max_qty          = (!empty($itemdetail->max_quantity)) ? $itemdetail->max_quantity : 999;
		$buybutton_status = 1;

		if ($usestock == 1 && $outofstock_allowship == 0)
		{
			if ($stock != null || $stock != '')
			{
				$max_qty = min($stock, $max_qty);
			}

			// 0  and not equal to NULL
			if ($stock <= 0 && $stock != "")
			{
				$buybutton_status = 0;
			}
			elseif ($stock == null)
			{
				// STOCK=NULL mean not entered or not require of stock (e-artical)
				$buybutton_status = 1;
			}
		}

		return $buybutton_status;
	}

	/**
	 * +manoj
	 * Used for category view => categorylist layout
	 * Returns arry of [catid] => no. of products
	 * Array ( [26] => 1 [28] => 3 )
	 *
	 * @param   INTEGER  $store_id  Store_id
	 *
	 * @return  boolean true or false.
	 */
	public function getCategoryProductsCount($store_id)
	{
		jimport('joomla.application.categories');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('c.id,c.title');
		$query->from('`#__categories` AS c');
		$query->where("extension='com_quick2cart'");
		$query->where('c.published = 1');
		$db->setQuery($query);
		$qtcCategories = $db->loadAssocList('id');

		// Get  category object instance
		$options               = array();
		$options['extension']  = 'com_quick2cart';
		$options['table']      = '#__kart_items';
		$options['field']      = 'category';
		$options['key']        = 'item_id';
		$options['statefield'] = 'state';
		$options['countItems'] = 1;

		// Get object
		if ($store_id)
		{
			$options['store_id'] = $store_id;
		}

		/*		else
		{
		$qtcJCategoriesObj = new JCategories($options);
		}*/

		require_once JPATH_SITE . '/components/com_quick2cart/helpers/qtccategories.php';
		$qtcJCategoriesObj = new qtccategories($options);

		if (!empty($qtcCategories))
		{
			foreach ($qtcCategories as $key => $cat)
			{
				$JCategoryNode = $qtcJCategoriesObj->get($cat['id']);

				if ($JCategoryNode)
				{
					$qtcCategories[$key]['count'] = $JCategoryNode->getNumItems(true);
				}
			}
		}

		return $qtcCategories;

		/*  Create a new query object.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		Select the required fields from the table.
		$query->select('COUNT(ki.item_id) AS count');
		$query->select('c.id AS cid');
		$query->from('`#__kart_items` AS ki');
		$query->join('LEFT', '`#__categories` AS c ON c.id=ki.category');
		$query->where('ki.state = 1');
		$query->group('c.id');

		$db->setQuery($query);
		$productCount = $db->loadAssocList();

		$pcArray = array();

		if (count($productCount))
		{
		foreach ($productCount as $pc)
		{
		$pcArray[$pc['cid']] = $pc['count'];
		}
		}
		return $pcArray;*/
	}

	/**
	 * This function return item's complete detail.
	 *
	 * @param   string  $item_id  item_id .
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function getItemCompleteDetail($item_id)
	{
		if (empty($item_id))
		{
			return false;
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$db                  = JFactory::getDbo();
		$query               = $db->getQuery(true);

		// 1. Get products basic details
		$query->select("i.`item_id`,i.`parent`,i.`product_id`,i.`store_id`,i.`name`,i.`stock`,i.`min_quantity`,
		i.`max_quantity`,i.`category`,i.`sku`,i.`images`,i.`description`,i.`video_link`,i.`cdate`,
		i.`mdate`,i.`state`,i.`featured`,i.`metakey`,i.`metadesc`,i.`item_length`,i.`item_width`,
		i.`item_height`,i.`item_length_class_id`,i.`item_weight`,i.`item_weight_class_id`,i.`taxprofile_id`,i.`shipProfileId`");
		$query->from('`#__kart_items` AS i');
		$query->where('item_id=' . $item_id);

		$db->setQuery($query);
		$prodBasicDetails = $db->loadObject();

		// 2.Get product all currencies prices irrespective of  session currency.
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from('`#__kart_base_currency` AS bc');
		$query->where('item_id=' . $item_id);
		$db->setQuery($query);
		$prodBasicDetails->prodPriceDetails = $db->loadObjectList('currency');

		// 3. Get Attribute Details.
		$prodBasicDetails->prodAttributeDetails = $this->getItemCompleteAttrDetail($item_id);

		// 4. Get media File detail.
		$prodBasicDetails->prodMediaDetails = $this->getMediaDetail($item_id);

		return $prodBasicDetails;
	}

	/**
	 * This function  provides all info about item attributes ( id,att_name,options details, price ).
	 *
	 * @param   integer  $item_id  item_id.
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function getItemCompleteAttrDetail($item_id)
	{
		if (empty($item_id))
		{
			return;
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$attributes          = $this->getAttributes($item_id);

		foreach ($attributes as $key => $att)
		{
			$att->optionDetails = $comquick2cartHelper->getAttributeDetails($att->itemattribute_id);
		}

		return $attributes;
	}

	/**
	 * This function  give item attributes.
	 *
	 * @param   integer  $item_id  item_id
	 *
	 * @since   2.2.2
	 *
	 * @return   Object list.
	 */
	public function getAttributes($item_id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("`itemattribute_id`,`itemattribute_name`,`attribute_compulsary`,`attributeFieldType`");
		$query->from('#__kart_itemattributes');
		$query->where('item_id=' . $item_id);

		$db->setQuery($query);

		return $db->loadobjectList();
	}

	/**
	 * This function check whether user bought the product or not.
	 *
	 * @param   integer  $user_id  user_id.
	 * @param   integer  $item_id  item_id.
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function isUserBoughtproduct($user_id = 0, $item_id = 0)
	{
	}

	/** Insert/update the selected attribute option. This function is called while updatating the order.
	 *
	 * */
	public function insertOptionToOrderItems($optionDetail)
	{
		if (!empty($optionDetail))
		{
			$db = JFactory::getDbo();
			$items_opt = new stdClass;
			$dbAction = 'insertObject';

			if (isset($optionDetail['orderitemattribute_id']))
			{
				$dbAction = 'updateObject';
				$items_opt->orderitemattribute_id = $optionDetail['orderitemattribute_id'];
			}

			if (isset($optionDetail['order_item_id']))
			{
				$items_opt->order_item_id = $optionDetail['order_item_id'];
			}

			if (isset($optionDetail['itemattributeoption_id']))
			{
				$items_opt->itemattributeoption_id = $optionDetail['itemattributeoption_id'];
			}

			if (isset($optionDetail['orderitemattribute_name']))
			{
				$items_opt->orderitemattribute_name = $optionDetail['orderitemattribute_name'];
			}

			if (isset($optionDetail['orderitemattribute_prefix']))
			{
				$items_opt->orderitemattribute_prefix = $optionDetail['orderitemattribute_prefix'];
			}

			// Load model file
			$path = JPATH_SITE . "/components/com_quick2cart/models/cartcheckout.php";

			if (!class_exists("Quick2cartModelcartcheckout"))
			{
				JLoader::register("Quick2cartModelcartcheckout", $path);
				JLoader::load("Quick2cartModelcartcheckout");
			}

			$Quick2cartModelcartcheckout = new Quick2cartModelcartcheckout;

			// Get option price [currency is referred in this function]
			$items_opt->orderitemattribute_price = $Quick2cartModelcartcheckout->getAttrOptionPrice($optionDetail['itemattributeoption_id']);

			try
			{
				if (!$db->$dbAction('#__kart_order_itemattributes', $items_opt, 'orderitemattribute_id'))
				{
					echo $this->_db->stderr();
					return 0;
				}

				return (array)$items_opt;
				//return $items_opt->orderitemattribute_id;
			}
			catch(Exception $e)
			{
				$this->setError($e->getMessage());
			}
		}
	}

	/**
	 * This function gives you option details from DB.
	 *
	 * @param   integer  $option_id           option id.
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function getAttributeOptionDetails($option_id)
	{
		// Get orderitemattribute_ids to update

		if ($option_id)
		{
			try
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select("*")
				->from('#__kart_itemattributeoptions')
				->where(" itemattributeoption_id = ". $option_id);
				$db->setQuery($query);

				return $db->loadAssoc('itemattributeoption_id');

			}
			catch(Exception $e)
			{
				$this->setError($e->getMessage());

				return array();
			}
		}
	}
	/**
	 * This function gives you option details from DB.
	 *
	 * @param   integer  $order_item_id           order item id.
	 * @param   integer  $itemattributeoption_id  attributeoption_id.
	 *
	 * @since   2.2.2
	 *
	 * @return   boolean true or false.
	 */
	public function getOrderAttributeOptionDetails($order_item_id, $itemattributeoption_id)
	{
		// Get orderitemattribute_ids to update

		if ($itemattributeoption_id && $order_item_id)
		{
			try
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select("`orderitemattribute_id`,order_item_id,itemattributeoption_id,orderitemattribute_name,orderitemattribute_price,orderitemattribute_prefix")
				->from('#__kart_order_itemattributes ')
				->where(" order_item_id = ". $order_item_id)
				->where(" itemattributeoption_id = " . $itemattributeoption_id);
				$db->setQuery($query);

				return $db->loadAssoc('itemattributeoption_id');

			}
			catch(Exception $e)
			{
				$this->setError($e->getMessage());

				return array();
			}
		}
	}
}
