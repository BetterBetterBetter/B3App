<?php
/**
 * @version     1.0.0
 * @package     com_quick2cart
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      sanjivani <sanjivani_p@tekdi.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_quick2cart/css/quick2cart.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_quick2cart');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_quick2cart&task=zones.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'zoneList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
</script>
?>
<script type="text/javascript">
    function deleteItem(item_id){
        if(confirm("<?php echo JText::_('COM_Q2C_DELETE_MESSAGE'); ?>")){
            document.getElementById('form-zone-delete-' + item_id).submit();
        }
    }
</script>
<?php if(JFactory::getUser()->authorise('core.create','com_quick2cart')): ?><a href="<?php echo JRoute::_('index.php?option=com_quick2cart&task=zone.edit&id=0'); ?>"><?php echo JText::_("COM_Q2C_ADD_ITEM"); ?></a>
	<?php endif; ?>

<div class="items">
    	<table class="table table-striped" id="zoneList">
				<thead>
					<tr>
					<th class='center' >
					<?php
					//echo JText::_('COM_QUICK2CART_ZONES_ZONE_NAME');
					//echo JHtml::_('grid.sort',  'COM_QUICK2CART_ZONES_ZONE_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<?php if (isset($this->items[0]->state)): ?>
						<th width="5%" class="nowrap center">
							<?php
							echo JText::_('JSTATUS');
							//echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
					<?php endif; ?>
					<th class='center'>
						<?php
						echo JText::_('COM_QUICK2CART_ZONES_ZONE_ID');
						//echo JHtml::_('grid.sort',  'COM_QUICK2CART_ZONES_ZONE_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>


					</tr>
				</thead>
				<tbody>
<?php $show = false; ?>
        <?php foreach ($this->items as $item) : ?>


				<?php
					if($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own',' com_quick2cart'))):
						$show = true;
						?>
							<tr><td>
								<a href="<?php echo JRoute::_('index.php?option=com_quick2cart&view=zone&id=' . (int)$item->id); ?>"><?php echo $item->name; ?></a>
								</td><td>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_quick2cart')):
									?>
										<a href="javascript:document.getElementById('form-zone-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_Q2C_UNPUBLISH_ITEM"); else: echo JText::_("COM_Q2C_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-zone-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_quick2cart&task=zone.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo (int)!((int)$item->state); ?>" />
											<input type="hidden" name="option" value="com_quick2cart" />
											<input type="hidden" name="task" value="zone.publish" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_quick2cart')):
									?>
									</td><td>
										<a href="javascript:deleteItem(<?php echo $item->id; ?>);"><?php echo JText::_("COM_Q2C_DELETE_ITEM"); ?></a>
										<form id="form-zone-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_quick2cart&task=zone.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="option" value="com_quick2cart" />
											<input type="hidden" name="task" value="zone.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?></td>
							</tr>
						<?php endif; ?>

<?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_Q2C_NO_ITEMS');
        endif;
        ?>
        </tbody>
    </table>
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>



