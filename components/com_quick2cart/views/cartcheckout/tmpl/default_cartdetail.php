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

// TO use lanugage cont in javascript
JText::script('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_SUCCESS', true);
JText::script('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_FAIL', true);

?>
	<!-- CART DETAIL START-->
<div class="qtc_chekout_cartdetailWrapper qtcAddBorderToWrapper"  class="broadcast-expands">
	<!--<legend><?php echo JText::_('QTC_CART')?>&nbsp;<small><?php echo JText::_('QTC_CART_DESC')?></small></legend> -->
	<?php $align_style='align="right"'; ?>
	<div>
		<?php echo $this->beforecart ?>
	</div>

	<?php

	$comparams = JComponentHelper::getParams('com_quick2cart');
	$currencies=$comparams->get('addcurrency');
	$currencies_sym=$comparams->get('addcurrency_sym');
	$default=$helperobj->getCurrencySession();

	//print_r($currencies);
	$multi_curr =$currencies;//"INR,USD,AUD"; //@TODO get this from the component params
	$option = array();
	if($multi_curr)
	{
	?>
		<div class="qtcChekoutCurrSelect" style=""> <!-- ///drop down  -->
			<?php
			$multi_currs = explode(",",$multi_curr);
			$currencies_syms = explode(",",$currencies_sym);
			foreach($multi_currs as $key => $curr){
				if(!empty($currencies_syms[$key]) ){
					$currtext = $currencies_syms[$key];
				}
				else{
					$currtext = $curr;
				}

				$option[] = JHtml::_('select.option', trim($curr), trim($currtext));
			}
			$cur_display='';
			if(count($multi_currs)==1){
				$cur_display= 'style="display:none"';
			}
			?>
			<div <?php echo $cur_display;?> > <span><?php echo JText::_('QTC_SEL_CURR');?> </span>
			<?php
			echo JHtml::_('select.genericlist',$option, "multi_curr", 'class="" onchange=" document.getElementById(\'task\').value=\'cartcheckout.setCookieCur\';document.adminForm.submit();" autocomplete="off" ', "value", "text", $default );
			?>

			</div>
	</div> <!-- ///drop down END -->
		<div style="clear:both;"></div>
		<?php
	}
	$showqty_style = "";
	$showqty = $comparams->get('qty_buynow',1);
	if( empty($showqty) )
	{
		$showqty_style = "display:none;";
	}
		?>
	<div class="table-responsive">
<!--
		<form method="post" name="cartForm" id="cartForm" enctype="multipart/form-data" class="form-horizontal form-validate" onsubmit="">
