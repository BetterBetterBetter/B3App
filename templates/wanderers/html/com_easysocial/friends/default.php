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
<?php if( $this->my->id != $user->id ){ ?>
	<!-- Include cover section -->
	<?php echo $this->loadTemplate( 'site/profile/mini.header' , array( 'user' => $user ) ); ?>
<?php } ?>

<div class="es-container" data-friends data-dashboard>
	<div class="row">
		<div class="col-md-3" data-sidebar>
			<a href="javascript:void(0);" class="btn btn-block btn-es-toggle btn-sidebar-toggle" data-sidebar-toggle>
				<i class="fa fa-bars"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
			</a>

			<div class="es-sidebar" data-sidebar>
				<?php echo $this->render( 'module' , 'es-friends-sidebar-top' ); ?>

				<div class="panel panel-default">
					<div class="panel-heading">
						<b><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_SIDEBAR_TITLE' );?></b>
					</div>

					<div class="panel-body">
						<ul class="panel-menu">
							<li class="filter-item<?php echo !$activeList->id && (!$filter || $filter == 'all' ) ? ' active' : '';?>"
								data-friends-filter
								data-filter="all"
								data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS' );?>"
								data-userid="<?php echo $user->id; ?>"
								data-url="<?php echo FRoute::friends( array( 'userid' => $this->my->id == $user->id ? '' : $user->getAlias() ) );?>"
							>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_ALL_FRIENDS_FILTER' );?>
									<b class="pull-right" data-total-friends><?php echo $totalFriends;?></b>
								</a>
							</li>

							<?php if( $this->my->id != $user->id ) { ?>
							<li class="filter-item<?php echo !$activeList->id && $filter == 'mutual' ? ' active' : '';?>"
								data-friends-filter
								data-filter="mutual"
								data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_MUTUAL_FRIENDS' );?>"
								data-userid="<?php echo $user->id; ?>"
								data-url="<?php echo FRoute::friends( array( 'filter' => 'mutual' , 'userid' => $this->my->id == $user->id ? '' : $user->getAlias() ) );?>"
							>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_MUTUAL_FRIENDS_FILTER' );?>
									<b class="pull-right"><?php echo $totalMutualFriends;?></b>
								</a>
							</li>
							<?php } ?>

							<?php if( $this->my->id == $user->id ){ ?>

							<li class="filter-item<?php echo !$activeList->id && $filter == 'suggest' ? ' active' : '';?>"
								data-friends-filter
								data-filter="suggest"
								data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_SUGGESTIONS' );?>"
								data-userid="<?php echo $user->id; ?>"
								data-url="<?php echo FRoute::friends( array( 'filter' => 'suggest' , 'userid' => $this->my->id == $user->id ? '' : $user->getAlias() ) );?>"
							>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_SUGGEST_FRIENDS_FILTER' );?>
									<b class="pull-right" data-total-friends-suggestion><?php echo $totalFriendSuggest;?></b>
								</a>
							</li>

							<li class="filter-item<?php echo !$activeList->id && $filter == 'pending' ? ' active' : '';?>"
								data-friends-filter
								data-filter="pending"
								data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_PENDING_APPROVAL' );?>"
								data-userid="<?php echo $user->id; ?>"
								data-url="<?php echo FRoute::friends( array( 'filter' => 'pending' , 'userid' => $this->my->id == $user->id ? '' : $user->getAlias() ) );?>"
							>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_PENDING_APPROVAL_FILTER' );?>
									<b class="pull-right" data-total-friends-pending><?php echo $totalPendingFriends;?></b>
								</a>
							</li>

							<li class="filter-item<?php echo !$activeList->id && $filter == 'request' ? ' active' : '';?>"
								data-friends-filter
								data-filter="request"
								data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_REQUESTS' );?>"
								data-userid="<?php echo $user->id; ?>"
								data-url="<?php echo FRoute::friends( array( 'filter' => 'request' , 'userid' => $this->my->id == $user->id ? '' : $user->getAlias() ) );?>"
							>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT_FILTER' );?>
									<b class="pull-right" data-frields-request-sent-count data-total-friends-request><?php echo $totalRequestSent;?></b>
								</a>
							</li>

							<?php if ($this->config->get('friends.invites.enabled')) { ?>
							<li class="filter-item<?php echo !$activeList->id && $filter == 'invites' ? ' active' : '';?>"
								data-friends-filter
								data-filter="invites"
								data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_REQUESTS' );?>"
								data-userid="<?php echo $user->id; ?>"
								data-url="<?php echo FRoute::friends(array('filter' => 'invites' , 'userid' => $this->my->id == $user->id ? '' : $user->getAlias() ) );?>"
							>
								<a href="javascript:void(0);">
									<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITED_FRIENDS');?>
									<b class="pull-right" data-invites-count><?php echo $totalInvites;?></b>
								</a>
							</li>
							<?php }?>	

							<?php } ?>
						</ul>
					</div>
				</div>

				<?php if( $this->my->id == $user->id && $this->config->get( 'friends.list.enabled' ) && $this->access->allowed( 'friends.list' ) ){ ?>
					<?php echo $this->loadTemplate( 'site/friends/default.lists' , array( 'lists' => $lists , 'user' => $user , 'totalFriends' => $totalFriends , 'activeList' => $activeList , 'totalFriendsList' => $totalFriendsList ) ); ?>
				<?php } ?>

				<?php echo $this->render( 'module' , 'es-friends-sidebar-bottom' ); ?>
			</div>
		</div>

		<div class="col-md-9">
			<?php echo $this->render( 'module' , 'es-friends-before-contents' ); ?>
		<?php if ($filter == 'invites') { ?>
			<?php echo $this->includeTemplate('site/friends/default.invites', array( 'user' => $user , 'pagination' => $pagination ) ); ?>
		<?php } else { ?>
			<?php echo $this->includeTemplate('site/friends/default.items', array( 'user' => $user , 'pagination' => $pagination ) ); ?>
		<?php } ?>
			<?php echo $this->render( 'module' , 'es-friends-after-contents' ); ?>
		</div>

	</div>
</div>
