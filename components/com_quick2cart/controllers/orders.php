<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Order controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerOrders extends quick2cartController
{
	/**
	 * Constructor
	 *
	 * @since    1.6
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->siteMainHelper = new comquick2cartHelper;
		$this->vdashboardItemid = $this->siteMainHelper->getitemid('index.php?option=com_quick2cart&view=vendor');
	}

	/**
	 * Method to save/update the order status.
	 *
	 * @return  void
	 *
	 * @since    1.0
	 */
	public function save()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
		$model  = $this->getModel('orders');
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get('layout', '', "STRING");

		$post     = $jinput->post;

		// For list view
		$store_id = $post->get('store_id');

		if (empty($store_id))
		{
			// For order detail view
			$store_id = $jinput->get('store_id', '', "INTEGER");
		}

		$model->setState('request', $post);
		$result = $model->store($store_id);

		if ($result == 1)
		{
			$msg = JText::_('QTC_FIELD_SAVING_MSG');
		}
		elseif ($result == 3)
		{
			$msg = JText::_('QTC_REFUND_SAVING_MSG');
		}
		else
		{
			$msg = JText::_('QTC_FIELD_ERROR_SAVING_MSG');
		}

		if ($layout == "storeorder")
		{
			$link = 'index.php?option=com_quick2cart&view=orders&layout=storeorder';
		}
		else
		{
			$link = 'index.php?option=com_quick2cart&view=orders';
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Method to update the order status and comment from **order detail page**.
	 *
	 * @return  void
	 *
	 * @since    1.0
	 */
	function updateStoreItemStatus()
	{
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get('layout', '', "STRING");
		$post     = $jinput->post;
		$store_id = $jinput->get('store_id', '', "INTEGER");
		$orderid = $jinput->get("orderid", '', "INTEGER");

		// $comment             = $data->get('comment', '', "STRING");
		$add_note_chk = $post->get('add_note_chk');
		$note         = '';
		$note = $post->get('order_note', '', "STRING");
		$status = $jinput->get('status', '', "STRING");
		$notify_chk = $post->get('notify_chk');

		if (!empty($notify_chk))
		{
			$notify_chk = 1;
		}
		else
		{
			$notify_chk = 0;
		}

		if ($orderid && $store_id)
		{
			// Update item status
			$this->siteMainHelper->updatestatus($orderid, $status, $note, $notify_chk, $store_id);

			// Save order history
			$orderItemsStr = $post->get("orderItemsStr", '', "STRING");
			$orderItems = explode("||", $orderItemsStr);

			foreach ($orderItems as $oitemId)
			{
				// Save order item status history
				$this->siteMainHelper->saveOrderStatusHistory($orderid, $oitemId, $status, $note, $notify_chk);
			}
		}

		// $layout == "order"
		$link = JRoute::_('index.php?option=com_quick2cart&view=orders&layout=order&orderid=' . $orderid .  '&store_id=' . $store_id . '&calledStoreview=1&Itemid' . $this->vdashboardItemid, false);

		$this->setRedirect($link, $msg);
	}

	/**
	 * Used to change store order status
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	public function changeStoreOrderStatus()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
		$model  = $this->getModel('orders');
		$jinput = JFactory::getApplication()->input;
		$post   = $jinput->post;
		$model->setState('request', $post);
		$store_id = $post->get('current_store', '', 'STRING');

		// Call model function
		//  1 for change store product status
		$result = $model->store($store_id);

		if ($result == 1)
		{
			$msg = JText::_('QTC_FIELD_SAVING_MSG', true);
		}
		elseif ($result == 3)
		{
			$msg = JText::_('QTC_REFUND_SAVING_MSG', true);
		}
		else
		{
			$msg = JText::_('QTC_FIELD_ERROR_SAVING_MSG', true);
		}

		if (!empty($store_id))
		{
			$link = "index.php?option=com_quick2cart&view=orders&layout=storeorder&change_store=" . $store_id;
		}
		else
		{
			$link = 'index.php?option=com_quick2cart&view=orders';
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Called on cancel button
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	public function cancel()
	{
		$msg = JText::_('CCK_FIELD_CANCEL_MSG', true);
		$this->setRedirect('index.php?option=com_quick2cart', $msg);
	}

	// To remove item tax and ship charges from order
	/**
	 * This function is used to migrate the tax and shipping charge detail to new format.
	 * OLD: order tax = SUM(item Tax) similary for shipping charges
	 * NEW: Item level tax is not add in order tax fields. Order tax fields contain only order tax.
	 *
	 * @return  ''.
	 *
	 * @since   2.2.5
	 */
	public function test()
	{
		$db                          = JFactory::getDBO();
		$comquick2cartHelper         = new Comquick2cartHelper;
		$path                        = JPATH_SITE . '/components/com_quick2cart/models/cartcheckout.php';
		$ckoutModel = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelcartcheckout");

		// A.Check for column itemTaxShipIncluded. If present then only migration required else not.
		$query = "SHOW COLUMNS FROM `#__kart_orders`";
		$db->setQuery($query);
		$columns = $db->loadColumn();

		// B.If col is not present then add
		if (!in_array('itemTaxShipIncluded', $columns))
		{
			// C. Else add column= 'itemTaxShipIncluded' to db
			$query = "ALTER TABLE  `#__kart_orders` ADD  `itemTaxShipIncluded` tinyint(1) NOT NULL DEFAULT '0' " .
			" COMMENT 'Flag : whether order tax and shipping is summation of order item tax and ship or not. 1 =>  orderTax = sum(order item tax)'";
			$db->setQuery($query);

			if (!$db->execute())
			{
				echo JText::_('COM_QUICK2CART_UNABLE_TO_ALTER_COLUMN') . " - #__kart_orders.";
				echo $db->getErrorMsg();

				return 0;
			}

			// D. Set its value column value to 1

			$query = $db->getQuery(true);

			// Fields to update.
			$fields = array(
				$db->quoteName('itemTaxShipIncluded') . ' = 1'
			);

			// Conditions for which records should be updated.
			$conditions = array();

			$query->update($db->quoteName('#__kart_orders'))->set($fields);
			$db->setQuery($query);
			$result = $db->execute();
		}

		// E:
		$query = $db->getQuery(true);
		$query->select(' o.* ');
		$query->from('#__kart_orders as o');
		$query->where("(o.order_tax > 0 OR o.order_shipping > 0 )");
		$query->where("itemTaxShipIncluded=1");
		$query->order("o.id DESC");

		$db->setQuery($query);
		$orderList = $db->loadObjectList('id');

		foreach ($orderList as $order)
		{
			$query             = $db->getQuery(true);
			$modifiedOrder     = new stdClass;
			$modifiedOrder->id = $order->id;

			// Get order item details
			$query->select('order_id, sum(product_final_price) as totalItemprice,sum(item_tax) as totalItemTax,sum(item_shipcharges) as totalShipCharge');
			$query->from('#__kart_order_item as i');
			$query->where("order_id= " . $order->id);
			$db->setQuery($query);
			$itemDetail = $db->loadAssoc('id');

			// 1. Update original amount
			$modifiedOrder->original_amount = $itemDetail['totalItemprice'];

			// 2.update tax and ship
			$OrderLevelTax = $order->order_tax - $itemDetail['totalItemTax'];

			if ($OrderLevelTax >= 0)
			{
				$modifiedOrder->order_tax = $OrderLevelTax;
			}
			else
			{
				$modifiedOrder->order_tax = 0;
			}

			$OrderLevelShip = $order->order_shipping - $itemDetail['totalShipCharge'];

			if ($OrderLevelTax >= 0)
			{
				$modifiedOrder->order_shipping = $OrderLevelShip;
			}
			else
			{
				$modifiedOrder->order_shipping = 0;
			}

			// 3. Check for coupon
			$copDiscount = 0;

			if ($order->coupon_code)
			{
				$copDiscount = $ckoutModel->afterDiscountPrice($modifiedOrder->original_amount, $order->coupon_code, "", "order", $modifiedOrder->id);

				$copDiscount = ($copDiscount >= 0) ? $copDiscount : 0;
			}

			// 4. Update amount column according to discount,tax,shipping details
			$modifiedOrder->amount = $modifiedOrder->original_amount + $modifiedOrder->order_tax + $modifiedOrder->order_shipping - $copDiscount;

			if (!$db->updateObject('#__kart_orders', $modifiedOrder, 'id'))
			{
				$this->setError($db->getErrorMsg());

				return 0;
			}
		}
	}
}
