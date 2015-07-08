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
<header class="profile-header"
	data-id="<?php echo $group->id;?>"
	data-name="<?php echo $this->html( 'string.escape' , $group->getName() );?>"
	data-avatar="<?php echo $group->getAvatar();?>">

	<?php echo $this->includeTemplate( 'site/groups/cover' ); ?>
	<?php echo $this->includeTemplate( 'site/groups/avatar' ); ?>
	<?php echo $this->render( 'widgets' , 'group' , 'item' , 'afterAvatar' , array( $group ) ); ?>

	<article class="es-header-content">
		<section class="es-header-details">
			<div class="">
				<div class="col-md-3">
					<?php echo $this->render( 'module' , 'es-groups-before-name' ); ?>
					<h2 class="header-name"><a href="<?php echo $group->getPermalink();?>"><?php echo $group->getName();?></a></h2>
					<div class="header-brief">
						<span>
							<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->getAlias() ) );?>">
								<i class="ies-database ies-small"></i> <?php echo $group->getCategory()->get( 'title' ); ?>
							</a>
						</span>
						<?php if( $group->isOpen() ){ ?>
						&middot;
						<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
							<i class="ies-earth"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?>
						</span>
						<?php } ?>

						<?php if( $group->isClosed() ){ ?>
						&middot;
						<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
							<i class="ies-locked"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?>
						</span>
						<?php } ?>

						<?php if( $group->isInviteOnly() ){ ?>
						&middot;
						<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_INVITE_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
							<i class="ies-locked"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_GROUP' ); ?>
						</span>
						<?php } ?>
					</div>
					<div class="mt-10">
						<a href="<?php echo FRoute::groups( array( 'layout' => 'info' , 'id' => $group->getAlias() ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_MORE_INFO' ); ?></a>
						<?php if( !$group->isOwner() && $this->access->allowed( 'reports.submit' ) && $this->config->get( 'reports.enabled' ) ){ ?>
						 &middot;
						<?php echo Foundry::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_GROUPS , $group->id , $group->getName() , JText::_( 'COM_EASYSOCIAL_GROUPS_REPORT_GROUP' ) ); ?>
						<?php } ?>
					</div>
					<?php echo $this->render( 'module' , 'es-groups-after-name' ); ?>
				</div>
				<div class="col-md-9">
					<ul class="es-header-meta list-unstyled">
						<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupStatsStart' , array( $group ) ); ?>
						<li>
							<a href="<?php echo FRoute::albums( array( 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP ) );?>">
								<span><?php echo $group->getTotalAlbums(); ?></span>
								<span><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_ALBUMS' ); ?></span>
							</a>
						</li>
						<li>
							<span><?php echo $group->getTotalMembers(); ?></span>
							<span><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_MEMBERS' ); ?></span>
						</li>
						<li>
							<span><?php echo $group->hits; ?></span>
							<span><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_VIEWS' ); ?></span>
						</li>
						<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupStatsEnd' , array( $group ) ); ?>
					</ul>
				</div>
			</div>
		</section>

		<footer class="es-header-actions">
			<?php echo $this->render( 'module' , 'es-groups-before-actions' ); ?>
			<?php echo $this->render( 'widgets' , 'group' , 'item' , 'beforeActions' , array( $group ) ); ?>

			<ul class="list-unstyled">

				<?php if( $group->isPendingMember() ){ ?>
				<li>
					<div class="dropdown_">
						<a class="dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="ies-eye"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_PENDING_APPROVAL' );?> <i class="ies-arrow-down"></i></a>
						<ul class="dropdown-menu dropdown-menu-user messageDropDown">
							<li>
								<a href="javascript:void(0);" data-es-group-withdraw><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_WITHDRAW_REQUEST' );?></a>
							</li>
						</ul>
					</div>
				</li>
				<?php } ?>

				<?php if( $group->isInvited() && !$group->isMember() ){ ?>
				<li>
					<a href="javascript:void(0);" data-es-group-respond>
						<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_RESPOND_TO_INVITATION' );?>
					</a>
				</li>
				<?php } ?>

				<?php if( !$group->isInviteOnly() && !$group->isMember() && !$group->isPendingMember() && !$group->isInvited() ){ ?>
				<li>
					<a href="javascript:void(0);" data-es-group-join>
						<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_JOIN_THIS_GROUP' );?>
					</a>
				</li>
				<?php } ?>

				<?php if( $group->isMember() ){ ?>
				<li>
					<a href="javascript:void(0);" data-es-group-invite>
						<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_FRIENDS' );?>
					</a>
				</li>
				<?php } ?>

				<?php if( $group->isMember() && !$group->isOwner() ){ ?>
				<li>
					<a href="javascript:void(0);" data-es-group-leave>
						<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_LEAVE_GROUP' );?>
					</a>
				</li>
				<?php } ?>

				<?php if( $this->my->isSiteAdmin() || $group->isOwner() || $group->isAdmin() ){ ?>
				<li class="dropdown_">
					<a href="javascript:void(0);" data-bs-toggle="dropdown">
						<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_MANAGE_GROUP' );?> <i class="ies-arrow-down"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-user messageDropDown">
						<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupAdminStart' , array( $group ) ); ?>
						<li>
							<a href="<?php echo FRoute::groups( array( 'layout' => 'edit' , 'id' => $group->getAlias() ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_EDIT_GROUP' );?></a>
						</li>

						<?php if( $this->my->isSiteAdmin() ){ ?>
						<li class="divider"></li>
						<li>
							<a href="javascript:void(0);" data-es-group-unpublish><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_UNPUBLISH_GROUP' );?></a>
						</li>
						<li>
							<a href="javascript:void(0);" data-es-group-delete><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_DELETE_GROUP' );?></a>
						</li>
						<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupAdminEnd' , array( $group ) ); ?>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>
				<li>
					<?php echo Foundry::sharing( array( 'url' => FRoute::groups( array( 'layout' => 'item', 'id' => $group->getPermalink(), 'external' => true, 'xhtml' => true ) ), 'display' => 'dialog', 'text' => JText::_( 'COM_EASYSOCIAL_STREAM_SOCIAL' ) , 'css' => 'fd-small' ) )->getHTML( true ); ?>
				</li>
			</ul>

			<?php echo $this->render( 'module' , 'es-groups-after-actions' ); ?>
			<?php echo $this->render( 'widgets' , 'group' , 'item' , 'afterActions' , array( $group ) ); ?>
		</footer>
	</article>
</header>
