<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php foreach ($authors as $author) { ?>
<div class="eb-composer-pick-item"
    data-eb-author-item
    data-id="<?php echo $author->id;?>"
    data-title="<?php echo $author->getName();?>"
    data-avatar="<?php echo $author->getAvatar();?>"
>
    <div class="radio">
        <input type="radio" name="radio-author" id="radio-<?php echo $author->id;?>" value="<?php echo $author->id;?>" <?php echo $selected == $author->id ? ' checked="checked"' : '';?> 
            data-avatar="<?php echo $author->getAvatar();?>"
            data-id="<?php echo $author->id;?>"
            data-name="<?php echo $author->getName();?>"
            data-eb-composer-authoritem
        />
        <label for="radio-<?php echo $author->id;?>" class="row-table">
            <div class="col-cell">
                <img src="<?php echo $author->getAvatar();?>" class="avatar" width="30" height="30" />
            </div>
            <div class="col-cell">
                <?php echo $author->getName();?>
            </div>
        </label>
    </div>
</div>
<?php } ?>