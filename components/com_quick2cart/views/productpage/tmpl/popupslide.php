<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

JHtml::_('behavior.framework');

//load style sheet
$document = JFactory::getDocument();
$comquick2cartHelper=new comquick2cartHelper;
//ADD CSS FILE REQUIRE IN PREVIEW PAGE--NOT REQUIRED..ANIKET
//$document->addStyleSheet(JUri::root().'components/com_quick2cart/assets/css/quick2cart.css' );//aniket
?>
<script type="text/javascript">
techjoomla.jQuery(function() {
        // Gallery
    if(jQuery("#gallery").length){
        // Declare variables
        var totalImages = jQuery("#gallery > li").length,
            imageWidth = jQuery("#gallery > li:first").outerWidth(true),
            totalWidth = imageWidth * totalImages,
            visibleImages = Math.round(jQuery("#gallery-wrap").width() / imageWidth),
            visibleWidth = visibleImages * imageWidth,
            stopPosition = (visibleWidth - totalWidth);

        jQuery("#gallery").width(totalWidth);

        jQuery("#gallery-prev").click(function(){
            if(jQuery("#gallery").position().left < 0 && !jQuery("#gallery").is(":animated")){
                jQuery("#gallery").animate({left : "+=" + imageWidth + "px"});
            }
            return false;
        });

        jQuery("#gallery-next").click(function(){
            if(jQuery("#gallery").position().left > stopPosition && !jQuery("#gallery").is(":animated")){
                jQuery("#gallery").animate({left : "-=" + imageWidth + "px"});
            }
            return false;
        });
    }
});
</script>
<?php


							//	print"<pre>"; print_r($images);
							//print_r($this->itemdetail->images); die;
							$params = JComponentHelper::getParams('com_quick2cart');
							$images=(!empty($this->itemdetail->images))?json_decode($this->itemdetail->images,true):'';

								?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<div id="gallery-wrap" class="span6">
			<ul id="gallery">
			<?php
			//print"<pre>"; print_r($images);
			require_once(JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'helpers'.DS.'media.php');//2.7.5b1 manoj
			//create object of media helper class
			$media=new qtc_mediaHelper();
			if(!empty($images))
			{
				foreach($images as $image ){
					$file_name_without_extension=$media->get_media_file_name_without_extension($image);
					$media_extension=$media->get_media_extension($image);
					$img=$comquick2cartHelper->isValidImg($file_name_without_extension.'_L.'.$media_extension);
					if(empty($img)){
						$img=JUri::base().'components'.DS.'com_quick2cart'.DS.'images'.DS.'default_product.jpg';
					}
						?>
				<li>
					<a href="<?php echo $img;?>">
						<img src="<?php echo $img;?>" alt="<?php echo  JText::_('QTC_IMG_NOT_FOUND') ?>" style='width: <?php $params->get( 'small_width' ) ?>px; height: <?php $params->get( 'small_height' ) ?>px;' />
					</a>
				</li>
					<?php }
			} ?>
			</ul>
			<span class="rightx carousel-control" id="gallery-next"><i class="icon-chevron-right"></i></span>
			<span class="left carousel-control" id="gallery-prev"><i class="icon-chevron-left"></i></span>
	</div>
</div>
