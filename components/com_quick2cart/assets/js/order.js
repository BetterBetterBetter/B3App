
/*alert(Joomla.JText._('COM_QUICK2CART_COULD_NOT_CHANGE_CART_DETAIL_NOW'));*/
function qtc_addtocart(pid)
{
	var addcart_str = qtc_itemdataformat(pid,1);
	qtc_commonCall(addcart_str,pid)
}

function qtc_itemdataformat(pid,formattype)
{
	/*pid like = com_content-6*/
	var count = techjoomla.jQuery("#"+pid+"_itemcount").val();
	var options ='';

	if(techjoomla.jQuery("."+pid+"_options"))
	{
		techjoomla.jQuery.each(techjoomla.jQuery("."+pid+"_options"),function()
		{
			options =  techjoomla.jQuery(this).val()+","+options;
		});
		options = options.slice(0, -1);
	}

	/** user fields*/
	var userFields = {};
	var index=0;
	var userData = '';
	if(techjoomla.jQuery("."+pid+"_UserField"))
	{
		techjoomla.jQuery.each(techjoomla.jQuery("."+pid+"_UserField"),function()
		{
			/* <- initialize an object, not an array*/
			var texboxFields ={};
			userFieldsValue =  techjoomla.jQuery(this).val();
			if(userFieldsValue)
			{
				attrFields =  techjoomla.jQuery(this).attr('name');
				attrFields = attrFields.split("_");
				texboxFields['itemattributeoption_id'] = attrFields[1];
				if(options)
				{
					options =  attrFields[1] + ","+options;
				}
				else
				{
					options =  attrFields[1];
				}
				texboxFields['type'] = "Textbox";
				texboxFields['value'] = userFieldsValue;
				/* use option id  as index BZ used next*/
				index = texboxFields['itemattributeoption_id']
				userFields[index] = texboxFields;
				/*userFields.concat(texboxFields);*/
				/*	index++;*/
			}
		});
		userData = JSON.stringify(userFields);
		/*userFields = userFields.slice(0, -1);*/
	}

	if(formattype ==1)
	{
		var addcart_str = "&id="+pid+"&count="+count;
		if(options != '')
		{
			addcart_str = addcart_str+"&options="+options;
		}

		/*addcart_str = addcart_str+"&"+qtc_token+"=1";*/

		var retDataObj = {};
		retDataObj['urlParamStr'] =addcart_str;
		retDataObj['userData'] =userData;

		/*//if(userData)
		{
			addcart_str = addcart_str+"&"+'userData='+userData;
		}*/
		return retDataObj;
	}
	else
	{
		var addcart_obj = {};
		addcart_obj['id'] =pid;
		addcart_obj['count'] =count;
		if(options != '')
		{
			addcart_obj['options'] =options;
		}
		/*addcart_obj['qtc_token'] =qtc_token;*/
		addcart_obj['userData'] =userFields;
		return addcart_obj;
	}
}

function qtcproduct_addtoCart(item_id)
{
	var count = techjoomla.jQuery("#"+item_id+"_qtcitemcount").val();

	/*GETTING SELECTED OPTION IDS*/
	var options ='';
	if(techjoomla.jQuery("."+item_id+"_qtcoptions"))
	{
		techjoomla.jQuery.each(techjoomla.jQuery("."+item_id+"_qtcoptions"),function()
		{
			options =  techjoomla.jQuery(this).val()+","+options;
		});
		options = options.slice(0, -1)
	}

	var addcart_str="&options="+options+"&item_id="+item_id +"&count="+ count;
	var retDataObj = {};
	retDataObj['urlParamStr'] =retDataObj;
	retDataObj['userData'] ='';
	qtc_commonCall(retDataObj,item_id);
	/*console.log(" options = "+options);*/
}

/**
 * @PARAM $options STRING ::string of comma seperated attribure_option_ids (OPTIONS IDS)
 * @PARAM $count-INTEGER :: Product count to buy
  * @PARAM $item_id -INTEGER :: ID of kart_items table
 * */