-->
			<table class="table table-striped ">
				<thead>
					<tr>
						<th class="cartitem_name"  align="left"><b><?php echo JText::_( 'QTC_CART_TITLE' ); ?> </b></th>
					<?php
					if($showoptioncol==1)
					{?>
						<th class="cartitem_opt" width="20%"	align="left"><b><?php echo JText::_( 'QTC_CART_OPTS' ); ?></b> </th>
					<?php	} ?>

						<th class="cartitem_price" width="15%"	align="left"><b><?php echo JText::_( 'QTC_CART_PRICE' ); ?></b> </th>
						<th style="<?php echo $showqty_style; ?>" class="cartitem_qty" width="15%"	align="left"><b><?php echo JText::_( 'QTC_CART_QTY' ); ?></b> </th>
						<th class="cartitem_tprice" width="15%"	<?php echo $align_style ?>><b><?php echo JText::_( 'QTC_CART_TOTAL_PRICE' ); ?> </b></th>
						<th width="5%"	align="left"></th>
					</tr>
				</thead>

				<tbody>
					<?php
					$tprice = 0;
					$store_array=array();
					if(version_compare(JVERSION, '3.0', 'lt')) {
							$qtc_icon_info=" icon-info-sign ";
					}
					else
					{ // for joomla3.0
						$qtc_icon_info=" icon-wand ";
					}

					$params = JComponentHelper::getParams('com_quick2cart');
					$multivendor_enable=$params->get('multivendor');
					$storeHelper = new storeHelper();

					foreach($this->cart as $cart)
					{
						// IF MUTIVENDER ENDABLE
						if(!empty($multivendor_enable))
						{
							if(!in_array($cart['store_id'], $store_array))
							{
								$store_array[]=$cart['store_id'];
								$storeinfo=$helperobj->getSoreInfo($cart['store_id']);
								$storeLink   = $storeHelper->getStoreLink($cart['store_id']);
								?>
								<tr class="info">
									<td colspan="<?php echo ( ($showoptioncol==1) ?"7" : "6" ); ?>" >
										<strong><a href="<?php echo $storeLink; ?>"><?php echo $storeinfo['title'];?></a></strong>
									</td>
								</tr>
							<?php
							}
						}?>
						<?php
								$product_link=$helperobj->getProductLink($cart['item_id']);
						?>
						<tr>
							<td class="cartitem_name" >
<!--
								<input class="inputbox cart_fields" id="cart_id" name="cart_id[]" type="hidden" value="<?php echo $cart['id']; ?>" size="5">
-->
								<input class="inputbox cart_fields" id="" name="<?php echo 'cartDetail[' . $cart['id'] . '][cart_item_id]'; ?>" type="hidden" value="<?php echo $cart['id']; ?>" size="5">

								<?php

								$images = $cart['item_images'];

								// Amol it req for Q2c changes
								//~ $img = JUri::base().'components/com_quick2cart/assets/images/default_product.jpg';
//~
								//~ if (!empty($images))
								//~ {
									//~ $file_name_without_extension = $this->media->get_media_file_name_without_extension($images[0]);
									//~ $file_name_without_extension;
									//~ $media_extension = $this->media->get_media_extension($images[0]);
									//~ $img = $helperobj->isValidImg($file_name_without_extension.'_S.'.$media_extension);
//~
									//~ if (empty($img))
									//~ {
										//~ $img = JUri::base().'components/com_quick2cart/assets/images/default_product.jpg';
									//~ }
								//~ } ?>

<!--
								<div class="caption">
									<a title="<?php echo $cart['title']; ?>" href="<?php echo $product_link; ?>">
										<img class=' img-rounded q2c_pin_image'
											src="<?php echo $images[0];?>"
											alt="<?php echo  JText::_('QTC_IMG_NOT_FOUND') ?>"
											title="<?php echo $cart['title']; ?>"
										/>
									</a>
								</div>
-->
								<?php

								if(empty($product_link))
								{
									echo $cart['title'];
								}
								else
								{
									?>
									<a href="<?php echo $product_link;?>"><?php echo $cart['title']; ?></a>
									<?php
								}
								?>
							</td>

								<!-- Product Options (Show editable cart attributes) -->
								<?php

							if($showoptioncol==1)
							{ ?>
								<td class="cartitem_opt" >
									<?php

									//$cart['prodAttributeDetails'] = '';
									if (!empty($cart['prodAttributeDetails']))
									{
										// seleted product attributes ids
										$product_attributes = explode(',', $cart['product_attributes']);

										// Show each product attribute
										foreach ($cart['prodAttributeDetails'] as $key=>$attribute)
										{
											?>
											<div class="qtc_bottom">
												<?php echo $attribute->itemattribute_name; ?>
												<input class="" id="" name="<?php echo 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][type]'; ?>" type="hidden" value="<?php echo $attribute->attributeFieldType ?>" >


												<?php

												// For text type attribute
												if (! empty($attribute->attributeFieldType) && $attribute->attributeFieldType == 'Textbox')
												{
													if(isset($attribute->optionDetails[0]->itemattributeoption_id))
													{
														$itemattributeoption_id = $attribute->optionDetails[0]->itemattributeoption_id;
													}
													else
													{
														$itemattributeoption_id = 'new';
													}

													$value = isset($cart['product_attributes_values'][$attribute->optionDetails[0]->itemattributeoption_id]->cartitemattribute_name)?$cart['product_attributes_values'][$attribute->optionDetails[0]->itemattributeoption_id]->cartitemattribute_name:'';
													?>
													<br/>

													<input type="text"
														name="<?php echo 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][value]' ?>"
														class="input input-small"
														value ="<?php echo $value; ?>"
													/>
													<!-- Attribute option id -->
													<input type="hidden" name="<?php echo 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][itemattributeoption_id]' ?>" class="input input-small" value ="<?php echo $itemattributeoption_id; ?>" />
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
													$data['product_id'] = $cart['item_id'];
													$data['attribute_compulsary'] = $attribute->attribute_compulsary;


													$attrDetailsObject = $cart['product_attributes_values'][$data['default_value']];
													//$data['field_name'] = 'attri_option'.$attrDetailsObject->cartitemattribute_id;
													$data['field_name'] = 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][value]';

													// Generate field html (select box)
													$fieldHtml = $productHelper->getAttrFieldTypeHtml($data);
													?>
													<br/>
														<?php echo $fieldHtml;?>

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
							} ?>

							<td class="cartitem_price" id="cart_price" name="cart_price[]">
								<div>
									<?php
									$original_prod_price=$pro_price=$cart['amt'] + $cart['opt_amt'];
									echo $helperobj->getFromattedPrice(number_format($original_prod_price,2));  ?>
									<?php
									if(!empty($coupons))
									{
										$cop_i = 0;
										$total_prod_disc = 0;
										$prodprice=(float)($cart["product_item_price"]	+ $cart["product_attributes_price"]);
										foreach($coupons as $k=>$coupon){
											$d_price=-1;
											if(!empty($coupon['item_id'])){
												if(in_array($cart['item_id'],  $coupon['item_id'], $strict = null))
												{
													//echo $cart['item_id'];echo ';;;';print_r($coupon);
													//  get coupon details  // is product specific coupon
													$prodcoupon=1;

													if($coupon['val_type'] == 1) // percentage
													{
														$prod_disc =  (((float)$coupon["value"]*$prodprice )/100) ;
													}
													else
													{
														$prod_disc = $coupon["value"];
													}
													$total_prod_disc += (float)$prod_disc;
													$d_price=$prodprice - (float)$total_prod_disc;

													// DISCOUNTED SUB TOATAL < 0 THEN MAKRE SUBTOTAL TO 0
													if($d_price < 0)
														$d_price=0;//$prodprice;

													$content=JText::_( 'QTC_DISCOUNT_CODE' )." : ".$coupon['code']."<br/>";
													$content .=JText::_( 'QTC_DISCOUNT' )." : ". $helperobj->getFromattedPrice(number_format($prod_disc,2));
													// if we use break then first coupon will be applies OTHERWISE last coupon will be applies
													//break;// apply get first  coupon
													$cop_i=$k;
												}
											}

										 $pro_price=(!($d_price==-1))?$d_price:$pro_price;


										?>

											<?php if(!($d_price==-1))
											{
											?>
											<div class="qtc_putmargintop">
												<input type="checkbox" id = "couponschk_<?php echo $cop_i;?>"  autocomplete="off" name = "couponschk[]" value="<?php echo $coupons[$cop_i]['code']; ?>" size= "10" onchange="remove_cop('<?php echo $cop_i;?>');" checked  />
												<a class="discount label label-info" data-content="<?php echo $content;?> " data-placement="top" data-html="html"  data-trigger="hover" rel="popover"  data-original-title="<?php echo JText::_( 'QTC_DIS_POP_TITLE' ); ?>">
													<i class="<?php echo $qtc_icon_info;?> icon-white"></i>
														<?php
														 $dis_str=$helperobj->getFromattedPrice(number_format(($prod_disc) ,2));
														  echo JText::sprintf('QTC_PRO_DISCOUNT',$dis_str); //echo JText::_( 'QTC_PRO_DISCOUNT' );
														 ?>
												</a>
											</div>
											<?php
											}
										}
									} // end of if(!empty($coupon))
											?>
								</div>
							</td>
							<td style="<?php echo $showqty_style; ?>" class="cartitem_qty" >
								<?php
								$minmax=$helperobj->getMinMax($cart['item_id']);
								$minmsg=JText::_( 'QTC_MIN_LIMIT_MSG' );
								$maxmsg= JText::_( 'QTC_MAX_LIMIT_MSG' );
								$qtc_min=isset($minmax['min_quantity'])?$minmax['min_quantity']:1;
								$qtc_max=isset($minmax['max_quantity'])?$minmax['max_quantity']:999;
								$caltotal_params="'".$cart['id'] ."',".$cart['amt'].",".$qtc_min.",".$qtc_max.",'".$minmsg."','".$maxmsg."'";
								?>
								<input type ="hidden" id="quantity_parmas_<?php echo $cart['id'];?>" value="<?php echo $caltotal_params ;?> " />

								<input id ="quantity_field_<?php echo $cart['id'];?>" class="cart_fields input-mini" id="cart_count" name="<?php echo 'cartDetail[' . $cart['id'] . '][cart_count]' ?>" type="text" value="<?php echo $cart['qty'];?>" size="5" maxlength="3">
							</td>
							<td class="cartitem_tprice" <?php echo $align_style ?> >
								<span id="cart_total_price<?php echo $cart['id'];?>"><?php echo $helperobj->getFromattedPrice(number_format(($pro_price * $cart['qty']) ,2));  ?>
								</span>
								<?php
								$tprice =$tprice + ($pro_price * $cart['qty']);

								?>
							</td>
							<td >
								<span >
									<img class="" src="<?php echo JUri::root(true) ?>/components/com_quick2cart/assets/images/updatecart.png" height="15" width="15" title="<?php echo JText::_('COM_QUICK2CART_UPDAE_CART_ITEM_DESC');?> " onclick="updateCartItemsAttribute('<?php echo $cart['id'];?>', '<?php echo $cart['item_id']; ?>')">
								</span>
								<span class="close remove_cart" onclick="removecart('<?php echo $cart['id'];?>');" title="<?php echo JText::_('QTC_CKOUT_REMVOVE_FROM_CART'); ?>">

								<?php echo "&times;" ; // JHtml::tooltip(JText::_('QTC_CKOUT_REMVOVE_FROM_CART'), '', '', '&times;' ) ;?>

								</span>
							</td>
						</tr>

					<?php
					} // END OF FOR EACH
					?>
				</tbody>

			</table>
<!--
		</form>
-->
	</div>
	<div class="table-responsive">
		<table class="table">
		<thead >
			<tr >
				<th  align="left"></th>
				<?php
				if($showoptioncol==1)
				{?>
					<th width="20%"	align="left"> </th>
				<?php
				} ?>
				<th width="15%"	align="left"> </th>
				<th width="15%"	align="left"></th>
				<th width="20%"	<?php echo $align_style ?>></th>
				<th width="5%"	align="left"></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php
				$col=2;

				if($showoptioncol==1)
				{	 $col=3;
				}?>

				<?php
				$msg_order_js = "'".JText::_('QTC_CART_EMPTY_CONFIRMATION')."','".JText::_('QTC_CART_EMPTIED')."'";
				?>
				<td colspan = "<?php echo $col; ?>">
					<button type="button" class="btn btn-danger btn-small" onclick="emptycart(<?php echo $msg_order_js ?>);" ><i class="icon-trash icon-white"></i>&nbsp;<?php echo JText::_('QTC_BTN_EMPTY_CART')?></button>
				</td>
				<td  class="cartitem_tprice_label" align="right" style="text-align: right;">
					<?php echo JText::_( 'QTC_TOTALPRICE' ); ?>
				</td>
				<td class="cartitem_tprice" <?php echo $align_style ?> style="text-align: center;"><span name="total_amt" id="total_amt"><?php echo 	$helperobj->getFromattedPrice(number_format($tprice,2));  ?></span>
					<!--<input type="hidden" value="<?php echo $tprice; ?>"	name="total_amt_inputbox"	id="total_amt_inputbox"> -->
				</td>
				<td></td>
			</tr>
			<?php
			$cval=0;
			$ccode='';  // && empty($prodcoupon)
			$cop_i = 0;
			if(!empty($coupons)) // if coupon present and not product specific coupon then only enter
			{
				foreach($coupons as $coupon)
				{

					if (empty($coupon['item_id']))
					{
						if($coupon['val_type'] == 1)
							$cval = ($coupon['value']/100)*$tprice;
						else
							$cval = $coupon['value'];
						$camt = $tprice - $cval;
						if($camt <= 0)
						{
							$camt=0;
						}
						$tprice = ($camt>=0) ? $camt : $tprice ;
						$cop_style = '';//style="display:block"';
						$ccode=$coupon['code'];
						break;
					}
					else
					{
						$cop_style = 'style="display:none"';
					}
				$cop_i++;
				}
			}
			else
			{
				$cop_style = 'style="display:none"';
			}
			?>
			<?php
			$col=6;
			//	$cop_ck_style=($prodcoupon==1)?'':$cop_style;
			if($showoptioncol==1 )
			{
				$col=7;
			}?>

			<tr >
				<td  colspan = "<?php echo $col;?>">
				<div class = "form-inline">
					<label class="checkbox">
						<input type="checkbox" id = "coupon_chk"  autocomplete="off" name = "coupon_chk" value="" size= "10" onchange="show_cop('<?php echo $cop_i;?>')" <?php echo ($ccode) ? 'checked' : '' ; ?>  /><?php echo JText::_('QTC_HAVE_COP');?>
					</label>
					<span id = "cop_tr" <?php echo  $cop_style ?>>
						<input type="text" class=""   id = "coupon_code" name="cop" value="<?php echo $ccode ?>"    placeholder="<?php echo JText::_('QTC_CUPCODE');?>"/>
						<input type="button"  class=""  onclick="applycoupon('<?php echo JText::_('QTC_ENTER_COP_COD')?>')" value="<?php echo JText::_('QTC_APPLY');?>" >
					</span>

				</div>
				</td>
			</tr>

			<?php	$col=2;
			if($showoptioncol==1)
			{
				$col=3;
			}?>
			<tr class="dis_tr" <?php echo $cop_style ?>>
				<td colspan = "<?php echo $col;?>"></td>
				<td class="cartitem_tprice_label"  align="right"><?php echo JText::_('QTC_COP_DISC');?></td>
				<td class="cartitem_tprice"  <?php echo $align_style ?> ><span id= "dis_cop" ><?php echo $helperobj->getFromattedPrice(number_format($cval,2)); ?></span></td>
			</tr>
			<?php	$col=2;
			if($showoptioncol==1)
			{
				$col=3;
			}?>
			<tr class="dis_tr" <?php echo $cop_style ?> >
				<td colspan = "<?php echo $col;?>"></td>
				<td class="cartitem_tprice_label"  align="right" ><?php echo JText::_('QTC_NET_AMT_PAY');?></td>
				<td class="cartitem_tprice"  <?php echo $align_style ?> ><span id= "dis_amt" ><?php echo $helperobj->getFromattedPrice(number_format($tprice,2)); ?></span>
				<!--	<input type="hidden" class="inputbox" value="<?php echo $tprice; ?>"	name="net_amt_pay_inputbox"	id="net_amt_pay_inputbox">						-->
				</td>
			</tr>

			<?php

			// taxation plugin
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('qtctax');//@TODO:need to check plugim type..
			$taxresults='';// $dispatcher->trigger('addTax',array($tprice));//Call the plugin and get the result

			if( !empty($taxresults) )
			{
				$tax_total = 0;
				foreach($taxresults as $tax)
				{
					if( !empty($tax) )
					{
					?>
						<!-- doubt ***** -->
						<?php	$col=2;
						if($showoptioncol==1)
						{	 $col=3; }?>
						<tr>
							<td colspan = "<?php echo $col;?>" ></td>
							<td class="cartitem_tprice_label"  align="right"><?php echo JText::sprintf('QTC_TAX_AMT_PAY',$tax[0]); ?></td>
							<td class="cartitem_tprice" <?php echo $align_style ?> ><span id= "tax_amt" ><?php echo $helperobj->getFromattedPrice(number_format($tax[1],2)); ?></span>
							<input type="hidden" class="inputbox" value="<?php echo $tax[0]; ?>"	name="tax[val][]"	id="tax[val][]">
							<input type="hidden" class="inputbox" value="<?php echo $tax[1]; ?>"	name="tax[amt][]"	id="tax[amt][]">

							</td>
						</tr>
					<?php
					$tax_total += $tax[1];
					}
				}
				if($tax_total)
				{
					$taxval= $helperobj->calamt($tprice,$tax_total);
					?>
						<tr >
							<td colspan = "<?php echo $col;?>" ></td>
							<td class="cartitem_tprice_label" align="right"  ><?php echo JText::_('QTC_TAX_TOTAL_AMT_PAY');?></td>
							<td class="cartitem_tprice" <?php echo $align_style ?> >
								<span id= "after_tax_amt" ><?php echo $helperobj->getFromattedPrice(number_format($taxval,2)); ?></span>
							</td>
						</tr>
					<?php
				}
				else{
					$taxval = $tprice;
				}
			}
			else{
				$taxval = $tprice;
			}
			?>
		</tbody>

	</table>
	</div>
		<div>
			<input type="hidden" class="inputbox" value="<?php echo $taxval; ?>" name="total_after_tax"	id="total_after_tax">
			<?php echo $this->aftercart;?>
		</div>
</div><!--End of Div qtc_cart1 -->

