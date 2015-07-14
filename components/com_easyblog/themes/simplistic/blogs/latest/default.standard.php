<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting" class="eb-post fd-cf" data-blog-posts-item data-id="<?php echo $blog->id;?>">
	<div class="eb-post-content">
		<div class="eb-post-head">
			<?php echo $this->output('site/blogs/admin.tools', array('blog' => $blog, 'return' => EB::_('index.php?option=com_easyblog'))); ?>

			<?php echo $this->output('site/blogs/latest/part.aside', array('blog' => $blog)); ?>

			<?php if ($blog->category->getParam('listing_show_title', true)) { ?>
			<h2 itemprop="name headline" class="eb-post-title reset-heading">
				<a href="<?php echo $blog->getPermalink();?>" class="text-inherit"><?php echo $blog->title;?></a>
			</h2>
			<?php } ?>

			<?php echo $this->output('site/blogs/latest/part.meta', array('blog' => $blog)); ?>
		</div>

		<?php if (in_array($post->getType(), array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
			<?php if ($post->image && $this->params->get('post_image', true) || (!$post->image && $this->params->get('post_image_placeholder', false) && $this->params->get('post_image', true))) { ?>
			<a href="<?php echo $post->getPermalink();?>" class="eb-post-image" style="background-image: url('<?php echo $post->getImage('medium');?>');">
			</a>
			<?php } ?>
		<?php } ?>

		<div class="eb-post-body">
			<?php echo $blog->text;?>
		</div>

		<?php if ($blog->category->getParam('listing_show_tags', true)) { ?>
			<?php echo $this->output('site/blogs/tags/item', array('tags' => $blog->tags)); ?>
		<?php } ?>

		<?php if ($this->config->get('main_ratings_frontpage') && $blog->category->getParam('listing_show_ratings', true)) { ?>
			<?php echo $this->output('site/ratings/frontpage', array('blog' => $blog)); ?>
		<?php } ?>

		<?php if ($blog->category->getParam('listing_show_social', true)) { ?>
			<?php echo EB::showSocialButton($blog, true); ?>
		<?php } ?>

		<?php echo $this->output('site/blogs/part.socialbuttons', array('post' => $blog)); ?>

		<?php echo $this->output('site/blogs/latest/part.comments', array('blog' => $blog)); ?>
	</div>

	<?php echo $this->output('site/blogs/latest/part.foot', array('blog' => $blog)); ?>
</div>
