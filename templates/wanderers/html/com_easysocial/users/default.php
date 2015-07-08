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

<div class="es-container" data-users>
	<div class="row">
		<div class="col-md-3">
			<a href="javascript:void(0);" class="btn btn-block btn-es-toggle btn-sidebar-toggle" data-sidebar-toggle>
				<i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
			</a>

			<div class="es-sidebar" data-sidebar style="">
				<?php echo $this->render( 'module' , 'es-users-sidebar-top' ); ?>

				<div class="panel panel-default">
					<div class="panel-heading">
						<b><?php echo JText::_( 'COM_EASYSOCIAL_USERS' );?></b>
					</div>

					<div class="panel-body">
						<ul class="panel-menu">
							<li class="<?php echo !$filter || $filter == 'all' ? 'active' : '';?>">
								<a href="<?php echo FRoute::users();?>"
								title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_USERS' , true );?>"
								data-users-filter
								data-filter="all"
								data-url=""
								>
									<?php echo JText::_( 'COM_EASYSOCIAL_USERS_FILTER_USERS_ALL_USERS' );?>
								</a>
							</li>
							<li class="<?php echo $filter == 'photos' ? 'active' : '';?>">
								<a href="<?php echo FRoute::users( array( 'filter' => 'photos' ) );?>"
								data-users-filter
								data-filter="photos"
								title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_USERS_WITH_PHOTOS' , true );?>"
								>
									<?php echo JText::_( 'COM_EASYSOCIAL_USERS_FILTER_USERS_WITH_PHOTOS' );?>
								</a>
							</li>
							<li class="<?php echo $filter == 'online' ? 'active' : '';?>">
								<a href="<?php echo FRoute::users( array( 'filter' => 'online' ) );?>"
								data-users-filter
								data-filter="online"
								title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_USERS_ONLINE_USERS' , true );?>"
								>
									<?php echo JText::_( 'COM_EASYSOCIAL_USERS_FILTER_ONLINE_USERS' );?>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<?php if ($searchFilters) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<?php echo JText::_('COM_EASYSOCIAL_USERS_BROWSE_BY_FILTERS');?>
					</div>

					<div class="panel-body">
						<ul class="panel-menu">
							<?php foreach ($searchFilters as $sFilter) { ?>
							<li class="filter-item<?php echo $filter == 'search' && $fid == $sFilter->id ? ' active' : '';?>">
								<a href="<?php echo FRoute::users(array('filter' => 'search', 'id' => $sFilter->getAlias()));?>"
								data-users-filter-search
								data-id="<?php echo $sFilter->id;?>"
								title="<?php echo $sFilter->get('title'); ?>"
								>
									<?php echo $this->html( 'string.escape' , $sFilter->get('title') ); ?>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>

				<?php if ($profiles) { ?>
				<div class="panel panel-default">
		            <div class="panel-heading">
		                <?php echo JText::_('COM_EASYSOCIAL_USERS_BROWSE_BY_PROFILES');?>
		            </div>

					<div class="panel-body">
						<ul class="panel-menu">
							<?php foreach ($profiles as $profile) { ?>
							<li class="filter-item<?php echo $filter == 'profiletype' && $activeProfile->id == $profile->id ? ' active' : '';?>">
								<a href="<?php echo FRoute::users(array('filter' => 'profiletype', 'id' => $profile->getAlias()));?>"
								data-users-filter-profile
								data-id="<?php echo $profile->id;?>"
								title="<?php echo $profile->get('title'); ?>"
								>
									<?php echo $profile->get('title'); ?>
									<span class="es-count-no pull-right"><?php echo $profile->totalUsers;?></span>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>

				<?php echo $this->render( 'module' , 'es-users-sidebar-bottom' ); ?>
			</div>
		</div>

		<div class="col-md-9">
			<div class="es-content">
				<?php echo $this->render( 'module' , 'es-users-before-contents' ); ?>

				<div data-users-content>
					<?php echo $this->loadTemplate( 'site/users/default.list' , array( 'users' => $users , 'filter' => $filter , 'sort' => $sort , 'isSort' => false , 'activeTitle' => $activeTitle ) ); ?>

					<div class="es-pagination-footer" data-users-pagination>
						<?php echo $pagination->getListFooter( 'site' );?>
					</div>
				</div>

				<?php echo $this->render( 'module' , 'es-users-after-contents' ); ?>
			</div>
		</div>
	</div>
</div>
