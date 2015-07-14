<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Unauthorized Access');

/**
 * Profile view for article app
 *
 * @since  1.0
 * @access public
 */
class  Q2c_boughtProductsViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since   1.0
	 * @access  public
	 * @param   int   The user id that is currently being viewed.
	 * @return  string  Layout path
	 */
	public function display ($userId = null, $docType = null)
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_app_user_q2c_boughtproducts', JPATH_ADMINISTRATOR);

		$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';

		if (! class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$product_path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php';

		if (! class_exists('productHelper'))
		{
			JLoader::register('productHelper', $product_path);
			JLoader::load('productHelper');
		}

		// Get the user params
		$params = $this->getUserParams($userId);

		// Get the app params
		$appParams = $this->app->getParams();

		// Get the blog model
		$no_of_porducts = $params->get('total', '10');
		$privacy = $params->get('profile_show_friends', 'onlyme');
		$pin_width = (int) $appParams->get('pin_width', 145);
		$pin_padding = (int) $appParams->get('pin_padding', 3);

		// Get profile id
		$model = new productHelper;

		// $target_data = $model->getUserStores($user->id,$no_of_stores);

		$no_authorize = '';
		$target_data = '';
		$my = Foundry::user();
		$logged_id = $my->id;

		if ($privacy === 'onlyme')
		{
			if ($logged_id == $userId)
			{
				$target_data = $model->getUserRecentlyBoughtproducts($userId, $no_of_porducts);
			}
			else
			{
				$no_authorize = 'no';
				$this->set('no_authorize', $no_authorize);
			}
		}
		elseif ($privacy === 'friend')
		{
			$check = Foundry::model('Friends')->isFriends($logged_id, $userId);

			if ($check == 1 || $logged_id == $userId)
			{
				$target_data = $model->getUserRecentlyBoughtproducts($userId, $no_of_porducts);
			}
			else
			{
				$no_authorize = 'no';
				$this->set('no_authorize', $no_authorize);
			}
		}
		elseif ($privacy === 'fof')
		{
			// $my = Foundry::user($userId);
			$check_friend = Foundry::model('Friends')->isFriends($logged_id, $userId);
			$friendlist = Foundry::model('Friends')->getFriends($userId);

			foreach ($friendlist as $ids)
			{
				$check_fof = Foundry::model('Friends')->isFriends($logged_id, $ids->id);

				if ($check_fof == 1 || $logged_id == $userId)
				{
					$check_friendoffriend = 1;
				}
			}

			if ($check_friend == 1 || $logged_id == $userId && $check_friendoffriend == 1)
			{
				$target_data = $model->getUserRecentlyBoughtproducts($userId, $no_of_porducts);
			}
			else
			{
				$no_authorize = 'no';
				$this->set('no_authorize', $no_authorize);
			}
		}
		elseif ($privacy === 'all')
		{
			$target_data = $model->getUserRecentlyBoughtproducts($userId, $no_of_porducts);
		}
		else
		{
			$target_data = '';
		}

		$this->set('no_authorize', $no_authorize);
		$this->set('target_data', $target_data);
		$this->set('userId', $userId);
		$this->set('privacy', $privacy);
		$this->set('pin_width', $pin_width);
		$this->set('pin_padding', $pin_padding);

		echo parent::display('profile/default');
	}
}
