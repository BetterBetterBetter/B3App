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
		<?php echo JText::_( 'COM_EASYSOCIAL_SEARCH_FILTER' );?>
	</div>

	<div class="panel-body">
		<ul class="panel-menu" data-advsearch-sidebar-ul>

			<li class="<?php echo empty( $fid ) ? ' active' : '';?>"
				data-sidebar-menu
				data-sidebar-item
				data-id="0"
				data-search-filter-0
			>
				<a  href="<?php echo FRoute::search( array( 'layout' => 'advanced' ) );?>"
					data-type="custom"
					data-search-filter-item
					data-id="0"
					data-title="<?php echo JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_NEW_SEARCH'); ?>"
				>
					<?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_NEW_SEARCH' );?>
					<div class="label label-notification pull-right mr-20"></div>
				</a>
			</li>

			<?php if( $filters) { ?>
				<?php foreach( $filters as $filter ) { ?>
					<?php echo $this->includeTemplate( 'site/advancedsearch/user/sidebar.filter.item', array( 'fid' => $fid, 'filter' => $filter ) ); ?>
				<?php } ?>
			<?php } ?>

		</ul>
	</div>

</div>
