<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-container" data-profile-edit>
	<div class="row">
			<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
				<i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE');?>
			</a>
			<div class="col-md-3" data-sidebar>

				<?php echo $this->render('module' , 'es-profile-edit-sidebar-top'); ?>

				<div class="panel panel-default">
					<div class="panel-heading">
						<b><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_ABOUT');?></b>
					</div>

					<div class="panel-body">
						<ul class="panel-menu list-unstyled">
							<?php $i = 0; ?>
							<?php foreach ($steps as $step){ ?>
								<li data-for="<?php echo $step->id;?>" class="step-item<?php echo $i == 0 ? ' active' :'';?>" data-profile-edit-fields-step>
									<a href="javascript:void(0);"><?php echo $step->get('title'); ?></a>
								</li>
								<?php $i++; ?>
							<?php } ?>
						</ul>
					</div>
				</div>

				<?php if ($showSocialTabs){ ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<b><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_SOCIALIZE');?></b>
					</div>

					<div class="panel-body">
						<ul class="panel-menu list-unstyled">
							<?php if ($associatedFacebook){ ?>
							<li data-for="facebook" data-profile-edit-fields-step data-profile-edit-facebook>
								<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_SOCIALIZE_FACEBOOK');?></a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>

				<?php if ($this->my->deleteable()){ ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<b><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_DELETE');?></b>
					</div>

					<div class="panel-body">
						<a href="javascript:void(0);" class="fd-small" data-profile-edit-delete><?php echo JText::_('COM_EASYSOCIAL_DELETE_YOUR_PROFILE_BUTTON');?></a>
					</div>
				</div>
				<?php } ?>

				<?php echo $this->render('module' , 'es-profile-edit-sidebar-bottom'); ?>
			</div>

			<section class="col-md-9">
				<div class="form-body" data-profile-edit-fields>
					<?php echo $this->render('module' , 'es-profile-edit-before-contents'); ?>

					<form method="post" action="<?php echo JURI::root(); ?>" class="form-horizontal" data-profile-fields-form>
						<article class="form-content">
							<div class="tab-content profile-content">
								<?php $i = 0; ?>
								<?php foreach ($steps as $step){ ?>
								<div class="step-content step-<?php echo $step->id;?> <?php if ($i == 0) { ?>active<?php }?>"
									data-profile-edit-fields-content data-id="<?php echo $step->id; ?>"
								>
									<?php if ($step->fields){ ?>
										<?php foreach ($step->fields as $field){ ?>
											<?php if (!empty($field->output)) { ?>
											<div data-profile-edit-fields-item data-element="<?php echo $field->element; ?>" data-id="<?php echo $field->id; ?>" data-required="<?php echo $field->required; ?>" data-fieldname="<?php echo SOCIAL_FIELDS_PREFIX . $field->id; ?>">
												<?php echo $field->output; ?>
											</div>
											<hr>
											<?php } ?>
										<?php } ?>
									<?php } ?>
								</div>
								<?php $i++; ?>
								<?php } ?>

								<?php if ($associatedFacebook) { ?>
								<div class="step-content step-facebook" data-profile-edit-fields-content data-id="facebook">
									<div class="form-social social-integrations">
										<legend class="es-legend"><?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_INTEGRATIONS');?></legend>
										<div class="es-desp">
											<?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_INTEGRATIONS_ASSOCIATED');?>
										</div>

										<?php if (isset($fbUserMeta[ 'avatar' ]) && isset($fbUserMeta[ 'link' ]) && isset($fbUserMeta[ 'username' ])){ ?>
										<div class="es-avatar-wrapper">
											<div class="es-avatar pull-left">
												<img src="<?php echo $fbUserMeta['avatar'];?>" width="16" />
											</div>
											<div class="es-username">
												<a href="<?php echo $fbUserMeta['link'];?>" target="_blank" class="label label-info"><?php echo $fbUserMeta['username']; ?></a>
											</div>
										</div>
										<?php } ?>

										<ul class="yesno-list mb-20">
											<?php if ($this->config->get('oauth.facebook.pull')){ ?>
											<!-- <li>
												<div class="yesno-item pull-left fd-small">
													<?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_INTEGRATIONS_PULL_STREAM_ITEMS');?>
												</div>
												<div class="pull-right">
													<?php echo $this->html('grid.boolean' , 'oauth.facebook.pull' , $fbOAuth->pull); ?>
												</div>
											</li> -->
											<?php } ?>

											<?php if ($this->config->get('oauth.facebook.push')){ ?>
											<li>
												<div class="yesno-item pull-left fd-small">
													<?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_INTEGRATIONS_PUSH_STREAM_ITEMS');?>
												</div>
												<div class="pull-right">
													<?php echo $this->html('grid.boolean' , 'oauth.facebook.push' , $fbOAuth->push , 'push' , array('data-oauth-facebook-push=""')); ?>
												</div>
											</li>
											<?php } ?>
										</ul>

										<legend class="es-legend"><?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_REVOKE_ACCESS');?></legend>
										<?php echo $facebookClient->getRevokeButton(FRoute::profile(array('layout' => 'edit' , 'external' => true)));?>
									</div>
								</div>
								<?php } ?>
							</div>
						</article>
						<footer class="form-actions text-right">
							<button type="button" class="btn btn-medium btn-es-primary" data-profile-fields-save>
								<?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON');?>
							</button>
						</footer>

						<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid');?>" />
						<input type="hidden" name="option" value="com_easysocial" />
						<input type="hidden" name="controller" value="profile" />
						<input type="hidden" name="task" value="save" />
						<input type="hidden" name="<?php echo Foundry::token();?>" value="1" />

						<input type="hidden" name="associatedFacebook" value="<?php echo $associatedFacebook ? 1 : ''; ?>" />
					</form>

					<?php echo $this->render('module' , 'es-profile-edit-after-contents'); ?>
				</div>
			</section>
	</div>
</div>
