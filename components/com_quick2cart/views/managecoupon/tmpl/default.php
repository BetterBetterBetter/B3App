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
//$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		JHtml::_('behavior.tooltip');
	/*	JToolBarHelper::back( JText::_('QTC_HOME') , 'index.php?option=com_quick2cart');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();

		JToolBarHelper::deleteList('', 'deletecoupon');
		JToolBarHelper::addNew($task = 'add', $alt = JText::_('QTC_NEW'));
		*/
		$document = JFactory::getDocument();

		//$document->addStyleSheet(JUri::base().'components/com_quick2cart/assets/css/quick2cart.css'); //aniket


		$input=JFactory::getApplication()->input;
		$cid		= $input->get(  'cid','','ARRAY' );
/*<?php if (JVERSION >= '1.6.0'){ ?>
	Joomla.submitbutton = function(action){
<?php } else { ?>
	function submitbutton( action ) {
<?php } */
?>

<?php
// check authorization to create coupon $this->authorized_store_id= storeid of user
if (empty($this->authorized_store_id)){
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
<div class="well" >
	<div class="alert alert-error">
		<span ><?php echo JText::_('QTC_NOT_AUTHORITY_TO_VIEW_COUPON_LIST'); ?> </span>
	</div>
</div>
</div><!-- eoc <?php echo Q2C_WRAPPER_CLASS; ?> -->
<?php
	return false;
}
?>

<script type="text/javascript">
function qtc_CopchangeState(state,couponid)
{
	var form = document.adminForm;
		switch(state)
		{
			case 'publish': form.task.value='publish';
			break

			case 'unpublish': form.task.value='unpublish';
			break
		}
		form.copId.value=couponid;
		form.submit();
}
//console.log(action);
function submitAction(action)
{
		var form = document.adminForm;
		if (action=='publish' || action=='unpublish' || action=='deletecoupon')
		{
				if (document.adminForm.boxchecked.value==0){
					alert("<?php echo JText::_('QTC_MAKE_SEL');?>");
				return;
				}
				switch(action)
				{
					case 'publish': form.task.value='publish';
					break

					case 'unpublish': form.task.value='unpublish';
					break

					case 'deletecoupon':
						var r=confirm("<?php echo JText::_('QTC_DELETE_CONFIRM_COUPON');?>");
						if (r==true)
						{
							var aa;
							form.task.value='deletecoupon';
						}
						else
						{
							return false;
						}

					break
				}
			//Joomla.submitform(action);
		}
		elseif (action=="newCoupon" )
		{
			switch(action)
			{
				case 'newCoupon': form.task.value='newCoupon';
				break

			}

		}
		else
		{
			window.location = 'index.php?option=com_quick2cart&view=managecoupon&layout=default';
		}
	form.submit();

	return;

 }
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
<form  method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php
	$active = 'storecoupons';
$comquick2cartHelper = new comquick2cartHelper;
$view=$comquick2cartHelper->getViewpath('vendor','toolbar');
ob_start();
	include($view);
	$html = ob_get_contents();