function qtc_mod_addtocart(options,count,item_id)
{
	var addcart_str="&options="+options+"&item_id="+item_id +"&count="+ count;
	var retDataObj = {};
	retDataObj['urlParamStr'] =addcart_str;
	retDataObj['userData'] ='';
	qtc_commonCall(retDataObj,item_id);
}

/** This function takes URL PARMAS STRING
 * @PARAM $options STRING ::PARMAS object
 * */
function qtc_commonCall(paramsObj,id)
{
	var urlParam = paramsObj['urlParamStr'];
	var postParam = {};
	postParam['userData'] = paramsObj['userData'];

	techjoomla.jQuery.ajax({
			url: qtc_base_url+"index.php?option=com_quick2cart&task=addcart&tmpl=component&format=raw"+urlParam,
			type: 'POST',
			data:postParam,
			cache: false,
			/*crossDomain: true,*/
			dataType: 'json',
			/*beforeSend: setHeader,*/

			success: function(msg)
			{
				if(msg['successCode'] == 1)
				{
					// Update module content.
					update_mod();

					if(techjoomla.jQuery('#'+id+'_popup').length > 0 )
					{
						techjoomla.jQuery('#'+id+'_popup .message').html('<div class="success">' + msg['message'] + '</div>');
						techjoomla.jQuery('#'+id+'_popup').fadeIn('slow');

						/*techjoomla.jQuery('.qtc_buyBtn_style').popover({
						content:'<div class="alert"><button type="button" class="close" onclick="techjoomla.jQuery(\'.qtc_buyBtn_style\').popover(\'hide\');">&times;</button><strong>Warning!</strong> Best check yo self, you are not looking too good.</div>'
						});
						techjoomla.jQuery('.qtc_buyBtn_style').popover('show');*/
					}
					else
					{
						SqueezeBox.open(qtc_base_url+"index.php?option=com_quick2cart&view=cart&tmpl=component",{handler: 'iframe', size: {x:650, y:470}});
					}
				}
				else if(msg['successCode'] == 2)
				{
					/* Single store checkout */
					alert(msg['message']);
					window.location = "index.php?option=com_quick2cart&view=cartcheckout";
				}
				else if(!msg['success'])
				{
					alert(msg['message']);
				}
			}
		});
}

function update_mod()
{
	techjoomla.jQuery.ajax({
		url: qtc_base_url+"index.php?option=com_quick2cart&task=cart.update_mod&tmpl=component",
		type: "GET",
		cache: false,
		success: function(data)
		{
			techjoomla.jQuery(".qtc_modulebody").html(data);
		}
	});
}

/**
 * this function allow only numberic and specified char (at 0th position)
 * ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
 * (code 46 for dot/full stop .)
 * @param el :: html element
 * @param allowed_ascii::ascii code that shold allow
 **/
function checkforalpha(el, allowed_ascii,enter_numerics )
{
/* amol change*/
	allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
	var i =0 ;
	for(i=0;i<el.value.length;i++)
	{
		if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 ))
		{
			/*+ allowing for phone no at first char*/
			if(allowed_ascii ==el.value.charCodeAt(i)) /*&& i==0)*/
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

/*Function addded by Sneha*/
/**
 * this function allow only numberic and specified char (at 0th position) and does not allow value 0
 * ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
 * (code 46 for dot/full stop .)
 * @param el :: html element
 * @param allowed_ascii::ascii code that shold allow
 **/
function checkforpricevalue(el, allowed_ascii )
{
	allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
	var i =0 ;
	for(i=0;i<el.value.length;i++)
	{
		if(el.value <= 0)
		{
			alert('Please Enter Price greater than one');
			el.value = el.value.substring(0,i);
			return false;
		}

		if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 ))
		{
			/* + allowing for phone no at first char*/
			if(allowed_ascii ==el.value.charCodeAt(i) )  /*&& i==0)*/
			{
				var temp=1;
			}
			else
			{
				alert('Please Enter Numerics');
				el.value = el.value.substring(0,i);
				return false;
			}
		}
	}
	return true;
}

