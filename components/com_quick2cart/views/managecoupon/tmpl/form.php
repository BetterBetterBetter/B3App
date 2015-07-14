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

jimport( 'joomla.form.formvalidator' );
jimport('joomla.html.pane');
JHtml::_('behavior.formvalidation');

JHtmlBehavior::framework();
jimport( 'joomla.html.parameter' );

$entered_numerics= "'".JText::_('QTC_ENTER_NUMERICS')."'";

?>
<!-- geo target start here -->
    <script src="<?php echo JUri::root().'administrator/components/com_quick2cart/assets/js/geo/jquery-1.7.2.js'?>"></script>
	<script src="<?php echo JUri::root().'administrator/components/com_quick2cart/assets/js/geo/jquery.ui.core.js'?>"></script>
	<script src="<?php echo JUri::root().'administrator/components/com_quick2cart/assets/js/geo/jquery.ui.widget.js'?>"></script>
	<script src="<?php echo JUri::root().'administrator/components/com_quick2cart/assets/js/geo/jquery.ui.position.js'?>"></script>
	<script src="<?php echo JUri::root().'administrator/components/com_quick2cart/assets/js/geo/jquery.ui.autocomplete.js'?>"></script>
	<script src="<?php echo JUri::root().'components/com_quick2cart/assets/js/auto.js'?>"></script>
	<link rel="stylesheet" href="<?php echo JUri::root().'administrator/components/com_quick2cart/assets/css/geo/geo.css' ?>">
<!-- geo target end here -->
<?php

$comquick2cartHelper=new comquick2cartHelper;
$allowed_role=$comquick2cartHelper->store_authorize("managecoupon_form");

if (empty($allowed_role))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
	<div class="well" >
		<div class="alert alert-error">
			<span ><?php echo JText::_('QTC_VIOLATING_UR_ROLE'); ?> </span>
		</div>
	</div>
	</div>
<?php
	return false;
}

// check authorization to create coupon $this->authorized_store_id= storeid of user
if (empty($this->authorized_store_id)){
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
<div class="well" >
	<div class="alert alert-error">
		<span ><?php echo JText::_('QTC_NOT_AUTHORITY_TO_CREATE_COUPON'); ?> </span>
	</div>
</div>
</div>
<?php
	return false;
}


	$document = JFactory::getDocument();

//	$document->addScript(JUri::base().'components/com_quick2cart/assets/js/adminsocialads.js');
	//$document->addStyleSheet(JUri::base().'components/com_quick2cart/assets/css/quick2cart.css');//aniket


	$jinput=JFactory::getApplication()->input;
	//change by Aniket for task #24718
	$cid	= $jinput->get( 'cid',array(),'ARRAY' );
	if (!empty($cid))
		$jinput->set('cid', $cid[0]);
	if (!$cid)
	{
		$this->coupons=array();
	}

	if ($this->coupons)
		$published 	= $this->coupons[0]->published;
	else
		$published 	= 0;

	$this->lists['published'] = JHtml::_('select.booleanlist',  'published', 'class="inputbox"', $published );

$js_key="
	/*
	function checkforalpha(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++){
			if ((el.value.charCodeAt(i) > 64 && el.value.charCodeAt(i) < 92) || (el.value.charCodeAt(i) > 96 && el.value.charCodeAt(i) < 123))
			{
				alert('Please Enter Numerics');
				el.value = el.value.substring(0,i); break;
				}
			}
	}
	*/
	function checkfornum(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++){
			if (el.value.charCodeAt(i) > 47 && el.value.charCodeAt(i) < 58) {
				alert('Numerics Not Allowed');
				el.value = el.value.substring(0,i); break;
			}
		}
	}
	";
	$document->addScriptDeclaration($js_key);


?>


<script type="text/javascript">


window.addEvent("domready", function(){
    document.formvalidator.setHandler('name', function (value) {
		if (value<=0){
			alert( "<?php echo JText::_('COM_QUICK2CART_COUPON_GRT')?>" );
			return false;
		}
		elseif (value == ' '){
			alert(" <?php echo JText::_('COM_QUICK2CART_COUPON_BLANK')?>" );
			return false;
		}
		else{
			return true;
		}
	});
});

