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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form method="post" action="<?php echo JRoute::_( 'index.php' );?>" data-profile-notifications-form class="form-horizontal">
<div class="view-edit_notifications" data-edit-notification>
	<div class="row">
		<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
			<i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
		</a>
		<div class="col-md-3" data-sidebar>
			<?php echo $this->render( 'module' , 'es-profile-editnotifications-sidebar-top' ); ?>

			<?php $i = 0; ?>
			<?php foreach( array( 'system', 'others' ) as $group ) {
				if( isset( $alerts[ $group ] ) ) {
			?>
			<section class="panel panel-default">
				<div class="panel-heading">
					<b><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_SIDEBAR_NOTIFICATIONS_GROUP_' . strtoupper( $group ) );?></b>
				</div>

				<div class="panel-body">
					<ul class="panel-menu">
					<?php

						foreach( $alerts[$group] as $element => $alert ) { ?>
						<?php $active = $i == 0 ? 'class="active"' : ''; ?>

						<li data-notification-item data-alert-element="<?php echo $element; ?>" <?php echo $active; ?>>
							<a href="javascript:void(0);"><?php echo $alert['title']; ?></a>
						</li>

						<?php $i++; ?>
					<?php
						}
					?>
					</ul>
				</div>
			</section>
			<?php
			 		}
				}
			?>

			<?php echo $this->render( 'module' , 'es-profile-editnotifications-sidebar-bottom' ); ?>
		</div>

		<div class="col-md-9">
			<div class="panel panel-default panel-content">
				<div class="panel-body">
					<?php echo $this->render( 'module' , 'es-profile-editnotifications-before-contents' ); ?>
					<div class="eb-notification-content tab-content notification-content form-notifications">
					<?php $i = 0; ?>
					<?php foreach( array( 'system', 'others' ) as $group ) {
							if( isset( $alerts[$group] ) ) {
					?>
						<?php foreach( $alerts[$group] as $element => $alert ) { ?>

							<?php $display = $i > 0 ? 'style="display: none;"' : ''; ?>
							<div class="notification-content-<?php echo $element; ?>" data-notification-content data-alert-element="<?php echo $element; ?>" <?php echo $display; ?>>
								<h4><?php echo $alert[ 'title' ];?></h4>

									<div class="row-table cell-hidden-mobile">
										<div class="col-cell">&nbsp;</div>
										<div class="col-cell" style="width: 1%">&nbsp;</div>
										<div class="col-cell cell-hidden-mobile" style="width: 20%"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_SYSTEM'); ?></div>
										<div class="col-cell cell-hidden-mobile" style="width: 20%"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_EMAIL'); ?></div>
									</div>
									<?php foreach( $alert[ 'data' ] as $rule ) { ?>
									<div class="row-table">
										<div class="col-cell cell-hidden-mobile"><span class="fd-small"><?php echo $rule->getTitle(); ?></span></div>
										<div class="col-cell" style="width: 1%">
											<i class="icon-es-help cell-hidden-mobile" <?php echo $this->html( 'bootstrap.popover' , $rule->getTitle() , $rule->getDescription()  , 'bottom' ); ?>></i>
											<!-- <hr class="cell-visible-mobile" /> -->
											<h5 class="cell-visible-mobile"><?php echo $rule->getTitle(); ?></h5>
											<p class="cell-visible-mobile"><?php echo $rule->getDescription(); ?></p>
										</div>
										<div class="col-cell" style="width: 20%">
											<p class="cell-visible-mobile"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_SYSTEM'); ?></p>
											<?php echo $rule->system >= 0 ? $this->html( 'grid.boolean', 'system[' . $rule->id . ']', $rule->system ) : JText::_( 'COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE' ); ?>
										</div>
										<div class="col-cell" style="width: 20%">
											<p class="cell-visible-mobile"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_EMAIL'); ?></p>
											<?php echo $rule->email >= 0 ? $this->html( 'grid.boolean', 'email[' . $rule->id .']', $rule->email ) : JText::_( 'COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE' ); ?>
										</div>
									</div>
									<?php } ?>
							</div>

							<?php $i++; ?>
						<?php } ?>
					<?php
							}
						}
					?>
					</div>

					<div class="form-actions">
						<div class="pull-right">
							<button class="btn btn-sm btn-es-primary" data-profile-notifications-save><?php echo JText::_( 'COM_EASYSOCIAL_SAVE_BUTTON' );?></button>
						</div>
					</div>
					<?php echo $this->render( 'module' , 'es-profile-editnotifications-after-contents' ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="profile" />
<input type="hidden" name="task" value="saveNotification" />
<input type="hidden" name="<?php echo Foundry::token();?>" value="1" />
</form>
