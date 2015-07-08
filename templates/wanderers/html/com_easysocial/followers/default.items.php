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
<div class="panel panel-default panel-content" data-followers-content>

	<div class="panel-heading">
		<div class="row-table">
			<div class="col-cell">
			<?php if( $active == 'followers' ){ ?>
				<h4><?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWERS_TITLE' ); ?></h4>
			<?php } ?>

			<?php if( $active == 'following' ){ ?>
				<h4><?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWING_TITLE' ); ?></h4>
			<?php } ?>
			</div>
		</div>
	</div>

	<ul class="followers-items grid-boxes grid-cells _2<?php echo !$users ? ' is-empty' : '';?>" data-followers-items>
		<?php if( $users ){ ?>
			<?php foreach( $users as $user ){ ?>
				<?php echo $this->loadTemplate( 'site/followers/default.item' , array( 'user' => $user , 'active' => $active , 'currentUser' => $currentUser ) ); ?>
			<?php } ?>
		<?php } ?>

		<li class="empty center mt-20" data-friends-emptyItems>
			<i class="icon-es-empty-follow mb-10"></i>
			<div>
				<?php if( $active == 'followers' ){ ?>
					<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_NO_FOLLOWERS_YET' ); ?>
				<?php } ?>

				<?php if( $active == 'following' ){ ?>
					<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_NOT_FOLLOWING_YET' ); ?>
				<?php } ?>
			</div>
		</li>
	</ul>
</div>