window.addEvent("domready", function(){
   document.formvalidator.setHandler('verifydate', function(value) {
      regex=/^\d{4}(-\d{2}){2}$/;
      return regex.test(value);
   })

})

var validcode1=0;
function myValidate(f)
{
	var msg = "<?php echo JText::_("COP_NOT_ACCEPTABLE_ENTERY")?>";
	//vm PRODUCT LAEVAL COP COMPULSORY ::coupon exist then only allow to save & check for other validation

	if (document.formvalidator.isValid(f)) {
		f.check.value='<?php echo JSession::getFormToken(); ?>';

		return true;
	}
	else {

		alert(msg);
	}

	return false;
}
function checkcode()
	{

		var selectedcode=document.getElementById('code').value;
		var cid=<?php if (!empty($cid[0])) echo $cid[0];else echo "0"; ?>;

		if (parseInt(cid)==0)
			var url = "index.php?option=com_quick2cart&task=getcode&controller=managecoupon&selectedcode="+selectedcode;
		else
			var url = "index.php?option=com_quick2cart&task=getselectcode&controller=managecoupon&couponid="+cid+"&selectedcode="+selectedcode;

		techjoomla.jQuery.ajax({
		url:url,
		type: 'GET',
		success: function(response) {
				cid=<?php if (!empty($cid[0])) echo $cid[0];else echo "0"; ?>;

				if (parseInt(cid)==0)
				{
					if (parseInt(response)!=0)
					{
						alert("<?php echo JText::_('COM_QUICK2CART_COUPON_EXIST')?>");
						document.adminForm.code.value="";
						validcode1=0;
						return 0;
					}
					else
					{
						validcode1=1;
						return 1;
					}
				}
				else
				{
					if (parseInt(response)!=0)
					{
						alert("<?php echo JText::_('COM_QUICK2CART_COUPON_EXIST')?>");
						document.adminForm.code.value="";
						validcode1=0;
						return 0;
					}
					else
					{
						validcode1=1;
						return 1;
					}
				}
			}
		});

	}	//end function check code

	function submitTask(action)
	{
		if (action=='save'  )
		{		// new coupon and not edit task::
			var submit_status=myValidate(document.adminForm);
			if (!submit_status)
				return false;
			var newcoupon="<?php echo (!isset($cid))?0:1;?>";
			if (validcode1==0 && newcoupon==0)
			{
				alert("<?php echo JText::_('COM_QUICK2CART_ENTER_VALID_COUPON_CODE');?>");
				return false;
			}
			 document.adminForm.task.value='save';
		}
		elseif (action="cancel")
			document.adminForm.task.value='cancel';
		document.adminForm.submit();
	}
	function saveCoupan()
	{
	var form = document.adminForm;
		var validateflag = document.formvalidator.isValid(document.adminForm);
		if (validateflag)
		{
				techjoomla.jQuery(document).ready(function() {
				//alert(selectedcode);
				var cid=<?php if (!empty($cid[0])) echo $cid[0];else echo "0"; ?>;
				if (parseInt(cid)==0)
				{
					//var selectedcode=document.getElementById('code').value;
					var selectedcode=techjoomla.jQuery('#code').val();
					var url = "index.php?option=com_quick2cart&task=getcode&controller=managecoupon&selectedcode="+selectedcode;
				}
				else
				{
					//var selectedcode=document.getElementById('code').value;
					var selectedcode=techjoomla.jQuery('#code').val();
					var url = "index.php?option=com_quick2cart&task=getselectcode&controller=managecoupon&couponid="+cid+"&selectedcode="+selectedcode;
				}
				<?php if (version_compare(JVERSION, '1.6.0', 'ge')) { ?>
						var a = new Request({url:url,
				<?php } else {?>
						new Ajax(url, {
				<?php } ?>
					method: 'get',
					onComplete: function(response) {
						var cid=<?php if (!empty($cid[0])) echo $cid[0];else echo "0"; ?>;
						if (parseInt(cid)==0)
						{
							if (parseInt(response)!=0)
							{
								alert("<?php echo JText::_('COP_EXIST')?>");
								validcode1=0;
								return false;
							}
							else
							{
								submitform( action );
								return true;
							}
						}
						else
						{
							if (parseInt(response)!=0)
							{
								alert("<?php echo JText::_('COP_EXIST')?>");
								validcode1=0;
								return false;
							}
							else
							{
								submitform( action );
								return true;
							}
						}
					}
			<?php if (version_compare(JVERSION, '1.6.0', 'ge'))  { ?>
				}).send();
			<?php } else {?>
				}).request();
			<?php } ?>
			});

		}//if validate flag
		else
		return false;

}


	/* this function allow only numberic and specified char (at 0th position)
	// ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
		(code 46 for .)
		@param el :: html element
		@param allowed_ascii::ascii code that shold allow

	*/
