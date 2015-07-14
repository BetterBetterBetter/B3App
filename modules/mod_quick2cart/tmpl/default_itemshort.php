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
?>

<tr>
	<td><?php echo JText::_('CCK_CART_ITEMS');?></td>
	<td><?php echo count($cart); ?></td>
</tr>

<?php
$tprice = 0;

foreach ($cart as $cart1)
{
	$tprice += $cart1['tamt'];
}
?>

<tr>
	<td><strong> <?php echo JText::_('CCK_CART_TOTAL');?></strong> </td>
	<td><span><?php echo $comquick2cartHelper->getFromattedPrice(number_format($tprice,2));?></span>&nbsp;</td>
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
		$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cart');
		$ckout_btname=(isset($ckout_text))?$ckout_text:JText::_('QTC_CHKOUT'); ?>
		<button class="btn btn-primary btn-small btn_margin" onclick="window.open('<?php echo JRoute::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid='.$Itemid);?>','_self')" >
			<i class="icon-chevron-right icon-white"></i><?php echo $ckout_btname;?>
		</button>
	</td>
</tr>
