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

$document=JFactory::getDocument();
jimport('joomla.filter.output');
jimport( 'joomla.utilities.date');
//$document->addStyleSheet(JUri::base().'components/com_quick2cart/assets/css/quick2cart.css');//backend css
$reportsHelper=new reportsHelper();
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
<?php
$active = 'payouts';
$comquick2cartHelper = new comquick2cartHelper();
$view=$comquick2cartHelper->getViewpath('vendor','toolbar');
ob_start();
	include($view);
	$html = ob_get_contents();
ob_end_clean();
echo $html;
?>
	<?php
	if($this->issite)
	{ ?>
		<!--page header-->
		<legend><?php echo JText::_('COM_QUICK2CART_MY_CASHBACK');?></legend>
		<?php
	}
	?>
	<form action="" method="post" name="adminForm"	id="adminForm">
		<?php
		if(empty($this->payouts))
		{
			?>
			<div class="well">
				<div class="alert alert-info">
				<?php	echo JText::_('COM_QUICK2CART_NO_DATA');			?>
				</div>
			</div>
			<input type="hidden" name="option" value="com_quick2cart" />
			<input type="hidden" name="view" value="reports" />
			<input type="hidden" name="layout" value="mypayouts" />

			<?php
	//		return false;
		}
		?>
		<table class="  table table-striped table-bordered">
			<?php
			if(!empty($this->payouts)) {
			?>
			<tr>
				<th><?php echo JText::_('COM_QUICK2CART_NUMBER');?></th>
			<!--	<th><?php echo JHtml::_( 'grid.sort', 'COM_QUICK2CART_PAYOUT_ID','id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', 'COM_QUICK2CART_PAYEE_DETAILS','email_id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', 'COM_QUICK2CART_TRANSACTION_ID','transaction_id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', 'COM_QUICK2CART_PAYOUT_DATE','date', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', 'COM_QUICK2CART_PAYMENT_STATUS','status', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th><?php echo JHtml::_( 'grid.sort', 'COM_QUICK2CART_PAYOUT_AMOUNT','amount', $this->lists['order_Dir'], $this->lists['order']); ?></th>				  -->
			<th><?php echo JText::_('COM_QUICK2CART_PAYOUT_ID');?></th>
			<th><?php echo JText::_('COM_QUICK2CART_PAYOUT_DATE');?></th>
			<th><?php
			$currency=$comquick2cartHelper->getCurrencySymbol();
			echo JText::_('COM_QUICK2CART_ORDER_AMOUNT');?></th>
		<!-- 	<th><?php echo JText::_('COM_QUICK2CART_CASHBACK_VALUE');?></th>	 -->
			</tr>
			<?php
			} ?>
			<?php
			$i=0;
			foreach($this->payouts as $payout)
			{ ?>
					<tr><td><?php echo $i+1;?></td>
						<td>
						<?php // prepending 0's to id so that it will look 6 digit
						if(strlen($payout->id)<=6)
						{
							$append='';
							for($z=0;$z<(6-strlen($payout->id));$z++){
								$append.='0';
							}
							$payout->id=$append.$payout->id;
						}
						echo $payout->id;
						?>
					</td>
					<td>
						<?php
						if(JVERSION<'1.6.0')
							echo JHtml::_( 'date', $payout->date, '%Y/%m/%d');
						else
							echo JHtml::_( 'date', $payout->date, "Y-m-d");
						?>
					</td><td><?php echo $comquick2cartHelper->getFromattedPrice(number_format($payout->amount,2)); ?></td>
		<!--			<td><?php //echo $payout->cashback;?></td>	 -->
				</tr>
			<?php
			$i++;
			}
			?>
			<tr rowspan="2" height="20">
				<td class="com_quick2cart_align_right" colspan="3"></td>
				<td></td>
			</tr>
						<!-- PAID AMOUNT-->
			<tr>
			<td class="com_quick2cart_align_right" colspan="3"><b><?php echo JText::_('COM_QUICK2CART_PAID_OUT'); ?></b></td>
				<td>
					<b>
					<?php
						$totalpaidamount=$reportsHelper->getTotalPaidOutAmount($this->logged_userid);
						echo $comquick2cartHelper->getFromattedPrice(number_format($totalpaidamount,2));
					?>
					</b>
				</td>
			</tr>
			<!-- TO BE PAY-->
			<tr>
				<td class="com_quick2cart_align_right" colspan="3"><b><?php echo JText::_( 'COM_QUICK2CART_CASHBACK'); ?></b></td>
				<td>
					<b>
					<?php
					$totalAmount2BPaidOut=$reportsHelper->getTotalAmount2BPaidOut($this->logged_userid);
					echo $comquick2cartHelper->getFromattedPrice(number_format($totalAmount2BPaidOut,2));
					?>
					</b>
				</td>
			</tr>


			<!-- COMMISSION AMOUNT-->
			<tr>
			<td class="com_quick2cart_align_right" colspan="3"><b><?php echo JText::_('COM_QUICK2CART_PAYOUT_COMMISSION'); ?></b></td>
				<td>
					<b>
					<?php
						$commission_cut=$reportsHelper->getCommissionCut($this->logged_userid);
						echo $comquick2cartHelper->getFromattedPrice(number_format($commission_cut,2));
					?>
					</b>
				</td>
			</tr>
			<tr>
				<td class="com_quick2cart_align_right" colspan="3"><b><?php echo JText::_( 'COM_QUICK2CART_BALANCE'); ?></b></td>
				<td>
					<b>
					<?php
						$balanceamt1=$totalAmount2BPaidOut-$totalpaidamount - $commission_cut;;
						$balanceamt=number_format($balanceamt1, 2, '.', '');
						if($balanceamt=='-0.00'){
							echo $comquick2cartHelper->getFromattedPrice('0.00');
							}
						else
							echo $comquick2cartHelper->getFromattedPrice(number_format($balanceamt1,2));
						//echo ' '.$this->currency_code;
					?>
					</b>
				</td>
			</tr>


			<?php
			if(!$this->issite)
			{
				?>
				<tr>
					<td colspan="6" class="pager com_quick2cart_align_center">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				<?php
			}
			?>

		</table>

		<?php
		if($this->issite)
		{
			?>
			<div class="pager com_quick2cart_align_center">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
			<?php
		}
		?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="reports" />
		<input type="hidden" name="layout" value="mypayouts" />
	<!--	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />  -->

	</form>
</div>
