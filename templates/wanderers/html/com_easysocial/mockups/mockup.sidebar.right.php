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
		<b><?php echo JText::_( 'Applications' );?></b>
	</div>

	<div class="panel-body">
		<ul class="panel-menu compact list-unstyled">
			<li>
				<a href="#">
					<i class="ico-blog"></i>Blog
				</a>
			</li>
			<li>
				<a href="#">
					<i class="ico-calendar"></i>Calendar
				</a>
			</li>
			<li>
				<a href="#">
					<i class="ico-feeds"></i>Feeds
				</a>
			</li>
			<li>
				<a href="#">
					<i class="ico-task"></i>Task
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<b><?php echo JText::_( 'Upcoming Birthday' );?></b>
	</div>

	<div class="panel-body">
		<div class="muted"><?php echo JText::_( 'There is no upcoming birthdays.' );?></div>
	</div>
</div>


<div class="panel panel-default">
	<div class="panel-heading">
		<b>Friends (133)</b>
	</div>
	<div class="panel-body">
		<div class="panel-thumbs row">
			<?php for( $x=0; $x<12; $x++ ) { ?>
			<a href="#" class="thumb"><!-- col-md-2 col-xs-3 -->
				<img src="http://distilleryimage1.s3.amazonaws.com/823c9df4743711e3a38b0e4771a9e3b5_6.jpg" />
			</a>
			<?php } ?>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<b><?php echo JText::_( 'Recent Photos' );?></b>
	</div>

	<div class="panel-body">
		<div class="panel-thumbs row">
			<?php for( $x=0; $x<12; $x++ ) { ?>
			<a href="#" class="thumb">
				<img class="recent-photo" src="http://distilleryimage1.s3.amazonaws.com/823c9df4743711e3a38b0e4771a9e3b5_6.jpg" />
			</a>
			<?php } ?>
		</div>
	</div>

	<div class="panel-footer">
		<a class="muted"><?php echo JText::_( 'All photos' );?></a>
	</div>
</div>