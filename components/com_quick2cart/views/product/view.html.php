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
jimport('joomla.application.component.view');
/**
 * View class for list view of products.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */

class Quick2cartViewProduct extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null)
	{
		$this->params = JComponentHelper::getParams('com_quick2cart');
		$this->comquick2cartHelper = new comquick2cartHelper;
		$storeHelper = new storeHelper;
		$this->productHelper = new productHelper;
		$input = JFactory::getApplication()->input;
		$layout = $input->get('layout', 'default');

		if ($layout == 'default')
		{
			// @TODO ADD CONDITION :: LOGGED IN USER MUST HV STORE

			// Gettting store id if store is changed
			$user = JFactory::getUser();
			global $mainframe;
			$mainframe = JFactory::getApplication();
			$change_storeto = $mainframe->getUserStateFromRequest('current_store', 'current_store', 0, 'INTEGER');

			// Get item_id from request from GET/POST
			$item_id = $mainframe->getUserStateFromRequest('item_id', 'item_id', '', 'STRING');

			// REMOVE FROM REQUEST
			$mainframe->setUserState('item_id', '');
			$this->client = $client = "com_quick2cart";
			$this->pid = 0;
			$Quick2cartModelcart = new Quick2cartModelcart;

			// If item_id NOT found then SET TO ''
			$this->item_id = '';

			// If edit task then fetch item DETAILS
			if (!empty($item_id))
			{
				// Check whether called from backend
				$admin_call = $mainframe->getUserStateFromRequest('admin_call', 'admin_call', 0, 'INTEGER');

				if (!empty($admin_call))
				{
					// CHECK SPECIAL ACCESS
					$special_access = $this->comquick2cartHelper->isSpecialAccess();
				}
				// Load Attributes model
				$path = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';
				$attri_model = $this->comquick2cartHelper->loadqtcClass($path, "quick2cartModelAttributes");

				// GET ITEM DETAIL
				$this->itemDetail = $itemDetail = $attri_model->getItemDetail(0, '', $item_id);

				// Getting attribure
				$this->item_id = !empty($this->itemDetail) ? $itemDetail['item_id'] : '';
				$this->allAttribues = $attri_model->getItemAttributes($this->item_id);
				$this->getMediaDetail = $this->productHelper->getMediaDetail($item_id);
			}
			// IF ITEM_ID AND SPECIAL ACCESS EG ADMIN THEN FETCH STORE ID
			// Means edit task
			if (!empty($item_id) && !empty($special_access))
			{
				// WE DONT WANT TO SHOW STORE SELECT LIST
				$this->store_id = $store_id = $this->store_role_list = $this->itemDetail['store_id'];
			}
			else
			{
				// Get all store ids of vendor
				$this->store_role_list = $store_role_list = $this->comquick2cartHelper->getStoreIds();

				// If Edit ck AUTORIZATION
				$authorized = 0;

				if (!empty($itemDetail) && !empty($itemDetail['store_id']))
				{
					// Item store  ==  logged in user releated store
					foreach ($this->store_role_list as $srole)
					{
						if ($itemDetail['store_id'] == $srole['store_id'])
						{
							$authorized = 1;
							break;
						}
					}
				}

				if ($authorized == 0)
				{
					// Remove all item details
					$this->allAttribues = "";
					$this->item_id = '';
					$this->itemDetail = '';
				}

				$this->store_id = $store_id = (!empty($this->itemDetail['store_id'])) ? $this->itemDetail['store_id'] : $store_role_list[0]['store_id'];
				$this->selected_store = $store_id;
			}
			// Get store's default settings
			$this->defaultStoreSettings = $storeHelper->getStoreDefaultSettings($this->store_id);

			// ALL FETCH ALL CATEGORIES

			if (!empty($this->itemDetail['category']))
			{
				$this->cats = $this->comquick2cartHelper->getQ2cCatsJoomla($this->itemDetail['category'], 0, 'prod_cat', ' required ');
			}
			else
			{
				$this->cats = $this->comquick2cartHelper->getQ2cCatsJoomla('', 0, 'prod_cat', ' required ');
			}
		}

		$this->_setToolBar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	private function _setToolBar()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('QTC_PRODUCT_PAGE'));
		/*
		$document  =  JFactory::getDocument();
		$document->addStyleSheet(JUri::base().'components/com_quick2cart/assets/css/quick2cart.css');
		$bar  =  JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'QTC_SETT' ), 'icon-48-quick2cart.png' );
		JToolBarHelper::save('save',JText::_('QTC_SAVE') );
		JToolBarHelper::cancel( 'cancel', JText::_('QTC_CLOSE') );
		*/
	}
}
