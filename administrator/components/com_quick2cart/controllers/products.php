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

// Load Quick2cart Controller for list views
require_once __DIR__ . '/q2clist.php';

/**
 * Products list controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerProducts extends Quick2cartControllerQ2clist
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  The array of config values.
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Product', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	function addnew()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=products&layout=new');
	}

	function edit()
	{
		$input = JFactory::getApplication()->input;

		// Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);

		$quick2cartBackendProductsHelper = new quick2cartBackendProductsHelper;
		$edit_link = $quick2cartBackendProductsHelper->getProductLink($cid[0], 'editLink');

		$this->setRedirect($edit_link);
	}

	function cancel()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=products');
	}

	function save($saveClose=0)
	{
		$jinput =JFactory::getApplication()->input;
		$cur_post 	= $jinput->post;
		$sku = $cur_post->get('sku','',"RAW");
		$sku = trim($sku);
		global $mainframe;
		$mainframe = JFactory::getApplication();

		$current_store = $cur_post->get('current_store');
		if (!empty($current_store))
		{
			$mainframe->setUserState('current_store', $current_store);
		}

		$item_name = $jinput->get('item_name','','STRING');
		//$currencydata = $cur_post['multi_cur'];
		$pid = $jinput->get('pid',0,'INT');
		$client = 'com_quick2cart';
		$stock = $jinput->get('itemstock','','INTEGER');
		$min_qty = $jinput->get('min_item');
		$max_qty = $jinput->get('max_item');

		$cat=$jinput->get('prod_cat','','INTEGER');
		//$sku=$jinput->get('sku');
		$params = JComponentHelper::getParams('com_quick2cart');
		$on_editor = $params->get('enable_editor',0);
		$youtubleLink=$jinput->get('youtube_link','',"RAW");
		$store_id = $jinput->get('current_store');//1; // @TODO hard coded for now store // @if store id is empty then calculate from item_id
		$data=  array();

		//get currency field count
		$multi_curArray = $cur_post->get('multi_cur',array(),'ARRAY');
		$originalCount=count($multi_curArray);
		$filtered_curr=array_filter($multi_curArray,'strlen');   //  remove empty currencies from multi_curr
		//get currency field count after filter enpty allow 0
		$filter_count=count($filtered_curr);

		if ($item_name &&  $originalCount==$filter_count)
		{
			//load Attributes model
			$comquick2cartHelper=new comquick2cartHelper;
			$path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'models'.DS.'attributes.php';
			$attri_model=$comquick2cartHelper->loadqtcClass($path,"quick2cartModelAttributes");

			$cur_post->set('saveAttri',1);  // whether have to save attributes or not
			$cur_post->set('saveMedia',1);
			$item_id = $comquick2cartHelper->saveProduct($cur_post);

			if (is_numeric($item_id))
			{
				//load product model
				$path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'models'.DS.'product.php';
				$prodmodel=$comquick2cartHelper->loadqtcClass($path,'quick2cartModelProduct');

				if ($saveClose==1)
				{
					return 1;
				}
				$mainframe->setUserState('item_id', $item_id);
				$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=products&layout=new&item_id=".$item_id, JText::_( 'COM_QUICK2CART_SAVE_SUCCESS' ) );
			}
			else
			{
				//save  attribute if any $msg = JText::_( 'C_SAVE_M_NS' );
				$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=products&layout=new", JText::_( 'C_SAVE_M_NS' ) );
			}
		}
		else
		{
			$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=products&layout=new", JText::_( 'C_FILL_COMPULSORY_FIELDS' ) );
		}
	}

	function checkSku()
	{
		$jinput=JFactory::getApplication()->input;
		$sku = $jinput->get( 'sku' );
		$model=$this->getModel('product');
		$itemid=$model->getItemidFromSku($sku);

		if (!empty($itemid))
		{
			echo '1';
		}
		else
		{
			echo '';
		}

		jexit();
	}

	function saveAndClose()
	{
		$Quick2cartControllerProducts=new Quick2cartControllerProducts;
		$Quick2cartControllerProducts->save(1);
		$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=products", JText::_( 'COM_QUICK2CART_SAVE_SUCCESS' ) );
	}

	function saveAndNew()
	{
		$Quick2cartControllerProducts=new Quick2cartControllerProducts;
		$Quick2cartControllerProducts->save(1);
		$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=products&layout=new", JText::_( 'COM_QUICK2CART_SAVE_SUCCESS' ) );
	}
}