/**
 * This function used to get Quantity limit
 * @param limit string eg. min/max
 **/
function selectstatusorder(appid,ele)
{
	var ele = document.getElementById('pstatus'+appid); /* amol change*/
	var status = document.getElementById("pstatus" + appid).value;

	document.getElementById('hidid').value = appid;
	document.getElementById('hidstat').value = status;
	submitbutton('orders.save');
	return;
}
function updateOrderStatus(orderId, ele)
{

	var noteId = "order_note_" + orderId;

	/* Update note field name to "order note" so that it will be compatible to oder detail page note field */
	//document.getElementById(noteId).setAttribute("name","qtcComment");

	var status = document.getElementById("pstatus" + orderId).value;
	document.getElementById('hidid').value = orderId;
	document.getElementById('hidstat').value = status;
	submitbutton('orders.save');
	return;
}

/**
 * FOR STORE RELEATED VIEW :: TO CHANGE STORE ORDER STATUS
 **/
function changeStoreOrderStatus(orderid,storeid,element)
{
	var selInd=element.selectedIndex;
	var status =element.options[selInd].value;

	document.getElementById('hidid').value = orderid;
	document.getElementById('hidstat').value = status;
	submitbutton('changeStoreOrderStatus');
	return;
}

function change_curr(curr)
{
	techjoomla.jQuery.ajax({
		url: qtc_base_url+'index.php?option=com_quick2cart&task=cartcheckout.setCurrencySession&tmpl=component&format=raw&&currency='+curr,
		type: 'GET',
		cache: false,
		success: function(data) {
			/*console.log('change_curr msg='+data);*/
			setCookie('qtc_currency',curr,7);
			window.location.reload();
		}
	});
}

function setCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	/*var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdays);*/
	document.cookie=c_name + "=" + c_value+ "; path=/";
}

function emptycart(empty_cart_comfirmation,cart_emtied)
{
	var flag= confirm(empty_cart_comfirmation);
	if(flag==true)
	{
		techjoomla.jQuery.ajax({
			url: qtc_base_url+"index.php?option=com_quick2cart&task=clearcart&tmpl=component&format=raw",
			type: "GET",
			cache: false,
			success: function(msg)
			{
				/*window.location.reload();*/
				alert(cart_emtied);
				window.location = msg;
			}
		});
	}
}

function getlimit(limit,pid,parent,min_qtc,max_qtc)
{
	var lim=limit.trim();
	if(lim=="min")
	{
		return min_qtc;
	}
	else/*if(lim=='max')*/
	{
		return max_qtc;
	}
	/*
	var data='&limit='+limit+'&pid='+pid+'&parent='+parent;
	var returndata=88;
	*/

	/*techjoomla.jQuery.ajax({
	url: "?option=com_quick2cart&controller=cart&task=stocklimit&tmpl=component&format=raw"+data,
	type: "GET",
	success: function(msg)
	{
		alert('success :'+msg);
		returndata= msg;
	}
	});*/
	//return returndata;
}

function qtc_increment(input_field,pid,parent,slab,min_qtc,max_qtc)
{
	var limit = getlimit('max',pid,parent,min_qtc,max_qtc);
	limit = parseInt(limit);
	var qty_el = document.getElementById(input_field);
	var qty = qty_el.value;
 	qty = parseInt(qty_el.value);


	if( !isNaN(qty) && qty < limit)
	{
		qty_el.value = parseInt(qty_el.value) + parseInt(slab);
	}
	return false;
}

