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
//JHtml::_('behavior.tooltip');

//JHtml::_('behavior.framework');

//JHtml::_('behavior.modal');
$lang = JFactory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);
$helperobj = new comquick2cartHelper;
$curr = $helperobj->getCurrencySession();
// Added by aniket
$entered_numerics = "'" . JText::_('QTC_ENTER_NUMERICS') . "'";
$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'models' . DS . 'attributes.php';

if (!class_exists('quick2cartModelAttributes'))
{
	JLoader::register('quick2cartModelAttributes', $path);
	JLoader::load('quick2cartModelAttributes');
}
$quick2cartModelAttributes = new quick2cartModelAttributes();
$item_id = (is_object($data)) ? $data->item_id : $data['item_id'];
$productHelper = new productHelper;
// Check whether product is allowd to buy or not. ( our of stock)
$itemDetailObj = (object)$data;
$qtcTeaserShowBuyNowBtn = $productHelper->isInStockProduct($itemDetailObj);
$prodAttDetails = $productHelper->getProdPriceWithDefltAttributePrice($item_id);
$it_price = $prodAttDetails; //$quick2cartModelAttributes->getCurrenciesvalue('0',$curr,'com_quick2cart',$item_id);

if (isset($it_price['itemdetail']))
{
	$item_price = $it_price['itemdetail'];
}

// STORE OWNER IS LOGGED IN
$store_owner = '';

if (!empty($store_list))
{

	if (in_array($data['store_id'], $store_list))
	{
		//$store_owner=$data['store_id'];
		$store_owner = 1;
	}
}
// class for publish and unpublish icon --- used further

if (version_compare(JVERSION, '3.0', 'lt'))
{
	$publish = " icon-ok ";
	$unpublish = " icon-remove ";
}
else
{
	// for joomla3.0
	$publish = " icon-ok ";
	$unpublish = " icon-cancel-2";
}

if (!empty($store_owner))
{
	$itemstate = $data['state'];
}
// GETTING ALL PRODUCTS ATTRIBURES
$attribure_option_ids = $prodAttDetails['attrDetail']['attrOptionIds'];
$tot_att_price = $prodAttDetails['attrDetail']['tot_att_price'];
$classes = !empty($classes) ? $classes : '';
$prodivsize = !empty($prodivsize) ? $prodivsize : 'default_product_div_size';
$com_params = JComponentHelper::getParams('com_quick2cart');
$img_width = $com_params->get('medium_width', 120);
// Getting item id
$catpage_Itemid = $helperobj->getitemid('index.php?option=com_quick2cart&view=category');

?>

