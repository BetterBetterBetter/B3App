<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');

class quick2cartModelAttributes extends JModelLegacy
{

	function getItemAttributes($item_id)
	{
		$db = JFactory::getDBO();

		if (!empty($item_id))
		{
			try
			{
				$query = $db->getQuery(true);
				 $query->select("itemattribute_id,item_id,itemattribute_name,attribute_compulsary,`attributeFieldType`,`attributeFieldType`")
				->from('#__kart_itemattributes')
				->where(" item_id = ". $item_id)
				->order(" itemattribute_id ASC");
				$db->setQuery($query);

				return $db->loadobjectList();

			}
			catch(Exception $e)
			{
				$this->setError($e->getMessage());

				return array();
			}
		}
	}

	function getItemAttributeOptions($attr_id)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT opt.itemattributeoption_name FROM #__kart_itemattributeoptions AS opt WHERE opt.itemattribute_id=' . (int)$attr_id . ' ORDER BY opt.ordering';
		$db->setQuery($query);
		$options = $db->loadColumn();

		return $options;
	}

	function getAttribute()
	{
		$jinput = JFactory::getApplication()->input;
		$id = $jinput->get('attr_id');
		$query = "SELECT itemattribute_name,attribute_compulsary,`attributeFieldType` FROM #__kart_itemattributes  WHERE itemattribute_id=" . (int)$id;
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();

		return $result;
	}

	function getAttributeoption($id = '')
	{
		if (empty($id))
		{
			$jinput = JFactory::getApplication()->input;
			$id = $jinput->get('attr_id');
		}
		$query = 'SELECT opt.itemattributeoption_id, opt.itemattributeoption_name, opt.itemattributeoption_price, opt.itemattributeoption_prefix, 	opt.ordering FROM #__kart_itemattributeoptions AS opt WHERE opt.itemattribute_id=' . (int)$id . ' ORDER BY opt.ordering';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	/**
	 * This function item from item table
	 * @param :: integer item_id
	 * @return :
	 *
	 */

	function deleteItem($item_id)
	{
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__kart_items  WHERE item_id=" . (int)$item_id;
		$this->_db->setQuery($query);
		return $this->_db->execute();
	}
	/**
	 * This function delete attribute and its attribute options
	 *	@param :: integer attribute_id
	 *  @return :
	 *
	 */

	function delattribute($id)
	{
		$query = "DELETE FROM #__kart_itemattributes  WHERE itemattribute_id=" . (int)$id;
		$this->_db->setQuery($query);
		$this->_db->execute();
		$query = "DELETE FROM #__kart_itemattributeoptions  WHERE itemattribute_id=" . (int)$id;
		$this->_db->setQuery($query);
		$this->_db->execute();
	}
	/**
	 * This function delete attribute
	 *	@param :: integer attribute_id
	 *  @return :
	 *
	 */

	function delattributeOnly($id)
	{
		$query = "DELETE FROM #__kart_itemattributes  WHERE itemattribute_id=" . (int)$id;
		$this->_db->setQuery($query);
		return $this->_db->execute();
	}
	/**
	 * This function delete attribute options
	 * @param :: integer attributeOptions_id
	 * @return :
	 *
	 */

	function delattributeoption($id)
	{
		$query = "DELETE FROM #__kart_itemattributeoptions  WHERE itemattributeoption_id=" . (int)$id;
		$this->_db->setQuery($query);
		return $this->_db->execute();
	}
	/**
	 * This function save/update attribute.
	 * @param :: integer attributeOptions_id
	 * @return :
	 *
	 */

	public function store($data, $sku = '')
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		// OnBeforeq2cAttributeSave
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$result = $dispatcher->trigger('OnBeforeq2cAttributeSave', array(
			$data
		));

		if (!empty($result[0]))
		{
			$data = $result[0];
		}
		// to store attribute name in #__kart_itemattributes table

		// Field type= textbox then there will not be any options
		$userFields = array();
		$userFields[] = 'Textbox';
		$userdata = (!empty($data['fieldType']) && in_array($data['fieldType'], $userFields)) ? 1 : 0; // 0: mean dont have options

		if (empty($userdata))
		{
			$ind = 0;

			if (!empty($data['attri_opt']))
			{

				foreach ($data['attri_opt'] as $key => $att_options)
				{
					$ind = $key;
				}
			}
			// if 0 menas :atleast one option is present then only save attribute

			if (!$this->validateAttributeOption($data['attri_opt'], $ind))
			{
				//return false;

			}
		}
		else
		{
		}
		$DelTask = 0;

		if (!empty($data['delete_attri'])) // if WE r deleting option ==> then delete attribute and remove from $data array
		{
			$DelTask = 1;
			$data = $this->deleteAttributeOption($data);
		}

		if (empty($userdata))
		{
			$data = $this->removeInvalideOption($data);
			// if Options r not present

			if (count($data['attri_opt']) == 0)
			{
				$this->noOptionThenDelAttr($data['attri_id']);
				$return = ($DelTask == 1) ? true : false; // if true when delete task
				return $return;
			}
		}
		$row = new stdClass;

		if ($data['attri_name'])
		{
			//$row = new stdClass;
			$row->itemattribute_name = $data['attri_name'];
			$row->attributeFieldType = $data['fieldType'];
			//1. store attribute name

			if (isset($data['iscompulsary_attr']))
			{
				$row->attribute_compulsary = true;
			}
			else
			{
				$row->attribute_compulsary = false;
			}

			// While updating the attribute

			if (!empty($data['attri_id']))
			{
				//@TODO VM: if fieldtype = text then delete all attribute option (in db)
				$row->itemattribute_id = $data['attri_id'];

				if (!$this->_db->updateObject('#__kart_itemattributes', $row, "itemattribute_id"))
				{
					echo $this->_db->stderr();
					return false;
				}
				/*if ATTRIB IS GOING TO UPDATE THEN COMPARE (DB OPTIONS AND POST OPTION) ,DEL EXTRA OPTION FROM DB ONLY */
				$att_option_ids = array();
				//GETTING OPTION ID'S ARRAY

				foreach ($data['attri_opt'] as $option)
				{
					$att_option_ids[] = $option['id'];
				}
				//DELETING EXTRA OPTIONS

				if (!empty($att_option_ids))
				{
					$productHelper = new productHelper();
					$productHelper->deleteExtraAttributeOptions($data['attri_id'], $att_option_ids);
				}
			}
			else
			{ // for new attribute

				//load Attributes model  // REQUIRE WHEN CALLED FROM BACKEND
				$comquick2cartHelper = new comquick2cartHelper;
				$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models' . DS . 'cart.php';
				$Quick2cartModelcart = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelcart");
				//$Quick2cartModelcart =  new Quick2cartModelcart;

				if (!empty($data['item_id']))
				{
					$item_id = $data['item_id'];
				}
				elseif (!empty($data['product_id']) && !empty($data['client']))
				{
					$item_id = $Quick2cartModelcart->getitemid($data['product_id'], $data['client']);
				}
				else
				if (!empty($data['sku']))
				{
					$item_id = $Quick2cartModelcart->getitemid(0, $data['client'], $data['sku']);
				}
				$row->item_id = $item_id;
				/*$row->parent = $data['client'];
				 $row->product_id = $data['product_id'];*/


				//~ // If checked
				//~ if (isset($data['iscompulsary_attr']))
				//~ {
					//~ $row->attribute_compulsary = true;
				//~ }
				//~ else
				//~ {
					//~ $row->attribute_compulsary = false;
				//~ }

				if (!$this->_db->insertObject('#__kart_itemattributes', $row, 'itemattribute_id'))
				{
					echo $this->_db->stderr();
					return false;
				}
			}
		}

		if (!empty($userdata))
		{
			// add extra option for user data
			$quick2cartModelAttributes = new quick2cartModelAttributes;
			//	$quick2cartModelAttributes->addDefaultEntryForUserFields($row->itemattribute_id,$row->itemattribute_name);
			$option = array();
			/*$option['itemattribute_id'] = $row->itemattribute_id;
			$option['itemattributeoption_name'] = $row->itemattribute_name;
			$option['itemattributeoption_price'] = 0;
			$option['itemattributeoption_prefix'] = '+';
			$option['ordering'] = 1;*/
			$op = $data['attri_opt'][0];
			$op['name'] = $row->itemattribute_name;
			//$option['itemattributeoption_price'] = 0;
			$op['prefix'] = '+';

			foreach ($op['currency'] as $key => $curr)
			{
				$op['currency'][$key] = 0;
			}
			$data['attri_opt'][0] = $op; // set default option to data

			//	return true;

		}
		//2. to store attribute option in #__itemattributeoptions_ table

		foreach ($data['attri_opt'] as $key => $attri_opt)
		{

			if ($attri_opt['name'] && $attri_opt['currency'] && $attri_opt['prefix'] && $attri_opt['order'])
			{
				$opt = new stdClass;
				$opt->itemattributeoption_name = $attri_opt['name'];
				$currkeys = array_keys($attri_opt['currency']); //make array of currency keys
				$currkey = $currkeys[0];
				$opt->itemattributeoption_price = $attri_opt['currency'][$currkey];
				$opt->itemattributeoption_prefix = $attri_opt['prefix'];
				$opt->ordering = $attri_opt['order'];
				// UPDATING ATT OPTION

				if (!empty($attri_opt['id']))
				{
					// update attribute option
					$opt->itemattributeoption_id = $attri_opt['id'];

					if (!$this->_db->updateObject('#__kart_itemattributeoptions', $opt, 'itemattributeoption_id'))
					{
						echo $this->_db->stderr();

						return false;
					}
					else
					{
						// After success update table #__kart_option_currency
						$db = JFactory::getDBO();
						$query = "select * from `#__kart_option_currency` where itemattributeoption_id=" . (int)$opt->itemattributeoption_id;
						$db->setQuery($query);
						$result = $db->loadAssocList();

						if ($result)
						{

							foreach ($attri_opt['currency'] as $key => $value)
							{
								$flag = - 1;
								$updateid = - 1; // To check currency field is present or not for that product

								foreach ($result as $dbkey => $dbvalue)
								{

									if ($key == $dbvalue['currency'])
									{
										$flag = 1;
										//print"FGlag =" . $flag;
										$updateid = $dbvalue['id'];
										break;
									}
								}

								if ($flag == 1 && $updateid) // found currency so update updateid row
								{
									$updateobj = new stdClass;
									$updateobj->id = $updateid;
									$updateobj->price = $value;

									if (!$this->_db->updateObject('#__kart_option_currency', $updateobj, 'id'))
									{
										echo $this->_db->stderr();
										return false;
									}
								}
								else
								// not found so insert i.e we r going to add currency for that artical/product

								{
									$updateobj = new stdClass;
									$updateobj->id = NULL;
									$updateobj->itemattributeoption_id = (int)$opt->itemattributeoption_id;
									$updateobj->currency = $key;
									$updateobj->price = $value;

									if (!$this->_db->insertObject('#__kart_option_currency', $updateobj, 'id'))
									{ //if error to insert
										echo $this->_db->stderr();
										return false;
									}
								}
							}
						}
						else
						{

							foreach ($attri_opt['currency'] as $key => $value)
							{
								$currobj = new stdClass;
								$currobj->id = NULL;
								$currobj->itemattributeoption_id = (int)$opt->itemattributeoption_id;
								$currobj->currency = $key;
								$currobj->price = $value;

								if (!$this->_db->insertObject('#__kart_option_currency', $currobj, 'id'))
								{
									echo $this->_db->stderr();
									return false;
								}
							}
						}
					}
				}
				else
				{
					// adding new  ATT OPTION
					$opt->itemattribute_id = $row->itemattribute_id;

					if (!$this->_db->insertObject('#__kart_itemattributeoptions', $opt, 'itemattributeoption_id'))
					{ //if error to insert
						echo $this->_db->stderr();
						return false;
					}
					else
					{ // if INSERTED AND NOT ERROR THEN STORE OPTION CURRENCY

						//add attribute option to DB
						$insert_id = $opt->itemattributeoption_id; // get last inserted id

						foreach ($attri_opt['currency'] as $key => $value)
						{
							$option = new stdClass;
							$option->id = NULL;
							$option->itemattributeoption_id = (int)$insert_id;
							$option->currency = $key;
							$option->price = (float)$value;

							if (!$this->_db->insertObject('#__kart_option_currency', $option, 'id'))
							{ //if error to insert
								echo $this->_db->stderr();
								return false;
							}
						}
					}
				}
			}
		} // end of foreach option
		return true;
		//$mainframe->redirect('index.php?option=com_broadcast&view=settings', $msg);

	} //store() ends

	/**
	 * Save the product basic option.
	 *
	 * @param   object  $curr_post  Post objec.
	 *
	 * @since   2.2
	 * @return   null
	 */

	function storecurrency($curr_post)
	{
		$itemname = $curr_post->get('item_name', '', 'STRING');
		$store_id = $curr_post->get('store_id', '', 'STRING');
		$pid = $curr_post->get('pid', '', 'STRING');

		//@TODO check - ^sanjivani
		if (empty($pid))
		{
			// For native product manager
			$pid = $curr_post->get('item_id', '', 'STRING');
		}

		//@TODO check - ^sanjivani
		$client = $curr_post->get('client', '', 'STRING');
		$sku = $curr_post->get('sku', '', 'RAW');
		$res = '';
		$message = '';
		$comquick2cartHelper = new comquick2cartHelper;
		$db = JFactory::getDbo();
		$params = JComponentHelper::getParams('com_quick2cart');
		$kart_curr_param = $params->get('addcurrency'); // used to store in kart_item table
		$kart_curr_param_array = explode(',', $kart_curr_param);
		$kart_item_curr = $kart_curr_param_array[0];
		$quick2cartModelAttributes = new quick2cartModelAttributes();
		//@TODO check - ^sanjivani

		//$item_id = $quick2cartModelAttributes->getitemid($pid, $client, $sku);
		$item_id = $quick2cartModelAttributes->getitemid($pid, $client);
		$img_dimensions = array();
		$img_dimensions[] = 'small';
		$img_dimensions[] = 'medium';
		$img_dimensions[] = 'large';
		$image_path = array();
		// STORING ALL IMAGES images upladed (on new or edit)

		foreach ($_FILES as $key => $imgfile)
		{

			if (!empty($imgfile['name']))
			{
				$image_path[] = $comquick2cartHelper->imageupload($key, $img_dimensions); //$imgfile['name']);

			}
		}
		$qtc_prodImgs = $curr_post->get('qtc_prodImg', array() , 'ARRAY');
		// ON EDIT

		if (!empty($qtc_prodImgs))
		{

			foreach ($image_path as $newImg)
			{
				$qtc_prodImgs[] = $newImg;
			}
			//$image_path = $curr_post->get('qtc_prodImg', array(), 'ARRAY'); //$curr_post['qtc_prodImg'];
			$image_path = array_filter($qtc_prodImgs, "trim");
		}

		if (!empty($image_path))
		{
			$image_path = json_encode($image_path);
		}
		else
		{
			$image_path = '';
		}
		//@TODO save images and store in DB
		$images = "";
		//GETTING ATTRIBUTE DETAILS,multi currency and discount details
		$att_detail = $curr_post->get('att_detail', array() , 'ARRAY');
		$multi_cur = $curr_post->get('multi_cur', array() , 'ARRAY');
		$multi_dis_cur = $curr_post->get('multi_dis_cur', array() , 'ARRAY');

		if (!$item_id)
		{
			$state = $curr_post->get('state');

			if (empty($state))
			{
				$state = 0;
			}
			// Call the trigger to add extra field in product page.
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin("system");
			$result = $dispatcher->trigger("beforeSavingProductBasicDetail", array(
				$curr_post,
				'insert'
			));
			// Save new product
			$item_id = $this->storeInKartItem('insert', $image_path, $multi_cur[$kart_item_curr], $curr_post);
		}
		else
		{
			$state = $curr_post->get('state'); // dont set default value as 1 (require for unpublish)

			if (isset($state))
			{
				$state = $state;
			}
			$productHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE . DS . "components" . DS . "com_quick2cart" . DS . "helpers" . DS . "product.php", "productHelper");
			$productHelper->deleteNotReqProdImages($item_id, $image_path);
			// Call the trigger to add extra field in product page.
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin("system");
			$result = $dispatcher->trigger("beforeSavingProductBasicDetail", array(
				$curr_post,
				'update'
			));
			$item_id = $this->storeInKartItem('update', $image_path, $multi_cur[$kart_item_curr], $curr_post);
		}
		$message = $item_id;
		$query = "SELECT * FROM #__kart_base_currency  WHERE item_id = " . (int)$item_id;
		$db->setQuery($query);
		$res = $db->loadAssocList();
		//if there is a entry in kart_base_currency table

		if ($res)
		{
			foreach ($multi_cur as $cur_name => $cur_value)
			{
				$db = JFactory::getDBO();
				$flag = 0;

				foreach ($res as $k => $v)
				{
					if ($cur_name == $v['currency'])
					{
						$flag = 1; // take currency value frm post and match whith Db currency
						break;
					}
				}

				if ($flag == 1)
				{
					$dis_curr = (!empty($multi_dis_cur[$cur_name]) ? (float)$multi_dis_cur[$cur_name] : 'NULL');
					//if (isset( $curr_post['multi_dis_cur'][$cur_name]) && $curr_post['multi_dis_cur'][$cur_name] != '' ){
					$dis_curr = " , discount_price=" . $dis_curr;
					//}
					$query = "UPDATE #__kart_base_currency SET price=" . (float)$cur_value . " " . $dis_curr . " WHERE `item_id`=" . (int)$item_id . " AND `currency`='" . $cur_name . "'";
					$db->setQuery($query);
					$update = $db->execute();
					/*if ($update)
					print"UPDATE";
					else
					print"NOT UPDATE";*/
				}
				else
				{
					$items = new stdClass;
					$items->item_id = $item_id;
					$items->currency = $cur_name; // eg INR,usd
					$items->price = $cur_value;
					//if (isset( $curr_post['multi_dis_cur'][$cur_name]) && $curr_post['multi_dis_cur'][$cur_name] != '' ){
					$items->discount_price = (!empty($multi_dis_cur[$cur_name]) ? (float)$multi_dis_cur[$cur_name] : 'NULL');
					//}

					if (!$db->insertObject('#__kart_base_currency', $items))
					{
						$messagetype = 'notice';
						$message = JText::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
					}
				}
			}
		}
		else
		{
			//insert query

			foreach ($multi_cur as $cur_name => $cur_value) //curr_post contain INR ,USD etc .....

			{
				$items = new stdClass;
				$items->item_id = $item_id;
				$items->currency = $cur_name; // eg INR,usd
				$items->price = $cur_value; // currencey value

				if (isset($multi_dis_cur[$cur_name]) && $multi_dis_cur[$cur_name] != '')
				{
					$items->discount_price = $multi_dis_cur[$cur_name];
				}

				if (!$db->insertObject('#__kart_base_currency', $items))
				{
					$messagetype = 'notice';
					$message = JText::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
				}
			}
		}

		return $message;
	} //end of storecurrency

	public function storeInKartItem($operation, $images, $price, $curr_post)
	{
		$db = JFactory::getDbo();
		$params = JComponentHelper::getParams('com_quick2cart');
		$on_editor = $params->get('enable_editor', 0);
		$kart_item = new stdClass;
		$kart_item->parent = $curr_post->get('client', '', 'STRING');
		$kart_item->store_id = $curr_post->get('store_id', '', 'INT');
		//@TODO check - ^manoj

		//$kart_item->store_id = $curr_post->get('current_store','','INT');
		$kart_item->product_id = $curr_post->get('pid', '', 'INT');
		$kart_item->name = $curr_post->get('item_name', '', 'STRING');
		$kart_item->price = $price;
		$kart_item->category = $curr_post->get('prod_cat', '', 'INT');
		$kart_item->sku = $curr_post->get('sku', '', 'RAW');
		$des = $curr_post->get('description', array() , 'ARRAY');

		if (!$on_editor)
		{
			// Remove html when editor is OFF
			$kart_item->description = !empty($des['data']) ? strip_tags($des['data']) : '';
		}
		else
		{
			$kart_item->description = !empty($des['data']) ? $des['data'] : '';
		}

		$kart_item->video_link = $curr_post->get('youtube_link', '', 'STRING');

		// #40581 = temporary fix (For zoo state field is overlapping with item's state field)
		$stateField = $curr_post->get('qtcProdState', '' ,'INT');

		if ($stateField === 0 || $stateField === 1)
		{
			$kart_item->state = $stateField;
		}
		else
		{
			$kart_item->state = $curr_post->get('state','0','INT');
		}

		$stock	   = $curr_post->get('stock');
		// @HAVE TO CODE TO STORE IMAGES
		if ( $stock !== "")    // if stock is present it may be 0 But not NULL
		{
			$kart_item->stock=$stock;
		}
		$kart_item->metadesc = $curr_post->get('metadesc', '', 'STRING');
		$kart_item->metakey = $curr_post->get('metakey', '', 'STRING');
		// Added by sanjivani
		$kart_item->item_length = $curr_post->get('qtc_item_length', '', 'FLOAT');
		$kart_item->item_width = $curr_post->get('qtc_item_width', '', 'FLOAT');
		$kart_item->item_height = $curr_post->get('qtc_item_height', '', 'FLOAT');
		$kart_item->item_length_class_id = $curr_post->get('length_class_id', '', 'INT');
		$kart_item->item_weight = $curr_post->get('qtc_item_weight', '', 'FLOAT');
		$kart_item->item_weight_class_id = $curr_post->get('weigth_class_id', '', 'INT');
		$kart_item->taxprofile_id = $curr_post->get('taxprofile_id', '', 'INT');
		$kart_item->shipProfileId = $curr_post->get('qtc_shipProfile', '', 'INT');

		$min_quantity = $curr_post->get('min_item', 1, 'INT');
		$max_quantity = $curr_post->get('max_item', 999, 'INT');

		if ($min_quantity == 0)
		{
			$kart_item->min_quantity = 1;
		}
		else
		{
			$kart_item->min_quantity = $min_quantity;
		}

		if ($max_quantity == 0)
		{
			$kart_item->max_quantity = 999;
		}
		else
		{
			$kart_item->max_quantity = $max_quantity;
		}

		$kart_item->slab = $curr_post->get('item_slab', '', 'INT');

		if ($operation == 'insert')
		{
			if (!empty($images))
			{
				$kart_item->images = $images;
			}
			$kart_item->cdate = date("Y-m-d");
			$kart_item->mdate = date("Y-m-d");

			if (!$db->insertObject('#__kart_items', $kart_item, 'item_id'))
			{
				$messagetype = 'notice';
				$message = JText::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
			}
			else
			{
				$inserid = $db->insertid();

				if ($kart_item->parent == "com_quick2cart")
				{
					$quick2cartModelAttributes = new quick2cartModelAttributes();
					$quick2cartModelAttributes->copyItemidToProdid($inserid);
				}
				// Add point to Community extension when product added into Quick2cart
				$params = JComponentHelper::getParams('com_quick2cart');
				$integrate_with = $params->get('integrate_with', 'none');
				$user = JFactory::getUser();

				if ($integrate_with != 'none')
				{
					$streamAddProd = $params->get('streamAddProd', 1);
					$point_system = $params->get('point_system', '0');

					// According to integration create social lib class obj.
					$comquick2cartHelper = new comquick2cartHelper;
					$libclass = $comquick2cartHelper->getQtcSocialLibObj();

					// Add in activity.
					if ($streamAddProd)
					{
						$prodLink = '<a class="" href="' . $comquick2cartHelper->getProductLink($inserid) . '">' . $kart_item->name . '</a>';
						$store_info = $comquick2cartHelper->getSoreInfo($kart_item->store_id);
						$storeLink = '<a class="" href="' . JUri::root() . substr(JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=store&store_id=' . $kart_item->store_id) , strlen(JUri::base(true)) + 1) . '">' . $store_info['title'] . '</a>';
						$originalMsg = JText::sprintf('QTC_ACTIVITY_ADD_PROD', $prodLink, $storeLink);
						$libclass->pushActivity($user->id, $act_type = '', $act_subtype = '', $originalMsg, $act_link = '', $title = '', $act_access = '');
					}

					// Add points
					$point_system = $params->get('point_system');
					$options['extension'] = 'com_quick2cart';

					if ($integrate_with == "EasySocial")
					{
						$options['command'] = 'add_product';
					}
					elseif ($integrate_with == "JomSocial")
					{
						$options['command'] = 'addproduct.points';
					}
					$libclass->addpoints($user, $options);
				}
			}
			return !empty($inserid) ? $inserid : "";
		}
		elseif ($operation == 'update')
		{
			$kart_item->images = $images;
			$kart_item->mdate = date("Y-m-d");
			$pid = $curr_post->get('pid', '', 'STRING');
			$client = $curr_post->get('client', '', 'STRING');
			$sku = $curr_post->get('sku', '', 'RAW');
			$res = '';
			$quick2cartModelAttributes = new quick2cartModelAttributes();
			$item_id = $quick2cartModelAttributes->getitemid($pid, $client);
			$db = JFactory::getDBO();
			$kart_item->item_id = $item_id;

			if (!$db->updateObject('#__kart_items', $kart_item, 'item_id'))
			{
				$message = JText::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
			}
			return $item_id;
			//return $message;

		}
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	/*function getCurrencies($pid) // currently not needed
	{
	$db =& JFactory::getDBO();
	$query = "SELECT currency,price FROM #__kart_base_currency  WHERE item_id = ".(int) $pid;

	$db->setQuery($query);
	$result = $db->loadAssocList();
	return $result;

	}*/

	function getCurrenciesvalue($pid, $curr, $client, $item_id = '')
	{

		if (empty($item_id))
		{
			$quick2cartModelAttributes = new quick2cartModelAttributes();
			$item_id = $quick2cartModelAttributes->getitemid($pid, $client);
		}
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__kart_base_currency  WHERE item_id = " . (int)$item_id . " AND currency='" . $curr . "'";
		$db->setQuery($query);
		$result = $db->loadAssocList();
		return $result;
	}

	function getitemid($product_id = 0, $client, $sku = "")
	{
		$db = JFactory::getDBO();

		if (!empty($product_id))
		{
			$query = "SELECT `item_id` FROM `#__kart_items`  where `product_id`=" . (int)$product_id . " AND parent='$client'";
		}
		else
		{
			$query = "SELECT `item_id` FROM `#__kart_items`  where parent='" . $client . "' AND sku=\"" . $sku . "\"";
		}
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

	function getItemvalue($pid, $client)
	{
		$db = JFactory::getDBO();
		$query = "SELECT `name` FROM `#__kart_items`  where `product_id`=" . (int)$pid . " AND parent='$client'";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

	function getOption_currencyValue($iap_id, $curr)
	{
		//$iap_id = item attribute option id
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__kart_option_currency WHERE `itemattributeoption_id`=" . (int)$iap_id . " AND currency='" . $curr . "'";
		$db->setQuery($query);
		$result = $db->loadAssocList();
		return $result;
	}
	/*
	 * This function update item stock,min_quantity,max_quantity from kart_items table ()
	 *
	 * @param  $pid product_id
	 * @param  $Stock status of item
	 * @param $min_qty minimum quanty to buy
	 * @param $max_qty max quanty to buy
	 * @param $max_qty max quanty to buy
	 * @client client Parent eg zoo,com_content etc.
	 **/

	/*function addItemStock($pid, $stock, $client, $min_qty, $max_qty)
	{
		$message = '';

		if (empty($min_qty)) $min_qty = 1;

		if (empty($max_qty)) $max_qty = 999;
		$quick2cartModelAttributes = new quick2cartModelAttributes();
		$item_id = $quick2cartModelAttributes->getitemid($pid, $client);
		$db = JFactory::getDBO();
		$updateobj = new stdClass;
		$updateobj->item_id = $item_id;

		if ($stock >= 0 && $stock != "") // if stock is present it may be 0 But not NULL

		{
			$updateobj->stock = $stock;
		}
		$updateobj->min_quantity = $min_qty;
		$updateobj->max_quantity = $max_qty;

		if (!$db->updateObject('#__kart_items', $updateobj, 'item_id'))
		{
			$message = JText::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
		}
		return $message;
	}*/
	/*This function check whether attribute option is valid or not ( Data is sufficient or not)
	To save attribute atleast first option if filled
	*/

	function validateAttributeOption($options, $index = 0)
	{

		if ($options[$index]['name'] && $options[$index]['prefix'] && $options[$index]['order'])
		{
			$noofcurr = count($options[$index]['currency']); // of currency text count
			$filledcurr = array_filter($options[$index]['currency'], 'strlen');
			$filledcurrCount = count($filledcurr); // count after removing empty fields

			if ($filledcurrCount == $noofcurr)
			{
				return true;
			}
			return false;
		}
		return false;
	}
	/*This function delete attributeOptions
	@param $data :: array() contain info about attribute
	*/

	function deleteAttributeOption($data)
	{
		$delete_attri = $data->get('delete_attri');
		$attri_id = $data->get('attri_id');

		if (!empty($delete_attri) && !empty($attri_id))
		{
			// select
			$del_ids = explode(',', trim($data->get('delete_attri', '', 'RAW')));
			$del_ids_array = array_filter($del_ids, 'strlen'); // remove only null/ empty element (keep zero)
			$del_ids = implode(',', $del_ids_array);
			$db = JFactory::getDBO();
			//step 1. Delete attribute Option
			$query = "DELETE FROM `#__kart_itemattributeoptions` where `itemattribute_id`=" . $data->get('attri_id') . " AND  `itemattributeoption_id` IN (" . $del_ids . ")";
			$db->setQuery($query);

			if (!$db->execute())
			{
				print $this->_db->getErrorMsg();
				return false;
			}
			//step 2: after successful deletion :: Remove deleted option from data array
			$attri_opt = $data->get('attri_opt', array() , 'ARRAY');

			foreach ($attri_opt as $key => $option)
			{

				if (in_array($option['id'], $del_ids_array))
				{
					unset($attri_opt[$key]);
				}
			}
		} // end of if (!empty($data->get('delete_attri'  ....
		return $data;
	}
	/* This function delete attribute if doesn't have options
	$param att_id :: int  attribute id
	$return
	*/

	function noOptionThenDelAttr($att_id)
	{

		if (!empty($att_id))
		{
			$db = JFactory::getDBO();
			$query = "Select count(*) from `#__kart_itemattributeoptions` where itemattribute_id=" . $att_id;
			$db->setQuery($query);
			$count = $db->loadResult();

			if ($count == 0) // delete attribute

			{
				$query = " delete from `#__kart_itemattributes` where itemattribute_id=" . $att_id;
				$db->setQuery($query);

				if (!$db->execute())
				{
					return $this->_db->getErrorMsg();
				}
			}
		}
	}
	/** Remove invalide options from attrbuteoption list
	 * $data array() :: formatted attribute detail array
	 *
	 */

	function removeInvalideOption($data)
	{

		foreach ($data['attri_opt'] as $key => $options)
		{
			$status = $this->validateAttributeOption($data['attri_opt'], $key);

			if ($status == false)
			{ // remove option from data

				//unset($data['attri_opt'][$key]);
				unset($options[$key]);
			}
		}
		return $data;
	}
	/**
	 * This function return product details
	 *
	 */

	function getItemDetail($product_id = 0, $client = '', $item_id = "")
	{
		$db = JFactory::getDBO();
		$colList = " `item_id`,`parent`,`product_id`,`store_id`,`name`,`stock`,`min_quantity`,`max_quantity`,`category`,`sku`,`images`,`description`,
			`video_link`,`state`,`featured` ";
		$colList = "*";

		if (!empty($item_id))
		{
			$query = 'SELECT ' . $colList . ' FROM `#__kart_items`  where `item_id`=' . (int)$item_id;
			$db->setQuery($query);
		}
		else
		{
			$query = 'SELECT ' . $colList . ' FROM `#__kart_items`  where `product_id`=' . (int)$product_id . " AND parent='$client'";
		}
		$db->setQuery($query);
		$result = $db->loadAssoc();
		return $result;
	}
	/**
	 * This function copy itemid to parent id
	 *
	 *
	 */

	function copyItemidToProdid($item_id)
	{
		$db = JFactory::getDBO();
		$kart_item = new stdClass;
		$kart_item->product_id = $item_id;
		$kart_item->item_id = $item_id;

		if (!$db->updateObject('#__kart_items', $kart_item, 'item_id'))
		{
			$message = JText::_('QTC_PARAMS_SAVE_FAIL') . " - " . $db->stderr();
		}
	}
	/**
	 *
	 *
	 */
	/*		function addDefaultEntryForUserFields($attribute_id,$optionName) {
	$db = JFactory::getDBO();
	$option = new stdClass;
	$option->itemattribute_id = $attribute_id;
	$option->itemattribute_id = $attribute_id;

	if (!$db->insertObject('#__kart_itemattributeoptions', $opt, 'itemattributeoption_id'))
	{//if error to insert
	echo $this->_db->stderr();
	return false;
	}

	}
	*/
}
?>