ob_end_clean();
echo $html;
?>

	<!-- <div class="page-header"><h2><?php echo JText::_('QTC_MANAGE_STORE_COUPONS')?>&nbsp;</h2>	</div> -->
	<legend><?php
	if (!empty($this->store_role_list))
	{
		$storehelp = new storeHelper();
		$index = $storehelp->array_search2d($this->store_id, $this->store_role_list);

		if (is_numeric( $index))
		{
			$store_name = $this->store_role_list[$index]['title'];
		}

		echo JText::sprintf('QTC_MANAGE_STORE_COUPONS',$store_name) ;
	}
	else
	{
		echo JText::_('QTC_MANAGE_STORE_COUPONS');
	}
	?>

	</legend>

	<div  class="row-fluid">
		<div class="table-tool">
			<div class="span5">
				<div class="filter-search pull-left">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search_list" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="input-small" onchange="document.adminForm.submit();" />
					<button class="btn btn-success btn-small" onclick="this.form.submit();"><?php echo JText::_( 'SA_GO' ); ?></button>
					<button class="btn btn-primary btn-small" onclick="document.getElementById('search_list').value='';this.form.getElementById('filter_type').value='0';this.form.getElementById('filter_logged').value='0';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
				 </div>
			</div>
			<div class="span7">
				<div class="filter-search pull-right">

					<button type="button" class="btn btn-success btn-small" title="<?php echo JText::_( 'COM_QUICK2CART_COUPON_NEW' ); ?>" onclick="submitAction('newCoupon');"><i class="<?php echo QTC_ICON_PLUS; ?> icon-white"></i><?php echo JText::_( 'COM_QUICK2CART_COUPON_NEW' ); ?></button>
					<button type="button" class="btn btn-success btn-small" title="<?php echo JText::_( 'COM_QUICK2CART_COUPON_PUBLISH' ); ?>" onclick="submitAction('publish');"><i class="<?php echo QTC_ICON_CHECKMARK;?> icon-white"></i><?php echo JText::_( 'COM_QUICK2CART_COUPON_PUBLISH' ); ?></button>
					<button type="button" class="btn btn-warning btn-small" title="<?php echo JText::_( 'COM_QUICK2CART_COUPON_UNPUBLISH' ); ?>" onclick="submitAction('unpublish');"><i class="<?php echo QTC_ICON_MINUS;?> icon-white"></i><?php echo JText::_( 'COM_QUICK2CART_COUPON_UNPUBLISH' ); ?></button>
					<button type="button" class="btn btn-danger btn-small" title="<?php echo JText::_( 'COM_QUICK2CART_COUPON_DELETE' ); ?> " onclick="return submitAction('deletecoupon');"><i class="icon-trash icon-white"></i><?php echo JText::_( 'COM_QUICK2CART_COUPON_DELETE' ); ?></button>
				</div>
			</div>

		</div>
	</div>
	<hr class="hr-condensed" />
	<table class="table table-striped table-condensed">
		<thead>
		<tr>
				<th width="2%" align="center" class="title">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
		<!--		<th width="2%" class="title" nowrap="nowrap" align="center">
					<?php echo JHtml::_('grid.sort',  JText::_( 'C_ID'), 'id',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
-->
				<th class="title" align="left" width="15%" align="center">
					<?php echo JHtml::_('grid.sort',   JText::_( 'COM_QUICK2CART_COUPON_LIST_NAME'), 'name',   $this->lists['order_Dir'],   $this->lists['order'] );

					 ?>
				</th>
				<th width="13%" class="title" align="center">
					<?php echo JHtml::_('grid.sort',   JText::_( 'COM_QUICK2CART_COUPON_LIST_PUB'), 'published',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title" align="left">
					<?php echo JHtml::_('grid.sort',   JText::_( 'COM_QUICK2CART_COUPON_LIST_COD'), 'code',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title" align="center">
					<?php echo JHtml::_('grid.sort',   JText::_( 'COM_QUICK2CART_COUPON_LIST_COUPONP_VAL'), 'value',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="left">
					<?php echo JHtml::_('grid.sort',   JText::_( 'COM_QUICK2CART_COUPON_LIST_C_TYP'), 'val_type',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="30%" class="title" align="">
					<?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_LIST_C_PROD'), JText::_('COM_QUICK2CART_COUPON_LIST_C_PROD'), '', JText::_('COM_QUICK2CART_COUPON_LIST_C_PROD')); ?>
				</th>
				<!--
				<th width="10%" class="title" align="">
					<?php //echo JHtml::tooltip(JText::_('DESC_C_USER'), JText::_('C_USER'), '', JText::_('C_USER')); ?>
				</th>
				-->
			</tr>
		</thead>

		<tbody>
		<?php
			$k = 0;
		if (!empty($this->coupons))  // if coupons are present
		{
			for ($i=0, $n=count( $this->coupons ); $i < $n ; $i++)
			{
			$zone_type='';

				$row 	= $this->coupons[$i];
				//$published 	= JHtml::_('grid.published', $row, $i,"tick.png");

				$link 	= 'index.php?option=com_quick2cart&amp;view=managecoupon&layout=form&amp;cid[]='. $row->id. '&change_store='.$this->store_id;


			?>
			<tr class="<?php echo 'row$k'; ?>">
		<!--		<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td> -->
				<td align="center"> <!-- check box -->
					<?php echo JHtml::_('grid.id', $i, $row->id ); ?>
					<!-- <input type="checkbox" title="JGRID_CHECKBOX_ROW_N" onclick="Joomla.isChecked(this.checked);" value="<?php echo$row->id;?>" name="cid[]" id="cb<?php echo$row->id;?>">
					-->
				</td>
		<!--		<td align="center">
					<?php echo $row->id; ?>
				</td>  -->
				<td align="left">
				<a href="<?php echo $link; ?>">
						<?php echo $row->name; ?></a>
				</td>
				<td align="center">
					<?php //echo $published; ?>

					<?php if (!empty($row->published))
							{
								$qtc_icon_state=QTC_ICON_CHECKMARK; // ok mark
								$qtcCopState=JText::_( 'QTC_PUBLISH' );
								$qtcstate='unpublish'; // toggle
								$color="btn-success";
								$title=JText::_("QTC_UNPUBLISH_COP");
							}
							else{
								$qtc_icon_state=QTC_ICON_REMOVE; // cross mark
								$qtcCopState=JText::_( 'QTC_UNPUBLISH' );
								$qtcstate='publish';// toggle
								$color="btn-danger";
								$title=JText::_("QTC_PUBLISH_COP");
							}

					?>
				<!--
					<a class="btn btn-mini active ?>" href="javascript:void();" onclick=" qtc_CopchangeState('<?php echo $qtcstate;?>',<?php echo $row->id;?>)">
					<i id="" class="  <?php echo $qtc_icon_state;?> icon-black"></i>
					<?php  echo $qtcCopState ?>
					</a>

					<a class="btn btn-mini active <?php echo $color;?>" href="javascript:void();" onclick=" qtc_CopchangeState('<?php echo $qtcstate;?>',<?php echo $row->id;?>)"><i id="" class="  <?php echo $qtc_icon_state;?> icon-black"></i>
					</a>
					-->
					<a class="" href="javascript:void();" title="<?php echo $title;?>" onclick=" qtc_CopchangeState('<?php echo $qtcstate;?>',<?php echo $row->id;?>)">
						<img src="<?php // echo JUri::root();?>administrator/components/com_quick2cart/images/<?php echo ( !empty($row->published)) ? 'tick.png' : 'publish_x.png';?>" width="16" height="16" border="0" />
					</a>

				</td>
			<td align="left">
					<?php echo stripslashes($row->code); ?>
				</td>
				<td align="center">
					<?php echo $row->value ?>
				</td>
				<td align="left">
					<?php  if ($row->val_type==0){echo JText::_( "COM_QUICK2CART_COUPON_LIST_C_FLAT");}else{echo JText::_( "COM_QUICK2CART_COUPON_PER");} ?>
				</td>


				<td align="center">
					<?php
						if (isset($row->item_id_name)){
							echo $row->item_id_name;
						}else{
							echo '-';
						}
						 ?>
				</td>
				<!--
				<td align="left">
					<?php/*
					if (isset($row->user_id_name)){
						echo $row->user_id_name ;
					}else{
						echo '-';
					}*/
					?>
				</td>-->
			</tr>
			<?php
				$k = 1 - $k;
				}
			}// end of if (!empty($this->coupons))  // if coupons are present
			else
			{
			?>
				<td colspan="13">
					<div class="well" >
						<div class="alert alert-error">
							<span ><?php echo JText::_('QTC_NO_COUPONS'); ?> </span>
						</div>
					</div>
				</td>
			<?php
		//	print"	empty Massage";
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="13">
					<div class="pager"><?php echo $this->pagination->getListFooter(); ?></div>
				</td>
			</tr>
				</tfoot>
	</table>

	<input type="hidden" name="option" value="com_quick2cart" />
	<input type="hidden" name="copId" value="" />
	<input type="hidden" name="view" value="managecoupon" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
