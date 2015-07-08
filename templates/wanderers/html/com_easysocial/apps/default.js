<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
EasySocial.require()
.script( 'site/apps/apps' )
.done(function($)
{
	$( '[data-apps]' ).implement( EasySocial.Controller.Apps ,
		{
			filter 			: "<?php echo $filter; ?>",
			requireTerms	: <?php echo $this->config->get( 'apps.tnc.required' ) ? 'true' : 'false';?>
		});
});