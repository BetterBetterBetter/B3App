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

$lang = JFactory::getLanguage();
$lang->load('mod_quick2cart', JPATH_ROOT);
$comparams = JComponentHelper::getParams( 'com_quick2cart' );
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true) . '/components/com_quick2cart/assets/css/quick2cart.css' );
$ckout_text = $params->get('checkout_text', '');
$ckout_text = trim($ckout_text);
$comquick2cartHelper = new comquick2cartHelper();
$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cart');
?>

<script type="text/javascript">
	function divHideShow(key)
	{
		/* toggle is for changing state - from hide to visible and vice versa*/
		techjoomla.jQuery("#qtc_showhide"+key).slideToggle('', '', function()
		{
			var isVisible = techjoomla.jQuery('#qtc_showhide'+key).is(':visible');
			var className = techjoomla.jQuery('#qtc_item_id'+key).attr('class');

			if (isVisible)
			{
				techjoomla.jQuery('#qtc_item_id'+key).removeClass('qtc_icon-plus').addClass('qtc_icon-minus');
			}
			else
			{
				techjoomla.jQuery('#qtc_item_id'+key).removeClass('qtc_icon-minus').addClass('qtc_icon-plus');
			}
		});
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS . ' ' . $params->get('moduleclass_sfx');?>" >
	<?php
	if (isset($beforecartmodule))
	{
		echo $beforecartmodule;
	}

	$default_currency = $comquick2cartHelper->getCurrencySession();

	$comparams = JComponentHelper::getParams('com_quick2cart');
	$currencies = $comparams->get('addcurrency');
	$currencies_sym = $comparams->get('addcurrency_sym');

	//"INR,USD,AUD";
	//@TODO get this from the component params
	$multi_curr = $currencies;
	$option = array();
	$currcount = 0;

	if ($multi_curr)
	{
		$multi_currs = explode(",", $multi_curr);
		$currcount = count($multi_currs);
		$currencies_syms = explode(",", $currencies_sym);

		foreach ($multi_currs as $key => $curr)
		{
			if (!empty($currencies_syms[$key]))
			{
				$currtext = $currencies_syms[$key];
			}
			else
			{
				$currtext = $curr;
			}

			$option[] = JHtml::_('select.option', trim($curr), trim($currtext));
		}

		if ($currcount>1)
		{
			?>
			<form method="post" name="qtc_mod_form" id="adminForm2" action="index.php?option=com_quick2cart&task=cartcheckout.setCookieCur">
				<div>
					<span><?php echo JText::_('QTC_SEL_CURR');?></span>
					<?php
					//write a func change_curr(); in order.js file to set session for Currency via Ajax.
					echo JHtml::_('select.genericlist', $option, "multi_curr", 'class="" onchange=" document.qtc_mod_form.submit();" autocomplete="off"', "value", "text", $default_currency );
					?>
				</div>

				<input type="hidden" name="qtc_current_url" value="<?php echo JUri::getInstance()->toString();?>"/>

			</form>
			<?php
		}
	}
	?>

	<div>
		<table class="table table-condensed ">
			<!-- detailed view of modulecart Table -->
			<?php
			if ($params->get('viewtype') == 'detail')
			{
				?>
				<thead>
					<tr>
						<th><?php echo JText::_('QTC_MOD_ITEM');?></th>
						<th><?php echo JText::_('QTC_MOD_PRICE');?></th>
					</tr>
				</thead>
				<?php
			}
			?>

			<tbody class="qtc_modulebody">
				<?php
				// IF cart is empty
				if (!empty($cart))
				{
					echo $comquick2cartHelper->get_module($params->get('viewtype'), $ckout_text);
				}
				else
				{
					?>
					<tr>
						<td colspan="2">
							<div class="well"><?php echo JText::_('CCK_CART_EMPTY_CART'); ?></div>
						</td>
					</tr>

					<?php
					if (isset($aftercartdisplay))
					{
						?>
						<tr>
							<td colspan="2">
								<?php echo $aftercartdisplay; ?>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>
