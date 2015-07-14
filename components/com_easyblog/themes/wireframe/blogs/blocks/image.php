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
<div class="eb-image<?php echo $classnames; ?>"<?php echo $imageContainerAttr; ?>>
    <div class="eb-image-figure"<?php echo $imageFigureAttr; ?>>
        <a class="eb-image-viewport"<?php echo $imageLinkAttr; ?>>
            <img src="<?php echo $block->data->url;?>"<?php echo $imageElementAttr; ?>/>
        </a>
        <?php if (!empty($block->data->popup_url)) { ?>
        <a class="eb-image-popup-button" href="<?php echo $block->data->popup_url;?>" target="_blank" title="<?php echo $block->data->caption_text;?>">
            <i class="fa fa-search"></i>
        </a>
        <?php } ?>
    </div>
    <?php if (!empty($block->data->caption_text)) { ?>
    <div class="eb-image-caption"<?php echo $imageCaptionAttr; ?>>
        <span><?php echo $block->data->caption_text; ?></span>
    </div>
    <?php } ?>
</div>