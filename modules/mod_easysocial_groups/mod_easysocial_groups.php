<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main engine
$engine = JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

jimport('joomla.filesystem.file');

if (!JFile::exists($engine)) {
	return;
}

// Include the engine file.
require_once($engine);

FD::language()->loadSite();

// Check if Foundry exists
if (!FD::exists()) {
	return;
}

$my = FD::user();

// If module is configured to display groups from logged in user, ensure that the user is logged in
if ($params->get('filter') == '3' && $my->guest) {
    return;
}

// Load up helper file
require_once(dirname(__FILE__) . '/helper.php');

$groups = EasySocialModGroupsHelper::getGroups($params);

if (!$groups) {
	return;
}

// Load up the module engine
$modules = FD::modules('mod_easysocial_groups');

// We need foundryjs here
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();

// We need these packages
$modules->addDependency('css', 'javascript');

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

require(JModuleHelper::getLayoutPath('mod_easysocial_groups', $layout));
