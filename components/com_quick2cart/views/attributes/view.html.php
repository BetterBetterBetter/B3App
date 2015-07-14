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

jimport('joomla.application.component.view');
class quick2cartViewattributes extends JViewLegacy
{
	function display($tpl = null)
	{
		$input=JFactory::getApplication()->input;
		$layout		= $input->get( 'layout','' );
		if($layout=='attribute'){
			$id = $input->get( 'attr_id',0,'INT' );
			$this->itemattribute_id=$id;
	//		$this->_setToolBar();

			$attribute = $this->get('Attribute');
			if($attribute)
			{
				$this->itemattribute_name=$attribute->itemattribute_name;
				$this->attribute_compulsary=$attribute->attribute_compulsary;
			}

			$attribute_options = $this->get('Attributeoption');
			$this->attribute_opt=$attribute_options;
		}
		parent::display($tpl);
	}
	function _setToolBar()
	{
		$document = JFactory::getDocument();
		//commented by aniket
		//$document->addStyleSheet(JUri::base().'components/com_quick2cart/assets/css/quick2cart.css');//aniket
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'QTC_SETT' ), 'icon-48-quick2cart.png' );
		JToolBarHelper::save('save',JText::_('QTC_SAVE') );
		JToolBarHelper::cancel( 'cancel', JText::_('QTC_CLOSE') );
	}
}
?>
