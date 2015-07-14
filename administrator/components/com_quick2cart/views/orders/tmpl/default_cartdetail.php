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
$this->params             = JComponentHelper::getParams('com_quick2cart');
?>
<!-- Start Cart detail -->
<?php
if (in_array('cart', $order_blocks))
{?>
	<h4><?php echo JText::_("COM_QUICK2CART_CART_DETAILS")?></h4>
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

		$totalItemTax = 0;
		$totalItemShip = 0;
		$itemLevalTaxship = 0;
		$orderItemIds = array();

		foreach ($this->orderitems as $citem)
		{
			if (!empty($citem->product_attribute_names) && $showoptioncol == 0)
			{
				$showoptioncol = 1; // atleast one found then show
				//break;
			}

			if ($itemLevalTaxship == 0)
			{

				if ($citem->item_tax > 0 || $citem->item_shipcharges > 0)
				{
					$itemLevalTaxship = 1;
				}
			}
		}

		?>
		<div class="table-responsive">
			<form action="" name="orderItemForm" id="orderItemForm" class=" form-validate " method="post">
				<table width="100%" class="table table-condensed table-bordered adminlist">
					<tr>
						<th class="cartitem_num" width="5%" align="right" style="<?php echo ($orders_email)?'text-align: left;' :'';  ?>" ><?php echo JText::_('QTC_NO'); ?></th>
						<th class="cartitem_name" align="left" style="<?php echo ($orders_email)?'text-align: left;' :'';  ?>" ><?php echo  JText::_('QTC_PRODUCT_NAM'); ?></th>
						<?php
						if ($showoptioncol == 1)
						{ ?>
							<th class="cartitem_opt" align="left" style="<?php echo ($orders_email)?'text-align: left;' :'';  ?>" ><?php echo JText::_('QTC_PRODUCT_OPTS'); ?></th>
						<?php
						} ?>
						<th class="cartitem_qty q2c_width_10" align="left" style="<?php echo ($orders_email)?'text-align: left;' :'';  ?>" ><?php echo JText::_('QTC_PRODUCT_QTY'); ?></th>
						<th class="cartitem_price q2c_width_25" width="20%" align="left"
							<?php echo $price_col_style;  ?>><?php echo JText::_('QTC_PRODUCT_PRICE'); ?></th>
<!--
						<th class="cartitem_tprice" align="left"
							<?php echo $price_col_style;  ?>><?php echo JText::_('QTC_PRODUCT_TPRICE'); ?></th>
-->
					</tr>
					<?php
					$qtc_store_row_styles  = "";
					$qtc_store_row_classes = "info";

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

					$multivendor_enable = $this->params->get('multivendor');

					foreach ($this->orderitems as $order)
					{
						// IF MUTIVENDER ENDABLE then SHOW STORE TITILE
						if (! empty($multivendor_enable))
						{
							if (! in_array($order->store_id, $store_array))
							{
								$store_array[] = $order->store_id;
								$storeinfo = $this->comquick2cartHelper->getSoreInfo($order->store_id);
								?>
								<tr class="<?php echo $qtc_store_row_classes;?>">
									<td></td>
									<td colspan="<?php echo ( ($showoptioncol==1) ?"6" : "4" ); ?>">
										<strong><?php echo $storeinfo['title'];?></strong>
									</td>
								</tr>
								<?php
							}
						} ?>

						<tr class="row0">
							<td class="cartitem_num"><?php echo $i++;?></td>
							<td class="cartitem_name">
								<?php
								$product_link = $this->comquick2cartHelper->getProductLink($order->item_id, "detailsLink", 1);

								if (empty($product_link))
								{
									echo $order->order_item_name;
								}
								else
								{ ?>

									<a class="no-print" href="<?php echo $product_link;?>">
										<?php echo $order->order_item_name; ?>
									</a>
									<span class="q2c-display-none print-this ">
										<?php echo $order->order_item_name; ?>
									</span> <?php
								}
 								?>
 								<span>
									<strong>
 								<?php
 									$prodprice = (float) ($order->product_item_price + $order->product_attributes_price);
									echo " <br/ >Price :" . $this->comquick2cartHelper->getFromattedPrice(number_format($prodprice, 2), $order_currency);
 								?>
									</strong>
 								</span>

 								<span>
								<?php
								//print"<pre>"; print_r($order);
								//$prodprice = (float) ($order->product_item_price + $order->product_attributes_price);
								//echo " Unit Price :" . $this->comquick2cartHelper->getFromattedPrice(number_format($prodprice, 2), $order_currency);

								if (! empty($order->params)) // if coupon found for order item
								{
									$total_prod_disc = 0;
									$item_c_code = json_decode($order->params);
									// foreach($item_c_codes as $item_c_code)
									if (! empty($item_c_code->coupon_code))
									{
										$d_price = - 1;
										$prodcoupon = 1;
										$coupon_detail = $this->comquick2cartHelper->getCouponDetail($item_c_code->coupon_code);
										if (! empty($coupon_detail['type'])) // percentage
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
										{
											$d_price = 0; // $prodprice;
										}

										$content = JText::_('QTC_DISCOUNT_CODE') . " : " . $item_c_code->coupon_code . "<br/>";
										$content .= JText::_('QTC_DISCOUNT') . " : " . $this->comquick2cartHelper->getFromattedPrice(number_format($prod_disc, 2));

										$pro_price = (! ($d_price == - 1)) ? $d_price : $prodprice;
										?>
											<?php

										if (! ($d_price == - 1))
										{
											?>
											<div class="qtc_putmargintop">
												<a class="discount label label-info" data-content="<?php echo $content;?> " data-placement="bottom" data-html="html"
													data-trigger="hover" rel="popover"
													data-original-title="<?php echo JText::_( 'QTC_DIS_POP_TITLE' ); ?>">
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
														}
														?>

												</a>
											</div>
											<?php
										}
									}
								}
								?>
								</span>

								<input class="inputbox cart_fields" id="" name="<?php echo 'cartDetail[' . $order->order_item_id . '][order_item_id]'; ?>" type="hidden" value="<?php echo $order->order_item_id; ?>" size="5">
								<?php
								$orderItemIds[] = $order->order_item_id;

								// DOWNLOAD LINK
								if (! empty($this->orderinfo->status) && $this->orderinfo->status == 'C')
								{
									// check where has any media files
									$medisFiles = $this->productHelper->isMediaForPresent($order->order_item_id);

									if (! empty($medisFiles))
									{
										$myDonloadItemid = $this->comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=downloads');
										$downloadLink = JUri::root() . substr(JRoute::_('index.php?option=com_quick2cart&view=downloads&orderid=' . $this->orderinfo->id . '&guest_email=' . $guest_email . '&Itemid=' . $myDonloadItemid), strlen(JUri::base(true)) + 1);
										?>
										<br class="no-print">
										<a href="<?php echo $downloadLink ;?>" class="no-print">
											<i class="icon-download-alt"></i>
											<?php echo JText::_('QTC_ORDER_PG_DOWN_NOW'); ?>
										</a>
										<?php
									}
								}
								?>

							</td>

							<?php

							if ($showoptioncol == 1)
							{
								?>
								<td>
								<?php
								if (!empty($order->prodAttributeDetails))
								{
									// Seleted product attributes ids
									$product_attributes = explode(',', $order->product_attributes);

									// Show each product attribute
									foreach ($order->prodAttributeDetails as $key=>$attribute)
									{
										?>
										<div class="control-group">
											<label class="control-label ">
												<?php echo $attribute->itemattribute_name; ?>
											</label>

											<!-- Store att type-->
											<input class="" id="" name="<?php echo 'cartDetail[' . $order->order_item_id . '][attrDetail][' . $attribute->itemattribute_id . '][type]'; ?>" type="hidden" value="<?php echo $attribute->attributeFieldType ?>" >

											<?php

											// For text type attribute
											if (! empty($attribute->attributeFieldType) && $attribute->attributeFieldType == 'Textbox')
											{ ?>
												<div class="controls">
													<?php

														if(!empty($attribute->optionDetails[0]->itemattributeoption_id))
														{
															$itemattributeoption_id = $attribute->optionDetails[0]->itemattributeoption_id;
														}
														else
														{
															$itemattributeoption_id = 'new';
														}
														$TextFieldValue = $attribute->itemattribute_name;
													?>

													<input type="text" name="<?php echo 'cartDetail[' . $order->order_item_id . '][attrDetail][' . $attribute->itemattribute_id . '][value]' ?>"
														class="input input-small <?php echo $itemattributeoption_id.'_Textbox'?>"
														value ="<?php echo $TextFieldValue; ?>"

														<!-- Attribute option id -->
													<input type="hidden" name="<?php echo 'cartDetail[' . $order->order_item_id . '][attrDetail][' . $attribute->itemattribute_id . '][itemattributeoption_id]' ?>" class="input input-small" value ="<?php echo $itemattributeoption_id ; ?>" />

												</div>

												<?php
											}
											else
											{
												foreach ($attribute->optionDetails as $optionDetail)
													{
														if(	in_array($optionDetail->itemattributeoption_id, $product_attributes)	)
														{
															$data['default_value'] = $optionDetail->itemattributeoption_id;
															break;
														}
													}

													$productHelper = new productHelper();
													$data['itemattribute_id'] = $attribute->itemattribute_id;
													$data['fieldType'] = $attribute->attributeFieldType;
													$data['product_id'] = $order->item_id;
													$data['attribute_compulsary'] = $attribute->attribute_compulsary;

													$data['field_name'] = 'cartDetail[' .  $order->order_item_id . '][attrDetail][' . $attribute->itemattribute_id . '][value]';

													// Generate field html (select box)
													$fieldHtml = $productHelper->getAttrFieldTypeHtml($data);
													?>
													<div class="controls">
														<?php echo $fieldHtml;?>
													</div>
													<?php


											}
											// else end
											?>
										</div>
									<?php
									}

								}
								?>
								</td>
									<?php
							}
							?>

							<td class="cartitem_qty"><?php //echo $order->product_quantity;?>
								<input class="cart_fields input-mini"  name="<?php echo 'cartDetail[' . $order->order_item_id . '][cart_count]' ?>" type="text" value="<?php echo $order->product_quantity; ?>">
							</td>
							<?php

								// product_final_price (its discounted product total prce+tax +ship amt)
								//$tprice += $order->product_final_price;
								$productPrice = $order->product_final_price - $order->item_tax - $order->item_shipcharges;
								$tprice += $productPrice;
								//$tprice += $order->product_final_price;

								$totalItemTax += $order->item_tax;
								$totalItemShip += $order->item_shipcharges;

							?>
							<td class="cartitem_price" <?php echo $price_col_style;  ?>>
								<div>

									<strong><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($productPrice,2),$order_currency); ?></strong>
								</div>

								<?php
								if ($itemLevalTaxship == 1 )
								{ ?>
								<div >
									<strong> +</strong>
									<?php echo " " . JText::_("COM_QUICK2CART_ITEM_TAX") . " :"; ?>
									<input type="text" name="itemTaxShipDetail[<?php echo $order->order_item_id ?>][tax]" class="input input-mini" id="" value="<?php echo $order->item_tax?>">

								</div>
								<div>
									<strong> +</strong>
									<?php echo " " . JText::_("COM_QUICK2CART_ITEM_SHIP") . " :"; ?>
									<input type="text" name="itemTaxShipDetail[<?php echo $order->order_item_id ?>][ship]" class="input input-mini" id="" value="<?php echo $order->item_shipcharges?>">
								</div>
								<span> Total : <?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($order->product_final_price,2),$order_currency); ?></span>
								<?php
								}
								?>


							</td>

	<!--						<td class="cartitem_tprice" <?php echo $price_col_style;  ?>>

								<span><strong><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($order->product_final_price,2),$order_currency); ?></strong></span>
							</td>
							-->

						</tr>
					<?php
					}
					?>
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<!--  sub total -->
					<?php
					$col = 2;
					if ($showoptioncol == 1)
					{
						$col = 3;
					}?>
					<tr>
						<td colspan="<?php echo $col;?>"></td>
						<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::_('QTC_PRODUCT_TOTAL');?></strong></td>
						<td class="cartitem_tprice" <?php echo $price_col_style;?>><span
							id="cop_discount"><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($tprice, 2), $order_currency);?></span></td>
					</tr>

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
							//if (!empty($orderTaxAmount))
							{
								// tax data
								$orderTaxPer = '';
								if (!empty($this->orderinfo->order_tax_details))
								{
									$orderTaxPerDetail = json_decode($this->orderinfo->order_tax_details);

									if (!empty($orderTaxPerDetail->val))
									{
										$orderTaxPer = $orderTaxPerDetail->val;
									}
								}?>
									<?php
							}?>

							<tr>
								<td colspan="<?php echo $col;?>"></td>
								<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::sprintf('QTC_TAX_AMT_PAY', $orderTaxPer);?></strong></td>
								<td class="cartitem_tprice" <?php echo $price_col_style;?>>
								<?php
								$ttax = number_format($this->orderinfo->order_tax + $totalItemTax , 2);
								if ($itemLevalTaxship == 0 )
								{
								?>
									<div class="input-append ">

										<input type="text" name="OrderTaxShipDetail[tax]" class="input input-small" id=""  value="<?php echo $ttax ?>">
										<span class="add-on"><?php echo $order_currency; ?></span>

									</div>
<!--
									<span class="pull-righ">
										<img class="pull-right" src="<?php echo JUri::root(true) ?>/components/com_quick2cart/assets/images/updatecart.png" height="15" width="15" title="<?php echo JText::_('COM_QUICK2CART_UPDAE_CART_ITEM_DESC');?>" onClick='updateOrderItemAttribute("<?php echo $this->orderinfo->id ;?>","<?php echo $order->order_item_id;?>","<?php echo JText::_('COM_QUICK2CART_ORDER_UPDATED'); ?>")'>
									</span>
-->
								<?php
								}
								else
								{
								?>
									<span
									id="tax_amt"><?php echo $this->comquick2cartHelper->getFromattedPrice($ttax , $order_currency);?></span>
									<?php
								}
								?>


								</td>
							</tr>
							<?php
							$orderShipAmount = (float)$this->orderinfo->order_shipping;
							//if(!empty($orderShipAmount))
							{?>
								<tr>
									<td colspan="<?php echo $col;?>"></td>
									<td class="cartitem_tprice_label" align="left"><strong><?php echo JText::sprintf('QTC_SHIP_AMT_PAY', '');?></strong></td>
									<td class="cartitem_tprice" <?php echo $price_col_style;?>>
										<?php
										$tship = number_format($this->orderinfo->order_shipping + $totalItemShip , 2);
										if ($itemLevalTaxship == 0 )
										{
										?>
											<div class="input-append ">

												<input type="text" name="OrderTaxShipDetail[ship]" class="input input-small" id=""  value="<?php echo $tship ?>">
												<span class="add-on"><?php echo $order_currency; ?></span>
											</div>
<!--
											<span class="pull-righ">
												<img class="pull-right" src="<?php echo JUri::root(true) ?>/components/com_quick2cart/assets/images/updatecart.png" height="15" width="15" title="<?php echo JText::_('COM_QUICK2CART_UPDAE_CART_ITEM_DESC');?>" onClick='updateOrderItemAttribute("<?php echo $this->orderinfo->id ;?>","<?php echo '55';?>","<?php echo JText::_('COM_QUICK2CART_ORDER_UPDATED'); ?>")'>
											</span>
-->
										<?php
										}
										else
										{
										?>
											<span
											id="tax_amt"><?php echo $this->comquick2cartHelper->getFromattedPrice($tship , $order_currency);?></span>
											<?php
										}
										?>

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



									<button type="button" class="btn btn-success pull-right no-print" onClick='updateOrderItemAttribute("<?php echo $this->orderinfo->id ;?>", "<?php echo JText::_('COM_QUICK2CART_ORDER_UPDATED', true); ?>")' title="<?php echo JText::_('COM_QUICK2CART_UPDATE_ORDER_CART_DESC', true); ?>"><?php echo JText::_('COM_QUICK2CART_UPDATE_ORDER_CART', true); ?></button>


								</td>
							</tr>
							<?php
						} // end of if vendor_order_view==0
					} // end of$this->vendor_email=1;?>
				</table>
			</form>
		</div> <!--table-responsive -->
	</div>
	<?php
}?>
<!-- End Cart detail -->

