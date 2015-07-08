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
<div class="panel panel-default">
	<div class="panel-heading">
		<b><?php echo JText::_( 'COM_EASYSOCIAL_APPS_SIDEBAR_TITLE' );?></b>

		<a class="panel-option" href="<?php echo FRoute::apps();?>">
			<i class="fa fa-plus"></i>
		</a>
	</div>

	<div class="panel-body">

		<?php if( $apps ){ ?>
			<ul class="panel-menu compact list-unstyled" data-dashboard-apps>
				<?php foreach( $apps as $app ){ ?>
					<li class="app-item<?php echo $activeApp == $app->id ? ' active' : '';?>"
						data-id="<?php echo $app->id;?>"
						data-layout="<?php echo $app->getViews( 'dashboard' )->type; ?>"
						data-canvas-url="<?php echo FRoute::apps( array( 'id' => $app->getAlias() , 'layout' => 'canvas' ) );?>"
						data-embed-url="<?php echo FRoute::dashboard( array( 'appId' => $app->getAlias() ) );?>"
						data-title="<?php echo $this->html( 'string.escape' , $user->getName() ) . ' - ' . $app->get( 'title' ); ?>"
						data-dashboardSidebar-menu
						data-profile-apps-item
						data-dashboardApps-item>
						<a href="javascript:void(0);">
							<i class="muted fa fa-file icon-<?php echo $app->element;?>"></i> <?php echo JText::_($app->title); ?>
							<div class="label label-notification pull-right mr-10"></div>
						</a>
					</li>
				<?php } ?>
			</ul>
		<?php } else { ?>
		<div class="fd-small">
			<?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_NO_APPS_INSTALLED_YET' ); ?>
		</div>
		<?php } ?>
	</div>
</div>
