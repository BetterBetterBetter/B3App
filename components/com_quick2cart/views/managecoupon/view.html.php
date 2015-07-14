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

jimport( 'joomla.application.component.view');


class quick2cartViewManagecoupon extends JViewLegacy
{


	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$input=$mainframe->input;
		$comquick2cartHelper=new comquick2cartHelper;
		// check for multivender COMPONENT PARAM
		$isMultivenderOFFmsg=$comquick2cartHelper->isMultivenderOFF();

		if(!empty($isMultivenderOFFmsg))
		{
			print $isMultivenderOFFmsg;
			return false;
		}

  		$option = $input->get('option');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'desc','word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'string' );
		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'search', 'search','', 'string' );
		$search = JString::strtolower( $search );
		$limit = '';
		$limitstart = '';
		$cid[0]='';
		if($search==null)
		$search='';
		$edit		= $input->get( 'edit','' );
		$layout		= $input->get( 'layout','' );
		$cid		= $input->get(  'cid','','ARRAY' );
		$model		=  $this->getModel( 'Managecoupon' );

		// get store id (list))from model and pass to  getManagecoupon()
		$this->store_role_list=$store_role_list=$comquick2cartHelper->getStoreIds();  // retrun store_id,role etc with order by role,store_id
		$change_store = $input->get->get('change_store',0,'INT');
		//	$this->authorized_store_id=$comquick2cartHelper->store_authorize("managecoupon_default",isset($change_storeto)?$change_storeto:$store_role_list[0]['store_id']);
		$this->store_id=$store_id =	(!empty($change_storeto))?$change_storeto:$store_role_list[0]['store_id'];
		$this->selected_store = $store_id;
		// if user has store OR has ROLE to create  then only create coupon or view list :: following coupon return store_id if EXIST
		 if($cid) // edit
		 {
		 	//$total 		= $this->get( 'Total');
			$pagination = $this->get( 'Pagination' );
			$this->authorized_store_id=$authorized_store_id=$comquick2cartHelper->createCouponAuthority($store_id,$cid[0]);//$this->authorized_store_id= storeid of user
 			$coupons = $model->Editlist($cid[0]);
			$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', 'limit', 'int' );
			$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
			$model->setState('limit', $limit); // Set the limit variable for query later on
		    $model->setState('limitstart', $limitstart);
		 }
		 else
		 {					/************ list view  and new *******************/
			$this->authorized_store_id=$authorized_store_id=$comquick2cartHelper->createCouponAuthority($store_id);//$this->authorized_store_id= storeid of user
			if(!empty($authorized_store_id) ) // if authorized then only fetch data
			{
				if( !empty($store_id))
				{
					 $coupons 		= $model->getManagecoupon($store_id);//($authorized_store_id);
					 $pagination = $model->getPagination($store_id);
				}
				/*if( !empty($change_storeto))  // fetch coupon list according to new store
				{
					 $coupons 		= $model->getManagecoupon($change_storeto);//($authorized_store_id);
					 $this->selected_store=$change_storeto;
				}
				else if( !empty($store_role_list))
				{
					$this->selected_store=$store_role_list[0]['store_id'];
				 	$coupons 		= $model->getManagecoupon($store_role_list[0]['store_id']);//($authorized_store_id);
				}*/
			}

			$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', 'limit', 'int' );
			$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
			$model->setState('limit', $limit); // Set the limit variable for query later on
		    $model->setState('limitstart', $limitstart);
		 }

		// search filter
		$lists['search_select']	= $search;
		$lists['search']		= $search;
		$lists['search_list']	= $filter_state;
		$lists['order']			= $filter_type;
		$lists['order_Dir']		= $filter_order_Dir;
		$lists['limit']			= $limit;
		$lists['limitstart']	= $limitstart;
		// Get data from the model
		$this->assignRef('lists', $lists);
		$this->assignRef('pagination', $pagination);
		if(!empty($coupons))
		{
			$this->assignRef('coupons',$coupons);
		}

		$this->_setToolBar();
		parent::display($tpl);
	}//function display ends here

	function _setToolBar()
	{
		//added by aniket for task #25690
		$document=  JFactory::getDocument();
		$document->setTitle( JText::_( 'QTC_COUPON_PAGE' ));
	}

}// class
