<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="stream-head stream-reply">
	<i class="stream-type"></i>
	<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_REPLIED_TO' ); ?>
	<a href="<?php echo $activity->comment->parentlink; ?>" class="parentLink" parentid="<?php echo $activity->comment->parent_id; ?>"><?php echo $activity->comment->parent_id; ?></a>
	<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_REPLIED_ON' ); ?>
	<a href="<?php echo $activity->comment->pagelink; ?>"><?php echo $activity->comment->contenttitle; ?></a>
	<?php // echo JText::_( 'COM_KOMENTO_ACTIVITY_REPLIED_IN' ); ?>
	<?php // echo $activity->comment->componenttitle; ?>
</div>
<div class="stream-body">
	<div class="kmt-comment-text"><?php echo $activity->comment->comment; ?></div>
</div>
<div class="stream-foot">
	<a href="<?php echo $activity->comment->permalink; ?>"><?php echo $activity->comment->created; ?></a>
</div>
