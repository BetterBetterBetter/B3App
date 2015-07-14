<?php
/**
 * @version     2.2
 * @package     com_quick2cart
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Techjoomla <contact@techjoomla.com> - http://techjoomla.com
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Taxprofiles list controller class.
 */
class Quick2cartControllerTaxprofiles extends Quick2cartController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Taxprofiles', $prefix = 'Quick2cartModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
     * Method use to delete taxprofile.
     *
     * @since	2.2
     */
	public function delete()
	{
		$app = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid','', 'array');
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('taxprofiles');
		$successCount = $model->delete($cid);

		if (!empty($successCount))
		{
				$msg = JText::sprintf('COM_QUICK2CART_S_TAXPROFILE_DELETED_SUCCESSFULLY');

		}else{
			$msg = JText::_('COM_QUICK2CART_S_TAXPROFILE_ERROR_DELETE').'</br>'.$model->getError();

		}

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect = JRoute::_('index.php?option=com_quick2cart&view=taxprofiles&Itemid='.$itemid,false);

		$this->setMessage($msg);
		$this->setRedirect($redirect);
	}


	/**
	 * This function publishes taxrate.
	 *
	 * @since   2.2
	 * @return   null
	 */
	function publish ()
	{
		$app = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid','', 'array');
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('taxprofiles');

		if ($model->setItemState($cid, 1))
		{
			$msg = JText::sprintf('COM_QUICK2CART_S_TAXPROFILES_PUBLISH_SUCCESSFULLY', count($cid));
		}

		$comquick2cartHelper= new comquick2cartHelper;
		$itemid=$comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect=JRoute::_('index.php?option=com_quick2cart&view=taxprofiles&Itemid='.$itemid,false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);

	}
	/**
	 * This function unpublishes taxrate.
	 *
	 * @since   2.2
	 * @return   null
	 */
	function unpublish ()
	{
		$app = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid','', 'array');
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('taxprofiles');

		if ($model->setItemState($cid, 0))
		{
			$msg = JText::sprintf(JText::_('COM_QUICK2CART_S_TAXPROFILES_UNPUBLISH_SUCCESSFULLY'), count($cid));
		}

		$comquick2cartHelper= new comquick2cartHelper;
		$itemid=$comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$redirect=JRoute::_('index.php?option=com_quick2cart&view=taxprofiles&Itemid='.$itemid,false);
		$this->setMessage($msg);
		$this->setRedirect($redirect);

	}
}
