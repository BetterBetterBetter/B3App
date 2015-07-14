<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class Quick2cartControllerShipmanager extends quick2cartController
{
	
	function loadState()									// controller
	{
		$db= JFactory::getDBO();
		$jinput=JFactory::getApplication()->input;
		$country = $jinput->get('country');
		$model = $this->getModel('shipmanager');			
		$state = $model->getStatelist($country);	
		
		//$cities = $model->getCity($country);
		$data=array();
		$data[0]=$state ;
		//$data[1]=$cities;
		//print "<pre>";print_r($data); die(" die c ship ");
		echo json_encode($data);
		//die('  country ='.$country);
		jexit();					
	}
	
	function loadCity()									// controller
	{
		$db= JFactory::getDBO();
		$jinput=JFactory::getApplication()->input;
		$state =  $jinput->get('state');
	//	die('  state ='.$state);
		$model = $this->getModel('shipmanager');			
		$cities = $model->getCity($state);
		echo json_encode($cities);
		//die('  state ='.$state);
		jexit();					
	}
	function saveShipOption()
	{
			$data = JRequest::get('get');
	//		print"<pre>"; print_r($data);
//			print"i am in shipmaneger die"; die();
			$model = $this->getModel('shipmanager');	
			$model->storeShipData($data);		

			$msg="";
			
			$this->setRedirect( 'index.php?option=com_quick2cart&view=shipmanager&layout=list', $msg );			
	}
	
	function remove()
	{
		
	
		$model	= $this->getModel( 'shipmanager' );
		$post	= JRequest::get('post');
		
		$orderid=$post['cid'];
	
		if ($model->deletshiplist($orderid)) 
		{
			$msg = JText::_( 'C_ORDER_DELETED_SCUSS' );
		}
		else 
		{
			$msg = JText::_( 'C_ORDER_DELETED_ERROR' );
		}
		if(JVERSION >= '1.6.0')
			$this->setRedirect( 'index.php?option=com_quick2cart&view=shipmanager&layout=list', $msg );
		else
			$this->setRedirect( 'index.php?option=com_quick2cart&view=shipmanager&layout=list', $msg );
		
	}
	//find the geo locations according the geo db
	function findgeo(){
		
		$geodata = $_POST['geo'];
		$element	= JRequest::getVar('element');
	 $element_val	= JRequest::getVar('request_term');
		$query_condi = array();
		$query_table = array();
		$first = 1;
		$first_key = key($geodata);
		$previous_field = '';
		$loca_list = array();
		//print_r($_POST);die;
		foreach ($geodata as $key=>$value){
				
				$value = trim($value);

		//	if(trim($value)){
				//$tablename = explode('_',$key);
				if($first){
					$query_table[] = '#__kart_'.$key.' as '.$key;
				}
				else if($element == $key ){
					$query_table[] = '#__kart_'.$key.' as '.$key.' ON '.$key.'.'.$previous_field.'_code = '.$previous_field.'.'.$previous_field.'_code';
			
				}
				$value = str_replace("||","','",$value);
				$value = str_replace('|','',$value);
				if($element == $key ){			
					$element_table_name = $key;
					$query_condi[] = $key.".".$key." LIKE '%".trim($element_val)."%'";
					if(trim($value)){
						$query_condi[] = $key.".".$key." NOT IN ('".trim($value)."')";
					}
					break;
					/*if($first_key == $key){
						$first = 0;
						break;
					}*/
					$previous_field = $key;
				}
				else if(trim($value) && $first ){
					$query_condi[] = $key.".".$key." IN ('".trim($value)."')";
					$previous_field = $key;
				}
				$first = 0;
			//}
		}
		$tables = (count($query_table) ? ' FROM '.implode("\n LEFT JOIN ", $query_table) : '');
		if($tables){
			$where = (count($query_condi) ? ' WHERE '.implode("\n AND ", $query_condi) : '');
			if($where)
			{
				$db   =JFactory::getDBO();
				$query = "SELECT distinct(".$element_table_name.".".$element.") \n ".$tables." \n ".$where;
				$db->setQuery($query);
				$loca_list = $db->loadRowList();					
			}
		}
		$data = array();
		if($loca_list){
			foreach ($loca_list as $row){
				$json = array();
				//$json['value'] = $row['1'];	//id of the location
				$data[] = $row['0']; //name of the location

				//$data[] = $json;
			}
		}
		//print_r($loca_list);
		
		echo json_encode($data);
		jexit();
	}
	
	

}
