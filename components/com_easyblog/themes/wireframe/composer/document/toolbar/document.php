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
?>
<div class="eb-composer-toolbar-set row-table is-primary" data-name="document">

    <div class="eb-composer-toolbar-group row-table">
        <div class="col-cell cell-tight toolbar-left">
            <div class="eb-composer-toolbar-group row-table">
                <div class="eb-composer-toolbar-item is-button col-cell eb-composer-add-block-button" data-eb-composer-add-block-button>
                    <i class="fa fa-cube"></i>
                    <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_BLOCK');?></span>
                </div>
                <div class="eb-composer-toolbar-item is-button col-cell eb-composer-add-media-button" data-eb-composer-add-media-button>
                    <i class="fa fa-camera"></i>
                    <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_MEDIA');?></span>
                </div>
                <div class="eb-composer-toolbar-item is-button col-cell eb-composer-add-post-button" data-eb-composer-add-post-button>
                    <i class="fa fa-file-text"></i>
                    <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_POST');?></span>
                </div>
                <div class="eb-composer-toolbar-item is-button col-cell eb-composer-toggle-sidebar mobile-show" style="padding: 0 20px;" data-eb-composer-show-drawer-button>
                    <i class="fa fa-cog mobile-show" style="font-size: 15px;"></i>
                </div>
            </div>
        </div>

        <div class="col-cell toolbar-center mobile-hide">&nbsp;</div>

        <div class="col-cell cell-tight toolbar-right mobile-hide">
            <div class="eb-composer-toolbar-group row-table">
                <div class="eb-composer-toolbar-item is-button col-cell eb-document-add-cover-button <?php echo $post->hasImage() ? ' has-cover' : '';?>" data-eb-composer-meta-button data-id="cover">
                    <i class="fa fa-image"></i>
                    <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_COVER');?></span>
                    <span style="display: none;"><?php echo JText::_('COM_EASYBLOG_COMPOSER_COVER');?></span>
                </div>

                <?php if ($this->config->get('main_locations') && EB::location($this->config->get('location_service_provider'))->isSettingsComplete()) { ?>
                <div class="eb-composer-toolbar-item is-button col-cell eb-document-add-location-button <?php echo $post->hasLocation() ? ' has-location' : '';?>" data-eb-composer-meta-button data-id="location">
                    <i class="fa fa-map-marker"></i>
                    <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_LOCATION');?></span>
                    <span style="display: none"><?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION');?></span>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- display on mobile view only -->
    <div class="eb-composer-toolbar-group row-table mobile-show">
        <div class="eb-composer-toolbar-item is-button col-cell eb-document-add-cover-button <?php echo $post->hasImage() ? ' has-cover' : '';?>" data-eb-composer-meta-button data-id="cover">
            <i class="fa fa-image"></i>
            <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_COVER');?></span>
            <span style="display: none;"><?php echo JText::_('COM_EASYBLOG_COMPOSER_COVER');?></span>
        </div>

        <?php if ($this->config->get('main_locations') && EB::location($this->config->get('location_service_provider'))->isSettingsComplete()) { ?>
        <div class="eb-composer-toolbar-item is-button col-cell eb-document-add-location-button <?php echo $post->hasLocation() ? ' has-location' : '';?>" data-eb-composer-meta-button data-id="location">
            <i class="fa fa-map-marker"></i>
            <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_LOCATION');?></span>
            <span style="display: none"><?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION');?></span>
        </div>
        <?php } ?>
    </div>
</div>

<div class="eb-float-menu">
    <div class="eb-float-btn-wrap pull-left">
        <label class="eb-float-btn eb-float-publish" data-eb-composer-publish-post-button>
            <?php echo JText::_('COM_EASYBLOG_PUBLISH_POST');?>
        </label>
    </div>

    <!-- div class="eb-float-btn-wrap pull-left" data-eb-composer-launcher-close-button>
        <label class="eb-float-btn eb-float-close">
            <i></i>
        </label>
    </div -->

    <div class="eb-float-btn-wrap pull-right" data-eb-composer-show-drawer-button>
        <label class="eb-float-btn eb-float-settings">
            <i class="fa fa-cog"></i>
        </label>
    </div>

    <div class="eb-float-btn-wrap pull-right">
        <label class="eb-float-btn eb-float-toggle" for="menu-open" data-eb-composer-mobile-blip>
            <i></i>
        </label>

        <div class="eb-float-links">
            <div class="eb-float-link eb-link-block" data-eb-composer-add-block-button>
                <i class="fa fa-cube"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_BLOCK');?></span>
            </div>

            <div class="eb-float-link eb-link-media" data-eb-composer-add-media-button>
                <i class="fa fa-camera"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_MEDIA');?></span>
            </div>

            <div class="eb-float-link eb-link-post" data-eb-composer-add-post-button>
                <i class="fa fa-file-text"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_POST');?></span>
            </div>

            <div class="eb-float-link eb-link-cover<?php echo $post->hasImage() ? ' has-cover' : '';?>" data-eb-composer-meta-button data-id="cover">
                <i class="fa fa-image"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_COVER');?></span>
            </div>

            <?php if ($this->config->get('main_locations') && EB::location($this->config->get('location_service_provider'))->isSettingsComplete()) { ?>
            <div class="eb-float-link eb-link-location<?php echo $post->hasLocation() ? ' has-location' : '';?>" data-eb-composer-meta-button data-id="location">
                <i class="fa fa-map-marker"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_LOCATION');?></span>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
