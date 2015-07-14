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

class quick2cartViewcart extends JViewLegacy
{
	function display($tpl = null)
	{
		$user=JFactory::getUser();
		$model	= $this->getModel( 'cart' );
		$params = JComponentHelper::getParams('com_quick2cart');
		$input=JFactory::getApplication()->input;
		$layout		= $input->get( 'layout' );

		//if(!$layout ){
			$cart = $model->getCartitems();
			$this->cart= $cart;
		//}
//START Q2C Sample development
		$beforecartdisplay = '';
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$result=$dispatcher->trigger('onBeforeCartDisplay');//Call the plugin and get the result
		if(!empty($result)){
				$beforecartdisplay=$result[0];
		}

		$this->beforecartdisplay=$beforecartdisplay;
//START Q2C Sample development
		$aftercartdisplay='';
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$result=$dispatcher->trigger('onAfterCartDisplay');//Call the plugin and get the result
		if(!empty($result)){
				$aftercartdisplay=$result[0];
		}
		$this->aftercartdisplay=$aftercartdisplay;


		parent::display($tpl);
	}
}

