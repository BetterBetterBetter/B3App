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

<?php if ($this->template->get('dashboard_login_guests')) { ?>
	<?php echo $this->includeTemplate('site/dashboard/default.guests.login'); ?>
<?php } ?>
 
<div class="es-dashboard es-container" data-dashboard>
	<div class="row">
		<div class="col-md-3">
			<a href="javascript:void(0);" class="btn btn-block btn-es-toggle btn-sidebar-toggle" data-sidebar-toggle>
				<i class="fa fa-bars"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
			</a>	

			<div class="es-sidebar" data-sidebar data-dashboard-sidebar>
				<?php echo $this->render( 'module' , 'es-dashboard-sidebar-top' ); ?>
				<?php echo $this->render( 'module' , 'es-unity-sidebar-top' , 'site/dashboard/sidebar.module.wrapper' , array( 'style' => 'es-widget' ) ); ?>

				<div class="panel panel-default">
					<div class="panel-heading">
						<b><?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS' );?></b>
					</div>

					<div class="panel-body">
						<ul class="panel-menu" data-dashboard-feeds>

							<li class="<?php echo $filter == 'everyone' ? ' active' : '';?>"
								data-dashboardSidebar-menu
								data-dashboardFeeds-item
								data-type="everyone"
								data-id=""
								data-url="<?php echo FRoute::dashboard( array( 'type' => 'everyone' ) );?>"
								data-title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_DASHBOARD_EVERYONE' , true ); ?>"
							>
								<a href="javascript:void(0);">
									<i class="ies-earth mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS_EVERYONE' );?>
									<div class="label label-notification pull-right mr-20" data-stream-counter-everyone>0</div>
								</a>
							</li>

						</ul>
					</div>
				</div>

				<?php echo $this->render( 'module' , 'es-dashboard-sidebar-after-newsfeeds' ); ?>
				<?php echo $this->render( 'module' , 'es-unity-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper' , array( 'style' => 'es-widget' ) ); ?>
				<?php echo $this->render( 'module' , 'es-dashboard-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper' ); ?>
			</div>
		</div>

		<div class="col-md-9" data-dashboard-content>
			<div class="es-content-body">
				<i class="loading-indicator fd-small"></i>
				<?php echo $this->render( 'module' , 'es-dashboard-before-contents' ); ?>

				<div data-dashboard-real-content>
					<div data-unity-real-content>
						<?php echo $stream->html( false, JText::_( 'COM_EASYSOCIAL_UNITY_STREAM_LOGIN_TO_VIEW' ) ); ?>

						<?php if( Foundry::user()->id == 0 ) { ?>
							<div class="pull-right">
								<a href="<?php echo FRoute::login( array() , false ); ?>"><?php echo JText::_( 'COM_EASYSOCIAL_UNITY_STREAM_LOGIN' ); ?></a>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php echo $this->render( 'module' , 'es-dashboard-after-contents'); ?>
			</div>
		</div>
	</div>
</div>
