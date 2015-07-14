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
$this->comquick2cartHelper = new comquick2cartHelper;
$this->params = JComponentHelper::getParams('com_quick2cart');
$jinput = JFactory::getApplication()->input;
$order_id = $jinput->get('orderid');

$jinput->set('orderid', $order_id);
$order = $order_bk = $this->comquick2cartHelper->getorderinfo($order_id);
$this->orderinfo = $order['order_info'];
$this->orderitems = $order['items'];
$this->orders_site = 1;
$this->orders_email = 1;
$this->order_authorized = 1;

if ($this->orderinfo[0]->address_type == 'BT')
{
	$billemail = $this->orderinfo[0]->user_email;
}
elseif ($this->orderinfo[1]->address_type == 'BT')
{
	$billemail = $this->orderinfo[1]->user_email;
}

$fullorder_id = $order['order_info'][0]->prefix . $order_id;
$this->qtcSystemEmails = 1;

if (!JFactory::getUser()->id && $this->params->get('guest'))
{
	$jinput->set('email', md5($billemail));
}
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" style="border-width: 1px 1px 1px 1px; border-style: solid; border-color: #DDD; border-collapse: separate;padding:5px;">
	<?php
	/* if user is on payment layout and log out at that time undefined order is is found
	in such condition send to home page or provide error msg
	*/
	if(isset($this->orders_site) && isset($this->undefined_orderid_msg) )
	{
			return false;
	}

	$user=JFactory::getUser();
	$jinput=JFactory::getApplication()->input;
	$guest_email = $jinput->get('email','','STRING');

	if($guest_email)
	{
		$guest_email_chk =0;
		$guest_email_chk = $this->comquick2cartHelper->checkmailhash($this->orderinfo[0]->id,$guest_email);
		if(!$guest_email_chk )
		{
			?>

			<div class="well" >
				<div class="alert alert-error">
					<span ><?php echo JText::_('QTC_GUEST_MAIL_UNMATCH'); ?> </span>
				</div>
			</div>
		</div>
		<!--Q2C_WRAPPER_CLASS -->
		<?php
			return false;
		}
	}
	else if(!$user->id && !$this->params->get( 'guest' ))
	{ ?>

		<div class="well" >
			<div class="alert alert-error">
				<span ><?php echo JText::_('QTC_LOGIN'); ?> </span>
			</div>
		</div>
	</div>
	<!--Q2C_WRAPPER_CLASS -->
	<?php
		return false;
	}

	// 1 check : for "MY ORDERS"=check for authorized user or not ( it should be site,authorized to view order and not store releated view)
	if(isset($this->orders_site) && empty($this->order_authorized) )
	{
		$authorized=0;
		//2 check : "FOR STORE ORDER " order should be releated to store
		if( !empty($this->storeReleatedView))  // if vendor releated view is present then current order should be releated to store
		{
			//3. store releated view but not logged in then (directly accessed known url at that time it require )
			if(empty($user->id))
			{
					?>
					<div class="well" >
						<div class="alert alert-error">
							<span ><?php echo JText::_('QTC_LOGIN'); ?> </span>
						</div>
					</div>
				</div>
				<!--Q2C_WRAPPER_CLASS -->
				<?php
			return false;
			}
			$result=$this->comquick2cartHelper->getStoreOrdereAuthorization($this->store_id,$this->orderid);
			$authorized=(!empty($result))?1:0;
		}

		if($authorized==0)
		{
			?>
				<div class="well" >
					<div class="alert alert-error">
						<span ><?php echo JText::_('QTC_NOT_AUTHORIZED_USER_TO_VIEW_ORDER'); ?> </span>
					</div>
				</div>
			</div>
			<!--Q2C_WRAPPER_CLASS --><!--Q2C_WRAPPER_CLASS -->
			<?php
				return false;
		}// end of if($authorized==0)
	}

	$coupon_code=$this->orderinfo[0]->coupon_code ;

	if($this->orderinfo[0]->address_type == 'BT')
		$billinfo = $this->orderinfo[0];
	else if($this->orderinfo[1]->address_type == 'BT')
		$billinfo = $this->orderinfo[1];


	if( $this->params->get( 'shipping' ) == '1' )
	{
		if($this->orderinfo[0]->address_type == 'ST')
			$shipinfo = $this->orderinfo[0];
		else if(isset($this->orderinfo[1]))
						if($this->orderinfo[1]->address_type == 'ST')
								$shipinfo = $this->orderinfo[1];
	}

	$this->orderinfo = $this->orderinfo[0];
	$orders_site=( isset($this->orders_site) )?$this->orders_site:0;  // 1 for site 0 for admin
	$orders_email=( isset($this->orders_email) )?$this->orders_email:0;
	$emailstyle="style='background-color: #cccccc'";
	$vendor_order_view=(!empty($this->store_id))?1:0;


	$order_currency = $this->orderinfo->currency;
	//$order_currency = ($this->orderinfo->currency)?$this->orderinfo->currency :$or_currency;

	if(isset($this->order_blocks))
	{
		$order_blocks = $this->order_blocks;
	}
	else
	{
		$order_blocks  = array ('0'=>'shipping','1'=>'billing','2'=>'cart','3'=>'order','4'=>'order_status');
	}

	$document = JFactory::getDocument();
	//$document->addScript(JUri::root().'components/com_quick2cart/assets/js/order.js');
	//$document->addStyleSheet(JUri::base().'components/com_quick2cart/assets/css/quick2cart.css' );//aniket

	$db = JFactory::getDBO();
	$json = '{"default":1}';
	$query = "SELECT title,address,phone,store_email,owner
	 FROM #__kart_store
	 WHERE extra LIKE '%".$json ."%'";//." AND o.payee_id=".$user->id;
	$db->setQuery($query);
	$invoice_result = $db->loadAssoc(); ?>


	<script src="<?php echo JUri::root().'components/com_quick2cart/assets/js/bootstrap-tooltip.js'?>"></script>
	<script src="<?php echo JUri::root().'components/com_quick2cart/assets/js/bootstrap-popover.js'?>"></script>

	<script type="text/javascript">
		techjoomla.jQuery(document).ready(function()
		{
			techjoomla.jQuery('.discount').popover(
			);

		});

		function	qtc_showpaymentgetways()
		{
			document.getElementById("qtc_paymentmethods").style.display='block';
		}
	</script>

	<div>
        <!-- header logo: style can be found in header.less -->
        <div class="wrapper row-offcanvas row-offcanvas-left">
          <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->

                <!-- Main content -->
                <section class="content invoice">
                    <!-- title row -->
                    <div class="row-fluid">
                        <div class="span12">
                            <h2 class="page-header">
                                <i class="fa fa-globe"></i> <?php echo JText::_('QTC_INVOICE_DETAIL')?>
                                <small style="float:right;"><?php echo JText::_('QTC_INVOICE_DATE');?>: <?php echo JFactory::getDate($this->orderinfo->cdate)->Format(JText::_("COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT"));?></small>
                            </h2>
                        </div><!-- /.span12 -->
                    </div>
                    <!-- info row -->
                    <div class="row-fluid invoice-info">
						<div class="span6 invoice-span">
						</div>
						<div class="span6 ">
							<span style="float:right;">
                            <b><?php echo JText::_('QTC_INVOICE_ID'); ?> </b> : <?php echo $this->orderinfo->prefix.$this->orderinfo->id; ?>
                            <br/>

                            <b><?php echo JText::_('QTC_INVOICE_DATE');?></b> : <?php echo JFactory::getDate($this->orderinfo->cdate)->Format(JText::_("COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT"));?><br/>
                            <b><?php echo JText::_('QTC_INVOICE_AMOUNT');?></b> : <?php echo $this->comquick2cartHelper->getFromattedPrice($this->orderinfo->amount,$order_currency);?> <br/>
                            <b><?php echo JText::_('QTC_INVOICE_USER');?>
								</b> :
									<?php
									$table   = JUser::getTable();
									$user_id = intval( $this->orderinfo->payee_id );
									if($user_id){
										$creaternm = '';
										if($table->load( $user_id ))
										{
											$creaternm = JFactory::getUser($this->orderinfo->payee_id);
										}
										echo (!$creaternm)?JText::_('QTC_NO_USER'): $creaternm->username;
									 }
									 else{
										 echo $billinfo->user_email;
									 }
										 ?>
								<br/>
                            <b><?php echo JText::_('QTC_INVOICE_PAID_MSG');?></b> : <?php echo JText::_('QTC_INVOICE_PAID');?> <br/>

							<?php if($this->orderinfo->processor){ ?>
								<b>
									<?php echo JText::_('QTC_INVOICE_PAYMENT');?></b> : <?php echo $this->orderinfo->processor;?>
								<br />
                            <?php } ?>

                         	<?php if($this->orderinfo->transaction_id){ ?>
								<b><?php echo JText::_('QTC_INVOICE_PAYMENT_TRANSAC');?></b> : <?php echo $this->orderinfo->transaction_id;?><br/>
							<?php } ?>

							<?php if(!empty($billinfo->vat_number)){ ?>
								<b><?php echo JText::_('QTC_BILLIN_VAT_NUM');?></b> : <?php echo $billinfo->vat_number;?> <br/>
							<?php } ?>

						</span>
                        </div><!-- /.col -->
                    </div><!-- /.row-fluid -->

					<div style="clear:both;"></div>
					<div style="  border-top: 1px solid #d8d8d8;margin-right: 0; margin-top: 6px;height: 5px;"></div>
					<?php
					$price_col_style = "style=\"".(!empty($orders_email)?'text-align: right;' :'')."\"";
					$showoptioncol=0;
					foreach($this->orderitems as $citem)
					{
						if(!empty($citem->product_attribute_names	)){
							$showoptioncol=1; // atleast one found then show
							break;
						}
					}
					?>
                    <!-- Table row-fluid -->
                    <div class="row-fluid">
                        <div class="span12 table-responsive">
                            <!-- Display cart detail -->
							<?php
							$view                = $this->comquick2cartHelper->getViewpath('orders', 'default_cartdetail');
							ob_start();
							include($view);
							$html = ob_get_contents();
							ob_end_clean();
							echo $html;	?>

                        </div><!-- /.col -->
                    </div><!-- /.row-fluid -->
                    <!-- this row will not appear when printing -->
                    <div class="row-fluid">
						<?php
						if (isset($this->email_table_bordered))
						{
							$this->email_table_bordered .= ";width:100%;";
						}
						else
						{
							$this->email_table_bordered = ";width:100%;";
						}
							// Display basic order detail.
							$view                = $this->comquick2cartHelper->getViewpath('orders', 'default_billing');
								ob_start();
									include($view);
									$html = ob_get_contents();
								ob_end_clean();
								echo $html;
						?>
					</div>
					<?php
					$mainSiteAdress = $this->params->get('mainSiteAdress');
					$vat_num = $this->params->get('vat_num');

					if ($mainSiteAdress || $vat_num)
					{
					?>
                    <hr/>
                    <div class="row-fluid no-print">
                        <div class="pull-right" style="  line-height: initial; color: gray;">
<!--
	@dj Site invoice detail and store invoice detail are different. Here we should display site detail.
-->
							 <b><i><?php echo JText::_('QTC_INVOICE_CONT_INFO'); ?></i></b> <br/>

							<?php
							if(!empty($mainSiteAdress))
							{
							?>
								<br />
								<b><?php echo JText::_('COM_QUICK2CART_INV_STIE_ADDRESS');?></b> :
								<?php echo $mainSiteAdress;
							}

							/*if(!empty($invoice_result['company']) )
							{ ?>
								<b><?php echo JText::_('QTC_INVOICE_COMP');?></b> :
								<?php echo $invoice_result['company_name'];?>
							<?php
							}
							else
							{ ?>
								<b><?php //echo JText::_('QTC_INVOICE_NAME');?></b> :
								<?php //echo $invoice_result['title'];
							}

							if(!empty($invoice_result['company']) )
							{ ?>
								<b><?php echo JText::_('QTC_INVOICE_ADDR');?></b> :
								<?php echo $invoice_result['address'];
							}

							if(!empty($invoice_result['phone']) )
							{ ?>
								<b><?php echo JText::_('QTC_INVOICE_PHON');?></b> :
								<?php echo $invoice_result['phone'];
							}
							?>
							<b><?php echo JText::_('QTC_INVOICE_EMAIL');?></b> :
							<?php echo $invoice_result['store_email'];

							*/
							if(!empty($vat_num))
							{
							?>
								<br /><b><?php echo JText::_('QTC_INVOICE_VAT');?></b> :
								<?php echo $vat_num;
							}
							?>
						</div>
					</div>
					<?php
					} ?>
				</section><!-- /.content -->
			</aside><!-- /.right-side -->
		</div><!-- ./wrapper -->
	</div>
</div>
<!--Q2C_WRAPPER_CLASS -->
