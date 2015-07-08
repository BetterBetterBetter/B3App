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
		<b><?php echo JText::_( 'Recent Photos' );?></b>
	</div>

	<div class="panel-body">
		<div class="panel-thumbs row">
			<?php for( $x=0; $x<12; $x++ ) { ?>
			<a href="#" class="thumb">
				<img class="recent-photo" src="https://s3.amazonaws.com/uifaces/faces/twitter/jqiuss/128.jpg" />
			</a>
			<?php } ?>
		</div>
	</div>

	<div class="panel-footer">
		<a class="muted"><?php echo JText::_( 'All photos' );?></a>
	</div>
</div>