function qtc_decrement(input_field,pid,parent,slab,min_qtc,max_qtc)
{
	var limit=getlimit('min',pid,parent,min_qtc,max_qtc);
	var qty_el = document.getElementById(input_field);
	var qty = qty_el.value;
	if( !isNaN( qty ) && qty > limit )
	{
		/*qty_el.value--;*/
		qty_el.value = parseInt(qty_el.value) - parseInt(slab);
	}
	return false;
}

function checkforalphaLimit(el,pid,parent,slab,min_qtc,max_qtc,min_violate_msg,max_violate_msg)
{
	var textval=Number(el.value);
	var minlim=getlimit('min',pid,parent,min_qtc,max_qtc)
	if(textval < minlim)
	{
		/*'<?php echo JText::_('QTC_MIN_LIMIT_MSG'); ?>'+minlim*/
		alert(min_violate_msg+minlim);
		el.value = minlim;
		return false;
	}

	var maxlim=getlimit('max',pid,parent,min_qtc,max_qtc)
	if(textval>maxlim)
	{
		/*'<?php echo JText::_('QTC_MAX_LIMIT_MSG'); ?> '+maxlim*/
		alert(max_violate_msg+maxlim);
		el.value =maxlim;
		return false;
	}

	var slabquantity=textval%slab;

	if(slabquantity!=0)
	{
		/* @TODO add jtext  */
		alert(Joomla.JText._('COM_QUICK2CARET_SLAB_SHOULD_BE_MULT_MIN_QTY')+ slab);
		el.value = el.defaultValue;
		return false;
	}

	return true;
}

function qtc_fieldTypeChange(element,attContainerId)
{
	if(element.value == 'Textbox')
	{
		/*techjoomla.jQuery('#'+attContainerId).find('.qtc_attributeOpTable').hide();'*/
		var parentdiv=document.getElementById(attContainerId);
		parentdiv.getElementsByClassName('qtc_attributeOpTable')[0].style.visibility='hidden';
	}
	else if(element.value == 'Select')
	{
		/*techjoomla.jQuery('#'+attContainerId).find('.qtc_attributeOpTable').show();*/
		var parentdiv=document.getElementById(attContainerId);
		parentdiv.getElementsByClassName('qtc_attributeOpTable')[0].style.visibility='visible';
	}
}

techjoomla.jQuery(function()
{
	/* remove the list from component page if more than one exists on page*/
	if(techjoomla.jQuery('.qtc_category_list').length > '1' && techjoomla.jQuery('.qtc_store_list').length > '1')
	{
		techjoomla.jQuery('.qtc_productblog .span3').remove();
		techjoomla.jQuery('.qtc_productblog .span9').addClass('span12').removeClass('span9');
	}

	if(techjoomla.jQuery('.qtc_category_list').length > '1')
	{
		techjoomla.jQuery('.qtc_productblog .qtc_category_list').remove();

			if(techjoomla.jQuery('.qtc_store_list').length = '0')
			{
				techjoomla.jQuery('.qtc_productblog .span3').remove();
				techjoomla.jQuery('.qtc_productblog .span9').addClass('span12').removeClass('span9');
			}
	}

	if(techjoomla.jQuery('.qtc_store_list').length > '1')
	{
		techjoomla.jQuery('.qtc_productblog .qtc_store_list').remove();

		if(techjoomla.jQuery('.qtc_category_list').length = '0')
			{
				techjoomla.jQuery('.qtc_productblog .span3').remove();
				techjoomla.jQuery('.qtc_productblog .span9').addClass('span12').removeClass('span9');
			}
	}

	/*show hide store owner option on hover*/
	techjoomla.jQuery(".techjoomla-bootstrap .product_wrapper").hover(
		function () {
			techjoomla.jQuery(this).find(".qtc_owner_opts").show();
		},
		function () {
			techjoomla.jQuery(this).find(".qtc_owner_opts").hide();
		}
	);

	/* Create slideshow instances*/
	if(techjoomla.jQuery("#gallery").length)
	{
		/* Declare variables*/
		var totalImages = techjoomla.jQuery("#gallery > li").length;
		if(totalImages >= 1)
		{
			var imageWidth = techjoomla.jQuery("#gallery > li:first").outerWidth(true),
			totalWidth = imageWidth * totalImages,
			visibleImages = Math.round(techjoomla.jQuery("#gallery-wrap").width() / imageWidth),
			visibleWidth = visibleImages * imageWidth,
			stopPosition = (visibleWidth - totalWidth);
			techjoomla.jQuery("#gallery").width(totalWidth);

			techjoomla.jQuery("#gallery-prev").click(function()
			{
				if(techjoomla.jQuery("#gallery").position().left < 0 && !techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery("#gallery").animate({left : "+=" + imageWidth + "px"});
					techjoomla.jQuery('#gallery-next').show();
					techjoomla.jQuery('#gallery-prev').show();
				}
				else if(!techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery('#gallery-prev').hide();
				}
				return false;
			});

			techjoomla.jQuery("#gallery-next").click(function()
			{
				if((techjoomla.jQuery("#gallery").position().left) > stopPosition && !techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery("#gallery").animate({left : "-=" + imageWidth + "px"});
					techjoomla.jQuery('#gallery-next').show();
					techjoomla.jQuery('#gallery-prev').show();
				}
				else if((techjoomla.jQuery("#gallery").position().left) < stopPosition)
				{
					techjoomla.jQuery("#gallery").position().left = stopPosition;
				}
				else if(!techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery('#gallery-next').hide();
				}
				return false;
			});
		}
	}
});

