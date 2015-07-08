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
<div class="pull-right btn-group">
	<a class="dropdown-toggle_ loginLink btn btn-es btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
		<i class="icon-es-dropdown"></i>
	</a>

	<?php if( $this->my->isSiteAdmin() ){ ?>
	<ul class="dropdown-menu dropdown-menu-user messageDropDown">
		<?php if( $featured ){ ?>
		<li>
			<a href="javascript:void(0);" data-groups-item-remove-featured><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_REMOVE_FEATURED' );?></a>
		</li>
		<?php } else { ?>
		<li>
			<a href="javascript:void(0);" data-groups-item-set-featured><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_SET_FEATURED' );?></a>
		</li>
		<?php } ?>
		<li>
			<a href="<?php echo FRoute::groups( array( 'layout' => 'edit' , 'id' => $group->getAlias() ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_EDIT_GROUP' );?></a>
		</li>
		<li class="divider"></li>
		<li>
			<a href="javascript:void(0);" data-groups-item-unpublish><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_UNPUBLISH_GROUP' );?></a>
		</li>
		<li>
			<a href="javascript:void(0);" data-groups-item-delete><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_DELETE_GROUP' );?></a>
		</li>
	</ul>
	<?php } ?>
</div>

<div class="media">
	<a href="<?php echo $group->getPermalink();?>" class="media-thumb pull-left">
		<img src="<?php echo $group->getAvatar( SOCIAL_AVATAR_SQUARE );?>" alt="<?php echo $this->html( 'string.escape' , $group->getName() );?>" class="group-image" />
		<b class="media-featured-label">Featured</b>
	</a>

	<div class="media-body">
		<h3 class="media-name">
			<a href="<?php echo $group->getPermalink();?>"><?php echo $group->getName();?></a>
		</h3>

		<section class="media-subject muted">
			<?php if( $group->isOpen() ){ ?>
			<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="top">
				<i class="ies-earth"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?>
			</span>
			<?php } ?>

			<?php if( $group->isClosed() ){ ?>
			<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
				<i class="ies-locked"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?>
			</span>
			<?php } ?>

			<?php if( $group->isInviteOnly() ){ ?>
			<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_INVITE_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
				<i class="ies-locked"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_GROUP' ); ?>
			</span>
			<?php } ?>

			<span>
				<?php echo $group->getCreatedDate()->format( JText::_( 'DATE_FORMAT_LC' ) ); ?>
			</span>
		</section>

		<?php if( $this->template->get( 'groups_description' , true ) ){ ?>
		<article class="media-story">
			<?php if( $group->description ){ ?>
				<?php echo $group->description;?>
			<?php } else { ?>
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_DESCRIPTION_YET' ); ?>
			<?php }?>
		</article>
		<?php } ?>

		<section class="media-meta">
			<span>
				<i class="ies-folder-3 muted"></i>
				<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->id ) );?>">
					<?php echo $group->getCategory()->get( 'title' ); ?>
				</a>
			</span>
			<span>
				<i class="ies-user muted"></i>
				<a href="<?php echo $group->getCreator()->getPermalink();?>">
					<?php echo $group->getCreator()->getName();?>
				</a>
			</span>
			<span>
				<i class="ies-users muted"></i> 
				<?php echo JText::sprintf( Foundry::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_MEMBERS' , $group->getTotalMembers() ) , $group->getTotalMembers() ); ?>
			</span>
		</section>

		<?php if( $group->isInvited() || ( !$group->isMember( $this->my->id ) && !$group->isInvited() ) ){ ?>
		<footer class="media-actions">
			<?php if( $group->isInvited() ){ ?>
			<a class="btn btn-es-success btn-sm mr-5" href="javascript:void(0);" data-groups-item-respond><i class="ies-eye ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_RESPOND_TO_INVITATION' );?></a>
			<?php } ?>

			<?php if( !$group->isMember( $this->my->id ) && !$group->isInvited() ){ ?>
			<a class="btn btn-es-success btn-sm mr-5" href="javascript:void(0);" data-groups-item-join><i class="ies-power ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_JOIN_THIS_GROUP' );?></a>
			<?php } ?>
		</footer>
		<?php } ?>
	</div>
</div>
