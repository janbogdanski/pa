<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 		PyroCMS
 * @subpackage 		Widgets
 * @author			Phil Sturgeon - PyroCMS Development Team
 * 
 * Admin controller for the widgets module.
 */
class Admin extends Admin_Controller
{
	function __construct()
	{
		parent::Admin_Controller();
		
		$this->load->library('widgets');
		$this->lang->load('widgets');
		
	    $this->template->set_partial('sidebar', 'admin/sidebar');
	    $this->template->append_metadata( js('widgets.js', 'widgets') );
	    $this->template->append_metadata( css('widgets.css', 'widgets') );
		
		$this->data->widgets = $this->widgets->list_widgets();
		$this->data->widget_areas = $this->widgets->list_areas();
	}
	
	function index()
	{
		// Go through all widget areas
		foreach($this->data->widget_areas as $area)
		{
			//... and get widgets links for each one
			//$this->data->widgets[$area->slug] = $this->widgets->list_widgets($area->slug);
		}
		
		// Create the layout
		$this->template->build('admin/index', $this->data);
	}
	
	function about($slug)
	{
		$widget = $this->widgets->get_widget($slug);
		
		$this->load->view('admin/about_widget', array(
			'widget' => $widget
		));
	}

}
?>