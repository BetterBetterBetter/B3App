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
		<b><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_YOUR_LIST' );?></b>
		<?php if( $this->my->id == $user->id && $this->access->allowed( 'friends.list.enabled' ) && !$this->access->exceeded( 'friends.list.limit' , $totalFriendsList ) ){ ?>
		<a href="<?php echo FRoute::friends( array( 'layout' => 'listForm' ) );?>" class="panel-option" title="<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NEW_LIST' ); ?>">
			<i class="fa fa-plus"></i>
		</a>
		<?php } ?>
	</div>
	<div class="panel-body" data-friends-list>
		<?php if( $lists ){ ?>
		<ul class="widget-list fd-nav fd-nav-stacked" data-friends-listItems>
			<?php foreach( $lists as $list ){ ?>
			<li class="filter-item item-<?php echo $list->id;?><?php echo $activeList->id == $list->id ? ' active' : '';?><?php echo $list->default ? ' default' : '';?>"
				 data-list-<?php echo $list->id;?>
				 data-id="<?php echo $list->id;?>"
				 data-title="<?php echo $this->html( 'string.escape' , $list->get( 'title' ) );?>"
				 data-url="<?php echo FRoute::friends( array( 'listId' => $list->id ) );?>"
				 data-friends-listItem
				>
				<a href="javascript:void(0);">
					<i class="ies-star ies-small filter-item-default"></i>
					<?php echo $this->html( 'string.escape' , $list->get( 'title' ) ); ?>
					<b class="pull-right" data-list-counter><?php echo $list->getCount();?></b>
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="panel-empty muted">
			<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_NO_LIST_CREATED_YET' ); ?>
		</div>
		<?php } ?>
	</div>
</div>
