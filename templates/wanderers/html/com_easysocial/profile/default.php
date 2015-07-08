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
<div class="es-profile userProfile" data-id="<?php echo $user->id;?>" data-profile>
<div class="row">
	<div class="col-md-9" data-profile-contents>
		<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'aboveHeader' , array( $user ) ); ?>
		<?php echo $this->render( 'module' , 'es-profile-before-header' ); ?>
		<!-- Include cover section -->
		<?php echo $this->includeTemplate( 'site/profile/default.header' ); ?>
		<?php echo $this->render( 'module' , 'es-profile-after-header' ); ?>

		<div class="row">
			<div class="col-md-9">
				<div class="es-content-head">
					<ol class="breadcrumb">
						<li class="active"><?php echo $user->getName();?>'s Feed</li>
					</ol>
				</div>
				<div class="es-content-body">
					<i class="loading-indicator fd-small"></i>
					<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'aboveStream' , array( $user ) ); ?>
					<?php echo $this->render( 'module' , 'es-profile-before-contents' ); ?>
					<div data-profile-real-content>
					<?php echo $contents; ?>
					</div>
					<?php echo $this->render( 'module' , 'es-profile-after-contents' ); ?>
				</div>
			</div>
			<div class="col-md-3">
				<?php echo $this->render( 'module' , 'es-profile-sidebar-top' ); ?>
				<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'sidebarTop' , array( $user ) ); ?>

				<div class="panel panel-default">
					<div class="panel-heading">
						<b>
							<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_APPS_HEADING' );?>
						</b>

						<?php if( $user->isViewer() ){ ?>
						<a class="panel-option" href="<?php echo FRoute::apps();?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_BROWSE' ); ?>">
							<i class="fa fa-plus"></i>
						</a>
						<?php } ?>
					</div>

					<div class="panel-body">
						<ul class="panel-menu" data-profile-apps>

							<li
								data-profile-apps-item
								data-layout="custom"
							>
								<a href="<?php echo FRoute::profile(array('id' => $user->getAlias(), 'layout' => 'about')); ?>" data-info <?php if (!empty($infoSteps)) { ?>data-loaded="1"<?php } ?>>
									<i class="icon-es-aircon-user mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_PROFILE_ABOUT'); ?>
								</a>
							</li>

							<?php if (!empty($infoSteps)) { ?>
								<?php foreach ($infoSteps as $step) { ?>
									<?php if (!$step->hide) { ?>
									<li
										class="<?php if ($step->active) { ?>active<?php } ?>"
										data-profile-apps-item
										data-layout="custom"
									>
										<a class="ml-20" href="<?php echo $step->url; ?>" title="<?php echo $step->title; ?>" data-info-item data-info-index="<?php echo $step->index; ?>">
											<i class="ies-info ies-small mr-5"></i> <?php echo $step->title; ?>
										</a>
									</li>
									<?php } ?>
								<?php } ?>
							<?php } ?>
							
							<li class="<?php echo !$activeApp ? 'active' : '';?>"
								data-layout="embed"
								data-id="<?php echo $user->id;?>"
								data-namespace="site/controllers/profile/getStream"
								data-embed-url="<?php echo FRoute::profile( array( 'id' => $user->getAlias() ) );?>"
								data-profile-apps-item
								>
								<a href="javascript:void(0);">
									<i class="icon-es-genius mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_TIMELINE' );?>
								</a>
							</li>
							<?php if( $apps ){ ?>
								<?php foreach( $apps as $app ){ ?>
									<?php $app->loadCss(); ?>
										<li class="app-item<?php echo $activeApp == $app->id ? ' active' : '';?>"
											data-app-id="<?php echo $app->id;?>"
											data-id="<?php echo $user->id;?>"
											data-layout="<?php echo $app->getViews( 'profile' )->type; ?>"
											data-namespace="site/controllers/profile/getAppContents"
											data-canvas-url="<?php echo FRoute::apps( array( 'id' => $app->getAlias() , 'layout' => 'canvas' , 'userid' => $user->getAlias() ) );?>"
											data-embed-url="<?php echo FRoute::profile( array( 'id' => $user->getAlias() , 'appId' => $app->getAlias() ) );?>"
											data-title="<?php echo $app->get( 'title' ); ?>"
											data-profile-apps-item
										>
											<a href="javascript:void(0);">
												<img src="<?php echo $app->getIcon();?>" class="app-icon-small mr-5" /> <?php echo $app->get( 'title' ); ?>
											</a>
										</li>
								<?php } ?>
							<?php } ?>
						</ul>
					</div>
				</div>

				
				
				<?php echo $this->render( 'module' , 'es-profile-sidebar-bottom' ); ?>
			</div>
		</div>
	</div>

	<div class="col-md-3" >
		<?php echo $this->render( 'module' , 'es-profile-sidebar-after-apps' ); ?>

		<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'sidebarBottom' , array( $user ) ); ?>
	</div>
</div>
</div>
