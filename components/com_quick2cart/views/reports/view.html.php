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

jimport( 'joomla.application.component.view');

class quick2cartViewReports extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe,$option;
		$mainframe=JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		//imp
		$this->issite=1;//this is frontend

		//default layout is default
		$layout = $jinput->get('layout','mypayouts','STRING');
		$this->setLayout($layout);

		//get logged in user id
		$user=JFactory::getUser();
		$this->logged_userid=$user->id;

		/*load language file for component backend*/
		$lang = 	 JFactory::getLanguage();

		if($layout=='mypayouts')
		{
			if(!$this->logged_userid)
			{
				$msg=JText::_('LOGIN_MSG');
				$uri = $_SERVER["REQUEST_URI"];
				$layout = $jinput->get('layout','mypayouts','STRING');
				$url=base64_encode($uri);
				$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$url),$msg);
			}
			//@TODO WHAT IF store is not present OR order is not places against store
			$payouts=$this->get('Payouts');
			$this->payouts=$payouts;

			//$filter_order_Dir=$mainframe->getUserStateFromRequest('com_jgive.filter_order_Dir','filter_order_Dir','desc','word');
			//$filter_type=$mainframe->getUserStateFromRequest('com_jgive.filter_order','filter_order','goal_amount','string');

			//$payee_name=$mainframe->getUserStateFromRequest('com_jgive', 'payee_name','', 'string' );

			//$lists['payee_name']=$payee_name;

			//$lists['order_Dir']=$filter_order_Dir;
			//$lists['order']=$filter_type;
			//$this->lists=$lists;

			// get total record count
			$total=$this->get('Total');
			$this->total=$total;


			$pagination=$this->get('Pagination');
			$this->pagination=$pagination;

		}
		$this->_setToolBar();
		parent::display($tpl);
	}

	function _setToolBar()
	{
		//added by aniket for task #25690
		$document=  JFactory::getDocument();
		$document->setTitle( JText::_( 'QTC_REPORTS_PAGE' ));
	}
}
?>
