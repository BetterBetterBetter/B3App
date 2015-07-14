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

<?php if ($config->get('main_ratings')) { ?>
<script type="text/javascript">
EasyBlog.require()
.script('ratings')
.done(function($) {

    $('#fd.mod-easyblogshowcase [data-rating-form]').implement(EasyBlog.Controller.Ratings);
});

</script>
<?php } ?>

<div id="fd" class="eb eb-mod mod-easyblogshowcase<?php echo $params->get('moduleclass_sfx'); ?>">
	<div class="mod-easyblogshowcase st-2 eb-gallery" data-autoplay="<?php echo $autoplay;?>" data-interval="<?php echo $autoplayInterval;?>">
		<div class="eb-gallery-stage">
			<div class="eb-gallery-viewport">
				<?php foreach ($posts as $post) { ?>
				<div class="eb-gallery-item">
					<div class="mod-table">

						<?php
							$imgSrc = $post->getImage();

							if (!$post->image && $params->get('photo_legacy', 1)) {
								// lets try to get image from content;
								$tmpImg = $post->getContentImage();
								if ($tmpImg) {
									$imgSrc = $tmpImg;
								}
							}
						?>

						<div class="eb-gallery-thumb mod-cell" style="background-image: url('<?php echo $imgSrc; ?>');">&nbsp;</div>
						
						<div class="mod-cell">
							<?php if ($params->get('authoravatar', true)) { ?>
								<div class="mod-cell cell-tight">
									<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="mod-avatar-sm mr-10">
										<img src="<?php echo $post->getAuthor()->getAvatar(); ?>" width="50" height="50" />
									</a>
								</div>
							<?php } ?>

							<div class="mod-cell">
								<?php if ($params->get('contentauthor', true)) { ?>
									<strong>
										<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="eb-mod-media-title"><?php echo $post->getAuthor()->getName(); ?></a>
									</strong>
								<?php } ?>

								<?php if ($params->get('contentdate' , true)) { ?>
									<div class="mod-muted mod-small mod-fit">
										<?php echo $post->getCreationDate()->format($params->get('dateformat', JText::_('DATE_FORMAT_LC3'))); ?>
									</div>
								<?php } ?>
							</div>

							

							<h3 class="eb-gallery-title">
								<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
							</h3>

							<div class="eb-gallery-category">
								<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo $post->getPrimaryCategory()->title;?></a>
							</div>

							<div class="eb-gallery-content">
								<?php echo $post->content; ?>
							</div>

							<?php if ($params->get('showratings', true)) { ?>
							    <div class="eb-rating">
							        <?php echo EB::ratings()->html($post, 'ebmostshowcase-' . $post->id . '-ratings', JText::_('MOD_SHOWCASE_RATE_BLOG_ENTRY'), $disabled); ?>
							    </div>
							<?php } ?>

							<div class="eb-gallery-more">
								<a href="<?php echo $post->getPermalink();?>"><?php echo JText::_('MOD_SHOWCASE_READ_MORE');?></a>
							</div>
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
				<div class="eb-gallery-menu-item <?php echo $i == 0 ? 'active' : '';?>"></div>
				<?php $i++;?>
			<?php } ?>
		</div>
	</div>
</div>
