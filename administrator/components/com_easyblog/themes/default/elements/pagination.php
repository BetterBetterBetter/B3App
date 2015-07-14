<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select name="<?php echo $name;?>" default="<?php echo $default;?>">
    <option value="-1"><?php echo JText::_('COM_EASYBLOG_INHERIT_FROM_JOOMLA');?></option>
    <option value="-2"><?php echo JText::_('COM_EASYBLOG_INHERIT_FROM_SETTINGS');?></option>
    <option value="5">5</option>
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="20">20</option>
    <option value="25">25</option>
    <option value="30">30</option>
    <option value="50">50</option>
    <option value="100">100</option>
</select>