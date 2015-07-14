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

defined('_JEXEC') or die('Restricted access');
?>
<head>
	<link href="modules/mod_easyblogticker/assets/styles/style.css?v=2011-04-25" rel="stylesheet" type="text/css" />
	<link href="modules/mod_easyblogticker/assets/styles/ticker-style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
	<script src="modules/mod_easyblogticker/assets/includes/site.js" type="text/javascript"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
	<script src="modules/mod_easyblogticker/assets/includes/jquery.ticker.js" type="text/javascript"></script>
</head>
<div>
	<ul id="js-news" class="js-hidden">
		<?php $i = 1; ?>
		<?php foreach ($items as $item) { ?>
			<li class="news-item"><?php echo $item->title; ?></li>
			<?php } ?>
	</ul>
</div>
