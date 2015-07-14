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
<?php echo $adsense->header;?>

<div itemscope itemtype="http://schema.org/BlogPosting" data-blog-post>
    <div id="entry-<?php echo $post->id; ?>" class="eb-entry fd-cf" data-blog-posts-item data-id="<?php echo $post->id;?>" data-uid="<?php echo $post->getUid();?>">

        <?php if ($post->isPending() && $post->canModerate()) { ?>
            <?php echo $this->output('site/blogs/entry/moderate'); ?>
        <?php } ?>

        <?php if ($post->isUnpublished()) { ?>
            <?php echo $this->output('site/blogs/entry/entry.unpublished'); ?>
        <?php } ?>

        <?php if ($preview) { ?>
            <?php if ($post->isPostPublished()) { ?>
                <?php echo $this->output('site/blogs/entry/preview.revision'); ?>
            <?php } else { ?>
                <?php echo $this->output('site/blogs/entry/preview.unpublished'); ?>
            <?php } ?>
        <?php } ?>

        <div class="eb-entry-tools row-table">
            <div class="col-cell">
                <?php echo $this->output('site/blogs/entry/tools'); ?>
            </div>

            <?php if (!$preview) { ?>
            <div class="col-cell cell-tight">
                <?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $post->getPermalink(false))); ?>
            </div>
            <?php } ?>
        </div>

        <?php echo $this->renderModule('easyblog-before-entry'); ?>

        <div class="eb-entry-head">

            <?php if ($post->getType() == 'quote' && $this->entryParams->get('show_title', true)) { ?>
                <div class="eb-placeholder-quote">
                    <h1 itemprop="name headline" id="title-<?php echo $post->id; ?>" class="eb-placeholder-quote-text eb-post-title reset-heading"><?php echo nl2br($post->title); ?></h1>
                    <?php if ($post->text) {  ?>
                        <div class="eb-placeholder-quote-source"><?php echo $post->text; ?></div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if ($post->getType() == 'link' && $this->entryParams->get('show_title', true)) { ?>
                <h1 itemprop="name headline" id="title-<?php echo $post->id; ?>" class="eb-entry-title reset-heading <?php echo ($post->isFeatured()) ? ' featured-item' : '';?> "><?php echo $post->title; ?></h1>
            <?php } ?>

            <?php if ($post->getType() == 'twitter' && $this->entryParams->get('show_title', true)) { ?>
                <div class="eb-placeholder-quote">
                    <h1 itemprop="name headline" id="title-<?php echo $post->id; ?>" class="eb-placeholder-quote-text eb-post-title reset-heading"><?php echo $post->text; ?></h1>

                    <?php if (!empty($screen_name) && !empty($created_at)) { ?>
                    <div class="eb-placeholder-quote-source">
                        <?php echo '@'.$screen_name.' - '.$created_at; ?>
                        &middot;
                        <a href="<?php echo $post->getPermalink();?>">
                            <?php echo JText::_('COM_EASYBLOG_LINK_TO_POST'); ?>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if ((in_array($post->getType(), array('photo', 'standard', 'video', 'email'))) && $this->entryParams->get('post_title', true)) { ?>
                <h1 itemprop="name headline" id="title-<?php echo $post->id; ?>" class="eb-entry-title reset-heading <?php echo ($post->isFeatured()) ? ' featured-item' : '';?> "><?php echo $post->title; ?></h1>
            <?php } ?>


            <?php echo $post->event->afterDisplayTitle; ?>

            <div class="eb-entry-meta text-muted">
                <?php if ($post->isFeatured()) { ?>
                <div class="eb-entry-featured">
                    <i class="fa fa-star text-muted"></i>
                    <span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></span>
                </div>
                <?php } ?>

                <?php if ($this->entryParams->get('post_date', true)) { ?>
                <div class="eb-entry-date">
                    <i class="fa fa-clock-o"></i>
                    <time class="eb-meta-date" itemprop="datePublished" content="<?php echo $post->getCreationDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
                        <?php echo $post->getDisplayDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
                    </time>
                </div>
                <?php } ?>

                <?php if ($this->entryParams->get('post_author', true)) { ?>
                <div class="eb-meta-author" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
                    <i class="fa fa-pencil"></i>
                    <span itemprop="name">
                        <a href="<?php echo $post->author->getPermalink();?>" itemprop="url" rel="author"><?php echo $post->author->getName();?></a>
                    </span>
                </div>
                <?php } ?>

                <?php if ($this->entryParams->get('post_category', true)) { ?>
                    <div class="eb-meta-category comma-seperator">
                        <i class="fa fa-folder-open"></i>
                        <?php foreach ($post->categories as $cat) { ?>
                        <span><a href="<?php echo $cat->getPermalink();?>"><?php echo $cat->getTitle();?></a></span>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($this->entryParams->get('post_hits', true)) { ?>
                <div class="eb-meta-views">
                    <i class="fa fa-eye"></i>
                    <?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
                </div>
                <?php } ?>

                <?php if ($this->config->get('main_comment') && $post->totalComments !== false && $this->entryParams->get('post_comment_counter', true) && $post->allowcomment) { ?>
                <div class="eb-meta-comments">
                    <?php if ($this->config->get('comment_disqus')) { ?>
                        <i class="fa fa-comments"></i>
                        <?php echo $post->totalComments; ?>
                    <?php } else { ?>
                        <i class="fa fa-comments"></i>
                        <a href="#comments"><?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->totalComments, true); ?></a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="eb-entry-body type-<?php echo $post->posttype; ?> clearfix">
            <div class="eb-entry-article clearfix" itemprop="articleBody" data-blog-content>

                <?php echo $post->event->beforeDisplayContent; ?>

                <?php if(!empty($post->toc)){ echo $post->toc; } ?>

                <?php echo EB::renderModule('easyblog-before-content'); ?>


                <?php if (in_array($post->posttype, array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
                    <?php if ($post->image && $this->entryParams->get('post_image', true)) { ?>

                        <?php if (!$this->config->get('cover_crop', false)) { ?>
                        <div class="eb-image eb-entry-image">
                            <a class="easyblog-thumb-preview eb-image-popup-button"
                               href="<?php echo $post->getImage('original');?>"
                               title="<?php echo $this->escape($post->title);?>"
                               target="_blank"
                            >
                                <img src="<?php echo $post->getImage('large');?>" alt="<?php echo $post->title; ?>">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <?php } ?>

                        <?php if ($this->config->get('cover_crop', false)) { ?>
                        <div class="eb-image eb-entry-image eb-entry-image-cover" style="background-image: url('<?php echo $post->getImage('large');?>')">
                            <a class="easyblog-thumb-preview eb-image-popup-button"
                               href="<?php echo $post->getImage('original');?>"
                               title="<?php echo $this->escape( $post->title );?>"
                               target="_blank"
                            >
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <?php } ?>

                    <?php } ?>

                    <!--LINK TYPE FOR ENTRY VIEW-->
                    <?php if ($post->getType() == 'link') { ?>
                        <div class="eb-post-headline">
                            <div class="eb-post-headline-source">
                                <a href="<?php echo $post->getAsset('link')->getValue(); ?>" target="_blank"><?php echo $post->getAsset('link')->getValue();?></a>
                            </div>
                        </div>    
                    <?php } ?>

                    <?php if ($this->entryParams->get('show_intro', true)) { ?>
                        <?php echo $post->getContent(EASYBLOG_VIEW_ENTRY); ?>
                    <?php } else { ?>
                        <?php echo $post->getContentWithoutIntro(); ?>
                    <?php } ?>
                <?php } ?>

                <?php if ($post->getType() == 'video') { ?>
                    <div>
                        <?php foreach ($post->videos as $video) { ?>
                        <div class="eb-responsive-video">
                            <?php echo $video->html;?>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php echo $this->renderModule('easyblog-after-content'); ?>

                <?php if ($post->fields && $this->entryParams->get('post_fields', true)) { ?>
                    <?php echo $this->output('site/blogs/entry/fields', array('fields' => $post->fields)); ?>
                <?php } ?>                

                <?php echo $this->output('site/blogs/part.socialbuttons', array('post' => $post)); ?>

            </div>

            <?php echo $this->output('site/blogs/entry/location', array('post' => $post)); ?>

            <?php echo $this->output('site/blogs/entry/copyright', array('post' => $post)); ?>

            <?php if ($this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true)) { ?>
            <div class="eb-entry-ratings">
                <?php echo $this->output('site/ratings/frontpage', array('post' => $post)); ?>
            </div>
            <?php } ?>

            <?php if ($this->entryParams->get('post_tags', true)) { ?>
            <div class="eb-entry-tags">
                <?php echo $this->output('site/blogs/tags/item', array('tags' => $post->tags)); ?>
            </div>
            <?php } ?>

            <?php echo $this->output('site/blogs/entry/navigation'); ?>
        </div>

        <?php if ($this->entryParams->get('post_author_box', true)) { ?>
            <?php echo $this->output('site/blogs/entry/author', array('post' => $post)); ?>
        <?php } ?>


        <?php if ($this->entryParams->get('post_related', true) && $relatedPosts) { ?>
        <h4 class="eb-section-heading reset-heading"><?php echo JText::_('Related Posts');?></h4>

        <div class="eb-entry-related clearfix eb-responsive">
            <?php foreach ($relatedPosts as $related) { ?>
            <div>
                <a href="<?php echo $related->getPermalink();?>" class="eb-related-thumb" style="background-image: url('<?php echo $related->getImage();?>');"></a>
                <h3 class="eb-related-title">
                    <a href="<?php echo $related->getPermalink();?>"><?php echo $related->title;?></a>
                </h3>
                <div class="text-muted">
                    <a class="eb-related-category text-inherit" href="<?php echo $related->getPrimaryCategory()->getPermalink();?>"><?php echo $related->getPrimaryCategory()->getTitle();?></a>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php } ?>
    </div>

    <?php echo $adsense->beforecomments; ?>

    <?php echo $post->event->afterDisplayContent; ?>

    <?php if ($this->config->get('main_comment') && $post->allowcomment && $this->entryParams->get('post_comment_form', true)) { ?>
        <?php echo EB::comment()->getCommentHTML($post);?>
    <?php } ?>
</div>

<?php echo $adsense->footer;?>
