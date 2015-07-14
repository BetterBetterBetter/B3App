<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

/**
 * This file define the icon set for admin views
 * */
// No direct access.
defined('_JEXEC') or die();

// Define constants
if (JVERSION < '3.0')
{
	// Icon constants.
	define('QTC_ICON_CHECKMARK', " icon-ok-sign");
	define('QTC_ICON_MINUS', " icon-minus");
	define('QTC_ICON_PLUS', " icon-plus-sign");

	// ICON old : icon-pencil-2
	define('QTC_ICON_EDIT', " icon-apply ");
	define('QTC_ICON_CART', " icon-shopping-cart");
	define('QTC_ICON_BACK', " icon-arrow-left");
	define('QTC_ICON_REMOVE', " icon-remove");
	define('Q2C_TOOLBAR_ICON_SETTINGS', "icon-cog");
}
else
{
	// Icon constants.
	define('QTC_ICON_CHECKMARK', " icon-checkmark");
	define('QTC_ICON_MINUS', " icon-minus-2");
	define('QTC_ICON_PLUS', " icon-plus-2");
	define('QTC_ICON_EDIT', " icon-pencil-2");
	define('QTC_ICON_CART', " icon-cart");
	define('QTC_ICON_BACK', " icon-arrow-left-2");
	define('QTC_ICON_REMOVE', " icon-cancel-2");
	define('Q2C_TOOLBAR_ICON_SETTINGS', "icon-cog");
}
