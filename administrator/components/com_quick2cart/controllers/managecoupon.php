<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
$lang = JFactory::getLanguage();
$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
class Quick2cartControllerManagecoupon extends quick2cartController
{
	/**
	 * save a ad fields
	 */
	 //find the auto suggestion according the db
	function findauto(){
		$jinput=JFactory::getApplication()->input;
		$element	=$jinput->get('element','','STRING');
		$element_val	=$jinput->get('request_term','','STRING');
		$autodata = $_POST[$element];
		$query_condi = array();
		$query_table = array();
		$loca_list = array();
//print_r($_POST);die;

		$autodata = str_replace("||","','",$autodata);
		$autodata = str_replace('|','',$autodata);
		if($element == "item_id"){
			$element_table = "kart_items";
			$element_field = "name";
			$store_id = $jinput->get('store');
			$query_condi[] = $element.".store_id = ".$store_id;
		}else if($element == "id"){
			$element_table = "users";
			$element_field = "name";
			$query_condi[] = $element.".block <> 1";
		}

		$query_table[] = '#__'.$element_table.' as '.$element;
		$element_table_name = $element;
		$query_condi[] = $element.".".$element_field." LIKE '%".trim($element_val)."%'";
		if(trim($autodata)){
			$query_condi[] = $element.".".$element." NOT IN ('".trim($autodata)."')";
		}
		$tables = (count($query_table) ? ' FROM '.implode("\n LEFT JOIN ", $query_table) : '');
		if($tables){
			$where = (count($query_condi) ? ' WHERE '.implode("\n AND ", $query_condi) : '');
			if($where)
			{
				$db   =JFactory::getDBO();
				$query = "SELECT distinct(".$element_table_name.".".$element."),".$element_table_name.".".$element_field."
				\n ".$tables." \n ".$where;
//echo $query;
				$db->setQuery($query);
				$loca_list = $db->loadRowList();
			}
		}

		$data = array();
		if($loca_list){
			foreach ($loca_list as $row){
				$json = array();
				$json['label'] = $row['1'];	//name of the location
				$json['value'] = $row['0'];	//id of the location
				$data[] = $json;

				//$data[] = $row['0']; //name of the location


			}
		}
		//print_r($loca_list);

		echo json_encode($data);
		jexit();
	}

	function saveCoupon()
	{
	  // Check for request forgeries
		$model	= $this->getModel( 'managecoupon' );
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post;
		// allow name only to contain html
		$model->setState( 'request', $post );
		if ($model->store($post))
		{

			$msg = JText::_( 'C_SAVE_M_S' );
		}
		else
		{
			$msg = JText::_( 'C_SAVE_M_NS' );
		}
			$task= $jinput->get('task');
		//echo $this->_task;die;
		switch ( $task )
		{
			case 'cancel':
			$cancelmsg = JText::_( 'FIELD_CANCEL_MSG' );
			$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=managecoupon&layout=default", $cancelmsg );
			break;
			case 'saveCoupon':
			$this->setRedirect( JUri::base()."index.php?option=com_quick2cart&view=managecoupon", $msg );
			break;
		}


	}
	//function save ends

	function getcode()
	{
		$jinput=JFactory::getApplication()->input;
		$selectedcode =  $jinput->get('selectedcode');
		$model	= $this->getModel( 'managecoupon' );
		$coupon_code=$model->getcode(trim($selectedcode));
		echo $coupon_code;
		exit();
	}
	function getselectcode()
	{
		$jinput=JFactory::getApplication()->input;
		$selectedcode	  = $jinput->get('selectedcode');
		$couponid= $jinput->get('couponid');
		$model	= $this->getModel( 'managecoupon' );
		$coupon_code=$model->getselectcode(trim($selectedcode),$couponid);
		echo $coupon_code;
		exit();
	}


}

