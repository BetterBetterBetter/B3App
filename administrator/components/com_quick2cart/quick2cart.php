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
$document = JFactory::getDocument();

if (!defined('DS'))
{
	define('DS', '/');
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_quick2cart'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
comquick2cartHelper::defineIcons("ADMIN");
// Lib load
if (JVERSION < '3.0')
{
	// Define wrapper class
	define('Q2C_WRAPPER_CLASS', "q2c-wrapper techjoomla-bootstrap");

	// Other
	JHtml::_('behavior.tooltip');
}
else
{
	// Define wrapper class
	define('Q2C_WRAPPER_CLASS', "q2c-wrapper");

	// Tabstate
	JHtml::_('behavior.tabstate');

	// Other
	JHtml::_('behavior.tooltip');

	// Bootstrap tooltip and chosen js
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
}

require_once JPATH_COMPONENT . DS . 'controller.php';
require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'products.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'storeHelper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'product.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'zoneHelper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'taxHelper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'qtcshiphelper.php';

$jinput   = JFactory::getApplication()->input;
$params   = JComponentHelper::getParams('com_quick2cart');

// Load assets
comquick2cartHelper::loadQuicartAssetFiles();

// When All products menu not present.
$multivendor_enable = $params->get('multivendor');

if (!empty($multivendor_enable))
{
	$link = 'index.php?option=com_quick2cart&view=category';
	$db   = JFactory::getDBO();
	$query = "SELECT id FROM #__menu WHERE link LIKE '%" . $link . "%' AND published = 1 LIMIT 1";
	$db->setQuery($query);
	$items = $db->loadResult();

	if (empty($items))
	{
		$link    = JText::_('COM_QUICK2CART_ALLPRODUCTSMENU');
		$not_msg = JText::sprintf('VANITY_REQ_MENU_WARNING', $link);
		JError::raiseNotice(100, $not_msg);

	}
}

// Backend css
$document->addStyleSheet(JUri::base(true) . '/components/com_quick2cart/assets/css/quick2cart.css');

// Responsive tables
$document->addStyleSheet(JUri::root(true) . '/components/com_quick2cart/assets/css/q2c-tables.css');

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Quick2cart');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
