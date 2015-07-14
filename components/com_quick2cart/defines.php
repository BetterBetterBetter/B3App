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

/**
 * This file define the icon set for admin views
 * */

/* J2.5.x */
if (JVERSION < '3.0')
{
	define('Q2C_ICON_HOME', " icon-home");
	define('QTC_ICON_CHECKMARK', "icon-publish icon-ok-sign");
	define('QTC_ICON_MINUS', " icon-minus");
	define('QTC_ICON_PLUS', " icon-plus-sign");
	define('QTC_ICON_EDIT', " icon-edit");
	define('QTC_ICON_CART', " icon-white icon-shopping-cart");
	define('QTC_ICON_BACK', " icon-arrow-left");
	define('QTC_ICON_REMOVE', " icon-remove");
	define('QTC_ICON_LIST', " icon-list");
}
else
{
	define('Q2C_ICON_HOME', " icon-home");
	define('QTC_ICON_CHECKMARK', " icon-publish icon-ok-sign");
	define('QTC_ICON_MINUS', " icon-minus-2	");
	define('QTC_ICON_PLUS', " icon-plus-2");
	define('QTC_ICON_EDIT', " icon-apply icon-pencil-2 icon-edit"); // icon-pencil-2 // icon-edit
	define('QTC_ICON_CART', " icon-cart qtc_icon-shopping-cart qtc_icon-white");
	define('QTC_ICON_BACK', " icon-arrow-left-2");
	define('QTC_ICON_REMOVE', " icon-remove");
	define('QTC_ICON_LIST', " icon-list");
}

$icon_color = " icon-white ";

if (version_compare(JVERSION, '3.0', 'lt'))
{
	$icon_color='';
}

/* Define toolbar icon classes*/
/* J2.5.x */
if (JVERSION < '3.0')
{
	define('Q2C_TOOLBAR_ICON_HOME', "icon-home");
	define('Q2C_TOOLBAR_ICON_SETTINGS', "icon-cog");
	define('Q2C_TOOLBAR_ICON_LIST', "icon-list");
	define('Q2C_TOOLBAR_ICON_PLUS', "icon-new icon-plus-sign");
	define('Q2C_TOOLBAR_ICON_CART', "icon-shopping-cart");
	define('Q2C_TOOLBAR_ICON_USERS', "icon-user");
	define('Q2C_TOOLBAR_ICON_COUPONS', "icon-gift");
	define('Q2C_TOOLBAR_ICON_PAYOUTS', "icon-briefcase");
}
else
{
	define('Q2C_TOOLBAR_ICON_HOME', "icon-home");
	define('Q2C_TOOLBAR_ICON_SETTINGS', "icon-cog");
	define('Q2C_TOOLBAR_ICON_LIST', "icon-list");
	define('Q2C_TOOLBAR_ICON_PLUS', "icon-new icon-plus-sign");
	define('Q2C_TOOLBAR_ICON_CART', "icon-cart qtc_icon-shopping-cart");
	define('Q2C_TOOLBAR_ICON_USERS', "icon-user");
	define('Q2C_TOOLBAR_ICON_COUPONS', "icon-gift");
	define('Q2C_TOOLBAR_ICON_PAYOUTS', "icon-briefcase");
}
