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
if (in_array('billing', $order_blocks))
{ ?>
	<h4 <?php echo ($orders_email) ? $emailstyle : '' ;?>><?php echo JText::_('QTC_CUST_INFO');?></h4>
	<div id='no-more-tables'>
		<table class="table table-condensed table-bordered" style="<?php echo $this->email_table_bordered ;?>">
			<thead>
				<tr>
					<th align="left">
						<?php echo JText::_('QTC_BILLIN_INFO');?>
					</th>
					<?php
					if ($params->get('shipping') == '1' && isset($shipinfo) && in_array('shipping', $order_blocks))
					{
					?>
						<th align="left">
							<?php echo JText::_('QTC_SHIPIN_INFO');?>
						</th>
					<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td data-title="<?php echo JText::_('QTC_BILLIN_INFO');?>">
						<address>
							<strong>
								 <?php 	echo $billinfo->firstname . ' ';
								if ($billinfo->middlename)
								{
									echo $billinfo->middlename . '&nbsp;';
								}
								echo $billinfo->lastname;
								?> &nbsp;&nbsp;
							</strong><br />
								<?php echo $billinfo->address; ?>
							<br/>

							<?php	echo $billinfo->city . ', ' ;
								echo (!empty($billinfo->state_name) ? $billinfo->state_name : $billinfo->state_code) . ' ' . $billinfo->zipcode;
								echo '<br/>';
								echo (!empty($billinfo->country_name) ? $billinfo->country_name : $billinfo->country_code) . ', ';

							?>
							<br/>
							<?php 	echo $billinfo->user_email; ?>
							<br/>
							 <abbr title="<?php echo JText::_('QTC_BILLIN_PHON');?>"><?php echo JText::_('QTC_BILLIN_PHON');?> :</abbr> <?php	echo $billinfo->phone; ?>
						</address>

					</td>
					<?php
					if ($params->get('shipping') == '1' && isset($shipinfo) && in_array('shipping', $order_blocks))
					{ ?>
						<td data-title="<?php echo JText::_('QTC_SHIPIN_INFO');?>">
							 <address>
								<strong>
									<?php echo $shipinfo->firstname . ' ';
										if ($shipinfo->middlename)
										{
											echo $shipinfo->middlename . '&nbsp;';
										}
									echo $shipinfo->lastname;
									?> &nbsp;&nbsp;
								</strong><br />
								 <?php echo $shipinfo->address; ?>
								<br/>
									<?php
									echo $shipinfo->city . ', ' ;
									echo (!empty($shipinfo->state_name) ? $shipinfo->state_name : $shipinfo->state_code) . ' ' . $shipinfo->zipcode;
									echo '<br/>';
									echo (!empty($shipinfo->country_name) ? $shipinfo->country_name : $shipinfo->country_code) . ', ';
									?>
								<br/>
								<?php echo $shipinfo->user_email;?>
								<br/>
								 <abbr title="<?php echo JText::_('QTC_BILLIN_PHON');?>"><?php echo JText::_('QTC_BILLIN_PHON');?>:</abbr> <?php echo $shipinfo->phone;?>
							</address>
						</td>
					<?php
					}
					?>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}
?>
