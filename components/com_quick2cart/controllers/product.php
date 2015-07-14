<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

$lang = JFactory::getLanguage();
//$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

class Quick2cartControllerProduct extends quick2cartController
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$comquick2cartHelper = new comquick2cartHelper;

		$this->my_products_itemid    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category&layout=my');
		$this->add_product_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=product&layout=default');

		parent::__construct($config);
	}

	// + manoj
	function addNew()
	{
		$link = JRoute::_('index.php?option=com_quick2cart&view=product&layout=default&Itemid=' . $this->add_product_itemid, false);

		$this->setRedirect($link);
	}

	// + manoj
	function edit()
	{
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);

		$comquick2cartHelper = new comquick2cartHelper;
		$edit_link = $comquick2cartHelper->getProductLink($cid[0], 'editLink');

		$this->setRedirect($edit_link);
	}

	function save($saveClose=0)
	{
		$params   = JComponentHelper::getParams('com_quick2cart');
		$app      = JFactory::getApplication();
		$jinput   = $app->input;
		$cur_post = $jinput->post;

		$sku = $cur_post->get('sku','',"RAW");
		$sku = trim($sku);

		$current_store = $cur_post->get('current_store');

		if (!empty($current_store))
		{
			$app->setUserState('current_store', $current_store);
		}

		$item_name = $jinput->get('item_name','','STRING');
		// $currencydata = $cur_post['multi_cur'];
		$pid     = $jinput->get('pid',0,'INT');
		$client  = 'com_quick2cart';
		$stock   = $jinput->get('stock','','INTEGER');
		$min_qty = $jinput->get('min_item');
		$max_qty = $jinput->get('max_item');
		$cat     = $jinput->get('prod_cat','','INTEGER');
		//$sku   = $jinput->get('sku');

		$on_editor = $params->get('enable_editor',0);

		if (empty($on_editor))
		{
			$des = $jinput->get('description', '', 'STRING');
		}
		else
		{
			$des_data = $jinput->get('description', array(), "ARRAY");
			$des = $des_data["data"];
		}

		$youtubleLink = $jinput->get('youtube_link', '', "RAW");
		$store_id = $jinput->get('store_id');
		$data = array();

		// Get currency field count
		$multi_curArray = $cur_post->get('multi_cur', array(), 'ARRAY');
		$originalCount  = count($multi_curArray);

		//  Remove empty currencies from multi_curr
		$filtered_curr = array_filter($multi_curArray, 'strlen');

		// Get currency field count after filter empty allow 0
		$filter_count = count($filtered_curr);

		if ($item_name &&  $originalCount==$filter_count)
		{
			$model = $this->getModel('attributes');

			//@TODO REMOVE ALL PARAMETER AND SEND FORMATEED POST DATEA
			$comquick2cartHelper = new comquick2cartHelper;
			$cur_post->set('saveAttri', 1);  // whether have to save attributes or not
			$cur_post->set('saveMedia', 1);

			//$item_id = $comquick2cartHelper->saveProduct($pid,$client,$current_store,$item_name,$cur_post,$stock,$min_qty,$max_qty,$cat,$sku,$des,$youtubleLink);
			// Code done by sanjivani
			//$jinput->set('saveAttri',1);  // whether have to save attributes or not
			//$jinput->set('saveMedia',1);

			$item_id = $comquick2cartHelper->saveProduct($cur_post);

			if (is_numeric($item_id))
			{
				/*
				$prodmodel = $this->getModel('product');

				// Added by Sneha
				$admin_app = $params->get('admin_approval');
				$url_item_id = $jinput->get('item_id');

				if ($admin_app == 1 && $url_item_id == '')
				{
					// While saving new product and admin approval set to 1
					$prodmodel->SendMailToAdminApproval($cur_post, $item_id, $newProduct = 1);
					$prodmodel->SendMailToOwner($cur_post);
				}

				$on_edit = $params->get('mail_on_edit');

				if ($on_edit == 1 && $url_item_id != '')
				{
					// while editing new product and admin approval set to 0
					$prodmodel->SendMailToAdminApproval($cur_post, $item_id, $newProduct = 0);
				}
				*/

				//$prodmodel->StoreAllAttribute($item_id,$cur_post['att_detail'],$cur_post['sku'],'com_quick2cart'); // already stored in saveproduct funtion

				if ($saveClose==1)
				{
					return 1;
				}
				//End Added by Sneha

				$app->setUserState('item_id', $item_id);
				$this->setRedirect(JUri::base() . "index.php?option=com_quick2cart&view=product&item_id=".$item_id, JText::_('C_SAVE_M_S'));
			}
			else
			{
				//save  attribute if any $msg = JText::_('C_SAVE_M_NS');
				$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=product", JText::_('C_SAVE_M_NS') );
			}
		}
		else
		{
			$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=product", JText::_('C_FILL_COMPULSORY_FIELDS') );
		}
	}

	function checkSku($sku = '')
	{
		$ajaxCall = 0;

		if (empty($sku))
		{
			$ajaxCall = 1;
		}

		if (empty($sku)) {
			$jinput=JFactory::getApplication()->input;
			$sku = $jinput->get('sku', '', 'RAW');
		}
		$model=$this->getModel('product');
		$itemid = $model->getItemidFromSku($sku);

		$return ='';
		if (!empty($itemid))
		{
			$return = '1';
		}

		if ($ajaxCall == 1)
		{
			// Ajax call.
			echo $return;
			jexit();
		}
		return $return;
	}

	function saveAndClose()
	{
		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		$Quick2cartControllerProduct->save(1);
		$this->setRedirect(JUri::base() . "index.php?option=com_quick2cart&view=category&layout=my", JText::_('C_SAVE_M_S'));
	}

	function saveAndNew()
	{
		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		$Quick2cartControllerProduct->save(1);
		$this->setRedirect(JUri::base() . "index.php?option=com_quick2cart&view=product", JText::_('C_SAVE_M_S'));
	}

	function cancel()
	{
		$link = JRoute::_('index.php?option=com_quick2cart&view=category&layout=my&Itemid=' . $this->my_products_itemid, false);

		$this->setRedirect($link);
	}

	/**
	 * This functio upload product media file called via ajax
	 * */
	function mediaUpload()
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$path=JPATH_SITE.DS."components".DS."com_quick2cart".DS."helpers".DS."media.php";
		$mediaHelper = $comquick2cartHelper->loadqtcClass($path,'qtc_mediaHelper');
		$mediaHelper->uploadProdFiles();
	}

	/** This function starts download*/
	function downStart()
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		$productHelper = new productHelper;
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$myDonloadItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=downloads');
		$jinput = JFactory::getApplication()->input;
		$file_id = $jinput->get('fid',0,'INTEGER');
		$strorecall = $jinput->get('strorecall',0,'INTEGER');
		$guest_email = $jinput->get('guest_email','','RAW');
		$orderid = $jinput->get('orderid',0,'INTEGER');
		$order_item_id = $jinput->get('order_item_id',0,'INTEGER');
		$authorize = $productHelper->mediaFileAuthorise($file_id,$strorecall,$guest_email,$order_item_id);

 		if (!empty($authorize['validDownload'])  && $authorize['validDownload']==1)
		{
			$productHelper = new productHelper;
		 	$filepath = $productHelper->getFilePathToDownload($file_id);

		 	// Download will start
			$down_status = $productHelper->download($filepath,'','',0);

			if ($down_status === 2)
			{
				// If filepath not exists The requested download file does not exists
				JFactory::getApplication()->enqueueMessage(JText::_('QTC_DOWNLOAD_FILEPATH_NOTEXISTS'), 'error');
				$mainframe->redirect(JUri::root().substr(JRoute::_('index.php?option=com_quick2cart&view=downloads&Itemid='.$myDonloadItemid),strlen(JUri::base(true))+1));
			}

			// Update file details  ( not for free files)
			elseif (!empty($authorize['orderItemFileId']))
			{
				// YOU WILL GET FOR THIS FIELD ONLY FOR PURCHASE REQUIRED FILE
				$productHelper->updateFileDownloadCount($authorize['orderItemFileId']);  // kart_orderItemFiles tables primary key
			}

			// Exit tab
			return ;
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('QTC_DOWNLOAD_NOT_AUTHORIZED'), 'error');
			$mainframe->redirect(JUri::root().substr(JRoute::_('index.php?option=com_quick2cart&view=downloads&Itemid='.$myDonloadItemid),strlen(JUri::base(true))+1));
		}

		JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error');
		$mainframe->redirect(JUri::root().substr(JRoute::_('index.php?option=com_quick2cart&view=downloads&Itemid='.$myDonloadItemid),strlen(JUri::base(true))+1));
	}

	function getTaxprofileList()
	{
		$jinput=JFactory::getApplication()->input;
		$store_id = $jinput->get('store_id');
		$selected = $jinput->get('selected');

		$storeHelper = new storeHelper;
		$tax_listSelect = $storeHelper->getStoreTaxProfilesSelectList($store_id, $selected , $fieldName = 'taxprofile_id',$fieldClass = '', $fieldId = 'taxprofile_id');

		$html = '';

		if (!empty($tax_listSelect))
		{
			$html = $tax_listSelect;
		}
		else
		{
			$html .= ' <label>'.JText::_('COM_QUICK2CART_NO_TAXPROFILE_FOR_STORE').'</label>';
		}

		$data['html'] = $html;
		echo json_encode($html);
		jexit();
	}

	/**
	 * Method to give availale shipprofile against store.
	 *
	 * @since   2.2
	 * @return   Json Plugin shipping methods list.
	 */
	function qtcUpdateShipProfileList()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$store_id = $jinput->get('store_id', 0, "INTEGER");

		$qtcshiphelper = new qtcshiphelper;
		$response['selectList'] =  $qtcshiphelper->qtcLoadShipProfileSelectList($store_id, ''	);
		echo json_encode($response);

		$app->close();
	}

}

