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

if (JVERSION >= '1.6.0')
{
	$core_js = JUri::root().'media/system/js/core.js';
	$flg = 0;

	foreach ($document->_scripts as $name => $ar)
	{
		if ($name == $core_js )
		{
			$flg = 1;
		}
	}

	if($flg == 0)
	{
		echo "<script type='text/javascript' src='".$core_js."'></script>";
	}
}
// @TODO use GET view path funtion
ob_start();
include(JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'views' . DS . 'orders' . DS . 'tmpl' . DS . 'invoice.php');
$html = ob_get_contents();
ob_end_clean();
echo $html;
