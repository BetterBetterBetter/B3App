<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table.user' );



class Quick2cartModelstore extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$jinput=$mainframe->input;

		//CATEGORY FILTER store_cat
		//$store_cat = $mainframe->getUserStateFromRequest( 'com_quick2cart.store_cat', 'store_cat',0, 'INTEGER' );
		$store_cat = $jinput->get('store_cat','','INTEGER');

		$this->setState('store_cat', $store_cat);

		// get component limit for pagination
		$params = JComponentHelper::getParams('com_quick2cart');

		if(!empty($store_cat))
		{		// show pagination only when pagination is selected
				$comp_limit=$params->get('all_prod_pagination_limit');
		}
		else
		{
				$comp_limit=$params->get('storeProdPage_limit');
		}
		//$filter_limit=$mainframe->getCfg('list_limit');
	 	//$filter_limit=!empty($default_limit)?$default_limit:$comp_limit;

		// Get the pagination request variables
		$limit 				= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $comp_limit, 'int' );
		$limitstart 		= $jinput->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);



		//$change_storeto= $mainframe->getUserStateFromRequest( 'current_store', 'current_store','', 'INTEGER' );
		//$this->setState('current_store', $change_storeto);


	}
	function _buildQuery($store_id='',$client='')
	{
		$where = $this->_buildContentWhere($store_id,$client);
		$query = "SELECT a.item_id,a.name,a.images,a.store_id,a.featured, a.stock  from #__kart_items as a". $where.' ORDER BY a.item_id DESC';
		return $query;
	}

	function _buildContentWhere($store_id='',$parent="")
	{
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$db=JFactory::getDBO();
		$jinput=$mainframe->input;

		$where = array();
		//PRODUCT WHICH ARE PUBLISHED
		$where[]=' a.`state`=1';
		// 	CHECK FOR parent
		if(!empty($parent))
		{
			$where[] = " a.parent='".$parent."' ";
		}
		if(!empty($store_id))
		{
			$where[]=' a.`store_id`=\''.$store_id.'\'';
		}

		//CATEGORY FILTER
		//print "**".$store_cat =$mainframe->getUserStateFromRequest( 'store_cat', 'store_cat',0, 'INTEGER' );
		$store_cat = $jinput->get('store_cat','','INTEGER');
		if(trim($store_cat)!= 0)
		{
			$where[] = " a.category=".$store_cat." ";
		}
		return $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

	}
   function getTotal($client='',$store_id)
   {
		// Lets load the content if it doesn’t already exist
		//if (empty($this->_total))
		{
			$query = $this->_buildQuery($store_id,$client);
			//$query 	   .= $this->_buildContentWhere($store_id,$client);
		$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
   }

  function getPagination($client='',$store_id)
  {

		//if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			// get component limit for pagination
		//$params = JComponentHelper::getParams('com_quick2cart');
		//$comp_limit=$params->get('all_prod_pagination_limit');

			$this->_pagination = new JPagination( $this->getTotal($client,$store_id), $this->getState('limitstart'), $this->getState('limit') );
		}


		return $this->_pagination;
  }

 function getAllStoreProducts($client='',$store_id='')
 {
		$query = $this->_buildQuery($store_id,$client);
		$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		return $this->_data;
 }

	/* Function to return store id for $owner
	 * */
	function getStoreId($owner=0)
	{
		$query = "Select id FROM #__kart_store WHERE owner=".$owner;
		$this->_db->setQuery($query);
		$store = $this->_db->loadResult();
		return $store;
	}

	function getStore()
	{

	}

	function saveStore($data)
	{
		$comparams = JComponentHelper::getParams('com_quick2cart');
		$row = new stdClass;
		$row->owner=$data['owner'];
		$row->title=$data['title'];
		$row->description=$data['description'];
		$row->address=$data['address'];
		$row->phone=$data['phone'];
		$row->store_email=$data['store_email'];
		$row->store_avatar=$data['store_avatar'];	 //@TODO get the uploaded image path
		$comquick2cartHelper = new comquick2cartHelper;
		$row->currency_name=(isset($data['currency_name'])? $data['currency_name'] : $comquick2cartHelper->getCurrencySession() );
		$row->use_ship=(isset($data['use_ship'])? $data['use_ship'] : $comparams->get( 'shipping' ) );
		$row->use_stock=(isset($data['use_stock'])? $data['use_stock'] : $comparams->get( 'usestock' ) );
		$row->ship_no_stock=(isset($data['ship_no_stock'])? $data['ship_no_stock'] : $comparams->get( 'outofstock_allowship' ) );
		$row->buy_button_text=(isset($data['buy_button_text'])? $data['buy_button_text'] : JText::_('QTC_ITEM_BUY') );
		if( isset($data['live']) )
			$row->live= $data['live'];
		if( isset($data['extra']) )
			$row->extra= $data['extra'];

		$query = "Select id FROM #__kart_store WHERE owner=".$data['owner'];
		$this->_db->setQuery($query);
		$store = $this->_db->loadResult();

	 	if($store)	//@TODO consider multi store for a single user
	 	{
	 		$row->id=$store;
			if(!$this->_db->updateObject('#__kart_store', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		else
		{
			if(!$this->_db->insertObject('#__kart_store', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}

		return true;
	}

	function saveStoreRole($data)
	{
		$comparams = JComponentHelper::getParams('com_quick2cart');
		$row = new stdClass;
		$row->store_id=$data['store_id'];
		$row->user_id=$data['user_id'];
		$row->role=$data['role'];

	 	if(isset($data['id']) )
	 	{
	 		$row->id=$data['id'];
			if(!$this->_db->updateObject('#__kart_role', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}
		else
		{
			if(!$this->_db->insertObject('#__kart_role', $row, 'id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		}

		return true;
	}


}

