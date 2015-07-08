<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="view-badges" data-dashboard>
	<div class="view-heading" data-dashboard-heading>
		<h3 data-heading-title><?php echo JText::_( 'COM_EASYSOCIAL_HEADING_BADGES' );?></h3>
		<p data-heading-desc><?php echo JText::_( 'COM_EASYSOCIAL_HEADING_BADGES_DESC' ); ?></p>
	</div>

	<div class="es-container pt-20">
		<?php if( $badges ){ ?>
		<div class="row">
		<ul class="list-unstyled badge-list">
		<?php foreach( $badges as $badge ){ ?>
			<div class="col-md-4">
			<li>
				<div class="es-widget es-badge">
					<div class="es-widget-head">
						<a href="<?php echo $badge->getPermalink();?>">
							<h5><?php echo $badge->get( 'title' ); ?></h5>
						</a>
					</div>

					<div class="es-widget-body">

						<a href="<?php echo $badge->getPermalink();?>">
							<img class="badge-icon" alt="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" src="<?php echo $badge->getAvatar();?>" />
						</a>
						<div class="badge-desp">
							<?php echo $badge->get( 'description' ); ?>
						</div>
						
					</div>

					<div class="es-widget-foot">
						<h6><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_ACHIEVERS' );?>:</h6>
						<span><?php echo $badge->getTotalAchievers();?></span>
					</div>
				</div>
			</li>
			</div>
		<?php } ?>
		</ul>
		</div>

		<?php } else { ?>
		<div class="empty">
			<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_NO_BADGES_YET' ); ?>
		</div>
		<?php } ?>
	</div>

	<div class="mt-20 text-center">
		<?php echo $pagination->getListFooter( 'site' );?>
	</div>
</div>
