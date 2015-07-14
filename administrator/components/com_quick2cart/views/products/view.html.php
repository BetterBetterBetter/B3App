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

class Quick2cartViewProducts extends JViewLegacy
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
		$mainframe = JFactory::getApplication();
		$input = $mainframe->input;
		$option = $input->get('option');
		$layout = $input->get('layout', 'default');
		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->productHelper = new productHelper;
		$storeHelper = new storeHelper;
		$productHelper = new productHelper;

		if ($layout == 'default')
		{
			$this->products = $this->items = $this->get('Items');
			$this->pagination = $this->get('Pagination');
			$this->state = $this->get('State');
			$this->filterForm = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');

			// Creating status filter.
			$sstatus = array();

			if (JVERSION < '3.0')
			{
				$sstatus[] = JHtml::_('select.option', '', JText::_('JOPTION_SELECT_PUBLISHED'));
				$sstatus[] = JHtml::_('select.option', 1, JText::_('COM_QUICK2CART_PUBLISH'));
				$sstatus[] = JHtml::_('select.option', 0, JText::_('COM_QUICK2CART_UNPUBLISH'));
				$this->sstatus = $sstatus;
			}
			// Create clients array
			$clients = array();

			if (JVERSION < '3.0')
			{
				$clients[] = JHtml::_('select.option', '', JText::_('COM_QUICK2CART_FILTER_SELECT_CLIENT'));
				$clients[] = JHtml::_('select.option', 'com_quick2cart', JText::_('COM_QUICK2CART_NATIVE'));
				$clients[] = JHtml::_('select.option', 'com_content', JText::_('COM_QUICK2CART_CONTENT_ARTICLES'));
				$clients[] = JHtml::_('select.option', 'com_flexicontent', JText::_('COM_QUICK2CART_FLEXICONTENT'));
				$clients[] = JHtml::_('select.option', 'com_k2', JText::_('COM_QUICK2CART_K2'));
				$clients[] = JHtml::_('select.option', 'com_zoo', JText::_('COM_QUICK2CART_ZOO'));
				$this->clients = $clients;
			}
			// Get all stores.
			$this->store_details = $this->comquick2cartHelper->getAllStoreDetails();
		}
		elseif ($layout == "new")
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

			// LOAD CART MODEL
			$Quick2cartModelcart = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/models/cart.php", "Quick2cartModelcart");

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
				$path = '/components/com_quick2cart/models/attributes.php';
				$attri_model = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . $path, "quick2cartModelAttributes");

				// GET ITEM DETAIL
				$this->itemDetail = $itemDetail = $attri_model->getItemDetail(0, '', $item_id);

				// Getting attribure
				$this->item_id = !empty($this->itemDetail) ? $itemDetail['item_id'] : '';
				$this->allAttribues = $attri_model->getItemAttributes($this->item_id);
				$this->getMediaDetail = $productHelper->getMediaDetail($item_id);
				$this->store_id = $store_id = $this->store_role_list = $this->itemDetail['store_id'];
			}
			else
			{
				$storeHelper = new storeHelper;
				$storeList = (array) $storeHelper->getUserStore($user->id);
				$this->store_id = $storeList[0]['id'];
			}

			// IF ITEM_ID AND SPECIAL ACCESS EG ADMIN THEN FETCH STORE ID // means edit task
			// Else :
			if (!empty($item_id) && !empty($special_access))
			{
				// WE DONT WANT TO SHOW STORE SELECT LIST
				$this->store_id = $store_id = $this->store_role_list = $this->itemDetail['store_id'];
			}
			else // Else : to get default store id (alex change)
			{
				$this->store_role_list = $store_role_list = $this->comquick2cartHelper->getStoreIds(); // as no NEED TO CHECK AUTHORIZATION AT ADMINSIDE
				$storeHelper = new storeHelper;
				$this->defaultStoreId = $defaultStoreId = $storeHelper->getAdminDefaultStoreId(); // get all store ids of vendor


				//	$this->authorized_store_id = $comquick2cartHelper->store_authorize("managecoupon_default",isset($change_storeto)?$change_storeto:$store_role_list[0]['store_id']);
				$this->store_id = $store_id =(!empty($change_storeto)) ? $change_storeto : $defaultStoreId;
				$this->selected_store = $store_id;

				if(!$this->store_id)
				{
					$user = JFactory::getUser();
					$storeHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE.DS."components".DS."com_quick2cart".DS."helpers".DS."storeHelper.php","storeHelper");

					$storeList = (array) $storeHelper->getUserStore($user->id);
					$this->store_id = $storeList[0]['id'];
				}
			}

			// ALL FETCH ALL CATEGORIES //$catid='',$onchangeSubmitForm=1,$name='prod_cat',$class=''
			if (!empty($this->itemDetail['category']))
			{
				$this->cats = $this->comquick2cartHelper->getQ2cCatsJoomla($this->itemDetail['category'], 0, 'prod_cat', ' required ');
			}
			else
			{
				$this->cats = $this->comquick2cartHelper->getQ2cCatsJoomla('', 0, 'prod_cat', ' required ');
			}
		}
		// Check for errors.

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->addToolbar();

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$mainframe = JFactory::getApplication();
		$input = $mainframe->input;
		$option = $input->get('option');
		$layout = $input->get('layout', 'default');

		// Get the toolbar object instance.
		$bar = JToolBar::getInstance('toolbar');

		if ($layout == "default")
		{
			JToolBarHelper::addNew('products.addnew', 'QTC_NEW');

			if (isset($this->items[0]))
			{
				JToolBarHelper::editList('products.edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::divider();
				JToolBarHelper::custom('products.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('products.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);

				// Featurd and unfeatured buttons.

				if (JVERSION >= '3.0')
				{
					JToolBarHelper::custom('products.featured', 'featured', '', 'COM_QUICK2CART_FEATURE_TOOLBAR');
					JToolBarHelper::custom('products.unfeatured', 'star-empty', '', 'COM_QUICK2CART_UNFEATURE_TOOLBAR');
				}
				else
				{
					JToolBarHelper::custom('products.featured', 'quick2cart-feature.png', '', 'COM_QUICK2CART_FEATURE_TOOLBAR');
					JToolBarHelper::custom('products.unfeatured', 'quick2cart-unfeature', '', 'COM_QUICK2CART_UNFEATURE_TOOLBAR');
				}

				JToolBarHelper::deleteList('', 'products.delete', 'JTOOLBAR_DELETE');
			}
			// Featurd and unfeatured buttons.

			if (JVERSION >= '3.0')
			{
				JToolBarHelper::title(JText::_('COM_QUICK2CART_PRODUCTS'), 'cart');
			}
			else
			{
				JToolBarHelper::title(JText::_('COM_QUICK2CART_PRODUCTS'), 'products.png');
			}

			JToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart');
		}
		elseif ($layout == "new")
		{
			//JToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart&view=products');
			JToolBarHelper::save($task = 'products.save', $alt = 'QTC_SAVE');
			JToolbarHelper::save('products.saveAndClose');
			JToolbarHelper::save2new('products.saveAndNew');
			//JToolBarHelper::cancel($task = 'products.cancel', $alt = 'QTC_CLOSE');
			JToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart&view=products');
			$isNew = ($this->item_id == 0);

			if ($isNew)
			{
				$viewTitle = JText::_('COM_QUICK2CART_ADD_PRODUCT');
			}
			else
			{
				$viewTitle = JText::_('COM_QUICK2CART_EDIT_PRODUCT');
			}

			if (JVERSION >= '3.0')
			{
				JToolBarHelper::title($viewTitle, 'pencil-2');
			}
			else
			{
				JToolBarHelper::title($viewTitle, 'product.png');
			}
		}
	}
}
