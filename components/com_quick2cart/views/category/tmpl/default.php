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

jimport('joomla.filesystem.file');

//JHtml::_('behavior.framework');
//JHtml::_('behavior.modal');
//JHtml::_('behavior.keepalive');
//JHtml::_('behavior.tooltip');

$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');

$categoryPage = $this->categoryPage;

// For featured and top seller product
$product_path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php';

if (!class_exists('productHelper'))
{
	JLoader::register('productHelper', $product_path );
	JLoader::load('productHelper');
}

$productHelper =  new productHelper();
$comquick2cartHelper = new comquick2cartHelper;
$store_id=0;//$this->store_id;
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> category-products">
	<form name="adminForm" id="adminForm" class="form-validate" method="post">
		<?php
		$input = JFactory::getApplication()->input;
		$option = $input->get('option', '', 'STRING' );
		$storeOwner = $input->get( 'qtcStoreOwner', 0, 'INTEGER');

		if (!empty($this->store_role_list) && $storeOwner==1)
		{
			$active = 'products';
			$view = $comquick2cartHelper->getViewpath('vendor', 'toolbar');
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
		}
		?>

		<div class="row-fluid qtc_productblog">
			<div class="span9">
				<div class="row-fluid">
					<div class="span12">
						<legend>
							<?php
							$lagend_title = "QTC_PRODUCTS_CATEGORY_ALL_BLOG_VIEW";
							/*$store_name = '';

							if (!empty($this->store_role_list))
							{
								$storehelp = new storeHelper();
								$index = $storehelp->array_search2d($this->store_id, $this->store_role_list);

								if (is_numeric($index))
								{
									$store_name = $this->store_role_list[$index]['title'];
									$lagend_title = "QTC_PRODUCTS_CATEGORY_BLOG_VIEW";
								}

								echo JText::sprintf($lagend_title, $store_name);
							}
							else
							{
								echo JText::_($lagend_title);
							}*/
							echo JText::_($lagend_title);
							?>
						</legend>
					</div>
				</div>

				<div id="filter-bar" class="btn-toolbar">
					<div class="filter-search btn-group pull-left">
						<input type="text" name="filter_search" id="filter_search"
						placeholder="<?php echo JText::_('COM_QUICK2CART_FILTER_SEARCH_DESC_PRODUCTS'); ?>"
						value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
						class="hasTooltip input-medium"
						title="<?php echo JText::_('COM_QUICK2CART_FILTER_SEARCH_DESC_PRODUCTS'); ?>" />
					</div>
					<div class="btn-group pull-left">
						<button type="submit" class="btn hasTooltip"
						title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
							<i class="icon-search"></i>
						</button>
						<button type="button" class="btn hasTooltip"
						title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
						onclick="document.id('filter_search').value='';this.form.submit();">
							<i class="icon-remove"></i>
						</button>
					</div>

					<?php if (JVERSION >= '3.0') : ?>
					<!--
						<div class="btn-group pull-right hidden-phone ">
							<label for="limit" class="element-invisible">
								<?php //echo JText::_('COM_QUICK2CART_SEARCH_SEARCHLIMIT_DESC'); ?>
							</label>
							<?php //echo $this->pagination->getLimitBox(); ?>
						</div> -->
					<?php endif; ?>
				</div>

				<!--Added by Sneha for free text search-->
				<!--
				<div class="row-fluid">
					<div class="pull-right">
						<input type="text" placeholder="Enter name.." name="search_list" id="search_list" value="<?php echo $this->lists['search_list']; ?>" class="input-medium pull-left" onchange="document.adminForm.submit();" />
						<div class="btn-group pull-right hidden-phone">
							<button type="button" onclick="this.form.submit();" class="btn tip hasTooltip" data-original-title="Search">
								<i class="icon-search"></i>
							</button>
							<button onclick="document.id('search_list').value='';this.form.submit();" type="button" class="btn tip hasTooltip" data-original-title="Clear">
								<i class="icon-remove"></i>
							</button>
						</div>
					</div>
				</div>
				<!--Added by Sneha for free text search-->

				<div class="clearfix">&nbsp;</div><div class="clearfix">&nbsp;</div>

				<div class="row-fluid">
					<div class="span12">
						<?php
						// GETTING ALL FEATURED PRODUCT
						$target_data = ($this->products);
						$prodivsize = "category_product_div_size";

						if (empty($target_data))
						{
							?>
								<div class="alert alert-error">
									<span><?php echo JText::_('QTC_NO_PRODUCTS_FOUND'); ?></span>
								</div>
							<?php
						}
						else
						{
							?>
							<?php $random_container = 'q2c_pc_category';?>
							<div id="q2c_pc_category">
								<?php
								// REDERING FEATURED PRODUCT
								foreach($target_data as $data)
								{
									// converting to array
									$data=(array)$data;
									$path=$comquick2cartHelper->getViewpath('product','product');
									$store_list= !empty($this->store_list)?$this->store_list:array();
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
								var pin_container_<?php echo $random_container; ?> = 'q2c_pc_category'
							</script>

							<?php
							$view = $comquick2cartHelper->getViewpath('product', 'pinsetup');
							ob_start();
							include($view);
							$html = ob_get_contents();
							ob_end_clean();
							echo $html;
							?>
							<?php
						}
						?>
					</div>
				</div>
				<!-- END ::for featured product  -->
			</div>

			<div class="span3">
				<!-- for category list-->
				<?php
				$view=$comquick2cartHelper->getViewpath('category','categorylist');
				ob_start();
				include($view);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
				?>
				<hr class="hr hr-condensed">
				<?php
				if($this->params->get('multivendor')):?>
					<!-- for store list-->
					<?php
					$storeHelper=new storeHelper();
					$options=$storeHelper->getStoreList();
					$view=$comquick2cartHelper->getViewpath('vendor','storelist');
					ob_start();
					include($view);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
					?>
				<?php endif;?>
			</div>
		</div>
		<!-- FIRST ROW-FLOUID DIV-->

		<?php if (JVERSION >= '3.0'): ?>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php else: ?>
			<div class="pager">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
		<?php endif; ?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="category" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="" />
	</form>
</div>
