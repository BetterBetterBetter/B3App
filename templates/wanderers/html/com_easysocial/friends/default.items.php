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

$activeUser = ( isset( $user ) ) ? $user : Foundry::user();
?>
<div class="panel panel-default panel-content" data-friends-content>

	<header class="panel-heading clearfix">
		<div class="row-table valign-middle">
			<div class="col-cell">
				<h4>
					<?php if( $filter == 'list' ){ ?>
						<?php echo $activeList->get( 'title' ); ?>
					<?php } else { ?>
						<?php if( $filter == 'pending' ){ ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_HEADING_PENDING_FRIENDS' ); ?>
						<?php } ?>

						<?php if( $filter == 'all' ){ ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_HEADING_ALL_FRIENDS' ); ?>
						<?php } ?>

						<?php if( $filter == 'mutual' ){ ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_HEADING_MUTUAL_FRIENDS' ); ?>
						<?php } ?>

						<?php if( $filter == 'suggest' ){ ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_HEADING_SUGGEST_FRIENDS' ); ?>
						<?php } ?>

						<?php if( $filter == 'request' ){ ?>
							<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_HEADING_FRIENDS_REQUEST_SENT' ); ?>
						<?php } ?>

					<?php } ?>
				</h4>
			</div>

			<?php // if( $filter == 'list' ){ ?>
			<div class="col-cell">
				<div class="btn-group btn-radius pull-right listActions"
					data-friendList-actions
					data-id="<?php echo $activeList->id;?>"
					data-userid="<?php echo $activeUser->id;?>"
					data-title="<?php echo $this->html( 'string.escape' , $activeList->title );?>"
				>
					<a href="javascript:void(0);" class="btn btn-es" data-bs-toggle="dropdown"><?php echo JText::_( 'COM_EASYSOCIAL_MANAGE_LIST_BUTTON' );?> <b class="caret"></b></a>

					<ul class="dropdown-menu dropdown-menu-lists dropdown-arrow-topright">
						<li>
							<a href="javascript:void(0);" data-es-conversations-compose data-es-conversations-listid="<?php echo $activeList->id;?>">
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_START_CONVERSATION' );?>
							</a>
						</li>
						<li>
							<hr />
						</li>
						<li>
							<a href="<?php echo FRoute::friends( array( 'layout' => 'listForm' , 'id' => $activeList->id ) );?>">
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_EDIT' );?>
							</a>
						</li>
						<li>
							<a href="javascript:void(0);" data-friendListActions-add>
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_ADD' );?>
							</a>
						</li>
						<li>
							<hr />
						</li>
						<li>
							<a href="javascript:void(0);" data-friendListActions-default>
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_SET_DEFAULT' );?>
							</a>
						</li>
						<li>
							<hr />
						</li>
						<li data-lists-delete>
							<a href="javascript:void(0);" data-friendListActions-delete>
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DELETE' );?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<?php // } ?>
	</header>
 
	<div class="friend-items grid-boxes row gap-x<?php echo !$friends ? ' is-empty' : '';?>" data-friends-items>
	<?php if( $friends ){ ?>
		<?php foreach( $friends as $user ){
			if( $filter == 'suggest' )
				$user = $user->friend;
		?>
			<?php echo $this->loadTemplate( 'site/friends/default.item' , array( 'user' => $user , 'filter' => $filter , 'activeUser' => $activeUser ) ); ?>
		<?php } ?>
	<?php } ?>
		<div class="empty center" data-friends-emptyItems>

			<?php if( $filter == 'pending' ){ ?>
			<i class="icon-es-empty-pending mb-10"></i>
			<div>
				<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NO_PENDING_APPROVALS' ); ?>
			</div>
			<?php } ?>

			<?php if( $filter == 'list' ){ ?>
			<i class="icon-es-empty-friends mb-10"></i>
			<div>
				<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NO_FRIENDS_IN_LIST' ); ?>
			</div>
			<?php } ?>

			<?php if( $filter == 'suggest' ){ ?>
			<i class="icon-es-empty-suggest mb-10"></i>
			<div>
				<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REQUEST_NO_FRIEND_SUGGESTION' ); ?>
			</div>
			<?php } ?>

			<?php if( $filter == 'all' ){ ?>
			<i class="icon-es-empty-friends mb-10"></i>
			<div>
				<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NO_FRIENDS_YET' ); ?>
			</div>
			<?php } ?>

			<?php if( $filter == 'request' ){ ?>
			<i class="icon-es-empty-request mb-10"></i>
			<div>
				<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NO_FRIENDS_REQUEST_SENT' ); ?>
			</div>
			<?php } ?>

			<?php if( $filter == 'mutual' ){ ?>
			<i class="icon-es-empty-friends mb-10"></i>
			<div>
				<?php echo ( $activeUser->id != Foundry::user()->id ) ?  JText::sprintf( 'COM_EASYSOCIAL_FRIENDS_NO_MUTUAL_FRIENDS_WITH', $activeUser->getName() ) : JText::_( 'COM_EASYSOCIAL_FRIENDS_NO_MUTUAL_FRIENDS' ) ; ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<div class="es-pagination-footer">
		<?php echo $pagination->getListFooter( 'site' );?>
	</div>
</div>
