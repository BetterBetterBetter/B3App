<?php

// no direct access
defined('_JEXEC') or die ('Restricted access');

if (!defined('DS'))
{
	define('DS', '/');
}

require_once JPATH_ROOT .'/components/com_community/libraries/core.php';

$lang = JFactory::getLanguage();
$lang->load('plg_community_quick2cartstore', JPATH_ADMINISTRATOR);
class plgCommunityQuick2cartstore extends CApplications
{
	var $name = "Quick2cartstore";
	var $_name	= 'quick2cartstore';

	function onProfileDisplay()
	{
		$cache = JFactory::getCache('community');
		$callback = array($this, '_getquick2cartstoreHTML');

		$content = $cache->call($callback);
		return $content;
	}

	function _getquick2cartstoreHTML()
	{
		jimport('joomla.filesystem.file');
		if( JFile::exists( JPATH_SITE.'/components/com_quick2cart/quick2cart.php') ){
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'helper.php';
		if(!class_exists('comquick2cartHelper'))
		{
		  //require_once $path;
		   JLoader::register('comquick2cartHelper', $path );
		   JLoader::load('comquick2cartHelper');
		}

		// Load assets
		comquick2cartHelper::loadQuicartAssetFiles();

			$product_path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'helpers'.DS.'product.php';
			if(!class_exists('productHelper'))
			{
			  //require_once $path;
			   JLoader::register('productHelper', $product_path );
			   JLoader::load('productHelper');
			}

			$params = $this->params;
			$no_of_stores = $params->get('no_of_stores','2');
				//Get profile id
			$user= CFactory::getRequestUser();
			$model =  new productHelper();
			$target_data = $model->getUserStores($user->_userid,$no_of_stores);
			if(!empty($target_data))
			{
				$html="
				<div class='techjoomla-bootstrap' >
					<div  class='row-fluid'>
					<ul class='thumbnails'  >
					";

				foreach($target_data as $data)
				{
					$path = JPATH_SITE.DS.'components'.DS.'com_quick2cart'.DS.'views'.DS.'vendor'.DS.'tmpl'.DS.'thumbnail.php';
						//@TODO  condition vise mod o/p
					ob_start();
					include($path);
					$html.= ob_get_contents();
					ob_end_clean();
				}
				$html.="
						</ul>
					</div>
				</div>";
				return $html;
			}
		}
	}

}
