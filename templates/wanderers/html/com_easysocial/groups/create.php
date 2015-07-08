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
<div class="panel panel-default" data-groups>
	<header class="center mt-20 mb-20">
		<h3><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_SELECT_CATEGORY' );?></h3>
		<p><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_SELECT_CATEGORY_INFO' ); ?></p>
		<hr>
	</header>

	<div class="panel-body">
		<div class="row gap-m" data-bs-toggle="radio-buttons">
			<?php foreach( $categories as $category ){ ?>
			<div class="col-md-4">
				<a href="<?php echo FRoute::groups( array( 'controller' => 'groups' , 'task' => 'selectCategory' , 'category_id' => $category->id ) );?>" class="btn btn-es btn-block">
					<img src="<?php echo $category->getAvatar( SOCIAL_AVATAR_SQUARE );?>" class="avatar mt-10" width="100" height="100" />
					<div class="pt-10 pb-10">
						<b><?php echo $category->get( 'title' ); ?></b>
					</div>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
