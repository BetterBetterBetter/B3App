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
<div class="es-groups es-container" data-es-groups>
	<div class="row">
		<div class="col-md-3">
			<a href="javascript:void(0);" class="btn btn-block btn-es-toggle btn-sidebar-toggle" data-sidebar-toggle>
				<i class="fa fa-bars"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
			</a>

			<div class="es-sidebar" data-sidebar>
				<?php echo $this->render( 'module' , 'es-groups-sidebar-top' ); ?>

				<section class="panel panel-default">
					<article class="panel-body">
						<?php if( $this->access->allowed( 'groups.create' ) ){ ?>
						<a href="<?php echo FRoute::groups( array( 'layout' => 'create' ) );?>" class="btn btn-es-primary btn-block btn-create">
							<i class="ies-plus ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_START_YOUR_GROUP' );?>
						</a>
						<hr>
						<?php } ?>
						<ul class="panel-menu" data-es-groups-filters>
							<li class="filter-item<?php echo $filter == 'all' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="all">
								<a href="<?php echo FRoute::groups();?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS' , true );?>">
									<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FILTER_ALL_GROUPS' );?>
									<b data-total-groups><?php echo $totalGroups;?></b>
								</a>
							</li>
							<li class="filter-item<?php echo $filter == 'featured' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="featured">
								<a href="<?php echo FRoute::groups( array( 'filter' => 'featured' ) );?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_FEATURED' , true );?>">
									<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FILTER_FEATURED_GROUPS' );?>
									<b data-total-featured><?php echo $totalFeaturedGroups;?></b>
								</a>
							</li>
							<?php if (Foundry::user()->id != 0) { ?>
							<li class="filter-item<?php echo $filter == 'mine' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="mine">
								<a href="<?php echo FRoute::groups( array( 'filter' => 'mine' ) );?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_MY_GROUPS' , true );?>">
									<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FILTER_MY_GROUPS' );?>
									<b data-total-created><?php echo $totalCreatedGroups;?></b>
								</a>
							</li>
							<li class="filter-item<?php echo $filter == 'invited' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="invited">
								<a href="<?php echo FRoute::groups( array( 'filter' => 'invited' ) );?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_INVITED' , true );?>" >
									<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITED' );?>
									<b data-total-invites><?php echo $totalInvites;?></b>
								</a>
							</li>
							<?php } ?>
						</ul>
					</article>
				</section>

				<section class="panel panel-default">
					<header class="panel-heading">
						<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORIES_SIDEBAR_TITLE' );?>
					</header>

					<article class="panel-body">
						<?php if( $categories ){ ?>
						<ul class="panel-menu" data-es-groups-categories>
							<?php foreach( $categories as $category ){ ?>
							<li data-es-groups-category data-es-groups-category-id="<?php echo $category->id;?>" class="<?php echo $activeCategory && $activeCategory->id == $category->id ? 'active' : '';?>">
								<a href="<?php echo FRoute::groups( array( 'categoryid' => $category->getAlias() ) );?>" title="<?php echo $this->html( 'string.escape' , $category->get( 'title' ) );?>">
									<?php echo $category->get( 'title' );?>
									<b data-total-groups="<?php echo $category->getTotalGroups();?>"><?php echo $category->getTotalGroups();?></b>
								</a>
							</li>
							<?php } ?>
						</ul>
						<?php } else { ?>
						<div class="panel-empty"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_CATEGORY_CREATED_YET' );?></div>
						<?php } ?>
					</article>
				</section>

				<?php echo $this->render( 'module' , 'es-groups-sidebar-bottom' ); ?>
			</div>
		</div>
		<div class="col-md-9">
			<?php echo $this->render( 'module' , 'es-groups-before-contents' ); ?>
			<div class="panel panel-default panel-content" data-es-groups-content>
				<?php echo $this->includeTemplate( 'site/groups/default.items' ); ?>
			</div>
			<?php echo $this->render( 'module' , 'es-groups-after-contents' ); ?>
		</div>
	</div>
</div>
