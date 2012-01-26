<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Images extends Public_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('images_m');
//		$this->load->model('images_categories_m');
//		$this->load->model('comments/comments_m');
        $this->load->helper('url');
//		$this->load->library(array('keywords/keywords'));
		$this->lang->load('images');

        if (!$this->ion_auth->logged_in()){
            redirect('users/login');
        }

	}

	// images/page/x also routes here
	public function index()
	{
        //todo lightbox
		$this->data->pagination = create_pagination('images/page', $this->images_m->count_by(array('user_id' => $this->current_user->id)), NULL, 3);
		$this->data->images = $this->images_m->limit($this->data->pagination['limit'])->get_many_by(array('user_id' => $this->current_user->id));

		// Set meta description based on post titles
//		$meta = $this->_posts_metadata($this->data->images);
		
		foreach ($this->data->images AS &$image)
		{
//            $image->keywords = Keywords::get_links($image->keywords, 'images/tagged');
		}

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
    
    public function upload(){
        ignore_user_abort(1);

            
        $this->load->library('imageshack');
//        print_r($this->session);
//        print_r($_FILES);

        $return = array();

        if (! empty ( $_FILES['image']['name'][0] ))
        {
//            print_r($_FILES);
            for($i=0; $i<count($_FILES['image']['name']); $i++){
                $tempFile = $_FILES ['image'] ['tmp_name'][$i];
                $targetFile = 'uploads/'.rand(1,999).$_FILES ['image'] ['name'][$i];

                move_uploaded_file ( $tempFile, $targetFile);

                $imageshack = new Imageshack();
                $response = $imageshack->upload($targetFile //, 
                // param('optsize'), 
                //  param('remove-bar') != 'no', 
                //  param('tags')
                );
//                print_r($response);
                $this->images_m->saveImgshackImage($response);
                unlink($targetFile);
//                echo 'asd;';

                //wyswietl na stronie ostanio dodane
                if (isset($response->links->{'thumb_link'})) {
                    $return['link'][] = $response->links->{'thumb_link'};
                } else {
                    $return['link'][] = $response->links->{'image_link'};
                }
                
            }
           echo  json_encode($return);
        }
    }
    
    public function fotoflexer_success($id){

        
        $image = $this->input->get('image');

        $this->load->library('imageshack');
        $imageshack = new Imageshack();
        $response = $imageshack->transload($image);
        $this->images_m->saveImgshackImage($response, $id);


        $this->session->set_flashdata('success', $this->lang->line('images_image_updated'));
        redirect('/images/index');

    }

    public function fotoflexer_cancel(){
        redirect('/images/index');

    }
    
    public function delete($id){

        $data = array(
            'image_id' =>  (int)$id,
            'user_id' => $this->current_user->id,
        );
        $this->images_m->delete_by($data);
        $this->session->set_flashdata('success', $this->lang->line('images_image_deleted'));
        redirect('images/index');
    }

   
    

	public function category($slug = '')
	{
		$slug OR redirect('images');

		// Get category data
		$category = $this->images_categories_m->get_by('slug', $slug) OR show_404();

		// Count total images posts and work out how many pages exist
		$pagination = create_pagination('images/category/'.$slug, $this->images_m->count_by(array(
			'category'=> $slug,
			'status' => 'live'
		)), NULL, 4);

		// Get the current page of images posts
		$images = $this->images_m->limit($pagination['limit'])->get_many_by(array(
			'category'=> $slug,
			'status' => 'live'
		));

		// Set meta description based on post titles
		$meta = $this->_posts_metadata($images);
		
		foreach ($images AS &$post)
		{
			$post->keywords = Keywords::get_links($post->keywords, 'images/tagged');
		}

		// Build the page
		$this->template->title($this->module_details['name'], $category->title )
			->set_metadata('description', $category->title.'. '.$meta['description'] )
			->set_metadata('keywords', $category->title )
			->set_breadcrumb( lang('images_images_title'), 'images')
			->set_breadcrumb( $category->title )
			->set('images', $images)
			->set('category', $category)
			->set('pagination', $pagination)
			->build('category', $this->data );
	}

	public function archive($year = NULL, $month = '01')
	{
		$year OR $year = date('Y');
		$month_date = new DateTime($year.'-'.$month.'-01');
		$this->data->pagination = create_pagination('images/archive/'.$year.'/'.$month, $this->images_m->count_by(array('year'=>$year,'month'=>$month)), NULL, 5);
		$this->data->images = $this->images_m->limit($this->data->pagination['limit'])->get_many_by(array('year'=> $year,'month'=> $month));
		$this->data->month_year = format_date($month_date->format('U'), lang('images_archive_date_format'));

		// Set meta description based on post titles
		$meta = $this->_posts_metadata($this->data->images);
		
		foreach ($this->data->images AS &$post)
		{
			$post->keywords = Keywords::get_links($post->keywords, 'images/tagged');
		}

		$this->template->title( $this->data->month_year, $this->lang->line('images_archive_title'), $this->lang->line('images_images_title'))
			->set_metadata('description', $this->data->month_year.'. '.$meta['description'])
			->set_metadata('keywords', $this->data->month_year.', '.$meta['keywords'])
			->set_breadcrumb($this->lang->line('images_images_title'), 'images')
			->set_breadcrumb($this->lang->line('images_archive_title').': '.format_date($month_date->format('U'), lang('images_archive_date_format')))
			->build('archive', $this->data);
	}

	// Public: View a post
	public function view($slug = '')
	{
		if ( ! $slug or ! $post = $this->images_m->get_by('slug', $slug))
		{
			redirect('images');
		}

		if ($post->status != 'live' && ! $this->ion_auth->is_admin())
		{
			redirect('images');
		}
		
		// if it uses markdown then display the parsed version
		if ($post->type == 'markdown')
		{
			$post->body = $post->parsed;
		}

		// IF this post uses a category, grab it
		if ($post->category_id && ($category = $this->images_categories_m->get($post->category_id)))
		{
			$post->category = $category;
		}

		// Set some defaults
		else
		{
			$post->category->id		= 0;
			$post->category->slug	= '';
			$post->category->title	= '';
		}

		$this->session->set_flashdata(array('referrer' => $this->uri->uri_string));

		$this->template->title($post->title, lang('images_images_title'))
			->set_metadata('description', $post->intro)
			->set_metadata('keywords', implode(', ', Keywords::get_array($post->keywords)))
			->set_breadcrumb(lang('images_images_title'), 'images');

		if ($post->category->id > 0)
		{
			$this->template->set_breadcrumb($post->category->title, 'images/category/'.$post->category->slug);
		}
		
		$post->keywords = Keywords::get_links($post->keywords, 'images/tagged');

		$this->template
			->set_breadcrumb($post->title)
			->set('post', $post)
			->build('view', $this->data);
	}
	
	public function tagged($tag = '')
	{
		$tag OR redirect('images');

		// Count total images posts and work out how many pages exist
		$pagination = create_pagination('images/tagged/'.$tag, $this->images_m->count_tagged_by($tag, array(
			'status' => 'live'
		)), NULL, 4);

		// Get the current page of images posts
		$images = $this->images_m->limit($pagination['limit'])->get_tagged_by($tag, array(
			'status' => 'live'
		));
		
		foreach ($images AS &$post)
		{
			$post->keywords = Keywords::get_links($post->keywords, 'images/tagged');
		}

		// Set meta description based on post titles
		$meta = $this->_posts_metadata($images);
		
		$name = str_replace('-', ' ', $tag);

		// Build the page
		$this->template->title($this->module_details['name'], lang('images_tagged_label').': '.$name )
			->set_metadata('description', lang('images_tagged_label').': '.$name.'. '.$meta['description'] )
			->set_metadata('keywords', $name )
			->set_breadcrumb( lang('images_images_title'), 'images')
			->set_breadcrumb( lang('images_tagged_label').': '.$name )
			->set('images', $images)
			->set('tag', $tag)
			->set('pagination', $pagination)
			->build('tagged', $this->data );
	}

	// Private methods not used for display
	private function _posts_metadata(&$posts = array())
	{
		$keywords = array();
		$description = array();

		// Loop through posts and use titles for meta description
		if(!empty($posts))
		{
			foreach($posts as &$post)
			{
				if($post->category_title)
				{
					$keywords[$post->category_id] = $post->category_title .', '. $post->category_slug;
				}
				$description[] = $post->title;
			}
		}

		return array(
			'keywords' => implode(', ', $keywords),
			'description' => implode(', ', $description)
		);
	}
}