function checkforalpha(el, allowed_ascii,enter_numerics )
	{
		// by defau
		allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
		var i =0 ;
		for(i=0;i<el.value.length;i++){
		  if ((el.value.charCodeAt(i) < 48 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 ))
		  {
			if (allowed_ascii ==el.value.charCodeAt(i))   // && i==0)  // + allowing for phone no at first char
			{
				var temp=1;
			}
			else
			{
					alert(enter_numerics);
					el.value = el.value.substring(0,i);
					return false;
			}


		  }
		}
		return true;
	}
</script>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
<form name="adminForm" id="adminForm" class="form-validate form-horizontal" method="post" onSubmit="return myValidate(this);" >
	<input type="hidden" name="check" value="post"/>
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
	<legend><?php echo JText::_('COM_QUICK2CART_COUPON_INFO')?></legend>

	<div>
		<div class="control-group">
			<label for="coupon_name" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_NAME_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_NAME'), '', "* ".JText::_('COM_QUICK2CART_COUPON_NAME'));?></label>
			<div class="controls">
				<input type="text" name="coupon_name" id="coupon_name" class="inputbox required validate-name"   size="20" value="<?php if ($this->coupons){  echo stripslashes($this->coupons[0]->name); } ?>" autocomplete="off" />
			</div>
		</div>
		<div class="control-group">
			<label for="code" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPAN_CODE_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_CODE'), '',"* ". JText::_('COM_QUICK2CART_COUPON_CODE'));?></label>
			<div class="controls">
				<input type="text" name="code" id="code" class="inputbox required validate-name"  onblur="checkcode();"  size="20" value="<?php if ($this->coupons){ echo $this->escape( stripslashes( $this->coupons[0]->code ) ); } ?>" 	 autocomplete="off" />
			</div>
		</div>
		<div class="control-group">
			<label for="" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_ENABLED_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_ENABLED'), '', JText::_('COM_QUICK2CART_COUPON_ENABLED'));?></label>

		<?php if (version_compare(JVERSION, '3.0.0', 'lt')){	?>
		<div class="controls">
			<?php echo $this->lists['published']; ?>
		</div>
		<?php }else{
			echo $this->lists['published'];
		 }
		 ?>

		</div>
		<!-- SELECT STORE -->
		<?php
		//				$options[] = JHtml::_('select.option', "", "Select Country");
		if (count($this->store_role_list))
		{
			?>
			<div class="control-group">
				<label for="qtc_store" class="control-label"><?php echo JHtml::tooltip(JText::_('QTC_PROD_SELECT_STORE_DES'), JText::_('QTC_PROD_SELECT_STORE'), '', JText::_('QTC_PROD_SELECT_STORE'));?></label>
				<div class="controls">
					<?php
					 $default=!empty($this->selected_store)?$this->selected_store:(!empty($store_role_list[0]['store_id'])?$store_role_list[0]['store_id']:'');
						$options = array();
						foreach($this->store_role_list as $key=>$value)
						{
							$options[] = JHtml::_('select.option', $value["store_id"],$value['title']);//submitAction('deletecoupon');
						}
						echo $this->dropdown = JHtml::_('select.genericlist',$options,'current_store','class=" qtc_putmargintop10px required" size="1"   ','value','text',$default,'current_store_id');
					?>
				</div>
			</div>
		<?php
		} ?>
		<!-- SELECT STORE END -->
		<div class="control-group">
			<label for="value" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPAN_VALUE_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_VALUE'), '', "* ".JText::_('COM_QUICK2CART_COUPON_VALUE'));?></label>
			<div class="controls">
				<input  class="inputbox required validate-name" type="text" name="value" id="value" Onkeyup= "checkforalpha(this,46,<?php echo $entered_numerics; ?>);" size="20" value="<?php if ($this->coupons){ echo $this->coupons[0]->value; } ?>" autocomplete="off" />
			</div>
		</div>
		<div class="control-group">
			<label for="val_type" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_VALUE_TYPE_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_VALUE_TYPE'), '', JText::_('COM_QUICK2CART_COUPON_VALUE_TYPE'));?></label>

							<?php
							if ($this->coupons)
							$val_type 	= $this->coupons[0]->val_type;
							else
							$val_type 	= 0;
							$val_type1[] = JHtml::_('select.option', '0', JText::_("COM_QUICK2CART_COUPON_FLAT"));
							$val_type1[] = JHtml::_('select.option', '1', JText::_("COM_QUICK2CART_COUPON_PER")); // first parameter is value, second is text
							$lists['val_type'] = JHtml::_('select.radiolist', $val_type1, 'val_type', 'class="inputbox" ', 'value', 'text', $val_type, 'val_type' );

							 ?>
			<?php if (version_compare(JVERSION, '3.0.0', 'le')){	?>
		<div class="controls">
					<?php echo $lists['val_type'];  ?>
		</div>
		<?php }else{
			echo $lists['val_type'];
		 }
		 ?>
		</div>
		<div class="control-group">
			<label for="selections.item_id" class="control-label qtc_product_cop_txtbox_lable"><?php echo JHtml::tooltip(JText::_('COUPAN_ITEMID_TOOLTIP'), JText::_('COUPAN_ITEMID'), '', "* ".JText::_('COUPAN_ITEMID'));?></label>
			<div class="controls">
				<ul class='selections' id='selections.item_id'>
					<input type="text" id="item_id" class="auto_fields inputbox qtc_product_cop_txtbox" size="20" value="<?php echo ($this->coupons) ? $this->coupons[0]->item_id : JText::_('ITEMID_START_TYP_MSG'); ?>" autocomplete="off" />
					<input type="hidden" class="auto_fields_hidden" name="item_id" id="item_id_hidden" value="" autocomplete='off' />
				</ul>
				<input type="hidden" class="" id="item_id_hiddenname" value="<?php echo ($this->coupons) ? $this->coupons[0]->item_id_name:'' ;?>" autocomplete='off' />
			<input type="hidden" name="store_ID" id="store_ID" value="<?php if (isset($this->store_id)) echo $this->store_id; else{ $jinput=JFactory::getApplication()->input; echo $jinput->get('store_id'); } ?>" />
			</div>
		</div>
		<!-- user lavel coupon-->
		<!--
		<div class="control-group">
			<label for="selections.id" class="control-label"><?php // echo JHtml::tooltip(JText::_('COUPAN_USER_TOOLTIP'), JText::_('COUPAN_USER'), '', JText::_('COUPAN_USER'));?></label>
			<div class="controls">
				<ul class='selections' id='selections.id'>
					<input type="text" id="id" class="auto_fields inputbox" size="20" value="<?php //echo ($this->coupons) ? $this->coupons[0]->user_id : JText::_('USER_START_TYP_MSG'); ?>" autocomplete="off" />
					<input type="hidden" class="auto_fields_hidden" name="id" id="id_hidden" value="" autocomplete='off' />
				</ul>
				<input type="hidden" class="" id="id_hiddenname" value="<?php //echo ($this->coupons) ? $this->coupons[0]->user_id_name:'' ;?>" autocomplete='off' />
			</div>
		</div>
		-->
		<div class="control-group">
			<label for="max_use" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_MAXUSES_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_MAXUSES'), '', JText::_('COM_QUICK2CART_COUPON_MAXUSES'));?></label>
			<div class="controls">
				<input type="text" name="max_use" id="max_use" class="inputbox" Onkeyup= "checkforalpha(this,'',<?php echo $entered_numerics; ?>);" size="20" value="<?php if ($this->coupons){ echo $this->coupons[0]->max_use; } ?>" autocomplete="off" />
			</div>
		</div>
		<div class="control-group">
			<label for="max_per_user" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_MAXUSES_PERUSER_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_MAXUSES_PERUSER'), '', JText::_('COM_QUICK2CART_COUPON_MAXUSES_PERUSER'));?></label>
			<div class="controls">
				<input type="text" name="max_per_user" id="max_per_user" class="inputbox" Onkeyup= "checkforalpha(this,'',<?php echo $entered_numerics; ?>);" size="20" value="<?php if ($this->coupons){  echo $this->coupons[0]->max_per_user; } ?>" autocomplete="off" />
			</div>
		</div>
		<div class="control-group">
			<label for="from_date" class="control-label"><?php echo JHtml::tooltip(JText::_('VALID_FROM_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_VALID_FROM'), '', JText::_('COM_QUICK2CART_COUPON_VALID_FROM'));?></label>
			<div class="controls">
						<?php
						if ($this->coupons)
						{
							if ($this->coupons[0]->from_date != '0000-00-00 00:00:00')
								$date_from=date("Y-m-d",strtotime($this->coupons[0]->from_date));
							else
								$date_from='';
						}
						else
							$date_from='';

						 echo JHtml::_("calendar", "$date_from", "from_date", "from_date", "%Y-%m-%d"); ?>
				</div>
		</div>
		<div class="control-group">
			<label for="exp_date" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_EXPIRES_ON_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_EXPIRES_ON'), '', JText::_('COM_QUICK2CART_COUPON_EXPIRES_ON'));?></label>
			<div class="controls">
						<?php
						if ($this->coupons)
						{
							if ($this->coupons[0]->exp_date != '0000-00-00 00:00:00')
								$date_exp=trim(date("Y-m-d",strtotime($this->coupons[0]->exp_date)));
							else
								$date_exp='';
						}
						else
							$date_exp='';
						  echo JHtml::_("calendar",  "$date_exp", "exp_date", "exp_date", "%Y-%m-%d");

						?>
				</div>
		</div>
		<div class="control-group">
			<label for="description" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_DESCRIPTION_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_DESCRIPTION'), '', JText::_('COM_QUICK2CART_COUPON_DESCRIPTION'));?></label>
			<div class="controls">
				<textarea   size="28" rows="3" name="description" id="description" class="inputbox" ><?php if ($this->coupons){  echo trim($this->coupons[0]->description); } ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label for="params" class="control-label"><?php echo JHtml::tooltip(JText::_('COM_QUICK2CART_COUPON_PARAMETERS_TOOLTIP'), JText::_('COM_QUICK2CART_COUPON_PARAMETERS'), '', JText::_('COM_QUICK2CART_COUPON_PARAMETERS'));?></label>
			<div class="controls">
				<textarea  size="28" rows="3" name="params" id="params" class="inputbox" ><?php if ($this->coupons){  echo trim($this->coupons[0]->params); } ?></textarea>
			</div>
		</div>

	</div>

	<div class="form-actions">
	<!--		<button onclick="this.adminForm.submit('save');" class="btn btn-success"><?php echo JText::_("SAVE")?></button> -->
		<input type="button" class="btn btn-success" value="<?php echo JText::_('QTC_COUPON_SAVE')?>" onclick="submitTask('save')"/>
		<input type="button" class="btn btn-inverse" value="<?php echo JText::_('QTC_COUPON_CANCEL')?>" onclick="submitTask('cancel')"/>

	</div>
	<input type="hidden" name="coupon_id" id="coupon_id" value="<?php if ($this->coupons){ echo $this->coupons[0]->id; } ?>" />
	<label for="coupon_id" ></label>
	<input type="hidden" name="option" value="com_quick2cart" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="view" value="managecoupon" />
	<input type="hidden" name="controller" value="managecoupon" />
	<!--<input type="hidden" name="store_id" value="<?php // echo $this->store_id;?>" /> -->
		<?php echo JHtml::_('form.token' ); ?>

</form>
</div>
