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

$privacy = $this->my->getPrivacy();
?>
<div class="col-md-12 grid-box friendItem"
	data-id="<?php echo $user->id;?>"
	data-friendId="<?php echo $this->my->getFriend( $user->id )->id; ?>"
	data-name="<?php echo $this->html( 'string.escape' , $user->getName() );?>"
	data-avatar="<?php echo $user->getAvatar();?>"
	data-friends-item
	data-friendItem-<?php echo $user->id;?>>
	<div class="cell">
		<div class="row-table gap-s">
			<figure class="col-cell cell-thumb">
				<a href="<?php echo $user->getPermalink();?>" class="media-avatar pull-left">
					<img src="<?php echo $user->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" width="30" height="30" />
					<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'small' ) ); ?>
				</a>
			</figure>

			<article class="col-cell">
				<a href="<?php echo $user->getPermalink();?>" class="cell-name"><?php echo $user->getName();?></a>
				<ul class="cell-brief list-unstyled fd-small mt-10">
					<li>
						<a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>" class="muted">
							<?php if( $user->getTotalFriends() ){ ?>
								<?php echo $user->getTotalFriends();?> <?php echo JText::_( Foundry::string()->computeNoun( 'COM_EASYSOCIAL_FRIENDS' , $user->getTotalFriends() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_FRIENDS_YET' ); ?>
							<?php } ?>
						</a>
					</li>

					<?php if( $this->config->get( 'followers.enabled' ) ) { ?>
					<li class="mt-5">
						<a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>" class="muted">
							<?php if( $user->getTotalFollowers() ){ ?>
								<?php echo $user->getTotalFollowers();?> <?php echo JText::_( Foundry::string()->computeNoun( 'COM_EASYSOCIAL_FOLLOWERS' , $user->getTotalFollowers() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_FOLLOWERS_YET' ); ?>
							<?php } ?>
						</a>
					</li>
					<?php } ?>

					<?php if ( $this->config->get( 'badges.enabled' ) ) { ?>
					<li class="mt-5">
						<a href="<?php echo FRoute::badges( array( 'userid' => $user->getAlias() , 'layout' => 'achievements' ) );?>" class="muted">
							<?php if( $user->getTotalbadges() ){ ?>
								<?php echo $user->getTotalbadges();?> <?php echo JText::_( Foundry::string()->computeNoun( 'COM_EASYSOCIAL_BADGES' , $user->getTotalbadges() ) ); ?>
							<?php } else { ?>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_BADGES_YET' ); ?>
							<?php } ?>
						</a>
					</li>
					<?php } ?>

					<?php if( $activeUser->isViewer() ){ ?>
					<?php 	if( $filter != 'request' && $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ){ ?>
					<li class="pending-action mt-15">
						<a class="btn btn-es-danger btn-small" data-friendItem-reject>
							<?php echo JText::_( 'COM_EASYSOCIAL_REJECT_BUTTON' ); ?>
						</a>

						<a href="javascript:void(0);" data-friendItem-approve class="btn btn-es-primary btn-small">
							<?php echo JText::_( 'COM_EASYSOCIAL_APPROVE_BUTTON' ); ?>
						</a>
					</li>
					<?php 	} else if( $filter == 'request' && $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ) { ?>
					<li class="mt-10">
						<a class="btn btn-es-danger btn-small" data-friendItem-cancel-request>
							<?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?>
						</a>
					</li>
					<?php 	} ?>
					<?php } ?>
				</ul>
			</article>

			
			<aside class="col-cell cell-options">
				<?php if( $this->access->allowed( 'reports.submit' ) || $filter == 'list' || $filter == 'all' || ( $filter == 'suggest' && $privacy->validate( 'friends.request' , $user->id ) ) ){ ?>
				<div class="btn-group pull-right">
					<a class="btn btn-option btn-dropdown dropdown-toggle_" data-bs-toggle="dropdown" href="javascript:void(0);">
						<i class="fa fa-cog"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-user messageDropDown">
						<?php if( $filter == 'list' ){ ?>
							<li data-lists-removeFriend>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REMOVE_FROM_LIST' );?>
								</a>
							</li>
							<li class="divider">
								<hr />
							</li>
						<?php } ?>

						<?php if( $filter == 'suggest' ) { ?>
							<li data-friends-addfriend>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_ADD_AS_FRIEND' );?>
								</a>
							</li>
							<li class="divider">
								<hr />
							</li>
						<?php } ?>

						<?php if( $filter == 'all' && $activeUser->isViewer() ) { ?>
							<li data-friends-unfriend>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_UNFRIEND' );?>
								</a>
							</li>
							<li class="divider">
								<hr />
							</li>
						<?php } ?>

						<?php if( $this->access->allowed( 'reports.submit' ) ){ ?>
						<li>
							<?php echo Foundry::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_USER , $user->id , $user->getName() , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER' ) , '' , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER_DESC' ) , $user->getPermalink( true , true ) ); ?>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
				<?php if( $this->my->id != $user->id && $this->access->allowed( 'conversations.create' ) ){ ?>
				<?php 	if( Foundry::privacy( $this->my->id )->validate( 'profiles.post.message' , $user->id ) ){ ?>
				<div class="pull-right mt-5" data-friendItem-message>
					<a href="javascript:void(0);" class="muted btn btn-option">
						<i class="fa fa-envelope-o"></i> 
						<?php // echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_SEND_MESSAGE' ); ?>
					</a>
				</div>
				<?php 	} ?>
				<?php } ?>
			</aside>
		</div>
	</div>
</div>