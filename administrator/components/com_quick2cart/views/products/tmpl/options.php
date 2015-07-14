<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

$app = JFactory::getApplication();
$qtc_base_url = JUri::root();
$document = JFactory::getDocument();
$edit_task = 0;

if (!empty($this->item_id))
{
	$edit_task = 1;
}

$user=JFactory::getUser();
// GETTING COMP PARAM @todo hv to use this->param to all layouts
$params = $this->params;
$admin_commmisson = $this->params->get('commission');
$product_image_limit = $this->params->get('maxProdImgUpload_limit', 6);

$entered_numerics= JText::_('QTC_ENTER_NUMERICS');

// For taskprofile radio button display
$storeHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE.DS."components".DS."com_quick2cart".DS."helpers".DS."storeHelper.php", "storeHelper");
$storeList = (array) $storeHelper->getStoreList();

if (empty($this->itemDetail))
{
	$selected_id = 0;
}
else
{
	$selected_id = $this->itemDetail['taxprofile_id'] ? $this->itemDetail['taxprofile_id'] : 0;
}
?>

<script type="text/javascript">
	var imageid=0;

	function addmoreImg(rId,rClass)
	{
		var selected_imgs=techjoomla.jQuery('.qtc_img_checkbox:checked').length;
		var visible_file=techjoomla.jQuery('.filediv').length;
		var allowed_img=<?php echo $product_image_limit;?> ;
		var remaing_imgs= new Number(allowed_img - selected_imgs - visible_file);

		if (remaing_imgs > 0)
		{
			imageid++;
			/*var num=techjoomla.jQuery('.'+rClass).length;*/
			var num=imageid;
			/*console.log('div total= '+num);*/
			var pre = new Number(num - 1);
			var removeButton="<span class=''>";
			removeButton+="<button class='btn btn-danger btn-mini' type='button' id='remove"+num+"'";
			removeButton+="onclick=\"removeClone('filediv"+num+"','jgive_container');\" title=\"<?php echo JText::_('COM_Q2C_REMOVE_TOOLTIP');?>\" >";
			removeButton+="<i class=\"<?php echo QTC_ICON_MINUS;?> icon-white \"></i></button>";
			removeButton+="</span>";

			/*create the new element via clone(), and manipulate it's ID using newNum value*/
			/*if (num==1)
			{*/
				var newElem = techjoomla.jQuery('#' +rId).clone().attr('id', rId + num);
				var delid=rId;
			/*}*/
			/*else*/
			/*{
				var newElem = techjoomla.jQuery('#' +rId+pre).clone().attr('id', rId + num);
				var delid=rId + pre;
			}*/

			newElem.find('.addmore').attr('id','addmoreid'+ num);
			newElem.find(':file').attr('name','prod_img'+ imageid);
			removeClone('addmoreid'+pre ,'addmoreid'+pre );
			techjoomla.jQuery('.'+rClass+':last').after(newElem);
			techjoomla.jQuery('#'+rId+num).append(removeButton);
		}
		else
		{
			alert("<?php echo JText::sprintf('QTC_U_ALLOWD_TO_UPLOAD_IMGES', $product_image_limit)?>");
		}
	}

	function checkForSku(sku)
	{
		var editprod="<?php echo $edit_task;?>";
		var formName = document.adminForm;
		var skuval=document.adminForm.sku.value;
		/*if not a edit task and not empty sku value then only call ajax*/
		if (skuval)
		{
			var oldSku="<?php if (!empty($this->itemDetail)){  echo stripslashes($this->itemDetail['sku']); } ?>";
			/*while edit sku is not changed*/
			if (skuval != oldSku)
			{
				var actUrl = "<?php echo $qtc_base_url ?>" + 'index.php?option=com_quick2cart&task=product.checkSku&sku='+sku;
				var skuele = formName.sku;
				qtcIsPresentSku(actUrl, skuele);

			}
		}
	}

</script>

