<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#general" data-form-tabs>
                    <i class="fa fa-wrench"></i>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_GENERAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#posts" data-form-tabs>
                    <i class="fa fa-th-large"></i>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_POST_OPTION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#pagination" data-form-tabs>
                    <i class="fa fa-table"></i>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_PAGINATION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#truncation" data-form-tabs>
                    <i class="fa fa-scissors"></i>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_TRUNCATION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#avatars" data-form-tabs>
                    <i class="fa fa-picture-o"></i>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_AVATARS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#layouttoolbar" data-form-tabs>
                    <i class="fa fa-columns"></i>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_TOOLBAR');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#cover" data-form-tabs>
                    <i class="fa fa-file-image-o"></i>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_POST_COVER');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="general" class="tab-pane active in">
            <?php echo $this->output('admin/settings/layout/general'); ?>
        </div>

        <div id="posts" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/posts'); ?>
        </div>

        <div id="pagination" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/pagination'); ?>
        </div>

        <div id="truncation" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/truncation'); ?>
        </div>

        <div id="avatars" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/avatars'); ?>
        </div>

        <div id="layouttoolbar" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/toolbar'); ?>
        </div>

        <div id="cover" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/cover'); ?>
        </div>

    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="layout" />
</form>
