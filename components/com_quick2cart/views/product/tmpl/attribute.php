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

$params = JComponentHelper::getParams('com_quick2cart');
$currencies=$params->get('addcurrency');
$curr=explode(',',$currencies);
$count = !empty($this->allAttribues)?count($this->allAttribues):0;
?>
<script type="text/javascript">
	// globle params
	var attribute_current_id = <?php echo $count; ?>;
	/** This function is used to add attribute(eg. color)
	*/
	/*add clone script*/
	function addClone(rId,rClass)
	{
		var curr="<?php echo $currencies ?>";
		var temp = new Array();
		var temp= curr.split(',');


		// CURRENT ATRIBURE ID -- global declaration
		attribute_current_id++;
		var num=attribute_current_id;//techjoomla.jQuery('.'+rClass).length;

		// CREATE REMOVE BUTTON
		var removeButton="<div class='com_qtc_remove_button pull-left span1'>";
		removeButton+="<button class='btn btn-mini' type='button' id='remove"+num+"'";
		removeButton+="onclick=\"removeClone('"+rId+num+"','"+rClass+"');\" title=\"<?php echo JText::_('COM_QUICK2CRT_REMOVE_TOOLTIP');?>\" >";
		removeButton+="<i class=\"<?php echo QTC_ICON_MINUS;?> \"></i></button>";
		removeButton+="</div>";

		var oldnum=num -1;
		var newElem=techjoomla.jQuery('#'+rId+'0').clone().attr('id',rId+num);
		newElem.removeClass('qtc_att_hide');
		techjoomla.jQuery('.'+rClass+':last').after(newElem);
		//techjoomla.jQuery('div.'+rClass +' :last').append(removeButton);
		techjoomla.jQuery('#'+rId+num).children().last().replaceWith(removeButton);

		var newelementid=	rId+num;
		var option=0;

		/*1. CHANGE ATTUBURE NAME */
		//techjoomla.jQuery(newElem).find('.com_qtc_repeating_block').children('.control-group').children('.controls').children('.inputbox').each(function()
		techjoomla.jQuery(newElem).find('.com_qtc_repeating_block').find('.qtc_attrib').each(function()
		{
			var kid = techjoomla.jQuery(this);
			var newname='att_detail['+num+'][attri_name]';
			kid.attr('name',newname);
			kid.attr('id','atrri_name_id'+num);
			//kid.attr(' value','');
			techjoomla.jQuery(this).attr({'value':''});

		});
		/*2. CHANGE CHECKBOX NAME */
		var newname='att_detail['+num+'][iscompulsary_attr]';
		var ck=newElem.find('.checkboxdiv').attr({'name': newname,'id':newname});

		// chagne select's onchange function name
		var newFnName = "qtc_fieldTypeChange(this,'"+rId+num+"')";'fun()' //rId+num
		var ck=newElem.find('.qtcfieldType').attr({'onchange': newFnName});

		//2A: change field type name
		var newname='att_detail['+num+'][fieldType]';
		var ck=newElem.find('.qtcfieldType').attr({'name': newname,'id':newname});

		//3.CHANGING the attribute OPTION NAME


		//techjoomla.jQuery('#'+newelementid).find('tr').each(function()
		techjoomla.jQuery('#'+newelementid+ ' table tr').each(function()
		{
			var selectname="att_detail[0][attri_opt]["+option+"]";
			var newselectname="att_detail["+num+"][attri_opt]["+option+"]";
			if (this .id)
			{ // avoid first tr (which is used for giving heading to column)
				if (option>0)
				{
						techjoomla.jQuery(this).remove();
				}
				techjoomla.jQuery("#"+ newelementid+" td input[name='"+ selectname+"[id]']").attr({'name': newselectname+'[id]','value':''});
				techjoomla.jQuery("#"+ newelementid +" td input[name='"+ selectname+"[name]']").attr({'name': newselectname+'[name]','value':''});
				techjoomla.jQuery("#"+newelementid+" td select[name='"+ selectname+"[prefix]']").attr({'name': newselectname+'[prefix]','value':'+'});
				techjoomla.jQuery("#"+newelementid+" td input[name='"+ selectname+"[order]']").attr({'name': newselectname+'[order]'});

				var index=0;
				techjoomla.jQuery(this).find('.qtc_currencey_textbox').find(':input').each(function()
				{
						var currname=newselectname + '[currency]['+temp[index]+']';
						techjoomla.jQuery(this).attr('name',currname);
						techjoomla.jQuery(this).val('');
						index++;
				});
				var iconplus="<?php echo QTC_ICON_PLUS;?>";
				var addOptionBtn=" <button type='button' id='"+newelementid+"' class='btnAdd btn btn-mini btn-primary'  onclick='addopt(this.id);'><i class='" + iconplus +" icon-white '></i></button> ";
				techjoomla.jQuery(this).find('button 	').replaceWith(addOptionBtn);

				option++;
			}
		});
	}

	function removeClone(rId,rClass){
		techjoomla.jQuery('#'+rId).remove();
	}


	/** This function is used to add attribute(eg. color) OPTIONS (eg red,blue)
	*/
	function addopt(attribute_container_id){
			// getting all currencies
		var curr="<?php echo $currencies ?>";
		var temp = new Array();
		var temp= curr.split(',');

		//getting attrubute index from attribute_id (if id=qtc_container8 then index should be 8)
		var base_attr_id_len = 'qtc_container'.length;
		var curr_attr_ld_len = attribute_container_id.length;
		var att_index=attribute_container_id.substring(base_attr_id_len,curr_attr_ld_len);
		var current_attribute = attribute_container_id;
		var num     = techjoomla.jQuery('#'+current_attribute).find('.clonedInput').length; // how many 'duplicatable' input fields we currently have
		num = num - 1;
		var newNum  = new Number(num + 1);      // the numeric ID of the new input field being added
		// create the new element via clone(), and manipulate it's ID using newNum value

		var newElem = techjoomla.jQuery('#'+current_attribute+' #attri_opts' + num).clone().attr('id', 'attri_opts' + newNum);
		// manipulate the name/id values of the input inside the new element
		newElem.find('td input[name=\"att_detail['+att_index+'][attri_opt][' + num + '][name]\"]').attr({'name': 'att_detail['+att_index+'][attri_opt][' + newNum + '][name]','value':''});

		newElem.find('select[name=\"att_detail['+att_index+'][attri_opt][' + num + '][prefix]\"]').attr({'name': 'att_detail['+att_index+'][attri_opt][' + newNum + '][prefix]','value':'' });
		// for hidded id
		//newElem.find('select[name=\"att_detail['+att_index+'][attri_opt][' + num + '][id]\"]').attr({'name': 'att_detail['+att_index+'][attri_opt][' + newNum + '][id]','value':'' });
		var currdiv=newElem.find('.qtc_currencey_textbox');
		var index=0;
		currdiv.find(':input')
		.each(function()
			{
				var newname='att_detail['+att_index+'][attri_opt]['+newNum+'][currency]['+temp[index]+']';
				techjoomla.jQuery(this).attr('name',newname);
				techjoomla.jQuery(this).val('');
				index++;
			});
		var ordernum=newElem.find('input[name=\"att_detail['+att_index+'][attri_opt][' + num + '][order]\"]').val();
		ordernum=Number(ordernum);
		var newordernum=new Number(ordernum + 1);
		 // change order name
		newElem.find('input[name=\"att_detail['+att_index+'][attri_opt][' + num + '][order]\"]').attr({'name': 'att_detail['+att_index+'][attri_opt][' + newNum + '][order]','value':newordernum });

		techjoomla.jQuery('#'+current_attribute+' #attri_opts' + num ).children().last().replaceWith('<button type=\"button\" class=\"btn btn-mini btn-danger\" id=\"btnRemove'+num+'\" title=\"<?php echo JText::_('QTC_REMOVE_MORE_OPTION_TITLE');?>\" onclick=\"techjoomla.jQuery(this).parent().remove();\" ><i class=\"icon-trash icon-white\"></i></button> ');

		// insert the new element after the last 'duplicatable' input field
		techjoomla.jQuery('#'+current_attribute+' #attri_opts' + num).after(newElem);
		techjoomla.jQuery('#attri_opts' + num ).focus();
}
</script>
	<!-- for attribute option ************************************** -->
	<div class="row-fluid">
		<div class="span12">

			<div class="row-fluid">
				<!-- Help msg -->
				<div class="alert alert-info">
					<?php echo JText::_('COM_QUICK2CART_ATTRIBUTES_MSG_HELP'); ?>
				</div>
			</div>

			<!--This is a repating block of html-->
			<?php
			$i=0;
				$count = !empty($this->allAttribues)?count($this->allAttribues):0;
				// Take backup
				$mediaDetailBackUp = !empty($this->allAttribues)?$this->allAttribues:'';

				// Make empty  As EDIT DONT FILL IN 0'TH INDEX
				$this->allAttribues = array();

				$attribute_container_id = "qtc_container0";
				 ?>
				<div id=<?php echo $attribute_container_id ?> class="qtc_container qtc_att_hide" >
					<div class="com_qtc_repeating_block well well-small span10">

						<?php
						// CHECK for view override
							$comquick2cartHelper = new comquick2cartHelper;
							$att_list_path=$comquick2cartHelper->getViewpath('product','attribute2',"SITE","SITE");
							ob_start();
								include($att_list_path);
								$html_attri = ob_get_contents();
							ob_end_clean();

						echo $html_attri;
						?>

					</div>
					<!-- required empty div-->
					<div></div>
				</div>
					<?php
				// Restore backup
				$this->allAttribues = $mediaDetailBackUp;
				if (!empty($this->allAttribues ))
				{
					//$this->allAttribues[] = $this->allAttribues[0];
					array_unshift($this->allAttribues, $this->allAttribues[0]);

				}

				for($i=1;$i<=$count;$i++) // for each attribute
				{
					$attribute_container_id = "qtc_container".$i;
				 ?>
				<div id=<?php echo $attribute_container_id ?> class="qtc_container">
					<div class="com_qtc_repeating_block well well-small span10">

						<?php
						// CHECK for view override
							$comquick2cartHelper = new comquick2cartHelper;
							$att_list_path=$comquick2cartHelper->getViewpath('product','attribute2',"SITE","SITE");
							ob_start();
								include($att_list_path);
								$html_attri = ob_get_contents();
							ob_end_clean();

						echo $html_attri;
						?>

					</div>
					<?php //if ($i != 0)
					{?>
					<div class='com_qtc_remove_button pull-left span1'>
						<button class='btn btn-mini' type='button' id='remove<?php echo $i;?>'
							onclick="removeClone('qtc_container<?php echo $i;?>','qtc_container');" title="<?php echo JText::_('COM_QUICK2CRT_REMOVE_TOOLTIP');?>" >
							<i class="<?php echo QTC_ICON_MINUS;?> "></i>
						</button>
					</div>
					<?php
					} ?>
				<!-- required empty div-->
					<div></div>
				</div>
				<?php
				} // end of attribute for loop

				 ?>

			<div class="com_qtc_add_button pull-left  span1">
				<button class="btn btn-mini " type="button" id='add'
				onclick="addClone('qtc_container','qtc_container');"
				title='<?php echo JText::_('COM_QUICK2CRT_ADDMORE_TOOLTIP');?>'>
					<i class="<?php echo QTC_ICON_PLUS; ?> "></i>
				</button>
			</div>

		</div><!--span12-->
	</div><!--row-fluid-->
