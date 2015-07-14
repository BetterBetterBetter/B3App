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

if (!defined('DS'))
{
	define('DS', '/');
}


require_once JPATH_ROOT .'/components/com_community/libraries/core.php';

$lang = JFactory::getLanguage();
$lang->load('plg_community_quick2cartproduct', JPATH_ADMINISTRATOR);

require_once JPATH_SITE . '/components/com_quick2cart/defines.php';

class plgCommunityQuick2cartproduct extends CApplications
{
	var $name = "Quick2cartproduct";
	var $_name	= 'quick2cartproduct';

	function onProfileDisplay()
	{
		$cache = JFactory::getCache('community');
		$callback = array($this, '_getquick2cartproductHTML');

		$content = $cache->call($callback);
		return $content;
	}

	function _getquick2cartproductHTML()
	{
		jimport('joomla.filesystem.file');

		if (JFile::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{
			$lang = JFactory::getLanguage();
			$lang->load('com_quick2cart', JPATH_SITE);
			$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';

			if(!class_exists('comquick2cartHelper'))
			{
				JLoader::register('comquick2cartHelper', $path );
				JLoader::load('comquick2cartHelper');
			}

			// Load assets
			comquick2cartHelper::loadQuicartAssetFiles();

			$product_path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php';

			if (!class_exists('productHelper'))
			{
				JLoader::register('productHelper', $product_path );
				JLoader::load('productHelper');
			}

			$params = $this->params;
			$no_of_prod = $params->get('no_of_prod','2');

			// Get profile id
			$user= CFactory::getRequestUser();
			$model =  new productHelper();
			$target_data = $model->getUserProducts($user->_userid,$no_of_prod);

			if (!empty($target_data))
			{
				$random_container = 'q2c_pc_js_my_products';
				$html = "
					<div class='" . Q2C_WRAPPER_CLASS . "' >
						<div class='row-fluid'>
							<div id='q2c_pc_js_my_products'>";

								foreach($target_data as $data)
								{
									$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'views' . DS . 'product' . DS . 'tmpl' . DS . 'product.php';
									ob_start();
									include($path);
									$html.= ob_get_contents();
									ob_end_clean();
								}

				$html .="
						</div>
					</div>
				</div>";

				ob_start();
				?>
					<?php
					// Get pin width
					$pin_width = $params->get('pin_width');

					if (empty($pin_width))
					{
						$pin_width = 170;
					}

					// Get pin padding
					$pin_padding = $params->get('pin_padding');

					if (empty($pin_padding))
					{
						$pin_padding = 7;
					}

					// Calulate columnWidth (columnWidth = pin_width+pin_padding)
					$columnWidth = $pin_width + $pin_padding;
					?>

					<style type="text/css">
						.q2c_pin_item_<?php echo $random_container;?> { width: <?php echo $pin_width . 'px'; ?> !important; }
					</style>

					<script type="text/javascript">
						var pin_container_<?php echo $random_container; ?> = 'q2c_pc_js_my_products';

						techjoomla.jQuery(document).ready(function()
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
						});
					</script>
				<?php

				$pin_html .= ob_get_contents();
				ob_end_clean();

				$html .= $pin_html;

				return $html;
			}
		}
	}
}
