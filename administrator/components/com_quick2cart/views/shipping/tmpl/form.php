<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.modal');

?>
<div class="quick2cart-wrapper  <?php echo Q2C_WRAPPER_CLASS; ?>">
	<?php
	if (!empty($this->form))
	{
		echo $this->form;
	}
	?>
</div>
