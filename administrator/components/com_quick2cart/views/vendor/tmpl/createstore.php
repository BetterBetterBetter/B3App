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

$lang = JFactory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$comquick2cartHelper = new comquick2cartHelper;

$path = $comquick2cartHelper->getViewpath('vendor', 'createstore', 'ADMINISTRATOR', 'SITE');
ob_start();
include($path);
$html = ob_get_contents();
ob_end_clean();
echo $html;

return;