<div class ="form-horizontal">
	<!-- for TITLE/ NAME -->
	<div class='qtc_title_textbox control-group' >
		<label for="item_name" class="control-label"><?php echo JHtml::tooltip(JText::_('QTC_PROD_TITLE_DES'), JText::_('QTC_PROD_TITLE'), '', '* '.JText::_('QTC_PROD_TITLE'));?></label>
		<?php
			$item_id=!empty($this->item_id)?$this->item_id:0;
			JLoader::import('cart', JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'models');
			$model =  new Quick2cartModelcart;

			$p_title ='';

			if (!empty($this->itemDetail) && $this->itemDetail['name'])
			{
				//$p_title = stripslashes($this->itemDetail['name']);
				$p_title = ($this->itemDetail['name']);
			}
		?>
		<div class="controls">
		<input type="text" class="inputbox required"  name="item_name" size="1" id="item_name" value="<?php echo  $this->escape($p_title);  ?>" />
		<!--
		<input type="hidden" class="inputbox"  name="store_id" size="1" id="store_id" value="<?php //echo $store_id; ?>" />
		-->
		</div>
	</div>

	<div class="control-group">
		<label for="prod_cat" class="control-label"><?php echo JHtml::tooltip(JText::_('QTC_PROD_SEL_CAT_TOOLTIP'), JText::_('QTC_PROD_SEL_CAT'), '', '* '.JText::_('QTC_PROD_SEL_CAT'));?></label>
		<div class="controls">
			<?php echo $this->cats; ?>
		</div>
	</div>

	<!-- SELECT STORE -->
	<?php
	//$options[] = JHtml::_('select.option', "", "Select Country");
	$defaultStore = $storeList[0]->id;
	$StoreIds_style = (count($storeList)) ? "display:block" : "display:none";

	?>
	<div class="control-group" style="<?php echo $StoreIds_style;?>">
		<label for="qtc_store" class="control-label">
			<?php echo JHtml::tooltip(JText::_('QTC_PROD_SELECT_STORE_DES'), JText::_('QTC_PROD_SELECT_STORE'), '', JText::_('QTC_PROD_SELECT_STORE'));?>
		</label>
		<div class="controls">
			<?php
			 $defaultStore = (!empty($this->itemDetail) ? stripslashes($this->itemDetail['store_id']) : $storeList[0]->id);

				foreach ($storeList as $key=>$value)
				{
					$value=(array)$value;
					$options[] = JHtml::_('select.option', $value["id"],$value['title']);
				}
				echo $this->dropdown = JHtml::_('select.genericlist',$options,'store_id','class=" qtc_putmargintop10px" size="1"  onchange="getTaxprofile();" ','value','text',$defaultStore,'current_store_id');
			?>
		</div>
	</div>

	<!-- sku -->
	<div class="control-group">
		<label for="qtc_sku" class="control-label">
			<?php echo JHtml::tooltip(JText::_('QTC_PROD_SKU_TOOLTIP'), JText::_('QTC_PROD_SKU'), '', '* '.JText::_('QTC_PROD_SKU'));?>
		</label>
		<div class="controls">
			<?php
				$edit='';
				$readonly='';

				if (!empty($this->item_id))
				{
					$readonly="readonly";
					$edit=1;
				}
			?>
			<input type="text" name="sku" id="qtc_sku" class="inputbox required" <?php //echo $readonly;?>  size="20" value="<?php if (!empty($this->itemDetail)){  echo stripslashes($this->itemDetail['sku']); } ?>" autocomplete="off" onBlur="checkForSku(this.value)" />
			<span class="help-inline"><?php echo JText::_('QTC_PROD_SKU_UNIQUE');?></span>
		</div>
	</div>

	<!-- publish and unpublish-->
	<div class='control-group' >
		<label for="state" class="control-label"><?php echo JHtml::tooltip(JText::_('QTC_PROD_STATUS'), JText::_('QTC_PROD_STATUS_DES'), '', JText::_('QTC_PROD_STATUS'));?></label>
		<div class="controls">
		<label class="radio inline">
			 <?php
			 $isPublished=" checked ";
			 $isUnPublished="";
			 if (!empty($this->itemDetail))
			 {
				if ($this->itemDetail['state']==0)
				{
					 $isPublished="";
					 $isUnPublished=" checked ";
				}
			}
			 ?>
			<input type="radio" name="state" id="state" value="1" <?php echo $isPublished; ?> >
			<?php echo JText::_('QTC_PROD_PUBLISH')?>
			</label>
			<label class="radio inline">
			<input type="radio" name="state"  value="0" <?php echo $isUnPublished; ?>>
			<?php echo JText::_('QTC_PROD_UNPUBLISH')?>
			</label>
		</div>
	</div>

	<!-- PRICE PRICE -->
	<div class='control-group qtc_currencey_textbox'>
		<label for="price_<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="control-label">
			<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_PRICE_DESC'), JText::_('QTC_ITEM_PRICE'), '', '* ' . JText::_('QTC_ITEM_PRICE'));?>
		</label>

		<div class="controls">
			<?php
			$currdata = array();
			$base_currency_id = "";
			// if all currency fields r filled
			$currfilled = 1;

			$multiCurrencies = 0;

			if (count($curr) > 1)
			{
				$multiCurrencies = 1;
			}

			// key contain 0,1,2... // value contain INR...
			foreach ($curr as $key=>$value)
			{
				//$name="jform[attribs][$value]";
				$currvalue="";

				if (!empty($this->item_id))
				{
					$currvalue = $quick2cartModelAttributes->getCurrenciesvalue($this->pid, $value, $this->client, $this->item_id);
				}

				$storevalue = !empty($currvalue) ? (isset($currvalue[0]['price']) ? $currvalue[0]['price'] : '') : '';

				if (empty($storevalue))
				{
					$currfilled=0;
				}

				if (!empty($curr_syms[$key]))
				{
					$currtext = $curr_syms[$key];
				}
				else
				{
					$currtext = $value;
				}
				?>

				<?php if ($multiCurrencies) : ?>
					<div>
						<div class="input-append curr_margin">
							<label for="price_<?php echo trim($value);?>" class="control-label">
								<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_PRICE_DESC'), JText::_('QTC_ITEM_PRICE'), '', JText::_('QTC_ITEM_PRICE') . ' ' . JText::_('COM_QUICK2CART_IN') . ' ' . trim($currtext));?> &nbsp;
							</label>
							<input
							Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
								class="span1 currtext required qtc_requiredoption"
								style="align:right;"
								id="price_<?php echo trim($value);?>"
								size="16"
								type="text"
								name="multi_cur[<?php echo trim($value);?>]"
								value="<?php echo $storevalue;?>"
								placeholder="<?php echo trim($currtext);?>" />
							<span class="add-on"><?php echo $currtext;?></span>
						</div>
					</div>
				<?php else : ?>
					<div class="input-append curr_margin">
						<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
							class="span1 currtext required qtc_requiredoption"
							style="align:right;"
							id="price_<?php echo trim($value);?>"
							size="16"
							type="text"
							name="multi_cur[<?php echo trim($value);?>]"
							value="<?php echo $storevalue;?>"
							placeholder="<?php echo trim($currtext);?>" />
						<span class="add-on"><?php echo $currtext;?></span>
					</div>
				<?php endif; ?>
			<?php
			}
			?>
		</div>
	</div>

	<!-- DISCOUNT PRICE -->
	<div class='control-group qtc_currencey_textbox' style="<?php echo (($params->get('usedisc') == '0') ? 'display:none;' : 'display:block;'); ?>" >
		<label for="disc_price_<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="control-label">
			<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_DIS_PRICE_DESC'), JText::_('QTC_ITEM_DIS_PRICE'), '', JText::_('QTC_ITEM_DIS_PRICE'));?>
		</label>
		<div class="controls">
			<?php
			$currdata = array();
			$base_currency_id = "";

			// key contain 0,1,2... // value contain INR...
			foreach ($curr as $key=>$value)
			{
				//$name="jform[attribs][$value]";
				$currvalue="";
				if (!empty($this->item_id))
				{
					$currvalue = $quick2cartModelAttributes->getCurrenciesvalue($this->pid, $value, $this->client, $this->item_id);
				}

				$storevalue = !empty($currvalue) ? (isset($currvalue[0]['discount_price']) ? $currvalue[0]['discount_price'] : '') : '';
				$currsymbol = $comquick2cartHelper->getCurrencySymbol($value);
				?>

				<?php if ($multiCurrencies) : ?>
					<div>
						<div class="input-append curr_margin">
							<label for="disc_price_<?php echo trim($value);?>" class="control-label">
								<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_DIS_PRICE_DESC'), JText::_('QTC_ITEM_DIS_PRICE'), '', JText::_('QTC_ITEM_DIS_PRICE') . ' ' . JText::_('COM_QUICK2CART_IN') . ' ' . trim($currsymbol));?>
							</label>
							<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
								class="span1 currtext"
								style="align:right;"
								id="disc_price_<?php echo trim($value);?>"
								size="16"
								type="text"
								name="multi_dis_cur[<?php echo trim($value);?>]"
								value="<?php echo $storevalue;?>"
								placeholder="<?php echo trim($currsymbol);?>" />
							<span class="add-on"><?php echo $currsymbol;?></span>
						</div>
					</div>
				<?php else : ?>
					<div class="input-append curr_margin">
						<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
							class="span1 currtext"
							style="align:right;"
							id="disc_price_<?php echo trim($value);?>"
							size="16"
							type="text"
							name="multi_dis_cur[<?php echo trim($value);?>]"
							value="<?php echo $storevalue;?>"
							placeholder="<?php echo trim($currsymbol);?>" />
						<span class="add-on"><?php echo $currsymbol;?></span>
					</div>
				<?php endif; ?>
			<?php
			}
			?>
		</div>
	</div>

	<!--  PROD description -->
	<div class="control-group">
		<label for="description" class="control-label">
			<?php echo JHtml::tooltip(JText::_('QTC_PROD_DES_TOOLTIP'), JText::_('QTC_PROD_DES'), '',JText::_('QTC_PROD_DES'));?>
		</label>
		<div class="controls">
			<?php
			$on_editor = $params->get('enable_editor',0);
			if (empty($on_editor))
			{
				?>
				<textarea  size="50" rows="5" name="description[data]" id="description" class="inputbox" ><?php if (!empty($this->itemDetail['description'])){  echo trim($this->itemDetail['description']); } ?></textarea>
			<?php
			}
			else
			{
				$editor      = JFactory::getEditor();
				if (!empty($this->itemDetail))
				{
					// If you set last parameter to false then other option will not display.
					echo $editor->display("description[data]",$this->itemDetail['description'],400,400,40,20,true);
				}
				else
				{
					echo $editor->display("description[data]",'',400,400,40,20,true);
				}
			}
				?>
		</div>
	</div>
	<!-- END :: PROD description -->

	<!--avatar -->
	<div class="control-group imagediv" id="imagediv">
		<label for="avatar" class="control-label"><?php echo JHtml::tooltip(JText::_('QTC_PROD_IMG_TOOLTIP'), JText::_('QTC_PROD_IMG'), '','* '. JText::_('QTC_PROD_IMG'));?></label>
		<div class="controls">
			<?php
			if (!empty($this->itemDetail['images']) )
			{
				?>
				<div class=" ">
					<div class="text-info">
						<?php echo JText::_('COM_Q2C_UNCHECK_TO_REMOVE_EXISTING_IMAGE');?>
					</div>
					<div class="row-fluid"><!-- wrapper for images-->
						<ul class="thumbnails qtc_ForLiStyle" id="qtc_nev">
							<?php
							$images=json_decode($this->itemDetail['images'],true);
							require_once(JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'helpers'.DS.'media.php');
							//create object of media helper class
							$media=new qtc_mediaHelper();
							foreach ($images as $key=>$img)
							{
									if (!empty($img))
									{
										$originalImg=$img;
										$file_name_without_extension=$media->get_media_file_name_without_extension($img);
										$media_extension=$media->get_media_extension($img);
										$img=$comquick2cartHelper->isValidImg($file_name_without_extension.'_S.'.$media_extension);
										if (empty($img)){
											$img=JUri::root().'components'.DS.'com_quick2cart'.DS.'images'.DS.'default_product.jpg';
										}
							?>
							<li>
								<label class="checkbox">
										<input class="qtcmarginLeft1px" type="checkbox" name="qtc_prodImg[<?php echo $key?>]" value="<?php echo $originalImg;?>"  class="qtc_img_checkbox" id="qtc_prodImg_<?php echo $key?>"  autocomplete="off" checked />
										<img class='img-rounded qtc_prod_img100by100 com_qtc_img_border'   src="<?php echo $img;?>" alt="<?php echo  JText::_('QTC_IMG_NOT_FOUND') ?>"/>
								</label>
							</li>
						<?php
								}
						}
						?>
							</ul>
					</div>
				</div>
				<!-- span12 END-->
				<?php
			}
			?>
			<div class="row-fluid">
				<?php
				$required=" required ";
				if (!empty($this->itemDetail['images']) )
				{
					$required="";
				}
				?>
				<?php
				/*@TODO JUGAD done for add more images display */
				if (version_compare(JVERSION, '3.0', 'lt')) { ?>
				<div class="filediv " id="filediv" >
					<input  type="file" name="prod_img"  id="avatar" placeholder="<?php echo '* '.JText::_('COM_QUICK2CART_IMAGE_MSG');?>" class="<?php echo $required; ?> >"  accept="image/*">
				</div>
				<?php }
				else{ ?>
				<span class="filediv pull-left" id="filediv" >
					<input  type="file" name="prod_img"  id="avatar" placeholder="<?php echo '* '.JText::_('COM_QUICK2CART_IMAGE_MSG');?>" class="<?php echo $required; ?> >"  accept="image/*">
				</span>
				<?php } ?>
			<!-- ADD MORE BTN-->

				<span class="addmore pull-left"  id="addmoreid"  id="addmoreid" >
					<button onclick="addmoreImg('filediv','filediv');" type="button" class="btn btn-mini btn-primary" title="<?php echo JText::_('COM_Q2C_IMAGE_ADD_MORE');?>">
						<i class="<?php echo QTC_ICON_PLUS;?> icon-white "></i>
					</button>
				</span>

				<div class="clearfix">&nbsp;</div>
				<div class="text-warning">
					<!--
					<p><?php //echo JText::sprintf('QTC_AVTAR_SIZE_MASSAGE', $height, $width);?></p>
					-->
					<p><?php echo JText::sprintf('COM_QUICK2CART_ALLOWED_IMG_FORMATS', 'gif, jpeg, jpg, png');?></p>
				</div>
			</div>
			<!--END OF ROW FLUID -->
		</div>
		<!-- END OF CONTROL-->
	</div>
	<!-- END OF control-group ->

	<!-- VIDEO LINK-->
	<div class="control-group">
		<label for="youtube_link" class="control-label">
			<?php echo JHtml::tooltip(JText::_('QTC_PROD_YOUTUBE_TOOLTIP'), JText::_('QTC_PROD_YOUTUBE'), '', JText::_('QTC_PROD_YOUTUBE'));?>
		</label>
		<div class="controls">
			<input type="text" name="youtube_link" id="youtube_link" class="inputbox "
			value="<?php if (!empty($this->itemDetail)){  echo stripslashes($this->itemDetail['video_link']); } ?>"
			placeholder="<?php echo JText::_('QTC_PROD_YOUTUBE_PLACE'); ?>" />
		</div>
	</div>

	<?php
	//@TODO get all product detail which from getItemDetail (modified)
	/*fetch Minimum/ max /stock  item Quantity*/
	// item_id present i.e  item is saved
	if (!empty($item_id))
	{
		$minmaxstock = $model->getItemRec($item_id);
	}

	$instock="";
	$outofstock="";

	if (isset($minmaxstock))
	{
		if ($minmaxstock->stock==1)
		{
			$instock="checked='checked'";
		}
		else
		{
			$outofstock="checked='checked'";
		}
	}

	if ($params->get('usestock'))
	{
		$qtc_stock_style = ($params->get('usestock')==1)?"display:block":"display:none";
	?>
		<div class="control-group" style="<?php echo $qtc_stock_style;?>">
			<label for="stock" class="control-label">
				<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_STOCK_DESC'), JText::_('PLG_QTC_ITEM_STOCK'), '', JText::_('PLG_QTC_ITEM_STOCK'));?>
			</label>
			<div class="controls">
				<input Onkeyup="checkforalpha(this,'45', '<?php echo addslashes($entered_numerics); ?>')"
				type="text" name="stock" id="stock" size="32"
				value="<?php if (isset($minmaxstock->stock)) echo $minmaxstock->stock;?>"
				class="input-mini inputbox validate-integer" />
			</div>
		</div>
	<?php
	}
	?>

	<!-- for Minimum/ max item Quantity -->
	<?php
	$qtc_min_max_status = $params->get('minmax_quantity');
	$qtc_min_max_style = ($qtc_min_max_status==1) ? "display:block" : "display:none";
	?>

	<div class="control-group" style="<?php echo $qtc_min_max_style;?>">
		<label class="control-label" for="item_slab">
		<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_SLAB_DESC'), JText::_('COM_QUICK2CART_ITEM_SLAB'), '', JText::_('COM_QUICK2CART_ITEM_SLAB'));?>
		</label>
		<div class="controls">
			<input Onkeyup="checkforalpha(this,'', '<?php echo addslashes($entered_numerics); ?>')"  Onchange="checkSlabValue();" type="text" name="item_slab" id="item_slab" size="32" value="<?php echo isset($minmaxstock) ? $minmaxstock->slab: 1  ?>" class="input-mini inputbox validate-integer"  >
		</div>
	</div>
	<div class="control-group" style="<?php echo $qtc_min_max_style;?>">
		<label for="min_item" class="control-label">
			<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_MIN_QTY_DESC'), JText::_('QTC_ITEM_MIN_QTY'), '', JText::_('QTC_ITEM_MIN_QTY'));?>
		</label>
		<div class="controls">
			<input onChange="checkSlabValueField(this,'', '<?php echo addslashes($entered_numerics); ?>')"
				type="text" name="min_item" id="min_item" size="32"
				value="<?php if (isset($minmaxstock)) echo $minmaxstock->min_quantity;?>"
				class="input-mini inputbox validate-integer" />
		</div>
	</div>

	<div class="control-group" style="<?php echo $qtc_min_max_style;?>">
		<label for="max_item" class="control-label">
			<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ITEM_MAX_QTY_DESC'), JText::_('QTC_ITEM_MAX_QTY'), '', JText::_('QTC_ITEM_MAX_QTY'));?>
		</label>
		<div class="controls">
			<input onChange="checkSlabValueField(this,'', '<?php echo addslashes($entered_numerics); ?>')"
				type="text" name="max_item" id="max_item" size="32"
				value="<?php if (isset($minmaxstock))  echo $minmaxstock->max_quantity;?>"
				class="input-mini inputbox validate-integer" />
		</div>
	</div>



	<!--
	<div class="alert">
		<button type="button" class="close" data-dismiss="alert"></button>
		<?php echo JText::_('QTC_NOTE'); ?><?php echo JText::_('QTC_OPTIONS_REQUIRED_MSG'); ?>
	</div>
	<div class="alert alert-error">
		<button type="button" class="close" data-dismiss="alert"></button>
		<?php echo JText::_('QTC_NOTE'); ?> <?php echo JText::_('QTC_SAVE_ITEM_PARAM_DESC'); ?>
	</div>

	<div class="form-actions">
	 <button  type="button" class="btn btn-success validate" onclick="savecurrency('<?php echo $pid; ?>','<?php echo $client; ?>','<?php echo $store_id; ?>');" >
	 <?php echo JText::_('QTC_ITEM_SAVE'); ?>
	 </button>
	</div> -->

	<div class="control-group">
		<label class="control-label" for="metadesc">
			<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_META_DESC_TOOLTIP'), JText::_('COM_QUICK2CART_META_DESC'), '', JText::_('COM_QUICK2CART_META_DESC'));?>
		</label>
		<div class="controls">
			<textarea name="metadesc" id="metadesc" cols="30" rows="3" class="input"><?php if (isset($this->itemDetail['metadesc'])) echo $this->itemDetail['metadesc']; ?></textarea>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="metakey">
			<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_META_KEYWORDS_TOOLTIP'), JText::_('COM_QUICK2CART_META_KEYWORDS'), '', JText::_('COM_QUICK2CART_META_KEYWORDS'));?>
		</label>
		<div class="controls">
			<textarea name="metakey" id="metakey" cols="30" rows="3" class="input"><?php if (isset($this->itemDetail['metakey'])) echo $this->itemDetail['metakey']; ?></textarea>
		</div>
	</div>
