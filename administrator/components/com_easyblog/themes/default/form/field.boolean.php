<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$booleanKey = ($prefix) ? $prefix . $field->attributes->name : 'params[' . $field->attributes->name . ']';

?>
<div class="form-group">
    <label for="page_title" class="col-md-5">
        <?php echo JText::_('COM_EASYBLOG_SHOW_INTROTEXT'); ?>

        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SHOW_INTROTEXT'); ?>"
            data-content="<?php echo JText::_('COM_EASYBLOG_SHOW_INTROTEXT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
    </label>

    <div class="col-md-7">
        <?php echo $this->html('grid.boolean', $booleanKey, $params->get($prefix . $field->attributes->name));?>
    </div>
</div>
