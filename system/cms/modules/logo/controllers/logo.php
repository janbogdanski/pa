<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logo extends Public_Controller
{
    protected  $data;
	public function __construct()
	{
		parent::__construct();
//		$this->load->model('logo_m');
        $this->load->helper(array('url', 'html'));
		$this->lang->load('logo');
        $this->load->library('Texttoimage');

        if (!$this->ion_auth->logged_in()){
//            redirect('users/login');
        }

	}

	// images/page/x also routes here
	public function index()
	{

        $fonts = array();
        $dir = new DirectoryIterator('fonts');
        foreach($dir as $file)
        {
            if(!$file->isDot() and !$file->isDir())
            {
                $fonts[$file->getFilename()] = $file->getFilename();
            }
        }

        $this->data = array(
            'fonts' => $fonts
        );

		$this->template
			->title($this->module_details['name'])
			->set_breadcrumb( lang('images_images_title'))
//			->set_metadata('description', $meta['description'])
//			->set_metadata('keywords', $meta['keywords'])
            ->append_metadata(js('jscolor.js', 'logo'))
            ->append_metadata(js('logo.js', 'logo'))
            ->append_metadata(css('logo.css', 'logo'))
			->build('index', $this->data);
	}

    public function image(){

        $fields = array(
            'text' => 'Wpisz tekst',
            'fontSize' => 19,
            'fontFamily' => 'georgia.ttf',
//            'fontColor' => array(246, 255, 0),
            'te1' => 246,
            'te2' => 255,
            'te3' => 0,
//            'fontShadow' => array(255, 25, 15),
            'is1' => 255,
            'is2' => 25,
            'is3' => 15
        ,
//            'imgBackground' => array(225, 59, 0),
            'im1' => 255,
            'im2' => 60,
            'im3' => 0,

            'xPaddingLeft' => 10,
            'yPaddingTop' => 10,
        );

        $logo = new TextToImage();

        if(!empty($_POST)){

            $imgBackground = $logo->hex2RGB($_POST['imgBackground']);
            $_POST['im1'] = $imgBackground['red'];
            $_POST['im2'] = $imgBackground['green'];
            $_POST['im3'] = $imgBackground['blue'];

            $fontColor = $logo->hex2RGB($_POST['fontColor']);
            $_POST['te1'] = $fontColor['red'];
            $_POST['te2'] = $fontColor['green'];
            $_POST['te3'] = $fontColor['blue'];
                    

            
            $fontShadow = $logo->hex2RGB($_POST['fontShadow']);
            $_POST['is1'] = $fontShadow['red'];
            $_POST['is2'] = $fontShadow['green'];
            $_POST['is3'] = $fontShadow['blue'];

            foreach ($fields as $field => $value) {

                if(!empty($_POST[$field])){
                    $fields[$field] = $_POST[$field];
                }
            } //end foreach
//                        print_r($fields);

            $logo->fsize = $fields['fontSize'];
            $logo->font = 'fonts/'.$fields['fontFamily'];
            $kolorTla = array($fields['im1'], $fields['im2'], $fields['im3']);
            $kolorCzcionki = array($fields['te1'], $fields['te2'], $fields['te3']);
            $kolorCienia = array($fields['is1'], $fields['is2'], $fields['is3']);
            $logo->color = $kolorCzcionki;
            $logo->bgcolor = $kolorTla;
            $logo->shadowcolor = $kolorCienia;
            $logo->x =0;
            $logo->Y =0;
            $logo->paddingW = $fields['xPaddingLeft'];
            $logo->paddingH = $fields['yPaddingTop'];
            $logo->makeImageF($fields['text']);
//            $logo->showAsJpg();
//            $logo->saveAsJpg('dsds');

            $uploader = new Imageshack(); //, param('cookie'));
            //  $public = param('public');
            // if ($public)
            //     $public = $public != 'no';

            echo $targetFile = base_url('logo/image').'?'.http_build_query($_POST);
            $response = $uploader->transload($targetFile);
//                 print_r($response);
            if(empty($response->error)){
                //todo sprawdz na docelowym!!
                $this->load->model('images/images_m');
                $this->images_m->saveImgshackImage($response);
            }

            $this->session->set_flashdata('success', 'OK');
            redirect('logo/index');

            die();
        }
        if(!empty($_GET)){
            foreach ($fields as $field => $value) {

                if(!empty($_GET[$field])){
                    $fields[$field] = $_GET[$field];
                }
            } //end foreach
//            print_r($fields);

            $logo->fsize = $fields['fontSize'];
            $logo->font = 'fonts/'.$fields['fontFamily'];
            $kolorTla = array($fields['im1'], $fields['im2'], $fields['im3']);
            $kolorCzcionki = array($fields['te1'], $fields['te2'], $fields['te3']);
            $kolorCienia = array($fields['is1'], $fields['is2'], $fields['is3']);
            $logo->color = $kolorCzcionki;
            $logo->bgcolor = $kolorTla;
            $logo->shadowcolor = $kolorCienia;
            $logo->x =0;
            $logo->Y =0;
            $logo->paddingW = $fields['xPaddingLeft'];
            $logo->paddingH = $fields['yPaddingTop'];
            $logo->makeImageF($fields['text']);
            $logo->showAsJpg();
//            $logo->saveAsJpg('dsds');
        } else{
            $logo->fsize = 19;
            $logo->font = 'fonts/georgia.ttf';
            $kolorTla = array(225, 59, 0);
            $kolorCzcionki = array(246, 255, 0);
            $kolorCienia = array(255, 25, 15);
            $logo->color = $kolorCzcionki;
            $logo->bgcolor = $kolorTla;
            $logo->shadowcolor = $kolorCienia;
            $logo->x =0;
            $logo->Y =0;
            $logo->paddingW = 10;
            $logo->paddingH = 15;
            $logo->makeImageF('Wpisz tekst');
            $logo->showAsJpg();

        }
    }
}
