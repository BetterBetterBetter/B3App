<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

require_once JPATH_SITE . '/components/com_quick2cart/defines.php';

if (!class_exists('comquick2cartHelper'))
{
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

// Load assets
comquick2cartHelper::loadQuicartAssetFiles();
?>

<div class="es-filterbar">
	<div class="filterbar-title h5 pull-left">
		<?php echo JText::_('APP_Q2C_BOUGHTPRODUCTS_USER_TITLE'); ?>
	</div>
	<div class="clearfix"> &nbsp;</div>
</div>

<div class="clearfix"> &nbsp;</div>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<?php
	if (empty($target_data))
	{
		$divhtml = '<div class="empty" style="display:block;"><i class="ies-droplet"></i>';

		if($no_authorize === 'no')
		{
			// Not authorized
			$divhtml .= JText::_('APP_Q2C_BOUGHTPRODUCTS_NOT_AUTHORIZED');
		}
		else
		{
			// Nothing found
			$divhtml .= JText::_('APP_Q2C_BOUGHTPRODUCTS_NO_PRODUCTS');
		}

		$divhtml .= '</div>';

		echo $divhtml;
	}
	else
	{
		?>
		<div class='row-fluid'>
			<?php $random_container = 'q2c_es_app_boughtproducts';?>
			<div id="q2c_es_app_boughtproducts">
				<?php
				foreach($target_data as $data)
				{
					$path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'views'.DS.'product'.DS.'tmpl'.DS.'product.php';
					ob_start();
					include($path);
					$html= ob_get_contents();
					ob_end_clean();
					echo $html;
				}
				?>
			</div>
		</div>
		<?php
	}
	?>
</div>

<?php
$random_container = 'q2c_es_app_boughtproducts';

// Calulate columnWidth (columnWidth = pin_width+pin_padding)
$columnWidth = $pin_width + $pin_padding;
?>

<style type="text/css">
	.q2c_pin_item_<?php echo $random_container;?> { width: <?php echo $pin_width . 'px'; ?> !important; }
</style>

<script type="text/javascript">
	var pin_container_<?php echo $random_container; ?> = 'q2c_es_app_boughtproducts';

	techjoomla.jQuery(document).ready(function()
	{
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
</script>
