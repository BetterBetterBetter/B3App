<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAdminView extends JViewLegacy
{
	protected $heading = null;
	protected $desc = null;
	protected $doc = null;
	protected $my = null;
	protected $app = null;
	protected $config = null;
	protected $jconfig = null;

	public function __construct()
	{
		$this->config = EB::getConfig();
		$this->jconfig = JFactory::getConfig();
		$this->app = JFactory::getApplication();
		$this->doc = JFactory::getDocument();
		$this->my = JFactory::getUser();
		$this->input = EB::request();
		$this->info = EB::info();

		$this->theme = EB::getTemplate(null, array('view' => $this, 'admin' => true));

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = EB::ajax();
		}

		parent::__construct();
	}

	/**
	 * Allows child classes to set heading of the page
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setHeading($heading, $desc = '', $icon = '')
	{
		$this->heading = $heading;

		if (empty($desc)) {
			$this->desc = $heading . '_DESC';
		}
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkAccess($rule)
	{
		if (!$this->my->authorise($rule , 'com_easyblog')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	}

	/**
	 * Override parent's implementation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Set the appropriate namespace
		$namespace 	= 'admin/' . $tpl;

		// Get the child contents
		$output = $this->theme->output($namespace);

		// Get the sidebar
		$sidebar = $this->getSidebar();

		// Determine if this is a tmpl view
		$tmpl = $this->input->get('tmpl', '', 'word');

		// Prepare the structure
		$theme = EB::getTemplate();

		// Get current version
		$version = EB::getLocalVersion();

		// Render a different structure prefix when tmpl=component
		$prefix = $tmpl == 'component' ? 'eb-window' : '';

		// Initialize all javascript frameworks
		EB::init('admin');

		// Collect all javascripts attached so that we can output them at the bottom of the page
		$scripts = EB::scripts()->getScripts();

		$theme->set('info', $this->info);
		$theme->set('prefix', $prefix);
		$theme->set('version', $version);
		$theme->set('heading', $this->heading);
		$theme->set('desc', $this->desc);
		$theme->set('output', $output);
		$theme->set('tmpl', $tmpl);
		$theme->set('sidebar', $sidebar);
		$theme->set('jscripts', $scripts);

		$contents = $theme->output('admin/structure/default');

		// If the toolbar registration exists, load it up
		if (method_exists($this, 'registerToolbar')) {
			$this->registerToolbar();
		}

		echo $contents;
	}

	/**
	 * Proxy for setting a variable to the template.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set($key, $value = '')
	{
		$this->theme->set($key, $value);
	}

	/**
	 * Processes counters from the menus.json
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCounter($namespace)
	{
		static $counters = array();

		list($model, $method) = explode('/', $namespace);

		if (!isset($counters[$namespace])) {
			$model = EB::model($model);

			$counters[$namespace] = $model->$method();
		}

		return $counters[$namespace];
	}

	/**
	 * Prepares the sidebar
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSidebar()
	{
		$file = JPATH_COMPONENT . '/defaults/menus.json';
		$contents = JFile::read($file);

		$view = $this->input->get('view', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');
		$menus = json_decode($contents);

		foreach ($menus as &$menu) {

			if (!isset($menu->view)) {
				$menu->link = 'index.php?option=com_easyblog';
				$menu->view = '';
			}

			if (isset($menu->counter)) {
				$menu->counter = $this->getCounter($menu->counter);
			}

			if (!isset($menu->link)) {
				$menu->link = 'index.php?option=com_easyblog&view=' . $menu->view;
			}

			if (isset($menu->childs) && $menu->childs) {

				foreach ($menu->childs as &$child) {

					$child->link = 'index.php?option=com_easyblog&view=' . $menu->view;

					if ($child->url) {
						foreach ($child->url as $key => $value) {

							if (!empty($value)) {
								$child->link .= '&' . $key . '=' . $value;
							}
						}
					}

					// Processes items with counter
					if (isset($child->counter)) {
						$child->counter = $this->getCounter($child->counter);
					}
				}
			}
		}

		$theme = EB::getTemplate();

		$theme->set('layout', $layout);
		$theme->set('view', $view);
		$theme->set('menus', $menus);

		$output = $theme->output('admin/structure/default.sidebar');

		return $output;
	}

}
