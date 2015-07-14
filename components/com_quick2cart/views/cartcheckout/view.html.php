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


/**
 * This Class supports checkout process.
 *
 * @package     Joomla.Site
 * @subpackage  com_quick2cart
 * @since       1.0
 */
class Quick2cartViewcartcheckout extends JViewLegacy
{
	/**
	 * Render view.
	 *
	 * @param   array  $tpl  An optional associative array of configuration settings.
	 *
	 * @since   1.0
	 * @return   null
	 */

	public function display($tpl = null)
	{
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();

		require_once(JPATH_SITE . '/components/com_quick2cart/helpers/media.php');
		// create object of media helper class
		$this->media = new qtc_mediaHelper;

		$model = $this->getModel('cartcheckout');

		$input = JFactory::getApplication()->input;
		$layout = $input->get('layout', '');

		// Send to joomla's registration of guest ckout is off
		if ($layout == 'cancel' || $layout == 'orderdetails')
		{
			$input->set('remote', 1);
			$sacontroller = new quick2cartController;
			$sacontroller->execute('clearcart');
		}
		else
		{
			$params = $this->params = JComponentHelper::getParams('com_quick2cart');
			$guestcheckout = $params->get('guest');

			if ($guestcheckout == 0 && !($user->id))
			{
				$itemid = $input->get('Itemid');

				// $uri=JRoute::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemid,false);
				$rurl = 'index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemid;
				$returnurl = base64_encode($rurl);
				$mainframe->redirect(JRoute::_('index.php?option=com_users&return=' . $returnurl, false), $msg);
			}
			// GETTING CART ITEMS
			JLoader::import('cart', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models');
//			$cartmodel = new Quick2cartModelcart;
	//		$cart = $cartmodel->getCartitems();
			// Amol change
			$cartCheckoutModel = new Quick2cartModelcartcheckout;
			$cart = $cartCheckoutModel->getCheckoutCartitemsDetails();
			// Amol change end
			$this->cart = $cart;

			$session = JFactory::getSession();
			$cops = $session->get('coupon');

			if (!empty($cops))
			{
				// Check for expiry
				$cop_array = array();

				foreach ($cops as $cop)
				{
					$valid_coupan = $model->getcoupon($cop['code']);

					if (!empty($valid_coupan))
					{
						$cop_array[] = $cop;
					}
				}

				$this->coupon = $cop_array;
			}
			else
			{
				$this->coupon = array();
			}

			$user = JFactory::getUser();

			if ($user->id != 0)
			{
				$userdata = $model->userdata();
				$this->userdata = $userdata;
			}

			if ($layout == 'payment')
			{
				$orders_site = '1';
				$orderid = $session->get('order_id');
				$comquick2cartHelper = new comquick2cartHelper;
				$order = $comquick2cartHelper->getorderinfo($orderid);

				if (!empty($order))
				{
					if (is_array($order))
					{
						$this->orderinfo = $order['order_info'];
						$this->orderitems = $order['items'];
					}
					elseif ($order == 0)
					{
						$this->undefined_orderid_msg = 1;
					}

					// $payhtml = $model->getpayHTML($order['order_info'][0]->processor,$orderid);
					JLoader::import('payment', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models');
					$paymodel = new Quick2cartModelpayment;
					$payhtml = $paymodel->getHTML($order['order_info'][0]->processor, $orderid);
					$this->payhtml = $payhtml[0];
				}
				else
				{
					$this->undefined_orderid_msg = 1;
				}

				$orders_site = '1';
				$this->orders_site = $orders_site;

				// Make cart empty
				JLoader::import('cart', JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models');
				$Quick2cartModelcart = new Quick2cartModelcart;
				$Quick2cartModelcart->empty_cart();
			}
			else
			{
				// START Q2C Sample development
					$dispatcher = JDispatcher::getInstance();
					JPluginHelper::importPlugin('system');

					// Call the plugin and get the result
					$result = $dispatcher->trigger('OnBeforeq2cCheckoutCartDisplay');
					$beforecart = '';

					if (!empty($result))
					{
							$beforecart = $result[0];
					}

					$this->beforecart = $beforecart;
					$result = $dispatcher->trigger('OnAfterq2cCheckoutCartDisplay');
					$aftercart = '';

					if (!empty($result))
					{
						$aftercart = $result[0];
					}

					$this->aftercart = $aftercart;

					// END Q2C Sample development
					// Q2C Sample development - ADD TAB in ckout page
					$dispatcher = JDispatcher::getInstance();
					JPluginHelper::importPlugin('system');
					$result = $dispatcher->trigger('qtcaddTabOnCheckoutPage', array($this->cart));
					$this->addTab = '';
					$this->addTabPlace = '';

					if (!empty($result))
					{
						$this->addTab = $result[0];
						$this->addTabPlace = !empty($result[0]['tabPlace']) ? $result[0]['tabPlace'] : '';
					}
					// END - Q2C Sample development - ADD TAB in ckout page
					// Trigger plg to add plg after shipping tab

				// GETTING country
					$country = $this->get("Country");
					$this->country = $country;
			}

			// Getting GETWAYS
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('payment');

			// $params->get( 'gateways' ) = array('0' => 'paypal','1'=>'Payu');

			if (!is_array($params->get('gateways')) )
			{
				$gateway_param[] = $params->get('gateways');
			}
			else
			{
				$gateway_param = $params->get('gateways');
			}

			if (!empty($gateway_param))
			{
				$gateways = $dispatcher->trigger('onTP_GetInfo', array($gateway_param));
			}

			$this->gateways = $gateways;
		}

		$this->_setToolBar();
		parent::display($tpl);
	}

/**
 * Method Allow to set toolbar.
 *
 * @return  ''
 */
	private function _setToolBar()
	{
		// Added by aniket for task #25690
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('QTC_CARTCHECKOUT_PAGE'));
	}
}
