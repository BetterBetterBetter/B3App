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
<article class="panel-body">
	<?php echo $this->loadTemplate( 'site/groups/default.items.category' , array( 'activeCategory' => isset( $activeCategory ) ? $activeCategory : false ) ); ?>

	<section class="media-featured<?php echo !$featuredGroups ? ' is-empty' : '';?>">
		<?php if( $featuredGroups ){ ?>
		<h3 class="label-featured">
			<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FEATURED_GROUPS' );?>
		</h3>
		<ul class="list-media list-unstyled">
			<?php foreach( $featuredGroups as $group ){ ?>
			<li class="media-featured"
				data-groups-featured-item
				data-groups-item-id="<?php echo $group->id;?>"
				data-groups-item-type="<?php echo $group->isOpen() ? 'open' : 'closed';?>"
			>
				<?php echo $this->loadTemplate( 'site/groups/default.items.group' , array( 'group' => $group , 'featured' => true ) ); ?>
			</li>
			<?php } ?>
		</ul>
		<br>
		<hr>
		<br>
		<?php } ?>

		<?php if( $filter == 'featured' ){ ?>
		<div class="empty">
			<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_FEATURED_GROUPS_YET' );?>
		</div>
		<?php } ?>
	</section>

	<section class="media-listing<?php echo !$groups ? ' is-empty' : '';?>">
		<?php if( $groups ){ ?>
		<ul class="list-media list-unstyled" data-groups-list>
			<?php foreach( $groups as $group ){ ?>
			<li data-id="<?php echo $group->id;?>" class="es-groups-item" data-groups-item
				data-groups-item-id="<?php echo $group->id;?>"
				data-groups-item-type="<?php echo $group->isOpen() ? 'open' : 'closed';?>"
			>
				<?php echo $this->loadTemplate( 'site/groups/default.items.group' , array( 'group' => $group , 'featured' => false ) ); ?>
			</li>
			<?php } ?>
		</ul>

		<?php echo $pagination->getListFooter( 'site' );?>

		<?php } else { ?>

			<?php if( $filter == 'invited' ){ ?>
			<div class="empty">
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_INVITED_GROUPS_YET' );?>
			</div>
			<?php } ?>

			<?php if( $filter != 'featured' && $filter != 'invited' ){ ?>
			<div class="empty">
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_GROUPS_YET' );?>
			</div>
			<?php } ?>
		<?php } ?>
	</section>
</article>