function qtc_expirationChange(mediaNum)
{
	/* Get the DOM reference of bill details*/
	var downEle = techjoomla.jQuery('[name="prodMedia['+mediaNum+'][downCount]"]');
	var expEle = techjoomla.jQuery('[name="prodMedia['+mediaNum+'][expirary]"]');

	if(document.getElementsByName('prodMedia['+mediaNum+'][purchaseReq]')[0].checked)
	{
		if(downEle)
		{
			/*downEle.style.display = "none";*/
			downEle.closest(".control-group").show();
		}
		if(expEle)
		{
			 expEle.closest(".control-group").show();
		}
	}
	else
	{
		if(downEle)
		{
			/*downEle.style.display = "none";*/
			 downEle.closest(".control-group").hide();
		}
		if(expEle)
		{
			 expEle.closest(".control-group").hide();
		}
	}
}

/* Funtion to Update shipping profile list*/
function qtcUpdateShipProfileList(store_id)
{
	techjoomla.jQuery.ajax({
		url:qtc_base_url+'index.php?option=com_quick2cart&task=product.qtcUpdateShipProfileList&store_id='+store_id,
		type: 'GET',
		dataType: 'json',
		success: function(data)
		{
			techjoomla.jQuery('#qtc_shipProfileSelListWrapper').html(data.selectList);
		}
	});
}

/* This function load taxprofiles according to store id*/
function qtcLoadTaxprofileList(store_id, selected_taxid)
{
	techjoomla.jQuery.ajax({
		url:qtc_base_url + 'index.php?option=com_quick2cart&task=product.getTaxprofileList&store_id='+store_id+'&selected='+selected_taxid,
		type: 'GET',
		dataType: 'json',
		success: function(data)
		{
			/*jQuery(td_id).html(data);*/
			techjoomla.jQuery('.taxprofile').html(data);
		}
	});
}

function qtcIsPresentSku(actUrl, skuele)
{
	techjoomla.jQuery.ajax({
		url: actUrl,
		cache: false,
		type: 'GET',
		success: function(data)
		{
			//*already exist*/
			if (data == '1')
			{
				alert(Joomla.JText._('QTC_SKU_EXIST'));
				skuele.value="";
			}
			else
			{
				var tem='';
			}
		}
	});
}
/* Amol change : show / hide note text area on check of add note from order list vie*/
function showHideNoteTextarea(ref, order_id)
{

	if(ref.checked == 1)
	{
		document.getElementById("order_note_"+order_id).style.display="block";
	}
	else
	{
		document.getElementById("order_note_"+order_id).style.display="none";
	}

}

