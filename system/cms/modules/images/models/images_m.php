<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Images_m $images_m
 */
class Images_m extends MY_Model {

	protected $_table = 'pa_images';
    
    public function saveImgshackImage($images, $id = null){

        if(isset($id)){
            $id = (int)$id;
        }


//       echo  $user = (int)$this->session->userdata('user_id');
        $user =  $this->current_user->id;
        if (isset($images->links->thumb_link)) {
            $thumb_link = $images->links->thumb_link;
            $thumb = 1;
        } else {
            $thumb_link =$images->links->image_link;
            $thumb = 0;
        }

        $data = array(
            'image_id' => $id,
            'user_id' => $user,
            'image_link' => (string)$images->links->image_link,
            'image_thumb' => $thumb,
            'image_thumb_link' => (string)$thumb_link,
            'image_date' => date("Y-m-d"),
        );
//        print_r($data);
//        die();
        if($id){
            $this->db->where('image_id', $id);
            $this->db->update($this->_table,$data);        
        } else{
            $this->db->insert($this->_table, $data);
        }

    }

	function get_all()
	{
		$this->db->select('pa_images.*');

		$this->db->order_by('image_id', 'DESC');

		return $this->db->get('pa_images')->result();
	}

	function get($id)
	{
		return $this->db->select('blog.*, profiles.display_name')
					->join('profiles', 'profiles.user_id = blog.author_id', 'left')
					->where(array('blog.id' => $id))
					->get('blog')
					->row();
	}
	
	public function get_by($key, $value = '')
	{
		$this->db->select('blog.*, profiles.display_name')
			->join('profiles', 'profiles.user_id = blog.author_id', 'left');
			
		if (is_array($key))
		{
			$this->db->where($key);
		}
		else
		{
			$this->db->where($key, $value);
		}

		return $this->db->get($this->_table)->row();
	}

	function get_many_by($params = array())
	{
		// Is a status set?
		if (!empty($params['user_id']))
		{
				// Otherwise, show only the specific status
				$this->db->where('user_id', $params['user_id']);
		}


		// Limit the results based on 1 number or 2 (2nd is offset)
		if (isset($params['limit']) && is_array($params['limit']))
			$this->db->limit($params['limit'][0], $params['limit'][1]);
		elseif (isset($params['limit']))
			$this->db->limit($params['limit']);

		return $this->get_all();
	}
	
	public function count_tagged_by($tag, $params)
	{
		return $this->select('*')
			->from('blog')
			->join('keywords_applied', 'keywords_applied.hash = blog.keywords')
			->join('keywords', 'keywords.id = keywords_applied.keyword_id')
			->where('keywords.name', str_replace('-', ' ', $tag))
			->where($params)
			->count_all_results();
	}
	
	public function get_tagged_by($tag, $params)
	{
		return $this->db->select('blog.*, blog.title title, blog.slug slug, blog_categories.title category_title, blog_categories.slug category_slug')
			->from('blog')
			->join('keywords_applied', 'keywords_applied.hash = blog.keywords')
			->join('keywords', 'keywords.id = keywords_applied.keyword_id')
			->join('blog_categories', 'blog_categories.id = blog.category_id', 'left')
			->where('keywords.name', str_replace('-', ' ', $tag))
			->where($params)
			->get()
			->result();
	}

	function count_by($params = array())
	{

		// Is a status set?
		if (!empty($params['user_id']))
		{
			// If it's all, then show whatever the status
				// Otherwise, show only the specific status
				$this->db->where('user_id', $params['user_id']);
		}

		return $this->db->count_all_results('pa_images');
	}

	function update($id, $input)
	{
		$input['updated_on'] = now();

		return parent::update($id, $input);
	}

	function publish($id = 0)
	{
		return parent::update($id, array('status' => 'live'));
	}

	// -- Archive ---------------------------------------------

	function get_archive_months()
	{
		$this->db->select('UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(t1.created_on), "%Y-%m-02")) AS `date`', FALSE);
		$this->db->from('blog t1');
		$this->db->distinct();
		$this->db->select('(SELECT count(id) FROM ' . $this->db->dbprefix('blog') . ' t2
							WHERE MONTH(FROM_UNIXTIME(t1.created_on)) = MONTH(FROM_UNIXTIME(t2.created_on))
								AND YEAR(FROM_UNIXTIME(t1.created_on)) = YEAR(FROM_UNIXTIME(t2.created_on))
								AND status = "live"
								AND created_on <= ' . now() . '
						   ) as post_count');

		$this->db->where('status', 'live');
		$this->db->where('created_on <=', now());
		$this->db->having('post_count >', 0);
		$this->db->order_by('t1.created_on DESC');
		$query = $this->db->get();

		return $query->result();
	}

	// DIRTY frontend functions. Move to views
	function get_blog_fragment($params = array())
	{
		$this->load->helper('date');

		$this->db->where('status', 'live');
		$this->db->where('created_on <=', now());

		$string = '';
		$this->db->order_by('created_on', 'DESC');
		$this->db->limit(5);
		$query = $this->db->get('blog');
		if ($query->num_rows() > 0)
		{
			$this->load->helper('text');
			foreach ($query->result() as $blog)
			{
				$string .= '<p>' . anchor('blog/' . date('Y/m') . '/' . $blog->slug, $blog->title) . '<br />' . strip_tags($blog->intro) . '</p>';
			}
		}
		return $string;
	}

	function check_exists($field, $value = '', $id = 0)
	{
		if (is_array($field))
		{
			$params = $field;
			$id = $value;
		}
		else
		{
			$params[$field] = $value;
		}
		$params['id !='] = (int) $id;

		return parent::count_by($params) == 0;
	}

	/**
	 * Searches blog posts based on supplied data array
	 * @param $data array
	 * @return array
	 */
	public function search($data = array())
	{
		if (array_key_exists('category_id', $data))
		{
			$this->db->where('category_id', $data['category_id']);
		}

		if (array_key_exists('status', $data))
		{
			$this->db->where('status', $data['status']);
		}

		if (array_key_exists('keywords', $data))
		{
			$matches = array();
			if (strstr($data['keywords'], '%'))
			{
				preg_match_all('/%.*?%/i', $data['keywords'], $matches);
			}

			if (!empty($matches[0]))
			{
				foreach ($matches[0] as $match)
				{
					$phrases[] = str_replace('%', '', $match);
				}
			}
			else
			{
				$temp_phrases = explode(' ', $data['keywords']);
				foreach ($temp_phrases as $phrase)
				{
					$phrases[] = str_replace('%', '', $phrase);
				}
			}

			$counter = 0;
			foreach ($phrases as $phrase)
			{
				if ($counter == 0)
				{
					$this->db->like('blog.title', $phrase);
				}
				else
				{
					$this->db->or_like('blog.title', $phrase);
				}

				$this->db->or_like('blog.body', $phrase);
				$this->db->or_like('blog.intro', $phrase);
				$this->db->or_like('profiles.display_name', $phrase);
				$counter++;
			}
		}
		return $this->get_all();
	}

}