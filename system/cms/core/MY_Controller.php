<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH."libraries/MX/Controller.php";

// Code here is run before ALL controllers

/**
 * @property CI_DB_active_record $db
 * @property CI_DB_forge $dbforge
 * @property CI_Benchmark $benchmark
 * @property CI_Calendar $calendar
 * @property CI_Cart $cart
 * @property CI_Config $config
 * @property CI_Controller $controller
 * @property CI_Email $email
 * @property CI_Encrypt $encrypt
 * @property CI_Exceptions $exceptions
 * @property CI_Form_validation $form_validation
 * @property CI_Ftp $ftp
 * @property CI_Hooks $hooks
 * @property CI_Image_lib $image_lib
 * @property CI_Input $input
 * @property CI_Lang $lang
 * @property CI_Loader $load
 * @property CI_Log $log
 * @property CI_Model $model
 * @property CI_Output $output
 * @property CI_Pagination $pagination
 * @property CI_Parser $parser
 * @property CI_Profiler $profiler
 * @property CI_Router $router
 * @property CI_Session $session
 * @property CI_Sha1 $sha1
 * @property CI_Table $table
 * @property CI_Trackback $trackback
 * @property CI_Typography $typography
 * @property CI_Unit_test $unit_test
 * @property CI_Upload $upload
 * @property CI_URI $uri
 * @property CI_User_agent $user_agent
 * @property CI_Validation $validation
 * @property CI_Xmlrpc $xmlrpc
 * @property CI_Xmlrpcs $xmlrpcs
 * @property CI_Zip $zip
 * @property CI_Javascript $javascript
 * @property CI_Jquery $jquery
 * @property CI_Utf8 $utf8
 * @property CI_Security $security
 * @property Ion_auth $ion_auth
 * 
 * 
 * @property Images_m $images_m

 */

class MY_Controller extends MX_Controller {

	// Deprecated: No longer used globally
	protected $data;
	
	public $module;
	public $controller;
	public $method;

	public function __construct()
	{
		parent::__construct();

		$this->benchmark->mark('my_controller_start');
		
		// No record? Probably DNS'ed but not added to multisite
		if ( ! defined('SITE_REF'))
		{
			show_error('This domain is not set up correctly. Please go to '.anchor('sites') .' and log in to add this new site.');
		}
		
		// TODO: Remove this in v2.1.0 as it just renames tables for v2.0.0
		if ($this->db->table_exists(SITE_REF.'_schema_version'))
		{	
			$this->load->dbforge();
			if ($this->db->table_exists(SITE_REF.'_migrations'))
			{
				$this->dbforge->drop_table(SITE_REF.'_schema_version');
			}
			else
			{
				$this->dbforge->rename_table(SITE_REF.'_schema_version', SITE_REF.'_migrations');
			}
		}
		
		// Upgrading from something old? Erf, try to shoehorn them back on track
		elseif ($this->db->table_exists('schema_version'))
		{
			$this->load->dbforge();
			$this->dbforge->rename_table('schema_version', 'migrations');
			
			// Migration logic helps to make sure PyroCMS is running the latest changes
			$this->load->library('migration');

			if ( ! ($schema_version = $this->migration->version(28)))
			{
				show_error($this->migration->error_string());
			}
			redirect(current_url());
		}

		// By changing the prefix we are essentially "namespacing" each site
		$this->db->set_dbprefix(SITE_REF.'_');

		// Load the cache library now that we know the siteref
		$this->load->library('pyrocache');

		// Add the site specific theme folder
		$this->template->add_theme_location(ADDONPATH.'themes/');

		// Migration logic helps to make sure PyroCMS is running the latest changes
		$this->load->library('migration');
		
		if ( ! ($schema_version = $this->migration->current()))
		{
			show_error($this->migration->error_string());
		}

		// Result of schema version migration
		else if (is_numeric($schema_version))
		{
			log_message('debug', 'PyroCMS was migrated to version: ' . $schema_version);
		}

		// With that done, load settings
		$this->load->library(array('session', 'settings/settings'));

		// Lock front-end language
		if ( ! (is_a($this, 'Admin_Controller') && ($site_lang = AUTO_LANGUAGE)))
		{
			$site_public_lang = explode(',', Settings::get('site_public_lang'));

			if (in_array(AUTO_LANGUAGE, $site_public_lang))
			{
				$site_lang = AUTO_LANGUAGE;
			}
			else
			{
				$site_lang = Settings::get('site_lang');
			}
		}

		defined('CURRENT_LANGUAGE') or define('CURRENT_LANGUAGE', $site_lang);

		$langs = $this->config->item('supported_languages');

		$pyro['lang'] = $langs[CURRENT_LANGUAGE];
		$pyro['lang']['code'] = CURRENT_LANGUAGE;

		// Set php locale time
		if (isset($langs[CURRENT_LANGUAGE]['codes']) && sizeof($locale = (array) $langs[CURRENT_LANGUAGE]['codes']) > 1)
		{
			array_unshift($locale, LC_TIME);
			call_user_func_array('setlocale', $locale);
			unset($locale);
		}

		// Reload languages
		if (AUTO_LANGUAGE !== CURRENT_LANGUAGE)
		{
			$this->config->set_item('language', $langs[CURRENT_LANGUAGE]['folder']);
			$this->lang->is_loaded = array();
			$this->lang->load(array('errors', 'global', 'users/user', 'settings/settings'));
		}
		else
		{
			$this->lang->load(array('global', 'users/user'));
		}

		$this->load->library(array('events', 'users/ion_auth'));

		// Use this to define hooks with a nicer syntax
		ci()->hooks =& $GLOBALS['EXT'];

		// Create a hook point with access to instance but before custom code
		$this->hooks->_call_hook('post_core_controller_constructor');

		// Load the user model and get user data
		$this->load->library('users/ion_auth');

		$this->template->current_user = ci()->current_user = $this->current_user = $this->ion_auth->get_user();

		// Work out module, controller and method and make them accessable throught the CI instance
		ci()->module = $this->module = $this->router->fetch_module();
		ci()->controller = $this->controller = $this->router->fetch_class();
		ci()->method = $this->method = $this->router->fetch_method();

		// Loaded after $this->current_user is set so that data can be used everywhere
		$this->load->model(array(
			'permissions/permission_m',
			'modules/module_m',
			'pages/page_m',
			'themes/theme_m',
		));

		// List available module permissions for this user
		ci()->permissions = $this->permissions = $this->current_user ? $this->permission_m->get_group($this->current_user->group_id) : array();

		// Get meta data for the module
		$this->template->module_details = ci()->module_details = $this->module_details = $this->module_m->get($this->module);

		// If the module is disabled, then show a 404.
		empty($this->module_details['enabled']) AND show_404();

		if ( ! $this->module_details['skip_xss'])
		{
			$_POST = $this->security->xss_clean($_POST);
		}

		$this->load->vars($pyro);
		
		$this->benchmark->mark('my_controller_end');
		
		// Enable profiler on local box
	    if (ENVIRONMENT === PYRO_DEVELOPMENT AND is_array($_GET) AND array_key_exists('_debug', $_GET) )
	    {
			unset($_GET['_debug']);
	    	$this->output->enable_profiler(TRUE);
	    }
	}
}

/**
 * Returns the CI object.
 *
 * Example: ci()->db->get('table');
 *
 * @staticvar	object	$ci
 * @return		object
 */
function ci()
{
	return get_instance();
}