</div>
<!-- form horizantal end-->

<?php
$js_key = 'var qtc_base_url = "' . JUri::root() . '";';
$js_key .="

function getTaxprofile()
{
	var isTaxationEnabled = " . $isTaxationEnabled . ";
	var qtc_shipping_opt_status = " . $qtc_shipping_opt_status . ";
	var store_id = techjoomla.jQuery('#current_store_id').val();
	if (store_id == null)
	{
		store_id = '" . $storeList[0]->id . "';
	}
	var selected_taxid = '".$selected_id."';

	if(isTaxationEnabled == 1)
	{
		qtcLoadTaxprofileList(store_id, selected_taxid);
	}

	/* Now update ship profiles */

	if(qtc_shipping_opt_status == 1)
	{
		qtcUpdateShipProfileList(store_id);
	}
}
";

$js_key .= "
window.onload = function ()
{
	var isTaxationEnabled = " . $isTaxationEnabled . ";
	var qtc_shipping_opt_status = " . $qtc_shipping_opt_status . ";
	var store_id = techjoomla.jQuery('#current_store_id').val();
	if (store_id == null)
	{
		store_id = '" . $storeList[0]->id . "';
	}
	var selected_taxid = '" . $selected_id . "';
	// Get tax profile list

	if(isTaxationEnabled == 1)
	{
		qtcLoadTaxprofileList(store_id, selected_taxid);
	}
}";
$document->addScriptDeclaration($js_key);
?>
