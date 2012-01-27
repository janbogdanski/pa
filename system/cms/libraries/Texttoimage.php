<?php
/**
 * Class for converting Text to Image.
 *
 * @author Taslim Mazumder Sohel
 *
 */

class TextToImage {

    private $im;
    public $font = "fonts/ARIAL.TTF";
    public $fsize = 15;
    public $color =array(22,11,33);
    public $shadowcolor =array(20,20,20);
    public $bgcolor = array(201,10,30);
    public $x =0;
    public $Y =0;
    public $paddingW = 10;
    public $paddingH = 10;

    /**
     * @name 				   : makeImageF
     *
     * Function for create image from text with selected font.
     *
     * @param String $text     : String to convert into the Image.
     * @param String $this->font     : Font name of the text.
     * @param int    $W        : Width of the Image.
     * @param int    $H        : Hight of the Image.
     * @param int	 $this->x        : x-coordinate of the text into the image.
     * @param int    $Y        : y-coordinate of the text into the image.
     * @param int    $this->fsize    : Font size of text.
     * @param array  $color	   : RGB color array for text color.
     * @param array  $bgcolor  : RGB color array for background.
     *
     */
    public function makeImageF($text){

        $size = imagettfbbox($this->fsize,0,$this->font,$text);
        $w = abs($size[2]-$size[0]);
        $h = abs($size[5]-$size[3]);

        $width = ($w + $this->paddingW*2)+ 0.4*$this->fsize;
        $height = ($h + $this->paddingH*2)+0.4*$this->fsize;
        $this->im = @imagecreate($width, $height)
            or die("Cannot Initialize new GD image stream");

        $background_color = imagecolorallocate($this->im, $this->bgcolor[0], $this->bgcolor[1], $this->bgcolor[2]);		//RGB color background.
        $shadow_color = imagecolorallocate($this->im, $this->shadowcolor[0], $this->shadowcolor[1], $this->shadowcolor[2]);			//RGB color text.
        $text_color = imagecolorallocate($this->im, $this->color[0], $this->color[1], $this->color[2]);			//RGB color text.
        //cien
        imagettftext($this->im, $this->fsize, 0, $this->paddingW, $height/2 + $this->fsize/2 , $shadow_color, $this->font, $text);
        //tekst wlasciwy
        imagettftext($this->im, $this->fsize, 0, $this->paddingW - 0.04 *$this->fsize, $height/2 + $this->fsize/2 - 0.04 *$this->fsize , $text_color, $this->font, $text);
    }
    // +$this->paddingH
    /**
     * @name showAsPng
     *
     * Function to show text as Png image.
     *
     */
    public function showAsPng(){

        header("Content-type: image/png");
        return imagepng($this->im);
    }

    /**
     * @name saveAsPng
     *
     * Function to save text as Png image.
     *
     * @param String $filename 		: File name to save as.
     * @param String $location 		: Location to save image file.
     */
    public function saveAsPng($fileName, $location= null){

        $_fileName = $fileName.".png";
        $_fileName = is_null($location)?$_fileName:$location.$_fileName;
        return imagepng($this->im, $_fileName);
    }

    /**
     * @name showAsJpg
     *
     * Function to show text as JPG image.
     *
     */
    public function showAsJpg(){

        header("Content-type: image/jpg");
        return imagejpeg($this->im,'',100);
    }

    /**
     * @name saveAsJpg
     *
     * Function to save text as JPG image.
     *
     * @param String $filename 		: File name to save as.
     * @param String $location 		: Location to save image file.
     */
    public function saveAsJpg($fileName, $location= null){

        $_fileName = $fileName.".jpg";
        $_fileName = is_null($location)?$_fileName:$location.$_fileName;
        return imagejpeg($this->im, $_fileName);
    }

    /**
     * @name showAsGif
     *
     * Function to show text as GIF image.
     *
     */
    public function showAsGif(){

        header("Content-type: image/gif");
        return imagegif($this->im);
    }

    /**
     * @name saveAsGif
     *
     * Function to save text as GIF image.
     *
     * @param String $filename 		: File name to save as.
     * @param String $location 		: Location to save image file.
     */
    public function saveAsGif($fileName, $location= null){

        $_fileName = $fileName.".gif";
        $_fileName = is_null($location)?$_fileName:$location.$_fileName;
        return imagegif($this->im, $_fileName);
    }

    public function HexToR($hex){

        return (int) substr($this->cutHex($hex),0,2);
    }
    public function HexToG($hex){
        return (int) substr($this->cutHex($hex),2,2);

    }
    public function HexToB($hex){
        return (int) substr($this->cutHex($hex),4,2);

    }
    public function cutHex($hex){

        $hex = trim($hex, '#');
        return substr($hex,0,6);
    }


    /**
     * Convert a hexa decimal color code to its RGB equivalent
     *
     * @param string $hexStr (hexadecimal color value)
     * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
     * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
     * @return array or string (depending on second parameter. Returns False if invalid hex color value)
     */
    public function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false; //Invalid hex color code
        }
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
    }
}