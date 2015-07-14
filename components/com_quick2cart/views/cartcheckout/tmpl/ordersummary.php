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

/* if user is on payment layout and log out at that time undefined order is is found
in such condition send to home page or provide error msg
*/
if (isset($this->orders_site) && isset($this->undefined_orderid_msg) )
{
		return false;
}

$params = JComponentHelper::getParams('com_quick2cart');
$user=JFactory::getUser();

if (!$user->id && !$params->get('guest'))
{
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
<div class="well" >
	<div class="alert alert-error">
		<span ><?php echo JText::_('QTC_LOGIN'); ?> </span>
	</div>
</div>
</div>
<?php
	return false;
}

	$session = JFactory::getSession();
	$document = JFactory::getDocument();
	// make cart empty
	JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
	$Quick2cartModelcart=new Quick2cartModelcart;
	$Quick2cartModelcart->empty_cart();
?>

<?php
//if ($this->orderinfo[0]->processor)
{
/*
	$processor=$this->orderinfo[0]->processor;

	//$comquick2cartHelper->getPluginName()
	$model	= $this->getModel('cartcheckout');
	// gettng plugin name which is set in plugin option
	$plgname=$model->getPluginName($processor);
	$plgname=!empty($plgname)?$plgname:$processor;

	if (empty($this->payhtml))
	{
		?>
		<div class="techjoomla-bootstrap" >
			<div class="well">
				<div class="alert alert-error">
					<span><?php echo JText::_('COM_QUICK2CART_ORDERSUMMERY_PLS_TRY_AGAIN_SOMETHING_WENT_WRONG');?></span>
				</div>
			</div>
		</div>

		<?php
		return false;
	}
*/
?>
<div>
	<?php
	$this->order_blocks = array ('0'=>'shipping','1'=>'billing','2'=>'cart');
	$this->order_authorized=1;
	// CHECK for view override
	$comquick2cartHelper = new comquick2cartHelper;
	$view=$comquick2cartHelper->getViewpath('orders','order');
	ob_start();
		include($view);
		$html = ob_get_contents();
	ob_end_clean();
	echo $html;
	?>
</div>
<?php
}
?>

<div style="clear:both"></div>
<!-- show payment option start -->
<div class="row-fluid">
	<div class="paymentHTMLWrapper well" id="qtcPaymentGatewayList">
		<?php
		$paymentListStyle = '' ;
		$mainframe = JFactory::getApplication();
		$qtcOrderPrice = 0;
		if (!empty($this->orderinfo->amount))
		{
			$qtcOrderPrice = (float)$this->orderinfo->amount;;
		}
		if (!empty($qtcOrderPrice))
		{
		?>
			<div class="" id="qtc_paymentlistWrapper" style="<?php echo $paymentListStyle?>">
			<div class="control-group " id="qtc_paymentGatewayList">
				<?php
				$default = "";
				$lable = JText::_('SEL_GATEWAY');
				$gateway_div_style=1;

				// Getting gateways
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper::importPlugin('payment');
				//$params->get('gateways') = array('0' => 'paypal','1'=>'Payu');

				if ( !is_array($params->get('gateways')))
				{
					$gateway_param[] = $params->get('gateways');
				}
				else
				{
					$gateway_param = $params->get('gateways');
				}

				// Get payment plugins info.
				if (!empty($gateway_param))
				{
					$gateways = $dispatcher->trigger('onTP_GetInfo',array($gateway_param));
				}

				$this->gateways = $gateways;

				// If only one geteway then keep it as selected
				if (!empty($this->gateways))
				{
					$default = $this->gateways[0]->id; // id and value is same
				}

				if (!empty($this->gateways) && count($this->gateways)==1) //if only one geteway then keep it as selected
				{
					$default=$this->gateways[0]->id; // id and value is same
					$lable=JText::_('SEL_GATEWAY');
					$gateway_div_style=1;  // to show payment radio btn even if only one payment gateway
				}
				?>

				<label for="" class="control-label"><h4><?php echo $lable ?> </h4></label>
				<div class="controls" style="<?php echo ($gateway_div_style==1)?"" : "display:none;" ?>">
					<?php
					if (empty($this->gateways))
						echo JText::_('NO_PAYMENT_GATEWAY');
					else
					{
						$default = ''; // removed selected gateway 26993
						$imgpath = JUri::root()."components/com_quick2cart/assets/images/ajax.gif";
						$ad_fun = 'onChange=qtc_gatewayHtml(this.value,'.$order_id.',"'.$imgpath.'")';
						$pg_list = JHtml::_('select.radiolist', $this->gateways, 'gateways', "class='inputbox required' ".$ad_fun . '  ', 'id', 'name',$default,false);
						echo $pg_list;
					}
					?>
				</div>
				<?php
				if (empty($gateway_div_style))
				{
					?>
						<div class="controls qtc_left_top">
						<?php echo 	$this->gateways[0]->name; // id and value is same ?>
						</div>
					<?php
				}
				?>
			</div> <!-- END OF control-group-->
			<!-- show payment hmtl form-->
			<div id="qtc_payHtmlDiv">
				<?php

				?>
			</div>
		</div>
		<?php
		}
		else
		{
			?>
			<!-- <div id="qtc_payHtmlDiv">
			<form method="post" name="sa_freePlaceOrder" class="" id="sa_freePlaceOrder">
			<div class="techjoomla-bootstrap" >


				<input type="hidden" name="option" value="com_socialads">
				<input type="hidden" name="controller" value="buildad" />
				<input type="hidden" id="task" name="task" value="sa_processFreeOrder">
				<input type="hidden" name="order_id" value="<?php //echo $order_id; ?>">

				<div class="form-actions " >
					<input type="submit" class="btn btn-success btn-large" value="<?php //echo JText::_('SA_CONFORM_ORDER'); ?>">
				</div >


			</div>
			</form>
			</div>
			-->
			<?php
					//	$this->orderinfo[0]->processor = JText::_('COM_QUICK2CART_FREE_CHCKOUT');
				$Quick2cartControllercartcheckout = new Quick2cartControllercartcheckout;
				echo $Quick2cartControllercartcheckout->getFreeOrderHtml($order_id);

			?>
			<?php

		}
		?>
	</div> <!-- end of paymentHTMLWrapper-->
</div>
<!-- show payment option end -->
