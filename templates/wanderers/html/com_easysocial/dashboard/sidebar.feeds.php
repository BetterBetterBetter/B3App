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
		<b><?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS' );?></b>

		<a class="panel-option" href="<?php echo FRoute::dashboard( array( 'type' => 'filterForm' ) ); ?>" 
			data-stream-filter-button
		>
			<i class="fa fa-plus"></i>
		</a>
	</div>

	<div class="panel-body">
		<ul class="panel-menu" data-dashboard-feeds>

			<li class="<?php echo !$isAppView && ( empty( $filter ) || $filter == 'me' ) ? 'active' : '';?>"
				data-dashboardSidebar-menu
				data-dashboardFeeds-item
				data-type="me"
				data-id=""
				data-url="<?php echo FRoute::dashboard();?>"
				data-title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_ME_AND_FRIENDS' , true ); ?>"
			>
				<a href="javascript:void(0);">
					<?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_ME_AND_FRIENDS' );?>
					<div class="label label-notification pull-right mr-20" data-stream-counter-me>0</div>
				</a>
			</li>

			<?php if( $this->template->get( 'dashboard_feeds_everyone' , true ) ){ ?>
				<li class="<?php echo $filter == 'everyone' ? ' active' : '';?>"
					data-dashboardSidebar-menu
					data-dashboardFeeds-item
					data-type="everyone"
					data-id=""
					data-url="<?php echo FRoute::dashboard( array( 'type' => 'everyone' ) );?>"
					data-title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_DASHBOARD_EVERYONE' , true ); ?>"
				>
					<a href="javascript:void(0);">
						<?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS_EVERYONE' );?>
						<div class="label label-notification pull-right mr-20" data-stream-counter-everyone>0</div>
					</a>
				</li>
			<?php } ?>

			<?php if( $this->config->get( 'followers.enabled' ) ){ ?>
			<li class="widget-filter<?php echo $filter == 'following' ? ' active' : '';?>"
				data-dashboardSidebar-menu
				data-dashboardFeeds-item
				data-type="following"
				data-id=""
				data-url="<?php echo FRoute::dashboard( array( 'type' => 'following' ) );?>"
				data-title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_FOLLLOW' ); ?>"
			>
				<a href="javascript:void(0);">
					<?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEEDS_FOLLOWING' );?>
					<div class="label label-notification pull-right mr-20" data-stream-counter-following>0</div>
				</a>
			</li>
			<?php } ?>

			<?php if( $this->config->get( 'friends.list.enabled' ) ){ ?>
				<?php if( $lists && count( $lists ) > 0 ) { ?>
					<?php foreach( $lists as $list ){ ?>
					<li class="widget-filter<?php echo $listId == $list->id ? ' active' : '';?>"
						data-dashboardSidebar-menu
						data-dashboardFeeds-item
						data-type="list"
						data-id="<?php echo $list->id;?>"
						data-url="<?php echo FRoute::dashboard( array( 'type' => 'list' , 'listId' => $list->id ) );?>"
						data-title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . $this->html( 'string.escape' , $list->get( 'title' ) ); ?>"
					>
						<a href="javascript:void(0);">
							<i class="ies-bookmarks mr-5"></i> <?php echo $list->title; ?>
							<div class="label label-notification pull-right mr-20" data-stream-counter-list-<?php echo $list->id; ?>>0</div>
						</a>
					</li>
					<?php } ?>
				<?php } ?>
			<?php } ?>

			<?php if ($this->config->get('stream.bookmarks.enabled')) { ?>
				<li class="dashboard-filter<?php echo $filter == 'bookmarks' ? ' active' : '';?>"
					data-dashboardSidebar-menu
					data-dashboardFeeds-item
					data-type="bookmarks"
					data-id=""
					data-url="<?php echo FRoute::dashboard( array( 'type' => 'bookmarks' ) );?>"
					data-title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_DASHBOARD_BOOKMARKS' ); ?>"
				>
					<a href="javascript:void(0);">
						<i class="ies-star mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_FEEDS_BOOKMARKS'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if( $appFilters && $this->template->get( 'dashboard_feeds_apps' ) ) { ?>
				<li class="panel-heading">
					<span><?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS_APPS' ); ?></span>
				</li>

				<?php if( $filterList && count( $filterList) > 0 ) { ?>
					<?php foreach( $filterList as $filter ) { ?>
						<?php echo $this->includeTemplate( 'site/dashboard/sidebar.feeds.filter.item', array( 'filter' => $filter ) ); ?>
					<?php } ?>
				<?php } ?>

				<?php $i = 1; ?>
				<?php foreach( $appFilters as $appFilter ) { ?>
					<?php echo $this->includeTemplate( 'site/dashboard/sidebar.feeds.appfilter.item', array( 'filter' => $appFilter , 'hide' => $i > 3 ) ); ?>
					<?php $i++; ?>
				<?php } ?>

				<?php if( count( $appFilters ) > 3 ){ ?>
					<li>
						<a href="javascript:void(0);" class="filter-more" data-app-filters-showall><?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_SHOW_MORE_FILTERS' ); ?></a>
					</li>
				<?php } ?>
			<?php } ?>

			<?php if( isset( $hashtag ) && $hashtag ) { ?>
				<li class="dashboard-filter active"
					style="display:none;"
					data-dashboardSidebar-menu
					data-dashboardFeeds-item
					data-type="hashtag"
					data-id=""
					data-tag="<?php echo $hashtag ?>"
					data-url="<?php echo FRoute::dashboard( array( 'layout' => 'hashtag' , 'tag' => $hashtag ) );?>"
					data-title="<?php echo $this->html( 'string.escape' , '#' . $hashtag ); ?>"
				>
					<a href="javascript:void(0);">
						<i class="ies-tag mr-5"></i> <?php echo '#' . $hashtag; ?>
					</a>
				</li>
			<?php } ?>

		</ul>
	</div>
</div>



