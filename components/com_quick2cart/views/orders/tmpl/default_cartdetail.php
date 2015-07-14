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
$this->productHelper = new productHelper;
?>
<!-- Start Cart detail -->
<?php
if (in_array('cart', $order_blocks))
{?>
	<div>
		<?php
		if ($orders_email)
		{ ?>
			<h4 <?php echo $emailstyle;?>><?php echo JText::_('QTC_ORDER_DETAILS');?></h4>
			<?php
		}
		elseif ($orders_site)
		{ ?>
			<!--<div class="fieldset_block">  -->
			<h4><?php echo JText::_('QTC_ORDER_DETAILS');?></h4>
			<?php
		}

		$price_col_style = "style=\"" . (!empty($orders_email) ? 'text-align: right;' : '') . "\"";
		$showoptioncol = 0;

		foreach ($this->orderitems as $citem)
		{
			if (!empty($citem->product_attribute_names))
			{
				$showoptioncol = 1; // atleast one found then show
				break;
			}
		}
		?>
		<div class="table-responsive">
			<table width="100%" class="table table-condensed table-bordered  " style="<?php echo $this->email_table_bordered; ?>">
				<!-- 	<tr> <td><h4 <?php echo $emailstyle;?> ><?php echo JText::_('QTC_ORDER_DETAILS');?></h4></td> </tr> -->
				<tr>
					<th class="cartitem_num" width="5%" align="right" style="<?php echo ($orders_email) ? 'text-align: left;' : '';?>" ><?php echo JText::_('QTC_NO');?></th>
					<!--			<th><?php //echo JHtml::_( 'grid.sort', JText::_('GETWAY'),'processor', $this->lists['order_Dir'], $this->lists['order']);?></th> -->
					<th class="cartitem_name" align="left" style="<?php echo ($orders_email) ? 'text-align: left;' : '';?>" ><?php echo JText::_('QTC_PRODUCT_NAM');?></th>
					<?php

					if ($showoptioncol == 1)
					{ ?>
					<th class="cartitem_opt" align="left" style="<?php echo ($orders_email) ? 'text-align: left;' : '';?>" ><?php echo JText::_('QTC_PRODUCT_OPTS');?></th>
					<?php
					}?>
					<th class="cartitem_qty" width="5%" align="left" style="<?php echo ($orders_email) ? 'text-align: left;' : '';?>" ><?php echo JText::_('QTC_PRODUCT_QTY');?></th>
					<th class="cartitem_price" align="left"
						<?php echo $price_col_style;?>><?php echo JText::_('QTC_PRODUCT_PRICE');?></th>
					<th class="cartitem_tprice" align="left" width="12%"
						<?php echo $price_col_style;?>><?php echo JText::_('QTC_PRODUCT_TPRICE');?></th>
				</tr>
				<?php
				$qtc_store_row_styles  = "";
				$qtc_store_row_classes = "info";
				if ($orders_email)
				{
					// here using INLINE STYLING FOR email instead of class "info"
					$qtc_store_row_style   = " background-color: #D9EDF7;";
					$qtc_store_row_classes = "";
				}
				if (version_compare(JVERSION, '3.0', 'lt'))
				{
					$qtc_icon_info = " icon-info-sign ";
				}
				else
				// for joomla3.0
				{
					$qtc_icon_info = " icon-wand ";
				}

				$tprice             = 0;
				$i                  = 1;
				$store_array        = array();
				$params             = JComponentHelper::getParams('com_quick2cart');
				$multivendor_enable = $params->get('multivendor');
				$orderItemIds = array();

				foreach ($this->orderitems as $order)
				{
					// IF MUTIVENDER ENDABLE then SHOW STORE TITILE
					if (!empty($multivendor_enable))
					{
						if (!in_array($order->store_id, $store_array))
						{
							$store_array[] = $order->store_id;
							$storeinfo     = $this->comquick2cartHelper->getSoreInfo($order->store_id);?>
							<tr class="<?php echo $qtc_store_row_classes;?>" style="<?php echo !empty($qtc_store_row_style) ? $qtc_store_row_style : '';?>">
								<td></td>
								<td colspan="<?php echo (($showoptioncol == 1) ? "5" : "4");?>">
									<strong><?php echo $storeinfo['title'];?></strong>
								</td>
							</tr>
						<?php
						}
					}?>
					<tr class="row0">
						<td class="cartitem_num"><?php echo $i++;?></td>
						<td class="cartitem_name">
							<?php
							$product_link = $this->comquick2cartHelper->getProductLink($order->item_id, 'detailsLink', 1);

							if (empty($product_link))
							{
								echo $order->order_item_name;
							}
							else
							{ ?>
								<a href="<?php	echo $product_link;?>"><?php echo $order->order_item_name;?></a>
							<?php
							}

							$orderItemIds[] = $order->order_item_id;

							// DOWNLOAD LINK
							if (!empty($this->orderinfo->status) && $this->orderinfo->status == 'C')
							{

								// check where has any media files
								$medisFiles = $this->productHelper->isMediaForPresent($order->order_item_id);

								if (!empty($medisFiles))
								{
									$myDonloadItemid = $this->comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=downloads');
									$downloadLink    = JUri::root() . substr(JRoute::_('index.php?option=com_quick2cart&view=downloads&orderid=' . $this->orderinfo->id . '&guest_email=' . $guest_email . '&Itemid=' . $myDonloadItemid), strlen(JUri::base(true)) + 1);?>
									<br>
									<a href="<?php echo $downloadLink;?>">
										<i class="icon-download-alt"></i><?php echo JText::_('QTC_ORDER_PG_DOWN_NOW');?>
									</a>
								<?php
								}
							}
							// Showing shipping method name
							if (!empty($order->item_shipDetail))
							{
								$item_shipDetail = json_decode($order->item_shipDetail,true);

								if (!empty($item_shipDetail['name']))
								{
									?>
										<span>
											<strong>
												<br />
												<?php echo JText::_('COM_QUICK2CART_ORDER_SHIP_METH') . ": " ?> </strong>

											<?php echo $item_shipDetail['name']; ?>
										</span>
									<?php
								}
							}
							?>

						</td>
							<?php

						if ($showoptioncol == 1)
						{ ?>
							<td class="cartitem_opt">
							<?php
								if (($order->product_attribute_names))
								{
									echo nl2br(str_replace(",", "\n", $order->product_attribute_names));
								}?>
							</td>
							<?php
						}?>

						<td class="cartitem_qty"><?php echo $order->product_quantity;?></td>
						<td class="cartitem_price" <?php echo $price_col_style;?>>
							<span><?php
								$prodprice = (float) ($order->product_item_price + $order->product_attributes_price);
								echo $this->comquick2cartHelper->getFromattedPrice(number_format($prodprice, 2), $order_currency);?>
								<?php
								if (!empty($order->params)) // if coupon found for order item
								{
									$total_prod_disc = 0;
									$item_c_code         = json_decode($order->params);
									// foreach($item_c_codes as $item_c_code)
									if (!empty($item_c_code->coupon_code))
									{
										$d_price       = -1;
										$prodcoupon    = 1;
										$coupon_detail = $this->comquick2cartHelper->getCouponDetail($item_c_code->coupon_code);
										if (!empty($coupon_detail['type'])) // percentage
										{
											$prod_disc = (((float) $coupon_detail["value"] * $prodprice) / 100);
										}
										else
										{
											// $d_price=$prodprice - (float)$coupon_detail["value"];
											$prod_disc = (float) $coupon_detail["value"];
										}
										$total_prod_disc += (float) $prod_disc;
										$d_price = $prodprice - (float) $total_prod_disc;
										// DISCOUNTED SUB TOATAL < 0 THEN MAKRE SUBTOTAL TO 0
										if ($d_price < 0)
											$d_price = 0; // $prodprice;

										$content = JText::_('QTC_DISCOUNT_CODE') . " : " . $item_c_code->coupon_code . "<br/>";
										$content .= JText::_('QTC_DISCOUNT') . " : " . $this->comquick2cartHelper->getFromattedPrice(number_format($prod_disc, 2));

										$prodprice = (!($d_price < 0)) ? $d_price : $prodprice;
										?>
										<?php

										if (!($d_price == -1))
										{ ?>
											<div class="qtc_putmargintop">
												<a class="discount label label-info" data-content="<?php echo $content;?> " data-placement="bottom" data-html="html"
													data-trigger="hover" rel="popover"
													data-original-title="<?php echo JText::_('QTC_DIS_POP_TITLE');?>">
														<i class="<?php echo $qtc_icon_info;?> icon-white"></i>
													<?php
														$dis_str = $this->comquick2cartHelper->getFromattedPrice(number_format(($prod_disc), 2));
														if ($orders_email)
														{
															echo JText::sprintf('QTC_PRO_DISCOUNT', $dis_str);
														}
														else
														{
															echo JText::sprintf('QTC_PRO_DISCOUNT_EMAIL', $dis_str);
														}?>
												</a>
											</div>
											<?php
										}
									}
								}?>
							</span>
						</td>
						<?php
							//~ $productPrice = $order->product_final_price - $order->item_tax - $order->item_shipcharges;
							//~ $tprice += $productPrice;

							$productPrice = ($order->product_quantity * $prodprice);
							$tprice += $productPrice;
							?>
						<td class="cartitem_tprice" <?php echo $price_col_style;?>>
							<span><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($productPrice, 2), $order_currency);?></span>
						</td>
					</tr>
					<?php
				}?>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
				<!--  sub total -->
				<?php
				$col = 3;
				if ($showoptioncol == 1)
				{
					$col = 4;
				}?>
				<tr>
					<td colspan="<?php echo $col;?>"></td>
					<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::_('QTC_PRODUCT_TOTAL');?></strong></td>
					<td class="cartitem_tprice" <?php echo $price_col_style;?>><span
						id="cop_discount"><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($tprice, 2), $order_currency);?></span></td>
				</tr>
				<?php
				// if not store releated view ( SHOW Comission cut & Value recd)
				if (!empty($this->storeReleatedView))
				{
					$storeHelper            = new storeHelper();
					$commission             = $params->get('commission');
					$commission_cutPrice    = $storeHelper->totalCommissionApplied($tprice);
					$commission_cutNetPrice = (float) $tprice - $commission_cutPrice;?>
					<!-- Commission price -->
					<tr>
						<td colspan="<?php echo $col;?>"></td>
						<td class="cartitem_tprice_label" align="left"><strong><?php echo sprintf(JText::_('QTC_COMMISSION_CUT_SUB_TOT'), '(' . $commission . '%)');?> 	</strong></td>
						<td class="cartitem_tprice" <?php echo $price_col_style;?>><span
							id="cop_discount"><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($commission_cutPrice, 2), $order_currency);?></span></td>
					</tr>
					<!-- CommissionCut net total -->
					<tr>
						<td colspan="<?php echo $col;?>"></td>
						<td class="cartitem_tprice_label" align="left"><strong><?php echo sprintf(JText::_('QTC_COMMISSION_CUT_NET_TOT'), '(' . $commission . '%)');?> 	</strong></td>
						<td class="cartitem_tprice" <?php echo $price_col_style;?>>
							<span id="cop_discount">
								<?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($commission_cutNetPrice, 2), $order_currency);?>
							</span>
						</td>
					</tr>
					<?php
				} // END OF SHOW Comission cut & Value recd)
				?>

				<!--discount price -->
				<?php
				// / if currently vendor specific code (CALLED FROM ::1.sending email to
				// vendor OR 2.viewing vendor it own order details)is NOT running CODE OR
				// vendor specific
				if (!isset($this->vendor_email))
				{
					if ($vendor_order_view == 0)
					{
						$coupon_code = trim($coupon_code);
						if (!empty($coupon_code))
						{
							JLoader::import('cartcheckout', JPATH_SITE . '/components/com_quick2cart/models');

							$Quick2cartModelcartcheckout = new Quick2cartModelcartcheckout();
							$dis_totalamt                = $Quick2cartModelcartcheckout->afterDiscountPrice($tprice, $coupon_code, $this->orderinfo->payee_id, 'order', $this->orderinfo->id);
							$discount                    = $tprice - $dis_totalamt;?>
							<tr>
								<td colspan="<?php echo $col;?>"></td>
								<td class="cartitem_tprice_label" align="left"><strong><?php echo sprintf(JText::_('QTC_PRODUCT_DISCOUNT'), $coupon_code);?></strong></td>
								<td class="cartitem_tprice" <?php echo $price_col_style;?>>
									<span
									id="coupon_discount">
									<?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($discount, 2), $order_currency);?>
									</span>
								</td>
							</tr>
							<!-- total amt after Discount row-->
							<tr class="dis_tr" <?php // echo $cop_style;?>>
								<td colspan="<?php echo $col;?>"></td>
								<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::_('QTC_NET_AMT_PAY');?></strong></td>
								<td class="cartitem_tprice" <?php echo $price_col_style;?>><span
									id="total_dis_cop">
								<?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($dis_totalamt, 2), $order_currency);?></span>
								</td>
							</tr>
								<?php
						}
						$orderTaxAmount = (float)$this->orderinfo->order_tax;
						if (!empty($orderTaxAmount))
						{
							// tax data
							$orderTaxPer = '';
							if (!empty($this->orderinfo->order_tax_details))
							{
								$orderTaxPerDetail = json_decode($this->orderinfo->order_tax_details);
								if (!empty($orderTaxPerDetail))
								{
									$orderTaxPer = $orderTaxPerDetail->val;
								}
							}?>
								<tr>
									<td colspan="<?php echo $col;?>"></td>
									<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::sprintf('QTC_TAX_AMT_PAY', $orderTaxPer);?></strong></td>
									<td class="cartitem_tprice" <?php echo $price_col_style;?>><span
										id="tax_amt"><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($this->orderinfo->order_tax, 2), $order_currency);?></span></td>
								</tr>
								<?php
						}?>
						<?php
						$orderShipAmount = (float)$this->orderinfo->order_shipping;
						if(!empty($orderShipAmount)){ ?>
							<tr>
								<td colspan="<?php echo $col;?>"></td>
								<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::sprintf('QTC_SHIP_AMT_PAY', '');?></strong></td>
								<td class="cartitem_tprice" <?php echo $price_col_style;?>>
									<span	id="ship_amt">
										<?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($this->orderinfo->order_shipping, 2), $order_currency);?>
									</span>
								</td>
							</tr>
							<?php
						}?>
								<!--  final order  total -->
						<tr>
							<td colspan="<?php echo $col;?>"></td>
							<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::_('QTC_ORDER_TOTAL');?></strong></td>
							<td class="cartitem_tprice" <?php echo $price_col_style;?>>
								<strong>
									<span id="final_amt_pay" name="final_amt_pay">
									<?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($this->orderinfo->amount, 2), $order_currency);?>
									</span>
								</strong>
							</td>
						</tr>
						<?php
					} // end of if vendor_order_view==0
				} // end of$this->vendor_email=1;?>
			</table>

		</div> <!--table-responsive -->
	</div>
	<?php
}?>
<!-- End Cart detail -->
