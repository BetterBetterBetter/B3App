<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row form-horizontal">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSING_EDITOR');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSING_EDITOR_INFO');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group" data-composer-editors>
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('form.editors', 'layout_editor', $this->config->get('layout_editor'), true); ?>
					</div>
				</div>
			</div>

		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_PANELS'); ?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_PANELS_INFO');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_BLOG_IMAGE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_BLOG_IMAGE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_BLOG_IMAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_dashboard_blogimage', $this->config->get('layout_dashboard_blogimage')); ?>
					</div>
				</div>
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SEO'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SEO'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SEO_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_dashboardseo', $this->config->get('layout_dashboardseo')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_LAYOUT_DASHBOARD_SHOW_TAGS_LISTING'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_LAYOUT_DASHBOARD_SHOW_TAGS_LISTING'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_LAYOUT_DASHBOARD_SHOW_TAGS_LISTING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'dashboard_tags_listing', $this->config->get('dashboard_tags_listing')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_LAYOUT_DASHBOARD_MAX_TAGS_ALLOWED'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_LAYOUT_DASHBOARD_MAX_TAGS_ALLOWED'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_LAYOUT_DASHBOARD_MAX_TAGS_ALLOWED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-lg-3">
								<input type="text" name="max_tags_allowed" class="form-control text-center" value="<?php echo $this->config->get('max_tags_allowed', '' );?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_TEMPLATES');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_TEMPLATES_DESC');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_ENABLE_TEMPLATES'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_ENABLE_TEMPLATES'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_ENABLE_TEMPLATES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'composer_templates', $this->config->get('composer_templates')); ?>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_TITLE'); ?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_INFO');?></div>
			</div>

			<div class="panel-body">
				<p><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_AUTOFILL_REQUIRES_API_KEY', '/administrator/index.php?option=com_easyblog&view=settings&layout=system');?></p>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_TAGS_ENABLE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_TAGS_ENABLE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_TAGS_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_autofill_tags', $this->config->get('main_autofill_tags'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_KEYWORDS_ENABLE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_KEYWORDS_ENABLE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOFILL_KEYWORDS_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
							<?php echo $this->html('grid.boolean', 'main_autofill_keywords', $this->config->get('main_autofill_keywords'));?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_TITLE'); ?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_INFO');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_ENABLE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_ENABLE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_autodraft', $this->config->get('main_autodraft'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_INTERVAL'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_INTERVAL'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOSAVE_INTERVAL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" size="10" name="main_autodraft_interval" class="form-control text-center" value="<?php echo $this->config->get('main_autodraft_interval', '0' );?>" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_SECONDS');?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE'); ?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE_INFO');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE_ENABLE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE_ENABLE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_keepalive', $this->config->get('main_keepalive'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE_INTERVAL'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE_INTERVAL'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_KEEPALIVE_INTERVAL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" size="10" name="main_keepalive_interval" class="form-control text-center" value="<?php echo $this->config->get('main_keepalive_interval', '0' );?>" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_SECONDS');?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
