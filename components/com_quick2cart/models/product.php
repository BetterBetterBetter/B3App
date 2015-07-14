<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
//Added by Sneha
require_once (JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'user.php');
//@vm replace call to main helper _sendmail funtion
$params = JComponentHelper::getParams('com_quick2cart');

class quick2cartModelProduct extends JModelLegacy
{
	/*function store($post)
	{
	$post['pid']=1;
	if ($post['pid']) // update
	{

	}
	//print_r($post); die;
	}*/

	function StoreAllAttribute($item_id, $allAttrib, $sku, $client)
	{
		// get  attributeid list FROM POST
		$attIdList = array();

		foreach ($allAttrib as $attributes)
		{

			if (!empty($attributes['attri_id']))
			{
				$attIdList[] = $attributes['attri_id'];
			}
		}
		// DEL EXTRA ATTRIBUTES

		if (!class_exists('productHelper'))
		{
			// require while called from backend
			JLoader::register('productHelper', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php');
			JLoader::load('productHelper');
		}
		//THIS  DELETE db attributes which is not present now or removed
		$productHelper = new productHelper();
		$productHelper->deleteExtaAttribute($item_id, $attIdList);

		if (!class_exists('quick2cartModelAttributes'))
		{
			// require while called from backend
			JLoader::register('quick2cartModelAttributes', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models' . DS . 'attributes.php');
			JLoader::load('quick2cartModelAttributes');
		}
		$quick2cartModelAttributes = new quick2cartModelAttributes();

		foreach ($allAttrib as $key => $attr)
		{
			$attr['sku'] = $sku;
			$attr['client'] = $client;
			$attr['item_id'] = $item_id;
			// Dont consider empty attributes
			if (!empty($attr['attri_name']))
			{
				$quick2cartModelAttributes->store($attr);
			}
		}
	}

	function getItemidFromSku($sku)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT `item_id` from `#__kart_items` where `sku`="' . $sku . '"';
		$db->setQuery($query);
		return $ietmid = $db->loadResult();
	}
	/*
	This model function manage items published or unpublished state
	*/

	function setItemState($items, $state)
	{
		$db = JFactory::getDBO();

		if (is_array($items))
		{

			foreach ($items as $id)
			{
				$db = JFactory::getDBO();
				$query = "UPDATE #__kart_items SET state=" . $state . " WHERE item_id=" . $id;
				$db->setQuery($query);

				if (!$db->execute())
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
	} // end of setitemstate

	//Function addded by Sneha

	//send mail to admin for product approval
/*	function SendMailToAdminApproval($values,$item_id)
	{
		$db=JFactory::getDBO();
		$query = "SELECT `images` FROM `#__kart_items` WHERE `item_id` = ".$item_id;
		$db->setQuery( $query );
		$image_path=$db->loadResult();

		$image_path = substr($image_path, 1, -1);
		$image_path =explode(",",$image_path);

		for($i=0; $i<count($image_path); $i++)
		{
			$mul_images[] = JUri::ROOT().'images/quick2cart/'.substr($image_path[$i], 1, -1);
		}

		$loguser=JFactory::getUser();
		$app=JFactory::getApplication();
		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$sitename = $app->getCfg('sitename');
		$params=JComponentHelper::getParams('com_quick2cart');
		$sendto = $params->get('sale_mail');

		$subject = JText::_('COM_Q2C_PRODUCT_AAPROVAL_SUBJECT');
		//$subject	= str_replace('{sellername}', $loguser->name, $subject);

		$body 	= JText::_('COM_Q2C_PRODUCT_AAPROVAL_BODY');
		$body	= str_replace('{title}', $values['item_name'], $body);
		$body	= str_replace('{sellername}', $loguser->name, $body);
		$body	= str_replace('{des}', $values['description']['data'], $body);
		$body	= str_replace('{link}', JUri::base().'administrator/index.php?option=com_quick2cart&view=products', $body);

		for($i=0; $i < count($image_path); $i++){
			$body	.= '<br><img src="'.$mul_images[$i].'" alt="No image" width="200" ><br>';
		}

		//$res=SendMailHelper::sendmail($to,$subject,$body);
	}
*/
	//Function addded by Sneha

	//send mail to campaign owner about pending product approval

	function SendMailToOwner($values)
	{
		$app = JFactory::getApplication();
		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$sitename = $app->getCfg('sitename');
		$loguser = JFactory::getUser();
		$sendto = $loguser->email;
		$subject = JText::_('COM_Q2C_PRODUCT_AAPROVAL_OWNER_SUBJECT');
		$subject = str_replace('{sellername}', $loguser->name, $subject);
		$comquick2cartHelper = new comquick2cartHelper;
		$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=category&layout=default');
		$body = JText::_('COM_Q2C_PRODUCT_AAPROVAL_OWNER_BODY');
		$body = str_replace('{sellername}', $loguser->name, $body);
		$body = str_replace('{title}', $values->get('item_name', '', 'RAW') , $body);
		$body = str_replace('{link}', JUri::base() . 'index.php?option=com_quick2cart&view=category&layout=default&Itemid=' . $itemid, $body);
		$body = str_replace('{sitename}', $sitename, $body);
		$res = $comquick2cartHelper->sendmail($mailfrom, $subject, $body, $sendto);
	}

	/**
	 * This function return product images according to integration
	 *
	 * @TODO: for now it only work for zoo & native, so the changes will be needed for other integration
	 *
	 * @params  INT  $item_id  Item id
	 *
	 * @return  product images
	 *
	 * @since  2.2.5
	 **/
	public function getProdutImages($item_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Get the product id ( this is needed integration) & client(parent)
		$query->select($db->quoteName(array('parent', 'product_id')));
		$query->from($db->quoteName('#__kart_items'));
		$query->where($db->quoteName('item_id') . ' = '. $item_id);

		$db->setQuery($query);
		$results = $db->loadObject();

		switch($results->parent)
		{
			// Get Zoo item image
			case 'com_zoo':

				$query = $db->getQuery(true);

				$query->select($db->quoteName(array('i.elements', 'i.application_id', 'i.type', 'app.application_group')));
				$query->from($db->quoteName('#__zoo_item','i'));
				$query->join('LEFT', $db->quoteName('#__zoo_application', 'app') . ' ON (' . $db->quoteName('app.id') . ' = ' . $db->quoteName('i.application_id') . ')');

				$query->where($db->quoteName('i.id') . ' = '. $results->product_id);
				$db->setQuery($query);

				$zoo_item = $db->loadObject();

				$image_path[0] = $this->getItemFieldData($zoo_item->application_group, $zoo_item->type, $zoo_item->elements);

				return $image_path;

			break;

			default:
				if (!empty($item_id))
				{
					$db = JFactory::getDBO();
					$query = "SELECT `images` FROM `#__kart_items` WHERE `item_id` = " . $item_id;
					$db->setQuery($query);
					$image_path = $db->loadResult();

					if (!empty($image_path)) return json_decode($image_path, false);
				}
		}
	}

	/**
	 * Get Zoo Item Image
	 *
	 * @param   STRING  $application_group  Zoo Item Application group
	 * @param   STRING  $type 				Zoo Item type
	 * @param   STRING  $elements 			Zoo element info
	 *
	 * @return zoo item image
	 *
	 * @since  2.2.5
	 */
	public static function getItemFieldData($application_group,$type,$elements)
	{
		$elements = json_decode($elements,true);

		$app 				= App::getInstance('zoo');
		$db  				= JFactory::getDBO();
		$application_group	= strtolower($application_group);
		$item_type			= strtolower($type);
		$zoo_config_file	= array();
		$zoo_config_file	= json_decode(file_get_contents( JPATH_SITE.DS.'media'.DS.'zoo'.DS.'applications'.DS.$application_group.DS.'types'.DS.$item_type.'.config'),true);

		// Get the image key
		$image_flag=0;

		// Check is the image available
		foreach($zoo_config_file['elements'] as $image_key=>$arr_row)
		{
			if($arr_row['type']=="image" AND $arr_row['name']!="Teaser Image")
			{
				$image_flag=1;
				break;
			}
			else if($arr_row['type']=="image" AND $arr_row['name']=="Teaser Image")
			{
				$image_flag=1;
				break;
			}
		}

		$result=array();

		// Get the image path from $element array
		if($image_flag==1)
		{
			$image = $elements[$image_key]['file'];
		}
		else
		{
			$image='';
		}

		// Intro text key
		/**
		$intro_text_flag=0;
		foreach($zoo_config_file['elements'] as $intro_key=>$arr_row)
		{
			if($arr_row['type']=="textarea")
			{
				$intro_text_flag=1;
				break;
			}
		}

		if($intro_text_flag==1)
		{
			if(!empty($elements[$intro_key][0]['value']))
			{
				$result['intro_text']=$elements[$intro_key][0]['value'];
			}
			else if(!empty($elements[$intro_key][1]['value']))
			{
				$result['intro_text']=$elements[$intro_key][1]['value'];
			}
			else
			{
				$result['intro_text']='';
			}
		}
		**/

		return $image;
	}

	//send mail to admin after editing product

	function SendMailToAdminApproval($prod_values, $item_id, $newProduct = 1)
	{
		$loguser = JFactory::getUser();
		$comquick2cartHelper = new comquick2cartHelper;
		$app = JFactory::getApplication();
		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$sitename = $app->getCfg('sitename');
		$params = JComponentHelper::getParams('com_quick2cart');
		$sendto = $params->get('sale_mail');
		$currency = $comquick2cartHelper->getCurrencySession(); //$params->get('addcurrency');  // @sheha : currency should take from session instead of param

		//$sendto = $mailfrom;
		$multiple_img = array();
		$count = 0;
		$prod_imgs = $prod_values->get('qtc_prodImg', array() , "ARRAY");
		$quick2cartModelProduct = new quick2cartModelProduct;
		$multiple_img = $quick2cartModelProduct->getProdutImages($item_id);
		$body = '';
		// Edit product

		if ($newProduct == 0)
		{
			$subject = JText::_('COM_Q2C_EDIT_PRODUCT_SUBJECT');
			$subject = str_replace('{sellername}', $loguser->name, $subject);
			$body = JText::_('COM_Q2C_EDIT_PRODUCT_BODY');
			$body = str_replace('{productname}', $prod_values->get('item_name', '', 'RAW') , $body);
			$pod_price = $prod_values->get('multi_cur', array() , "ARRAY");
			$body = str_replace('{price}', $pod_price[$currency], $body);
			$body = str_replace('{sellername}', $loguser->name, $body);
			$body = str_replace('{sku}', $prod_values->get('sku', '', 'RAW') , $body);

			//~ for($i=0; $i < count($multiple_img); $i++)
			//~ {
				//~ print"<pre>"; print_r($multiple_img); die("222882");
				//~ $body	.= '<br><img src="' . JUri::root() . 'images/quick2cart/' . $multiple_img[$i] . '" alt="No image" ><br>';
			//~ }

			if (!empty($multiple_img))
			{
				$multiple_img = (array) $multiple_img;

				foreach($multiple_img as $key=>$img)
				{
					$body	.= '<br><img src="' . JUri::root() . 'images/quick2cart/' . $img[$key] . '" alt="No image" ><br>';
				}
			}
		}

		// New product
		else
		{
			$subject = JText::_('COM_Q2C_PRODUCT_AAPROVAL_SUBJECT');
			$body = JText::_('COM_Q2C_PRODUCT_AAPROVAL_BODY');
			$body = str_replace('{title}', $prod_values->get('item_name', '', 'RAW'), $body);
			$body = str_replace('{sellername}', $loguser->name, $body);
			$desc = $prod_values->get('description', '', 'ARRAY');
			$desc = strip_tags(trim($desc['data']));
			$body = str_replace('{des}', $desc, $body);
			$body = str_replace('{link}', JUri::base() . 'administrator/index.php?option=com_quick2cart&view=products&filter_published=0', $body);

			for ($i = 0;$i < count($multiple_img);$i++)
			{
				$body.= '<br><img src="' . JUri::ROOT() . 'images/quick2cart/' . $multiple_img[$i] . '" alt="No image" ><br>';
			}
		}

		$res = $comquick2cartHelper->sendmail($mailfrom, $subject, $body, $sendto);
	}
}
