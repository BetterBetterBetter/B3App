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
<div class="es-widget panel panel-default <?php echo htmlspecialchars($params->get('moduleclass_sfx'));?>">
	<?php if( $module->showtitle ){ ?>
	<div class="panel-heading">
		<?php echo JText::_( $module->title ); ?>
	</div>
	<?php } ?>

	<div class="panel-body">
		<?php echo $contents; ?>
	</div>
</div>