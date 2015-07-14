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

$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cart');
$subtotal = 0;
$totqty = 0;

foreach ($cart as $key=>$item)
{
	$showoptioncol = 0;

	if (!empty($item['options']))
	{
		// Atleast one found then show
		$showoptioncol = 1;
	}
	?>

	<tr>
		<td>
			<?php
			if (!empty($showoptioncol))
			{
				?>
				<i class="qtc_icon-plus" id="qtc_item_id<?php echo $key?>" onclick="divHideShow('<?php echo $key;?>')"></i>
				<?php
			}

			echo $item['title']."( ". $item['qty'] ." )"?>
		</td>
		<td><?php echo $comquick2cartHelper->getFromattedPrice($item['tamt']);?></td>
	</tr>

	<?php
	$attoptionIds = $cart[$key]['product_attributes'];
	$option = $cart[$key]['options'];

	$attoptionIds = array_filter(explode(',', $attoptionIds), "trim");
	$option = array_filter(explode(',', $option), "trim");

	// Getprefix return as "+ 5.00  USD"
	// model is acquired in mod_qick2cart.php
	$prefix = $model->getPrefix($attoptionIds);
	?>

	<tr class="qtc_showhide" id="qtc_showhide<?php echo  $key;?>" style="display:none;" >
		<td colspan=2>
			<?php
			foreach ($option as $k=>$op)
			{
				?>
				<div>
					<?php echo $op . " " . $prefix[$k]; ?>
				</div>
				<?php
			}
			?>
		</td>
	</tr>

	<?php
	$subtotal += $item['tamt'];
	$totqty += $item['qty'];
}
?>

<tr>
	<td><strong><?php echo JText::_('QTC_MOD_SUBTOTAL')?></strong></td>
	<td><?php echo $comquick2cartHelper->getFromattedPrice(number_format($subtotal,2));?></td>
</tr>

<?php
if(isset($aftercartdisplay))
{
	?>
	<tr>
		<td colspan="2">
			<?php echo $aftercartdisplay; ?>
		</td>
	</tr>
	<?php
}
?>

<tr>
	<td colspan="2">
		<?php
		// Added by aniket for jtext in order.js file.
		$msg_order_js = "'".JText::_('QTC_CART_EMPTY_CONFIRMATION')."','".JText::_('QTC_CART_EMPTIED')."'";
		?>

		<button class="btn btn-danger btn-small btn_margin" onclick="emptycart(<?php echo $msg_order_js ?>);" >
			<i class="icon-trash icon-white"></i>&nbsp;<?php echo JText::_('QTC_MOD_EMPTY_CART')?>
		</button>

		<?php
		$ckout_btname = (!empty($ckout_text)) ? $ckout_text : JText::_('QTC_CHKOUT'); ?>
		<button class="btn btn-primary btn-small btn_margin" onclick="window.open('<?php echo JRoute::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid='.$Itemid);?>','_self')" >
			<i class="icon-chevron-right icon-white"></i><?php echo $ckout_btname;?>
		</button>
	</td>
</tr>