<div class="q2c_pin_item_<?php echo $random_container;?>">
	<div class="q2c_pin_wrapper">
		<div class="thumbnail">
			<div class="caption">
				<?php
				// p_link:: if product has attribute then use plink to open product page
				$p_link = 'index.php?option=com_quick2cart&view=productpage&layout=default&item_id=' . $data['item_id'] . '&Itemid=' . $catpage_Itemid;
				// $product_link = JUri::root() . substr(JRoute::_($p_link), strlen(JUri::base(true)) + 1);
				$product_link = $helperobj->getProductLink($data['item_id'], 'detailsLink');

				if (isset($data['featured']))
				{
					if ($data['featured']=='1')
					{
						?>
						<img title="<?php echo JText::_('QTC_FEATURED_PROD');?>"
							 src="<?php echo JUri::base().'components' . DS . 'com_quick2cart' . DS . 'assets' . DS . 'images' . DS . 'featured.png'; ?>" />
						<?php
					}
				}

				// GETTIN PRODUCT TITLE LIMIT
				$prodTitleLimit = $com_params->get('ProductTitleLimit', 15);
				$prodname = $data['name'];

				if (strlen($data['name']) > $prodTitleLimit)
				{
					$prodname = substr($data['name'], 0, $prodTitleLimit). '...';
				}
				?>

				<strong class="center">
					<a title="<?php echo $data['name'];?>" href="<?php echo $product_link; ?>">
						<?php echo $prodname;?>
					</a>
				</strong>

			</div>

			<?php
			$images = json_decode($data['images'], true);
			$img = JUri::base().'components' . DS . 'com_quick2cart' . DS . 'assets' . DS . 'images' . DS . 'default_product.jpg';

			if (!empty($images))
			{
				// Get first key
				$firstKey = 0;
				foreach ($images as $key=>$img)
				{
					$firstKey = $key;

					break;
				}

				require_once(JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'media.php');

				// create object of media helper class
				$media = new qtc_mediaHelper();
				$file_name_without_extension = $media->get_media_file_name_without_extension($images[$firstKey]);
				$media_extension = $media->get_media_extension($images[$firstKey]);
				$img = $helperobj->isValidImg($file_name_without_extension.'_L.'.$media_extension);

				if (empty($img))
				{
					$img = JUri::base().'components' . DS . 'com_quick2cart' . DS . 'assets' . DS . 'images' . DS . 'default_product.jpg';
				}
			}
			?>

			<div class="caption">
				<a title="<?php echo $data['name'];?>" href="<?php echo $product_link; ?>">
					<img class=' img-rounded q2c_pin_image'
						src="<?php echo $img;?>"
						alt="<?php echo  JText::_('QTC_IMG_NOT_FOUND') ?>"
						title="<?php echo $data['name'];?>" />
				</a>
			</div>

			<div class="caption">
				<div class="center">
					<?php
						$p_price = (!empty($item_price['discount_price']) && ceil($item_price['discount_price'])) ? $item_price['discount_price'] : $item_price['price'];
						echo JText::_('QTC_ITEM_AMT') . " : " . $helperobj->getFromattedPrice($p_price + $tot_att_price);
					?>
				</div>
				<hr class="hr hr-condensed"/>
				<?php
				$textboxid = $data['parent'] . '-' . $item_id . "_itemcount";
				$parent = $data['parent'];
				$slab = !empty($data['slab']) ? $data['slab'] : 1;
				$limits = $data['min_quantity'] . "," . $data['max_quantity'];
				$arg= "'" . $textboxid."','" . $item_id."','" . $parent . "','" . $slab . "'," . $limits;

				$min_msg = JText::_('QTC_MIN_LIMIT_MSG');
				$max_msg = JText::_('QTC_MAX_LIMIT_MSG');
				$fun_param = $parent . '-' . $data['product_id'];
				//com_content-31_itemcount
				$qty_buynow = $com_params->get('qty_buynow', 1);
				$qtyDivStyle = "";
				$qtyDivSpan = "span6";
				$buyBtnSpan = "span6";
				$buyBtnClass = " pull-left ";

				if (empty($qty_buynow))
				{
					// dont show quantity
					$qtyDivStyle = "display:none";
					$qtyDivSpan = "";
					$buyBtnSpan = "span12";
					$buyBtnClass = "";
				}
				?>

				<div class="clearfix"></div>
				<div class="form-horizontal">
					<?php
					$options_str = implode(',', $attribure_option_ids);

					if (empty($qtcTeaserShowBuyNowBtn))
					{
						// Show out of stock msg.
						?>
						<div>
							<span class="label label-warning "><?php echo JText::_('QTC_OUT_OF_STOCK_MSG'); ?></span>
						</div>
						<?php
					}
					elseif (!empty($categoryPage))
					{
						?>
						<div class="center">
							<!--
							<div class="<?php echo $qtyDivSpan;?>" style="<?php echo $qtyDivStyle;?>">
							-->
							<div class="" style="<?php echo $qtyDivStyle;?>">
								<?php
								$qtc_qnt_textbox_style = "";

								if (version_compare(JVERSION, '3.0', 'lt'))
								{
									$qtc_qnt_textbox_style = "style='width:15px;'";
								}
								?>

								<div class="pull-left">
									<label class=""
										for="<?php echo $textboxid;?>" >
										<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_PIN_QUANTITY_TOOLTIP'), JText::_('COM_QUICK2CART_PIN_QUANTITY'), '', JText::_('COM_QUICK2CART_PIN_QUANTITY'));?>
									</label>
								</div>

								<div class="pull-right">

									<span class="qtc_itemcount qtc_float_right" >
										<input type="button" onclick="qtc_increment(<?php echo $arg;?>)" class="qtc_icon-qtcplus qtc_pointerCusrsor" />
										<input type="button" onclick="qtc_decrement(<?php echo $arg;?>)" class="qtc_icon-qtcminus qtc_pointerCusrsor" />
									</span>

									<input id="<?php echo $textboxid;?>" <?php echo $qtc_qnt_textbox_style;?>
										name="<?php echo $data['product_id'];?>_itemcount"
										class="qtc_textbox_small qtc_count qtc_float_right"
										type="text"
										value="<?php echo $data['min_quantity'];?>"
										size="2"
										maxlength="3"
										onblur="checkforalphaLimit(this,'<?php echo $data['product_id'];?>','<?php echo $parent;?>','<?php echo $slab;?>',<?php echo $limits;?>,'<?php echo $min_msg;?>','<?php echo $max_msg;?>');"
										Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>)" />
								</div>

								<div class="clearfix"></div>

							</div>
							<hr class="hr hr-condensed"/>
							<div class="clearfix"></div>

							<!--
							<div class="<?php //echo $buyBtnSpan;?>">
							-->
							<div class="center">
								<?php
								if (!empty($options_str))
								{
									?>
									<!--
									<button class="btn btn-small btn-success <?php echo $buyBtnClass;?>"
									-->
									<button class="btn btn-small btn-success"
										type="button"
										onclick="window.open('<?php echo JRoute::_($p_link);?>','_self')">
											<i class="<?php echo QTC_ICON_CART;?>"></i> <?php echo JText::_('QTC_ITEM_BUY');?>
									</button>
									<?php
								}
								else
								{
									?>
									<button class="btn btn-small btn-success qtc_buyBtn_style" type="button" onclick="qtc_addtocart('<?php echo $fun_param; ?>');"><i class="<?php echo QTC_ICON_CART;?>"></i> <?php echo JText::_('QTC_ITEM_BUY');?></button>
									<?php
								}
								?>
							</div>
						</div>
						<?php
					}
					else
					{
					?>
						<!--  for buy now button-->
						<div class="center">
							<?php
							$client = "q2c";
							$pid = 0;
							$item_id = $item_id;

							$count = $data['min_quantity'];
							$temp = "'" . $options_str . "','" . $count . "','" . $item_id . "'";

							//@TODO temp fix ... need to have like com_quick2cart-5 here
							$fun_param = $item_id;

							if (!empty($options_str))
							{
								?>
								<button class="btn btn-small btn-success" type="button"
									onclick="window.open('<?php echo JRoute::_($p_link);?>','_self')">
										<i class="<?php echo QTC_ICON_CART;?>"></i> <?php echo JText::_('QTC_ITEM_BUY');?>
								</button>
								<?php
							}
							else
							{
								?>
								<button class="btn btn-small btn-success qtc_buyBtn_style" type="button" onclick="qtc_mod_addtocart(<?php echo $temp ?>);"><i class="<?php echo QTC_ICON_CART;?>"></i> <?php echo JText::_('QTC_ITEM_BUY');?></button>
								<?php
							}
							?>
						</div>
					<?php
					}
					?>
				</div>

				<?php
				$popup_buynow = $com_params->get('popup_buynow',1);

				if ($popup_buynow == 2)
				{
					$checkout = 'index.php?option=com_quick2cart&view=cart';
					$itemid = $helperobj->getitemid($checkout);
					$action_link = JUri::root() . substr(JRoute::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemid, false), strlen(JUri::base(true)) + 1);
					?>
					<div class="row-fluid" align="center">
						<div class="span12" >
							<div class="cart-popup" id="<?php echo $fun_param; ?>_popup" style="display: none;">
								<div class="message"></div>
								<div class="cart_link">
									<a class="btn btn-success" href="<?php echo $action_link; ?>">
										<?php echo JText::_('COM_QUICK2CART_VIEW_CART')?>
									</a>
								</div>
								<i class="icon-remove cart-popup_close" onclick="techjoomla.jQuery(this).parent().slideUp().hide();"></i>
							</div>
						</div>
					</div>

					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
