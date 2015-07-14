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

jimport('joomla.application.component.view');

class quick2cartViewProductpage extends JViewLegacy
{
	function display($tpl = null)
	{
		$comquick2cartHelper=new comquick2cartHelper;
		$input=JFactory::getApplication()->input;
		$layout		= $input->get( 'layout','default' );
		$option		= $input->get( 'option','' );

		$this->params = JFactory::getApplication()->getParams('com_quick2cart');
		// check for multivender COMPONENT PARAM
		// vm: commented for task #20773
	/*	$isMultivenderOFFmsg=$comquick2cartHelper->isMultivenderOFF();
		if(!empty($isMultivenderOFFmsg))
		{
			print $isMultivenderOFFmsg;
			return false;
		}*/
		if($layout=='default')
		{ // product page
				//DECLARATION SECTION
				$this->client=$client="com_quick2cart";
				$this->pid=0;
				$this->item_id=$item_id		= $input->get( 'item_id','' );
				JLoader::import('cart', JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'models');
				$model =  new Quick2cartModelcart;

				if(empty($item_id)) // # if entry is not present in kart_item
					return false;

					// retrun store_id,role etc with order by role,store_id
				$this->store_role_list=$comquick2cartHelper->getStoreIds();
				// GETTING AUTHORIZED STORE ID
				$storeHelper=new storeHelper();
				$this->store_list=$storeHelper->getuserStoreList();
				// GETTING PRICE
				$this->price=$price = $model->getPrice($item_id,1);	   // return array of price

				//GETTING ITEM COMPLEATE DETAIL (attributes and its option wil get)
				//$itemDetail=$model->getItemCompleteDetail($item_id);

				//getting stock min max,cat,store_id
				$this->itemdetail = $model->getItemRec($item_id);

				if(!empty($this->itemdetail))
				{
					///get attributes
					$this->attributes = $model->getAttributes($item_id);

					// for RELEATED PROD FROM CATEGORY
					$product_path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'helpers'.DS.'product.php';
					if(!class_exists('productHelper'))
					{ //require_once $path;
						 JLoader::register('productHelper', $product_path );
						 JLoader::load('productHelper');
					}
					$productHelper =  new productHelper();

					// get free products media file
					$this->mediaFiles = $productHelper->getProdmediaFiles($item_id);

					$this->prodFromCat=$productHelper->getSimilarProdFromCat($this->itemdetail->category,$this->item_id);

					$this->prodFromSameStore=$productHelper->prodFromSameStore($this->itemdetail->store_id,$this->item_id);

					$this->peopleAlsoBought=$productHelper->peopleAlsoBought($this->item_id);

					$this->peopleWhoBought=$productHelper->peopleWhoBought($this->item_id);
					//print_r($this->peopleWhoBought);die;
					$social_options= '';
					$route = $comquick2cartHelper->getProductLink($this->item_id);

					// Jilke
					$dispatcher = JDispatcher::getInstance();
					JPluginHelper::importPlugin('system');
					$result=$dispatcher->trigger('onProductDisplaySocialOptions',array($this->item_id,'com_quick2cart.productpage',$this->itemdetail->name,$route) );//Call the plugin and get the result
					if(!empty($result)){
						$social_options=$result[0];
					}
					$this->social_options=$social_options;
					$this->showBuyNowBtn = $productHelper->isInStockProduct($this->itemdetail);
				}

		}// END OF default layout
		elseif($layout=='popupslide')
		{
				$this->item_id=$item_id		= $input->get( 'qtc_prod_id','' );
				JLoader::import('cart', JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'models');
				$model =  new Quick2cartModelcart;

				if(empty($item_id)) // # if entry is not present in kart_item
					return false;

			$this->itemdetail = $model->getItemRec($item_id);
		}

		$this->item = $this->itemdetail;

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		//@TODO Need to uncomment this when a menu for single product item can be created.
		/*
		$menu = $menus->getActive();

		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('QTC_PRODUCTPAGE_PAGE'));
		}

		$title = $this->params->get('page_title', '');
		*/

		//@TODO Need to comment this if when a menu for single product item can be created.
		if (empty($title))
		{
			$title = $this->itemdetail->name . ' - ' . JText::_('QTC_PRODUCTPAGE_PAGE');
		}

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
