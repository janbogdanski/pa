<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logo extends Public_Controller
{
	public function __construct()
	{
		parent::__construct();
//		$this->load->model('logo_m');
        $this->load->helper('url');
		$this->lang->load('logo');

        if (!$this->ion_auth->logged_in()){
            redirect('users/login');
        }

	}

	// images/page/x also routes here
	public function index()
	{

		$this->template
			->title($this->module_details['name'])
			->set_breadcrumb( lang('images_images_title'))
//			->set_metadata('description', $meta['description'])
//			->set_metadata('keywords', $meta['keywords'])
            ->append_metadata(js('jquery/MultiFile.js'))
            ->append_metadata( js('jquery/jquery.form.js') )
            ->append_metadata(js('images.js', 'images'))
            ->append_metadata(css('images.css', 'images'))
			->build('index', $this->data);
	}
}