/*
 * Editable cart item.
*/
function updateCartItemsAttribute(cart_item_id, item_id)
{
	//serialize
	var formData = techjoomla.jQuery('#adminForm').serialize();

	techjoomla.jQuery.ajax({
		url : "?option=com_quick2cart&task=cartcheckout.update_cart_item&tmpl=component",
		type : 'POST',
		dataType : 'json',
		data:
		{
			'formData': formData,
			'cart_item_id' : cart_item_id,
			'item_id' : item_id
		},
		success : function(ret)
		{
			if (ret.status)
			{
				//alert(Joomla.JText._('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_SUCCESS'));
			}
			else
			{
				//alert(Joomla.JText._('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_FAIL') + ' - ' + ret.message);
			}
			window.location.reload();
		},
		error : function (e)
		{
			console.log('Someting is wrong');
		}
	});

}
/* Amol change :  called from backend

*/

function updateOrderItemAttribute(orderId , COM_QUICK2CART_ORDER_UPDATED)
{
	/* Serialize */
	var formData = techjoomla.jQuery('#orderItemForm').serialize();

	techjoomla.jQuery.ajax({
		url : "?option=com_quick2cart&task=orders.updateOrderItemAttribute&tmpl=component",
		type : 'POST',
		dataType : 'json',
		data:
		{
			'order_id': orderId,
			'formData': formData
		},
		success : function(data)
		{
			//alert(COM_QUICK2CART_ORDER_UPDATED);
			window.location.reload();
		},
		error : function (e)
		{
			console.log('Someting is wrong');
		}
	});
}
/* This function is used for LOT/slab from add product view*/
function checkSlabValue()
{
	//~ var alphaStatus = checkforalpha(el, allowed_ascii,enter_numerics );
//~
	//~ if (alphaStatus)
	{
		var slabvalue = techjoomla.jQuery('#item_slab').val();
		if(slabvalue==0)
		{
			alert(Joomla.JText._('COM_QUICK2CARET_LOT_VALUE_SHOULDNOT_BE_ZERO'));
		}

		if(slabvalue!=1 && slabvalue!=0)
		{
			var minval=techjoomla.jQuery('#min_item').val();
			if(minval!='' && minval!=0 )
			{
				/* Get Remandor */
				var Rem = minval % slabvalue;

				if(Rem!=0) /*  || ((slabvalue.trim) > (minval.trim)))*/
				{
					alert(Joomla.JText._('COM_QUICK2CARET_SLAB_MIN_QTY'));

					/* Copy Lot value to min value */
					techjoomla.jQuery('#min_item').val(slabvalue);
				}
			}
		}
	}
}

/* Check the slab value for while modifing min field */
function checkSlabValueField(el, allowed_ascii,enter_numerics )
{
	var alphaStatus = checkforalpha(el, allowed_ascii,enter_numerics );

	if (alphaStatus)
	{
		var slabvalue = techjoomla.jQuery('#item_slab').val();
		if(slabvalue > 0)
		{
			//if(slabvalue!=1 && slabvalue!=0)
			{
				var minval=techjoomla.jQuery('#'+ el.id).val();
				if(minval!='' && minval!=0 )
				{
					/* Get Remandor */
					var Rem = minval % slabvalue;

					if(Rem!=0) /*  || ((slabvalue.trim) > (minval.trim)))*/
					{
						alert(Joomla.JText._('COM_QUICK2CARET_SLAB_MIN_QTY'));

						/* Copy Lot value to min value */
						techjoomla.jQuery('#'+ el.id).val(slabvalue);
					}
				}
			}
		}
	}
}
