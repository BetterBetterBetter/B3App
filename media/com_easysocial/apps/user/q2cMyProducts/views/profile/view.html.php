<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

/**
 * Profile view for Q2CProducts app
 *
 * @since  1.0
 * @access  public
 */
class Q2cMyProductsViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @param   int   The user id that is currently being viewed.
	 * @param   string   layout path
	 * @since   1.0
	 * @access  public
	 */
	public function display ($userId = null, $docType = null)
	{
		// Get the user params
		$params = $this->getUserParams($userId);

		// Get the app params
		$appParams = $this->app->getParams();

		// Get the blog model
		$total = (int) $params->get('total', $appParams->get('total', 5));
		$pin_width = (int) $appParams->get('pin_width', 145);
		$pin_padding = (int) $appParams->get('pin_padding', 3);

		// Get list of all products created by the user on the site.
		$input = JFactory::getApplication()->input;
		$storeid = $input->get('storeid', '', 'INT');
		$model = $this->getModel('q2cmyproducts');
		$products = $model->getItems($userId, $total, $storeid);
		$productsCount = $model->getProductsCount($userId, $storeid);

		$user = Foundry::user($userId);

		// Get store list of user
		require_once JPATH_ROOT . '/components/com_quick2cart/helpers/storeHelper.php';
		$storeHelper = new storeHelper;
		$storelist = $storeHelper->getUserStore($userId);

		$this->set('user', $user);
		$this->set('userId', $userId);
		$this->set('total', $total);
		$this->set('pin_width', $pin_width);
		$this->set('pin_padding', $pin_padding);
		$this->set('products', $products);
		$this->set('productsCount', $productsCount);
		$this->set('storelists', $storelist);

		echo parent::display('profile/default');
	}
}
