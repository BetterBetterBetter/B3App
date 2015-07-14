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

require_once JPATH_SITE . '/components/com_quick2cart/defines.php';

if (!class_exists('comquick2cartHelper'))
{
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

// Load assets
comquick2cartHelper::loadQuicartAssetFiles();

$input = JFactory::getApplication()->input;
$storeid = $input->get('storeid', '', 'INT');

if ($storelists)
{
	?>
	<div class="es-filterbar">
		<div class="filterbar-title h5 pull-left">
			<?php echo JText::_('APP_Q2CMYPRODUCTS_USER_TITLE'); ?>
		</div>
		<div class="pull-right">
			<?php
			$options = array();

			$firstStore = (! empty($storelists)) ? $storelists[0]['id'] : '';
			$options[] = JHtml::_('select.option', 0, JText::_('APP_Q2CMYPRODUCTS_SELECT_STORE'));

			foreach ($storelists as $storelist)
			{
				$options[] = JHtml::_('select.option', $storelist["id"], $storelist['title']);
			}

			if (count($storelists) > 2 )
			{
				echo $this->dropdown = JHtml::_('select.genericlist', $options, 'storeid', 'class="chzn-done"  size="1" title="' . JText::_('APP_Q2CMYPRODUCTS_SELECT_STORE_DESC') . '"', 'value', 'text', $firstStore, 'storeid');
			}
			?>
			<div class=""> &nbsp;</div>
		</div>
	</div>

	<div class="clearfix"> &nbsp;</div>
	<?php
}
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> app-Q2CMyProducts" data-Q2CMyProducts>
	<div class="row-fluid app-contents<?php echo !$products ? ' is-empty' : '';?>">
		<?php if ($products): ?>
			<?php $random_container = 'q2c_pc_es_app_my_products';?>
			<div id="q2c_pc_es_app_my_products">
				<?php
				foreach ($products as $data)
				{
					$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'views' . DS . 'product' . DS . 'tmpl' . DS . 'product.php';
					ob_start();
					include $path;
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
				}
				?>
			</div>

			<?php
				if ($productsCount > $total):
					$storeHelper = new storeHelper;
					$storeLink = $storeHelper->getStoreLink($firstStore);

			?>
				<div class="pull-right">
					<a href="<?php echo $storeLink; ?>"><?php echo JText::_('APP_Q2CMYPRODUCTS_SHOW_ALL') . " (" . $productsCount . ")";?></a>
				</div>
			<?php endif; ?>

		<?php else: ?>
			<div class="empty">
				<i class="ies-droplet"></i>
				<?php echo JText::sprintf('APP_Q2CMYPRODUCTS_NO_PRODUCTS_FOUND', $user->getName()); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
// Calulate columnWidth (columnWidth = pin_width+pin_padding)
$columnWidth = $pin_width + $pin_padding;
?>

<style type="text/css">
	.q2c_pin_item_<?php echo $random_container;?> { width: <?php echo $pin_width . 'px'; ?> !important; }
</style>

<script type="text/javascript">
	var pin_container_<?php echo $random_container; ?> = 'q2c_pc_es_app_my_products'

	techjoomla.jQuery(document).ready(function()
	{
		techjoomla.jQuery('#storeid').attr('data-chosen', 'com_q2c');
		initiateQ2cPins();
	});

	function initiateQ2cPins()
	{
		var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
		var msnry = new Masonry( container_<?php echo $random_container;?>, {
			columnWidth: <?php echo $columnWidth; ?>,
			itemSelector: '.q2c_pin_item_<?php echo $random_container;?>',
			gutter: <?php echo $pin_padding; ?>});

		setTimeout(function(){
			var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
			var msnry = new Masonry( container_<?php echo $random_container;?>, {
				columnWidth: <?php echo $columnWidth; ?>,
			itemSelector: '.q2c_pin_item_<?php echo $random_container;?>',
				gutter: <?php echo $pin_padding; ?>});
		}, 1000);

		setTimeout(function(){
			var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
			var msnry = new Masonry( container_<?php echo $random_container;?>, {
				columnWidth: <?php echo $columnWidth; ?>,
			itemSelector: '.q2c_pin_item_<?php echo $random_container;?>',
				gutter: <?php echo $pin_padding; ?>});
		}, 3000);

		setTimeout(function(){
			var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
			var msnry = new Masonry( container_<?php echo $random_container;?>, {
				columnWidth: <?php echo $columnWidth; ?>,
			itemSelector: '.q2c_pin_item_<?php echo $random_container;?>',
				gutter: <?php echo $pin_padding; ?>});
		}, 4000);

		setTimeout(function(){
			var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
			var msnry = new Masonry( container_<?php echo $random_container;?>, {
				columnWidth: <?php echo $columnWidth; ?>,
			itemSelector: '.q2c_pin_item_<?php echo $random_container;?>',
				gutter: <?php echo $pin_padding; ?>});
		}, 6000);
	}

	/*techjoomla.jQuery(window).bind("load", function()
	{
		var select = techjoomla.jQuery('#storeid :selected').val();
		jQuery.ajax({
			type     : 'post',
			url      : '?option=com_quick2cart&task=updateEasysocialApp',
			data     : {"storeid" : select,"uid" : <?php echo $userId;?>,"total" : <?php echo $total;?>},
			dataType : 'json',
			success  : function(data)
			{
				jQuery(".app-Q2CMyProducts").html(data.html);
				eval(data.js);
			}
		});
	});*/

	techjoomla.jQuery('#storeid').change(function()
	{
		var val = techjoomla.jQuery(this).val();
		var url = window.location;
		javascript:void(0);
		var newURLString = window.location.href + "&storeid=" + val;

		/*window.location.href = newURLString;*/

		jQuery.ajax({
			type     : 'post',
			url      : '?option=com_quick2cart&task=updateEasysocialApp',
			data     : {"storeid" : val,"uid" : <?php echo $userId;?>,"total" : <?php echo $total;?>},
			dataType : 'json',
			success  : function(data)
			{
				jQuery(".app-Q2CMyProducts").html(data.html);
				eval(data.js);
			}
		});
	});
</script>
