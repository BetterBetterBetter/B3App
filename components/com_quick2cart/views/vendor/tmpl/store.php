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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');
JHtml::_('behavior.modal');

// check for store
if (empty($this->store_id))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<div class="well">
			<div class="alert alert-error">
				<span>
					<?php echo JText::_('QTC_ILLEGAL_PARAMETARS'); ?>
				</span>

				<?php
				if (version_compare(JVERSION, '3.0', 'lt'))
				{
					$qtc_back = " icon-arrow-left ";
				}
				else
				{
					// for joomla3.0
					$qtc_back = " icon-arrow-left-2 ";
				}
				?>

				<button type="button"  title="<?php echo JText::_( 'QTC_DEL' ); ?>" class="btn btn-mini btn-primary pull-right" onclick="javascript:history.back();" >
					<i class="<?php echo $qtc_back;?> icon-white"></i>&nbsp; <?php echo JText::_( 'QTC_BACK_BTN');?>
				</button>

			</div>
		</div>
	</div>
	<?php
	return false;
}

//load style sheet
$document = JFactory::getDocument();

// for featured and top seller product
$product_path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php';

if (!class_exists('productHelper'))
{
	JLoader::register('productHelper', $product_path );
	JLoader::load('productHelper');
}

$productHelper = new productHelper();
$comquick2cartHelper = new comquick2cartHelper;
$store_id = $this->store_id;
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<form name="adminForm" id="adminForm" class="form-validate" method="post">
		<div class="row-fluid">
			<div class="span9">
				<!-- START ::for store info  -->
				<?php
				if (!empty($this->storeDetailInfo))
				{
					$sinfo = $this->storeDetailInfo;
				}
				?>

				<legend align="center">
					<?php echo JText::sprintf('QTC_WECOME_TO_STORE',$sinfo['title']) ;?>
				</legend>

				<?php
				if (empty($this->change_prod_cat))
				{
					$view = $comquick2cartHelper->getViewpath('vendor', 'storeinfo');
					ob_start();
					include($view);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
				}
				?>
				<!-- END ::for store info  -->

				<?php
				// featured prod and top seller should be shown only if categoty is not selected
				if (empty($this->change_prod_cat))
				{
					?>
					<!-- START ::for featured product  -->
					<?php
					// 	GETTING ALL FEATURED PRODUCT
					$params = JComponentHelper::getParams('com_quick2cart');
					$featured_limit = $params->get('featured_limit');
					$target_data = $productHelper->getAllFeturedProducts($store_id, $this->change_prod_cat, $featured_limit);

					if (!empty($target_data))
					{
						?>
						<div class="row-fluid">
							<div class="span12 well well-small" >
								<legend align="center">
									<?php echo JText::_('QTC_FEATURED_PRODUCTS') ;?>
								</legend>
								<?php $random_container = 'q2c_pc_featured';?>
								<div id="q2c_pc_featured">
									<?php
									// REDERING FEATURED PRODUCT
									foreach($target_data as $data)
									{
										$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'views' . DS . 'product' . DS . 'tmpl' . DS . 'product.php';

										ob_start();
										include($path);
										$html = ob_get_contents();
										ob_end_clean();
										echo $html;
									}
									?>
								</div>

								<!-- setup pin layout script-->
								<script type="text/javascript">
									var pin_container_<?php echo $random_container; ?> = 'q2c_pc_featured'
								</script>

								<?php
								$view = $comquick2cartHelper->getViewpath('product', 'pinsetup');
								ob_start();
								include($view);
								$html = ob_get_contents();
								ob_end_clean();
								echo $html;
								?>
							</div>
						</div>
						<?php
					}
						?>
					<!-- END ::for featured product  -->

					<?php
					// GETTING ALL Top  seller PRODUCT
					$topSeller_limit = $params->get('topSeller_limit');
					$target_data = $productHelper->getTopSellerProducts($store_id, $this->change_prod_cat, $topSeller_limit);

					if (!empty($target_data))
					{
						?>
						<!-- START ::for top seller  -->
						<div class="row-fluid">
							<div class="span12 well well-small" >
								<legend align="center">
									<?php echo JText::_('QTC_TOP_SELLER_STORE_PRODUCTS') ;?>
								</legend>
								<?php $random_container = 'q2c_pc_top_seller';?>
								<div id="q2c_pc_top_seller">
									<?php
									// REDERING Top  seller  PRODUCT
									foreach($target_data as $data)
									{
										$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'views' . DS . 'product' . DS . 'tmpl' . DS . 'product.php';
										ob_start();
										include($path);
										$html = ob_get_contents();
										ob_end_clean();
										echo $html;
									}
									?>
								</div>

								<!-- setup pin layout script-->
								<script type="text/javascript">
									var pin_container_<?php echo $random_container; ?> = 'q2c_pc_top_seller'
								</script>

								<?php

								$view = $comquick2cartHelper->getViewpath('product', 'pinsetup');
								ob_start();
								include($view);
								$html = ob_get_contents();
								ob_end_clean();
								echo $html;
								?>
							</div>
						</div>
						<?php
					}

				}
				// end of empty($this->change_prod_cat)
				?>

				<!-- All products frm store -->
				<?php
				if (!empty($this->allStoreProd))
				{
					?>
					<!-- START ::for top seller  -->
					<div class="row-fluid">
						<div class="span12 well well-small" >
							<legend align="center">
								<?php echo JText::_('QTC_PROD_FROM_THIS_STORE_PRODUCTS') ;?>
							</legend>
							<?php $random_container = 'q2c_pc_store_products';?>
							<div id="q2c_pc_store_products">
								<?php
								// REDERING Top  seller  PRODUCT
								foreach($this->allStoreProd as $data)
								{
									$data=(array)$data;
									$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'views' . DS . 'product' . DS . 'tmpl' . DS . 'product.php';
									ob_start();
									include($path);
									$html = ob_get_contents();
									ob_end_clean();
									echo $html;
								}
								?>
							</div>

							<!-- setup pin layout script-->
							<script type="text/javascript">
								var pin_container_<?php echo $random_container; ?> = 'q2c_pc_store_products'
							</script>

							<?php
							$view = $comquick2cartHelper->getViewpath('product', 'pinsetup');
							ob_start();
							include($view);
							$html = ob_get_contents();
							ob_end_clean();
							echo $html;
							?>
						</div>
					</div>
					<?php
					if (!empty($this->change_prod_cat))
					{
						?>
						<div class="pager">
							<?php echo $this->pagination->getPagesLinks(); ?>
						</div>
						<?php
					}
				}
				?>
				<!-- END ALL PRODU FRM store -->
			</div>

			<div class="span3">
				<div class="row-fluid">
					<?php
					if (!empty($this->cats))
					{
						/*$defaultcat=$this->change_prod_cat;
						// first for "select cat ". show only if catas >= 2 cat
						if (count($this->cats) >= 2)
						{
							$default=!empty($this->itemDetail)?$this->itemDetail['category']:0;
							echo JHtml::_('select.genericlist',$this->cats,'prod_cat','class="required" size="1" onchange="document.adminForm.submit();" ','value','text',$defaultcat);
						}*/
						//echo $this->cats;
					}
					?>
					<!-- for category list-->

					<?php
					// DECLARE STORE RELEATED PARAMS
					$qtc_catname = "store_cat";
					$qtc_view = "vendor";
					$qtc_layout = "store";
					$qtc_store_id = $this->store_id;

					//GETTING STORE RELEATED CATEGORIES
					$storeHelper = new storeHelper();
					$storeHomePage = 1;
					$viewReleated_cats = $storeHelper->getStoreCats($this->store_id, '', '', '', '', 0);
					// getStoreCats($store_id,$catid='',$onchangeSubmitForm=1,$name='store_cat',$class='',$givedropdown=1)
					$view = $comquick2cartHelper->getViewpath('category', 'categorylist');
					ob_start();
					include($view);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
					?>
				</div>
			</div>
		</div>
		<!-- FIRST ROW-FLOUID DIV-->

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="vendor" />
		<input type="hidden" name="task" value="refreshStoreView" />
		<input type="hidden" name="controller" value="vendor" />
	</form>
</div>
