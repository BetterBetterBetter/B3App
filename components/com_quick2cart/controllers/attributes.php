<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die();

class Quick2cartControllerAttributes extends quick2cartController
{
	function __construct()
	{
		parent::__construct();
	}

	function delattribute()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$jinput = JFactory::getApplication()->input;
		$pid = $jinput->get('pid');
		$attr_id = $jinput->get('attr_id');
		$model = $this->getModel('attributes');
		$result = $model->delattribute($attr_id);
		$quick2cartModelAttributes = new quick2cartModelAttributes();
		$attributes = $quick2cartModelAttributes->getItemAttributes($pid);

		if (empty($attributes))
		{
			$html = '<thead>
			<tr>
			<th width="35%" align="left"><b>' . JText::_('QTC_ADDATTRI_NAME') . ' </b></th>
			<th width="30%"	align="left"><b>' . JText::_('QTC_ADDATTRI_OPT') . '</b> </th>
			<th width="15%"	align="left"></th>
			</tr>
			</thead>
			<tbody>';
			$html.= '<tr id="empty_attr">
			<td colspan="3">' . JText::_('QTC_ADDATTRI_EMPTY_MSG') . '</td>
			</tr>';
			$data['html'] = $html;
			echo json_encode($html);
		}

		jexit();
	}

	function delattributeoption()
	{
		$jinput = JFactory::getApplication()->input;
		$op_id = $jinput->get('opt_id');
		if (!empty($op_id))
		{

			// get attribute id

			$db = JFactory::getDBO();
			$query = 'SELECT itemattribute_id	From `#__kart_itemattributeoptions`
			WHERE `itemattributeoption_id`=' . $op_id;
			$db->setQuery($query);
			$att_id = $db->loadResult();

			// GET ATT OPTION COUNT

			$query = 'SELECT count(*)	From `#__kart_itemattributeoptions`
			WHERE `itemattribute_id`=' . $att_id;
			$db->setQuery($query);
			$count = $db->loadResult();
			$productHelper = new productHelper();
			if ($count == 1)
			{ // delete attribute with its option
				$productHelper->delWholeAttribute($att_id);
			}
			else
			{
				// DELTE OPTION ONLY

				$productHelper->delWholeAttributeOption($op_id);
			}
		}
	}

	function save()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		JSession::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('attributes');
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		switch ($jinput->get('task'))
		{
		case 'cancel':
			$this->setRedirect('index.php?option=com_quick2cart');
			break;

		case 'save':
			$edit = $post->get('edit');
			$att_detail = $post->get('att_detail', array() , 'ARRAY');
			$result = $model->store($att_detail);
			if ($result)
			{
				$msg = JText::_('QTC_ATTRI_SAVE');
				/*echo '<script type="text/javascript">
				window.setTimeout("closeme();", 300);
				function closeme()
				{
				parent.SqueezeBox.close();
				}

				</script>'; */
				if ($edit === '1')
				{
					echo $edit = 3; //die;
				}

				$this->setRedirect("index.php?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=" . $post->get('product_id') . "&edits=" . $edit . "&client=" . $post->get('client') , $msg);
			}
			else
			{
				$msg = JText::_('QTC_ATTRI_SAVE_PROBLEM');
				$this->setRedirect("index.php?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=" . $post->get('product_id') . "&client=" . $post->get('client') , $msg);
			}

			break;
		}
	} //function save ends
	function addcurrency()
	{
		$jinput = JFactory::getApplication()->input;
		$cur_post = $jinput->post;
		$item_name = $jinput->get('item_name', '', 'STRING');
		$multi_cur = $cur_post->get('multi_cur', array() , 'ARRAY');
		$attdata = $cur_post->get('attdata', array() , 'ARRAY');
		$data = array();
		$originalCount = count($multi_cur);
		$filtered_curr = array_filter($multi_cur, 'strlen'); //  remove empty currencies from multi_curr
		$filter_count = count($filtered_curr);

		if ($item_name && $originalCount == $filter_count)
		{
			$model = $this->getModel('attributes');
			$comquick2cartHelper = new comquick2cartHelper;
			$result = $comquick2cartHelper->saveProduct($cur_post);

			if ($result && !is_numeric($result))
			{
				$data = array(
					'0' => '0',
					'1' => JText::_('QTC_OPTIONS_NOT_SAVE', true)
				);
			}
			else
			{
				$data = array(
					'0' => '1',
					'1' => JText::_('COM_QUICK2CART_ITEM_SAVED_SUCCESSFULLY', true)
				);
			}
		}
		else
		{
			$data = array(
			'0' => '0',
			'1' => JText::_('QTC_OPTIONS_REQUIRED')
			);
		}

		echo json_encode($data);
		jexit();
	}

	function checkSku()
	{
		$jinput = JFactory::getApplication()->input;
		$sku = $jinput->get('sku', '', 'RAW');

		// call to front end controller funtion to make consistant
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'controllers' . DS . 'product.php';
		if (!class_exists('Quick2cartControllerProduct'))
		{

			// require_once $path;

			JLoader::register('Quick2cartControllerProduct', $path);
			JLoader::load('Quick2cartControllerProduct');
		}

		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		echo $res = $Quick2cartControllerProduct->checkSku($sku);
		jexit();
	}

	function addMediaFile()
	{
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		$media_detail = $post->get('prodMedia', array() , 'ARRAY');
		$item_id = $post->get('item_id', '', 'INT');
		$mediafile_id = $post->get('mediafile_id', '', 'INT');
		$productHelper = new productHelper;
		$status = $productHelper->saveProdMediaDetails($media_detail, $item_id, 0);
		$edit = $post->get('edit');
		if (!empty($status) && $status == 1)
		{
			$msg = JText::_('QTC_ATTRI_SAVE_SUCCESSFULL_CN_ADD_MORE');

			// $redirect = JRoute::_('index.php?option=com_quick2cart&view=attributes&layout=media&tmpl=component&item_id='.$item_id,true);

		}
		else
		{
			$msg = JText::_('QTC_MEDIA_SAVE_PROBLEM');
		}

		// $this->setRedirect($redirect,$msg);

		if ($edit === '1')
		{
			$edit = 3;
		}

		$this->setRedirect("index.php?option=com_quick2cart&view=attributes&layout=media&tmpl=component&item_id=" . $item_id . "&edits=" . $edit . "&file_id=" . $mediafile_id, $msg);
	}

	function deleteMediFile()
	{
		// Add Language file.
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

		// get Product_id via ajax url.

		$jinput = JFactory::getApplication()->input;
		$item_id = $jinput->get('pid');

		// Get file id for delete.

		$file_id = $jinput->get('file_id');
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models' . DS . 'attributes.php';
		if (!class_exists('attributes'))
		{
			// require_once $path;
			JLoader::register('attributes', $path);
			JLoader::load('attributes');
		}

		$quick2cartModelAttributes = new quick2cartModelAttributes();
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php';
		if (!class_exists('productHelper'))
		{
			// require_once $path;
			JLoader::register('productHelper', $path);
			JLoader::load('productHelper');
		}

		$productHelper = new productHelper;
		$delFiles = array();
		$delFiles[] = $file_id;
		$productHelper->deleteProductMediaFile($delFiles);
		$attributes = $quick2cartModelAttributes->getItemAttributes($item_id);
		$getMediaDetail = $productHelper->getMediaDetail($item_id);
		if (empty($getMediaDetail))
		{
			$html = '<thead>
						<tr>
							<th width="35%" align="left"><b>' . JText::_('QTC_MEDIAFILE_NAME') . '</b></th>
							<th width="30%"	align="left"><b>' . JText::_('QTC_MEDIAFILE_PURCHASE_REQUIRE') . '</b> </th>
							<th width="15%"	align="left"></th>
						</tr>
					</thead>';
			$html.= '<tr class="empty_media">
					<td colspan="3">' . JText::_('QTC_MEDIAFILE_EMPTY_MSG') . '</td>
				</tr>';
			$data['html'] = $html;
			echo json_encode($html);
		}

		jexit();
	}

	// This function for only edit attribute

	function EditAttribute()
	{
		JHtml::_('behavior.modal', 'a.modal');
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$params = JComponentHelper::getParams('com_quick2cart');
		$jinput = JFactory::getApplication()->input;
		$pid = $jinput->get('pid');
		$attr_id = $jinput->get('att_id');
		$model = $this->getModel('attributes');
		$quick2cartModelAttributes = new quick2cartModelAttributes();
		$attributes = $quick2cartModelAttributes->getItemAttributes($pid);
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers.php';

		if (!class_exists('comquick2cartHelper'))
		{

			// require_once $path;

			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$qtc_base_url = JUri::root();
		$add_link = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=' . $pid;

		// echo count($attributes_info);echo $jinput->get('count');

		$del_link = $qtc_base_url . 'index.php?option=com_quick2cart&controller=attributes&task=delattribute';
		$html = '';
		if (!empty($attributes))
		{
			$invalid_op_price = array();

			// $i = 1;

			foreach($attributes as $attributes)
			{
				if ($attr_id == $attributes->itemattribute_id)
				{
					$html = '<tr class="' . "att_" . $attributes->itemattribute_id . '">
								<td>' . $attributes->itemattribute_name . '</td>
								<td id="' . "att_list_" . $attributes->itemattribute_id . '">';
					$comquick2cartHelper = new comquick2cartHelper;
					$currencies = $params->get('addcurrency');
					$curr = explode(',', $currencies);
					$atri_options = $comquick2cartHelper->getAttributeDetails($attributes->itemattribute_id);
					foreach($atri_options as $atri_option)
					{
						$html.= '<div>';
						$noticeicon = "";
						$opt_str = $atri_option->itemattributeoption_name . ": " . $atri_option->itemattributeoption_prefix;
						$itemnotice = '';
						foreach($curr as $value)
						{
							if (property_exists($atri_option, $value))
							{
								if ($atri_option->$value)
								{
									$opt_str.= $atri_option->$value . " " . $value . ", ";
								}
							}
							else
							{
								$invalid_op_price[$value] = $value; // add current cur
								if (empty($itemnotice))
								{
									$noticeicon = "<i class='icon-hand-right'></i> ";
								}
							}
						}

						$html.= $detail_str = $noticeicon . $opt_str;
						$html.= '</div>';
					}

					$html.= '</td>';
					$edit_link = $add_link . '&attr_id=' . $attributes->itemattribute_id . '&edit=1';
					$del_link = $del_link . '&attr_id=' . $attributes->itemattribute_id;
					$html.= '<td><a  rel="{handler: \'iframe\', size: {x: 550, y: 470}, onClose: function(){EditAttribute(' . $attributes->itemattribute_id . ',' . $pid . ');}}" class="btn btn-mini btn-primary modal qtc_modal" href="' . $edit_link . '"> <i class="icon-pencil-2 icon-white"></i></a>
				 <button type="button" class="btn btn-mini btn-danger "  onclick=\'deleteAttribute("' . $attributes->itemattribute_id . '","' . $pid . '" )\'><i class="icon-trash icon-white"></i></button>
				 </td>
				</tr>';
				}

				// $i++;

			} // end of foreach($attributes as $attributes)
		}

		// echo $html;die;

		$data['html'] = $html;
		echo json_encode($html);
		jexit();
	}

	// This function for only edit attribute

	function AddNewAttribute()
	{
		JHtml::_('behavior.modal', 'a.modal');
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$params = JComponentHelper::getParams('com_quick2cart');
		$jinput = JFactory::getApplication()->input;
		$pid = $jinput->get('pid');

		// $attr_id = $jinput->get('attr_id');

		$model = $this->getModel('attributes');
		$quick2cartModelAttributes = new quick2cartModelAttributes();
		$attributes = $quick2cartModelAttributes->getItemAttributes($pid);

		// print_r($attributes );die;

		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers.php';
		if (!class_exists('comquick2cartHelper'))
		{

			// require_once $path;

			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$qtc_base_url = JUri::root();
		$add_link = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=attribute&tmpl=component&pid=' . $pid;

		// echo count($attributes_info);echo $jinput->get('count');

		$del_link = $qtc_base_url . 'index.php?option=com_quick2cart&controller=attributes&task=delattribute';
		$html = '';
		$count = $jinput->get('count');
		$count = $count - 1;
		if (!empty($attributes))
		{
			$invalid_op_price = array();

			// $i = 1;

			for ($i = 0; $i < count($attributes); $i++)
			{
				if ($i > $count)
				{
					$html.= '<tr class="' . "att_" . $attributes[$i]->itemattribute_id . '">
								<td>' . $attributes[$i]->itemattribute_name . '</td>
								<td id="' . "att_list_" . $attributes[$i]->itemattribute_id . '">';
					$comquick2cartHelper = new comquick2cartHelper;
					$currencies = $params->get('addcurrency');
					$curr = explode(',', $currencies);
					$atri_options = $comquick2cartHelper->getAttributeDetails($attributes[$i]->itemattribute_id);
					foreach($atri_options as $atri_option)
					{
						$html.= '<div>';
						$noticeicon = "";
						$opt_str = $atri_option->itemattributeoption_name . ": " . $atri_option->itemattributeoption_prefix;
						$itemnotice = '';
						foreach($curr as $value)
						{
							if (property_exists($atri_option, $value))
							{
								if ($atri_option->$value)
								{
									$opt_str.= $atri_option->$value . " " . $value . ", ";
								}
							}
							else
							{
								$invalid_op_price[$value] = $value; // add current cur
								if (empty($itemnotice))
								{
									$noticeicon = "<i class='icon-hand-right'></i> ";
								}
							}
						}

						$html.= $detail_str = $noticeicon . $opt_str;
						$html.= '</div>';
					}

					$html.= '</td>';
					$edit_link = $add_link . '&attr_id=' . $attributes[$i]->itemattribute_id . '&edit=1';
					$del_link = $del_link . '&attr_id=' . $attributes[$i]->itemattribute_id;
					$html.= '<td><a  rel="{handler: \'iframe\', size: {x: 550, y: 470}, onClose: function(){EditAttribute(' . $attributes[$i]->itemattribute_id . ',' . $pid . ');}}" class="btn btn-mini btn-primary modal qtc_modal" href="' . $edit_link . '"> <i class="icon-pencil-2 icon-white"></i></a>
				 <button type="button" class="btn btn-mini btn-danger "  onclick=\'deleteAttribute("' . $attributes[$i]->itemattribute_id . '","' . $pid . '" )\'><i class="icon-trash icon-white"></i></button>
				 </td>
				</tr>';
				}

				// $i++;

			} // end of foreach($attributes as $attributes)
		}

		// echo $html;die;

		$data['html'] = $html;
		echo json_encode($html);
		jexit();
	}

	function EditMediFile()
	{

		// Add Language file.

		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$qtc_base_url = JUri::root();

		// get Product_id via ajax url.

		$jinput = JFactory::getApplication()->input;
		$item_id = $jinput->get('pid');

		// Get file id for delete.

		$file_id = $jinput->get('file_id');
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models' . DS . 'attributes.php';
		if (!class_exists('attributes'))
		{

			// require_once $path;

			JLoader::register('attributes', $path);
			JLoader::load('attributes');
		}

		$quick2cartModelAttributes = new quick2cartModelAttributes();
		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php';
		if (!class_exists('productHelper'))
		{

			// require_once $path;

			JLoader::register('productHelper', $path);
			JLoader::load('productHelper');
		}

		$productHelper = new productHelper;
		$delFiles = array();
		$delFiles[] = $file_id;
		$attributes = $quick2cartModelAttributes->getItemAttributes($item_id);
		$getMediaDetail = $productHelper->getMediaDetail($item_id, $file_id);
		$addMediaLink = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=media&tmpl=component&item_id=' . $item_id;
		$html = '';
		$count = $jinput->get('count');
		$count = $count - 1;
		if (!empty($getMediaDetail))
		{
			for ($i = 0; $i < count($getMediaDetail); $i++)
			{
				if ($i > $count)
				{
					$html.= '<tr class="' . "file_" . $getMediaDetail[$i]['file_id'] . '">
							<td>' . $getMediaDetail[$i]['file_display_name'] . '</td>
							<td>';
					$mediaClass = ' badge';
					$purchaseStatus = JText::_('QTC_ADDATTRI_PURCHASE_REQ_NO');
					if (!empty($getMediaDetail[$i]['purchase_required']))
					{
						$mediaClass = ' badge badge-success';
						$purchaseStatus = JText::_('QTC_ADDATTRI_PURCHASE_REQ_YES');
					}

					$html.= '<span class="' . $mediaClass . '">' . $purchaseStatus . '</span>
							</td>';
					$edit_link = $addMediaLink . '&file_id=' . $getMediaDetail[$i]['file_id'] . '&edit=1';
					$del_link = $addMediaLink . '&file_id=' . $getMediaDetail[$i]['file_id'];
					$html.= '<td>
								<a  rel="{handler: \'iframe\', size: {x: 800, y: 550}, onClose: function(){EditFile(' . $getMediaDetail[$i]['file_id'] . ',' . $item_id . ');}}" class="btn btn-mini btn-primary modal qtc_modal" href="' . $edit_link . '"> <i class="icon-pencil-2 icon-white"></i>
								</a>
								<button type="button" class="btn btn-mini btn-danger "  onclick="deleteMediFile(' . $getMediaDetail[$i]['file_id'] . ',' . $item_id . ' )"><i class="icon-trash icon-white"></i></button>

							 </td>
						</tr>';
				}
			}

			$data['html'] = $html;
			echo json_encode($html);
		}

		jexit();
	}
}

