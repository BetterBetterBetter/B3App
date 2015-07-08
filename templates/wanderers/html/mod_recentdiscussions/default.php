<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="discuss-mod recent-discussions<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<?php if( $posts ){ ?>
	<div class="list-item">
		<?php foreach( $posts as $post ){ ?>

		<?php
		$post_content 	= JString::substr(DiscussStringHelper::escape( strip_tags($post->content) ), 0, $params->get( 'max_content' )) . '...';
		$post_title		= (JString::strlen( $post->title ) > $params->get( 'max_title', 50 ))? JString::substr( $post->title, 0, $params->get( 'max_title', 50 )) . '...' : $post->title;
		?>
		<div class="item">
			<div class="story">
				<div class="item-user">
					<strong>
						<a class="item-title" <?php echo $params->get( 'show_content_tooltips' , false ) ? ' rel="ed-popover" data-placement="top" data-original-title="' . $post_title . '" data-content="' . $post_content . '"' : '';?> href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id .'&Itemid='.$itemid );?>"><?php echo (JString::strlen( $post->title ) > $params->get( 'max_title', 50 ))? JString::substr( $post->title, 0, $params->get( 'max_title', 50 )) . '...' : $post->title; ?></a>
					</strong>
					<div class="small"><?php echo JText::sprintf( 'MOD_RECENTDISCUSSIONS_STARTED_BY' , '<a href="' . $post->profile->getLink() . '">' . $post->profile->getName() . '</a>' );?></div>
				</div>

				<?php if ($params->get('show_footer')) { ?>
				<div class="item-info push-top small">
					<span class="time-info">
						<img src="<?php echo JURI::root(); ?>modules/mod_recentdiscussions/images/clock.png">
						<?php echo DiscussDateHelper::getLapsedTime($post->created); ?>
					</span>
					<span class="reply-info" data-original-title="<?php echo $post->num_replies . ' ' . JText::_( 'MOD_RECENTDISCUSSIONS_REPLIES' );?>" rel="ed-tooltip">
						<img src="<?php echo JURI::root(); ?>modules/mod_recentdiscussions/images/replies.png">
						<?php echo $post->num_replies;?>
					</span>
					<span class="views-info" data-original-title="<?php echo $post->hits . ' ' . JText::_('MOD_RECENTDISCUSSIONS_VIEWS' ); ?>" rel="ed-tooltip">
						<img src="<?php echo JURI::root(); ?>modules/mod_recentdiscussions/images/views.png">
						<?php echo $post->hits;?>
					</span>
				</div>
				<?php } ?>

			</div>
			<div class="both"></div>
		</div>
		<?php } ?>
	</div>

<?php } else { ?>
	<div class="no-item">
		<?php echo JText::_('MOD_RECENTDISCUSSIONS_NO_ENTRIES'); ?>
	</div>
<?php } ?>
</div>
