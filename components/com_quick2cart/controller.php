<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');
jimport('joomla.application.component.controller');


class Quick2cartController extends JControllerLegacy
{

	public function display ($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	/**
	 * Function: clearcart
	 * clears the cart from session
	 * Parameters:		none
	 * Returns:
	 */
	function clearcart ()
	{
		// http://testjugad.com/~dipti/shine17/index.php?option=com_quick2cart&task=clearcart&remote=0
		$jinput = JFactory::getApplication()->input;
		$remote = $jinput->get("remote");
		$model = $this->getModel('cart');
		$model->empty_cart();
		if (! (isset($remote)))
		{
			echo JUri::root();
			// Echo 'cleared the Cart!!';
			jexit();
		}
		return;
	}

	/**
	 * Function: removecart removes the item from the cart from session
	 * Parameters:			id : id of the item
	 * Returns:
	 */
	function removecart ()
	{
		// http://testjugad.com/~dipti/shine17/index.php?option=com_quick2cart&task=removecart&id=<id>
		$jinput = JFactory::getApplication()->input;
		$id = $jinput->get("id");

		$model = $this->getModel('cart');
		$model->remove_cart($id);

		// for setting current tab status one page chkout::
		$session = JFactory::getSession();
		$session->set('one_pg_ckout_tab_state', 'qtc_cart');

		echo JUri::root();
		jexit();
	}
	/*
	 * Function: updatecart updates the cart and also calculates the tax and
	 * shipping charges Parameters: none Returns: json:
	 */
	function updatecart ()
	{
		$jinput = JFactory::getApplication()->input;
		$postdata = $jinput->post;
		$model = $this->getModel('cart');

		// GeT CART ITEM AND ITS QTY
		$cart_id = $postdata->get('cart_id', array(), "ARRAY");
		$cart_count = $postdata->get('cart_count', array(), "ARRAY");
		echo $model->update_cart($cart_id, $cart_count);

		// for setting current tab status one page chkout::
		$session = JFactory::getSession();
		$session->set('one_pg_ckout_tab_state', 'qtc_cart');
		jexit();
	}

	function addcart()
	{
		//http://testjugad.com/~dipti/shine17/index.php?option=com_quick2cart&task=addcart&id=4&title=socialads&amt=30&tmpl=component&lang=en
		//JSession::checkToken( 'get' ) or die( 'Invalid Token' );

		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		$item_id = $jinput->get("item_id", 0, "INTEGER");

		// IF item_id is present then no need of pid and client
		if (!empty($item_id))
		{
			$item['item_id'] =$item_id;
		}
		else
		{
			$id = $jinput->get("id");
			$id_arr = explode('-',$id);
			$item['id'] = $id_arr[1];
			$item['parent'] = $id_arr[0];
		}

		// Getting quantity
		$item['count'] = $jinput->get("count");

		// Getting product attribure option values
		$item['options'] = $jinput->get("options", '', 'STRING');

		$op=array_filter(explode(',', $item['options'])); // remove empty options
		$item['options'] =implode(",", $op);

		// Getting user data like "text to print on T-shirt"
		$userData = $post->get('userData', '', "RAW");

		if (!empty($userData))
		{
			$userData = json_decode($userData, true);
		}

		$model = $this->getModel('cart');
		//$item['userData'] = $userData;
		//$user=JFactory::getUser();
		//        $session = JFactory::getSession();

		if (empty($item_id))
		{
			if(empty($item['id']) || empty($item['count']) ||empty($item['parent']))
			{
				echo -1;
				jexit();
			}
		}

		// CALL add to cart Api
		$comquick2cartHelper = new comquick2cartHelper;
		$msg = $comquick2cartHelper->addToCartAPI($item,$userData);
		echo json_encode($msg);
		jexit();

	}

	function deletecoupon()
	{
		$input = JFactory::getApplication()->input;
		$view = $input->get('view', 'managecoupon');
		// Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);
		if ($view == "managecoupon")
		{
			$model = $this->getModel('managecoupon');
			if ($model->deletecoupon($cid))
			{
				$msg = JText::sprintf('Coupon(s) deleted ', count($cid));
			}
		$this->setRedirect('index.php?option=com_quick2cart&view=managecoupon&layout=default',$msg);
		}
	}
	/* Publish */
	function publish ()
	{
		$input = JFactory::getApplication()->input;
		$view = $input->get('view', 'managecoupon');
		// Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);
		if ($view == "managecoupon")
		{
			// FOR icon change
			if ($cop_id = $input->get('copId', '', 'INTEGER'))
			{
				// clicked on publish or unpublish link
				$cid = array();
				$cid[] = $cop_id;
			}
			$model = $this->getModel('managecoupon');
			if ($model->setItemState($cid, 1))
			{
				$msg = JText::sprintf('Coupon(s) published ', count($cid));
			}
		$this->setRedirect('index.php?option=com_quick2cart&view=managecoupon&layout=default',$msg);
		}
		elseif ($view == "vendor")
		{
			// FOR icon change
			if ($storeid = $input->get('storeid', '', 'INTEGER'))
			{
				// clicked on publish or unpublish link
				$cid = array();
				$cid[] = $storeid;
			}
			$model = $this->getModel('vendor');
			if ($model->setItemState($cid, 1))
			{
				$msg = JText::sprintf('Vendor store(s) published ', count($cid));
			}
			$this->setRedirect('index.php?option=com_quick2cart&view=vendor',$msg);
		}
	}

	/* Unpublish */
	function unpublish ()
	{
		$input = JFactory::getApplication()->input;
		$view = $input->get('view', 'managecoupon');
		// Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);
		if ($view == "managecoupon")
		{
			$model = $this->getModel('managecoupon');
			if ($cop_id = $input->get('copId', '', 'INTEGER'))
			{
				// clicked on publish or unpublish link
				$cid = array();
				$cid[] = $cop_id;
			}
			if ($model->setItemState($cid, 0))
			{
				$msg = JText::sprintf('Coupon(s) unpublished ', count($cid));
			}
			$this->setRedirect('index.php?option=com_quick2cart&view=managecoupon&layout=default',$msg);
		}
		elseif ($view == "vendor")
		{
			// FOR icon change
			if ($storeid = $input->get('storeid', '', 'INTEGER'))
			{
				// clicked on publish or unpublish link
				$cid = array();
				$cid[] = $storeid;
			}
			$model = $this->getModel('vendor');
			if ($model->setItemState($cid, 0))
			{
				$msg = JText::sprintf('Vendor store(s) unpublished ', count($cid));
			}
			$this->setRedirect('index.php?option=com_quick2cart&view=vendor',$msg);
		}
	} // end of unpublish
	/* NEW */
	function newCoupon ()
	{
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->post;
		$change_store = $post->get('change_store', 0, 'INT');
		$jinput->set('change_store', $change_store);
		$this->setRedirect('index.php?option=com_quick2cart&view=managecoupon&layout=form&change_store=' . $change_store);
	}

	function delete ()
	{
		$input = JFactory::getApplication()->input;
		$view = $input->get('view', ''); // Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		JArrayHelper::toInteger($cid);
		if ($view == "vendor")
		{
			$model = $this->getModel('vendor');
			if ($model->deletevendor($cid))
			{
				$msg = JText::sprintf('Vendor store(s) deleted ', count($cid));
			}
			$this->setRedirect('index.php?option=com_quick2cart&view=vendor',$msg);
		}
	}

	function edit ()
	{
		$input = JFactory::getApplication()->input;
		$view = $input->get("view", "");
		$cid = $input->get("cid", "", "array");
		JArrayHelper::toInteger($cid);
		/*if ($view == "vendor")
		{
			$link = "index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=" . $cid[0] . '';
			$this->setRedirect($link);
		}*/
	}

	function addNew ()
	{
		$input = JFactory::getApplication()->input;
		$view = $input->get("view", "");
		/*if ($view == "vendor")
		{
			$link = "index.php?option=com_quick2cart&view=vendor&layout=createstore";
			$this->setRedirect($link);
		}*/
	}

	/**
	 * This function calls respective task on respective plugin
	 */
	function callSysPlgin ()
	{
		$input = JFactory::getApplication()->input;
		$plgType = $input->get("plgType", "");
		$plgtask = $input->get("plgtask", "");

		// called from Ajax(0) or URL (1)
		$callType = $input->get("callType", 0);

		// START Q2C Sample development
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin($plgType);
		$result = $dispatcher->trigger($plgtask); // Call the plugin and get the
		                                        // result
		$OnBeforeCreateStore = '';
		if (! empty($result))
		{
			$OnBeforeCreateStore = $result[0];
		}

		if (empty($callType))
		{
			echo $OnBeforeCreateStore;
			jexit();
		}
	}

	function updateEasysocialApp ()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_app_user_q2cMyProducts', JPATH_ADMINISTRATOR);

		// Get storeid,useris and total from ajax responce.
		$input = JFactory::getApplication()->input;

		$storeid = $input->get('storeid', '', 'INT');
		$userid = $input->get('uid', '', 'INT');
		$total = $input->get('total', '', 'INT');

		// Load app modal getitem function.
		require_once JPATH_ROOT . '/media/com_easysocial/apps/user/q2cMyProducts/models/q2cmyproducts.php';
		$q2cMyProductsModel = new q2cmyproductsModel('', $config = array());
		$products = $q2cMyProductsModel->getItems($userid, $total, $storeid);
		$store_product_count = $q2cMyProductsModel->getProductsCount($userid, $storeid);

		// load store helper file
		JLoader::register('storeHelper', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'storeHelper.php');

		// Laod store helper class.
		JLoader::load('storeHelper');
		$storeHelper = new storeHelper();

		// Get store link
		$store_link = $storeHelper->getStoreLink($storeid);

		// Set q2c products return by modal of easysocial app.
		$this->set('products', $products);

		if ($products)
		{
			$random_container = 'q2c_pc_es_app_my_products';
			$html = '<div id="q2c_pc_es_app_my_products">';

			foreach ($products as $data)
			{
				$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'views' . DS . 'product' . DS . 'tmpl' . DS . 'product.php';

				// @TODO condition vise mod o/p
				ob_start();
				include $path;
				$html .= ob_get_contents();
				ob_end_clean();
			}

			$html .= '</div>';
			$html .= '<div class="clearfix"></div>';

			if ($store_product_count > $total)
			{
				$html .= "
				<div class='row-fluid span12'>
					<div class='pull-right'>
						<a href='" . $store_link . "'>" . JText::_('APP_Q2CMYPRODUCTS_SHOW_ALL') . " (" . $store_product_count . ") </a>
					</div>
					<div class='clearfix'>&nbsp;</div>
				</div>";
			}
		}
		else
		{
			$user = JFactory::getUser($userid);
			$html  = '<div class="empty" style="display:block;">';
			$html .= JText::sprintf('APP_Q2CMYPRODUCTS_NO_PRODUCTS_FOUND', $user->name);
			$html .= '</div>';
		}

		$js = 'initiateQ2cPins();';

		$data['html'] = $html;
		$data['js'] = $js;
		echo json_encode($data);
		jexit();
	}
}
