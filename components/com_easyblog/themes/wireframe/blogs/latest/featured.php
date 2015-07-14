<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($featured && $this->params->get('featured_slider', true)) { ?>
<div class="eb-featured eb-responsive">
    <div id="eb-showcases" class="eb-showcases carousel slide mootools-noconflict"  data-featured-posts>
        <?php if ($this->params->get('featured_bottom_navigation', true) && count($featured) > 1) { ?>
            <ol class="eb-showcase-indicators carousel-indicators reset-list text-center">
                <?php for ($i = 0; $i < count($featured); $i++) { ?>
                    <li data-target=".eb-showcases" data-bp-slide-to="<?php echo $i;?>" class="<?php echo $i == 0 ? 'active' : '';?>"></li>
                <?php } ?>
            </ol>
        <?php } ?>

        <div class="carousel-inner">
            <?php $i = 0; ?>
            <?php foreach ($featured as $post) { ?>
            <?php ++$i;?>
            <div class="item<?php echo $i == 1 ? ' active' : '';?>">
                <div class="eb-showcase">

                    <?php if ($this->params->get('featured_post_image', true)) { ?>
                        <?php
                            $imgSrc = $post->getImage('medium');

                            if (!$post->image) {
                                // lets try to get image from content;
                                $tmpImg = $post->getContentImage();
                                if ($tmpImg) {
                                    $imgSrc = $tmpImg;
                                }
                            }
                        ?>
                        <div class="col-cell cell-tight eb-showcase-thumbs">
                            <a class="eb-showcase-thumb<?php echo !$post->image ? ' is-empty' : '';?>" style="background-image: url('<?php echo $imgSrc; ?>');"></a>
                        </div>
                    <?php } ?>

                    <div class="col-cell eb-showcase-content">

                        <div class="eb-showcase-publish">
                            <?php if ($this->params->get('featured_post_author_avatar', true)) { ?>
                            <div class="col-cell cell-tight">
                                <a href="<?php echo $post->author->getProfileLink(); ?>" class="eb-avatar-sm">
                                    <img src="<?php echo $post->author->getAvatar();?>" class="eb-avatar eb-avatar-sm" width="30" height="30" alt="<?php echo $this->html('string.escape', $post->author->getName());?>" />
                                </a>
                            </div>
                            <?php } ?>

                            <div class="col-cell">
                                <?php if ($this->params->get('featured_post_author', true) && $this->params->get('featured_post_date', true)) { ?>
                                <a href="<?php echo $post->author->getProfileLink();?>"><?php echo $post->author->getName(); ?></a>
                                <br>
                                <span class="muted small"><?php echo JText::sprintf('<time>' . $post->getDisplayDate($this->params->get('featured_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')) . '</time>'); ?></span>
                                <?php } ?>

                                <?php if ($this->params->get('featured_post_author', true) && !$this->params->get('featured_post_date', true)) { ?>
                                    <?php echo JText::sprintf('COM_EASYBLOG_BLOG_POSTED_BY', '<a href="' . $post->author->getProfileLink() . '">' . $post->author->getName() . '</a>'); ?>
                                <?php } ?>

                                <?php if (!$this->params->get('featured_post_author', true) && $this->params->get('featured_post_date', true)) { ?>
                                    <?php echo JText::sprintf('COM_EASYBLOG_BLOG_POSTED_ON', '<time>' . $post->getDisplayDate($this->params->get('featured_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')) . '</time>'); ?>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if ($this->params->get('featured_post_title', true)) { ?>
                        <h2 class="eb-showcase-title reset-heading">
                            <a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
                        </h2>
                        <?php } ?>

                        <?php if ($this->params->get('featured_post_category', true)) { ?>
                        <div class="eb-showcase-category comma-seperator">
                            <?php echo JText::_('COM_EASYBLOG_POSTED_UNDER'); ?>:
                            <?php foreach ($post->getCategories() as $category) { ?>
                            <span>
                                <a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
                            </span>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <?php if ($this->params->get('featured_post_content', true)) { ?>
                        <div class="eb-showcase-article">
                            <?php echo $this->html('string.truncater', $post->getIntro(EASYBLOG_STRIP_TAGS), $this->params->get('featured_post_content_limit', 250));?>
                        </div>
                        <?php } ?>

                        <?php if ($this->params->get('featured_post_readmore', true)) { ?>
                        <div class="eb-post-more">
                            <a class="btn btn-danger" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="eb-showcase-control btn-group">
            <a class="btn btn-default btn-sm" href="#eb-showcases" role="button" data-bp-slide="prev">
                <span class="fa fa-angle-left"></span>
            </a>
            <a class="btn btn-default btn-sm" href="#eb-showcases" role="button" data-bp-slide="next">
                <span class="fa fa-angle-right"></span>
            </a>
        </div>
    </div>
</div>
<?php } ?>
