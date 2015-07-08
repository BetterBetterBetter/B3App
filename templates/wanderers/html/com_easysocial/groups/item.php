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
<div class="view-group_page" data-es-group-item data-id="<?php echo $group->id;?>">

	<!-- Group Header -->
	<?php echo $this->loadTemplate( 'site/groups/item.header' , array( 'group' => $group ) ); ?>

	<div class="es-container">
		<div class="row">
			<div class="col-md-3" >
				<a href="javascript:void(0);" class="btn btn-block btn-es-toggle btn-sidebar-toggle" data-sidebar-toggle>
					<i class="fa fa-bars"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
				</a>

				<div class="es-sidebar" data-sidebar>
					<?php echo $this->render( 'module' , 'es-groups-sidebar-top' ); ?>
					<?php echo $this->render( 'widgets' , SOCIAL_TYPE_GROUP , 'groups' , 'sidebarTop' , array( 'uid' => $group->id , 'group' => $group ) ); ?>

					<section class="panel panel-default">
						<header class="panel-heading">
							<b><?php echo JText::_( 'COM_EASYSOCIAL_GROUP_TIMELINE' );?></b>

							<a class="panel-option" href="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() , 'type' => 'filterForm' ) );?>" data-stream-filter-button>
								<i class="fa fa-plus"></i>
								<?php // echo JText::_( 'COM_EASYSOCIAL_GROUP_FEED_ADD_FILTER' ); ?>
							</a>
						</header>
						<article class="panel-body">
							<ul class="panel-menu" data-es-grpup-ul >
								<li class="<?php echo ( !$appId && !$filterId && !$hashtag ) ? 'active' : '';?>"
									data-es-group-filter
									data-dashboardSidebar-menu
									data-type="<?php echo  SOCIAL_TYPE_GROUP; ?>"
									data-id="<?php echo $group->id; ?>"
									data-fid="0"
								>
									<a href="<?php echo $group->getPermalink();?>" data-es-group-stream>
										<i class="ies-earth ies-small mr-5 hidden"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUP_TIMELINE' ); ?>
										<div class="label label-notification pull-right mr-20" data-stream-counter-<?php echo  SOCIAL_TYPE_GROUP; ?>>0</div>
									</a>
								</li>

								<?php if( $filters ) { ?>
									<?php foreach( $filters as $filter ) { ?>
										<?php echo $this->includeTemplate( 'site/groups/item.filter', array( 'filter' => $filter, 'filterId' => $filterId, 'group' => $group ) ); ?>
									<?php } ?>
								<?php } ?>

								<?php if( isset( $hashtag ) && $hashtag ) { ?>
									<li class="widget-filter active"
										style="display:none;"
										data-es-group-filter
										data-dashboardSidebar-menu
										data-dashboardFeeds-item
										data-type="<?php echo  SOCIAL_TYPE_GROUP; ?>"
										data-id="<?php echo $group->id; ?>"
										data-tag="<?php echo $hashtag ?>"
									>
										<a href="javascript:void(0);">
											<i class="ies-tag mr-5"></i> <?php echo '#' . $hashtag; ?>
										</a>
									</li>
								<?php } ?>
							</ul>
						</article>
					</section>

					<section class="panel panel-default">
						<header class="panel-heading">
							<b><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_APPS_SIDEBAR_TITLE' );?></b>
						</header>
						<article class="panel-body">
							<ul class="panel-menu">
								<?php foreach( $apps as $app ){ ?>
								<li class="<?php echo $appId == $app->id ? 'active' : '';?>" data-es-group-filter>
									<a href="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() , 'appId' => $app->getAlias() ) );?>"
										data-es-group-item-app
										data-app-id="<?php echo $app->id;?>"
										title="<?php echo $this->html( 'string.escape' , $group->getName() );?> - <?php echo $app->get( 'title' );?>"
									>
										<img src="<?php echo $app->getIcon();?>" class="app-icon-small mr-5 hidden" /> <?php echo $app->get( 'title' ); ?>
									</a>
								</li>
								<?php } ?>
							</ul>
						</article>
					</section>

					<?php echo $this->render( 'widgets' , SOCIAL_TYPE_GROUP , 'groups' , 'sidebarMiddle' , array( 'uid' => $group->id ,  'group' => $group ) ); ?>
					<?php echo $this->render( 'widgets' , SOCIAL_TYPE_GROUP , 'groups' , 'sidebarBottom' , array( 'uid' => $group->id , 'group' => $group ) ); ?>
					<?php echo $this->render( 'module' , 'es-groups-sidebar-bottom' ); ?>
				</div>
			</div>

			<div class="col-md-9">
				<i class="loading-indicator fd-small"></i>
				<?php echo $this->render( 'module' , 'es-groups-before-contents' ); ?>

				<section class="panel panel-default panel-content" data-es-group-item-content>
					<article class="panel-body">
						<?php if( $contents ){ ?>
							<?php echo $contents; ?>
						<?php } else { ?>
							<?php echo $this->includeTemplate( 'site/groups/item.content' ); ?>
						<?php } ?>
					</article>
				</section>

				<?php echo $this->render( 'module' , 'es-groups-after-contents' ); ?>
			</div>
		</div>
	</div>
</div>
