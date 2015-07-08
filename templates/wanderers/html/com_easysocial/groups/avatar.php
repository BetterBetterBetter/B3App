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
<figure class="header-avatar" data-group-avatar>
	<a href="<?php echo $group->getPermalink();?>" class="avatar">
		<img data-avatar-image src="<?php echo $group->getAvatar( SOCIAL_AVATAR_SQUARE );?>" alt="<?php echo $this->html( 'string.escape' , $group->getName() );?>">
	</a>

	<?php if( $group->isAdmin() ){ ?>
	<div class="avatar-control dropdown_" data-avatar-menu>
		<a href="javascript:void(0);"
		   class="es-flyout-button dropdown-toggle_"
		   data-bs-toggle="dropdown">
		   <?php echo JText::_( 'COM_EASYSOCIAL_PHOTOS_EDIT_AVATAR' );?>
		</a>
		<ul class="dropdown-menu">
			<li data-avatar-upload-button>
				<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_UPLOAD_AVATAR"); ?></a>
			</li>
			<li data-avatar-select-button>
				<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_PHOTOS_SELECT_AVATAR' ); ?></a>
			</li>
			<?php if ($group->hasAvatar()) { ?>
			<li class="divider"></li>
			<li data-avatar-remove-button>
				<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&controller=profile&task=removeAvatar' , true , '' , null , true );?>"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_REMOVE_AVATAR"); ?></a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
</figure>

