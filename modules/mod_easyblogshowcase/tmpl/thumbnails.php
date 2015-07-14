<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd" class="eb eb-mod mod-easyblogshowcase<?php echo $params->get('moduleclass_sfx'); ?>">
	<div class="mod-easyblogshowcase st-3 eb-gallery" data-autoplay="<?php echo $autoplay;?>" data-interval="<?php echo $autoplayInterval;?>">
		<div class="eb-gallery-stage">
			<div class="eb-gallery-viewport">

				<?php foreach ($posts as $post) { ?>
				<div class="eb-gallery-item" style="background-image: url('<?php echo $post->getImage();?>');?>">
					<div class="eb-gallery-post">
						<div class="eb-gallery-category">
							<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo $post->getPrimaryCategory()->title;?></a>
						</div>

						<h3 class="eb-gallery-title">
							<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
						</h3>

						<div class="eb-gallery-date">
							<?php echo $post->getCreationDate(true)->format(JText::_('DATE_FORMAT_LC1'));?>
						</div>

						<div class="eb-gallery-content">
							<?php echo $post->getIntro(true); ?>
						</div>

						<div class="eb-gallery-more">
							<a href="<?php echo $post->getPermalink();?>"><?php echo JText::_('Read More');?></a>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>

			<?php if (count($posts) > 1) { ?>
			<div class="eb-gallery-button eb-gallery-next-button">
				<i class="fa fa-angle-right"></i>
			</div>
			<div class="eb-gallery-button eb-gallery-prev-button">
				<i class="fa fa-angle-left"></i>
			</div>
			<?php } ?>
		</div>

		<div class="eb-gallery-menu">
			<?php $i = 0; ?>
			<?php foreach ($posts as $post) { ?>
				<div class="eb-gallery-menu-item <?php echo $i == 0 ? 'active' : '';?>">
					<div class="eb-gallery-menu-thumb" style="background-image: url('<?php echo $post->getImage();?>');"></div>
				</div>
				<?php $i++;?>
			<?php } ?>
		</div>
	</div>
</div>