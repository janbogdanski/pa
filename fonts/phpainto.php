<?php
//zmienne
/** xPaddingLeft, xPaddingRight, yPaddingTop, yPaddingBottom
 * imgBackground, imgShadow, tmpBackground, //tlo koncowego obrazka, cien obrazka i tymczasowe tlo prezentacji
 * fontShadow, fontColor, fontFamily, fontAlt, fontSize, text
 *  
 */
class Sidebar
{
	protected  $_header;
	protected $_cotent;
	
	public function __construct($header)
	{
		$this->_header = $header;

	}
	
	public function setHeader($header)
	{
		$this->_header = $header;
	}
	
	
	public function setContent($content)
	{
		$this->_cotent = '<div class="content">'.$content.'</div>';
	}

	public function displayHeader()
	{
		echo $this->_header;
	}
	public function displaySidebar()
	{
		echo '<div class="sidebar">'."\n";
		echo $this->_header;
		echo $this->_cotent;
		echo '</div>';
	}
	
}


class FormElement
{
	protected  $_name;
	protected $_value;
	protected $_class;
	protected $_id;
	protected $_onchange;
	protected $_onkeyUp;
	
	public function setName($name){
		$this->_name = $name;
	}
	public function setValue($value){
		$this->_value = $value;
	}
	public function setClass($class){
		$this->_class = $class;
	}
	public function setId($id){
		$this->_id = $id;
	}
	public function setonChange($change){
		$this->_onchange = $change;
	}
	public function setonkeyUp($keyUp){
		$this->_onkeyUp = $keyUp;
	}
	
	public function getName(){
		return $this->_name;
	}
	public function getValue(){
		return $this->_value;
	}
	public function getClass(){
		return $this->_class;
	}
	public function getId(){
		return $this->_id;
	}
	
	
	protected function _buildAttr(array $attr)
	{
		$code = '';
		foreach ($attr as $name => $value){
			if ($value !== null) {
				$code .= ' '.$name .'="'.$value.'"';
			}
		}
		return $code;
	}
}

class FormInput extends FormElement 
{
	public function display()
	{
		return '<input '. $this->_buildAttr(array('type' => 'text', 'name' => $this->_name, 'value' => $this->_value, 'id' => $this->_id, 'class' =>$this->_class, 'onchange' => $this->_onchange, 'onkeyup' => $this->_onkeyUp )) .'" />';
	}
}

class FormInputLabel extends FormInput  
{	
	private $_label;
	
	public function setLabel($label){
		$this->_label = $label;
	}
	public function display()
	{
		return '<span>'.$this->_label .'</span> <input '. $this->_buildAttr(array('type' => 'text', 'name' => $this->_name, 'value' => $this->_value, 'id' => $this->_id, 'class' =>$this->_class, 'onchange' => $this->_onchange, 'onkeyup' => $this->_onkeyUp )) .'" />';
	}
}


class FormSelect extends FormElement 
{
	private $_options = array();
	
	   public function setOptions()
	   {
	   	foreach(glob('fonts/{*.ttf}', GLOB_BRACE) as $file){
	   		$file = explode('/', $file);
	   		$this->_options[] = $file[1];
	   	}
	   }
   
   

	public function display()
	{
		echo '<select '
		.$this->_buildAttr(array('id' => $this->_id, 'name' => $this->_name,'onchange' => $this->_onchange)) .'">';
		
		foreach ($this->_options as $option){
			echo '<option>'.$option.'</option>';
		
		}
		echo '</select>';
		
		


	}
}
	
	

?>
<html lang="pl">

<head> 
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="jscolor/jscolor.js"></script>
<script type="text/javascript" src="ajaxcore.js"></script>
<style type="text/css">
#side{
	float:left;
}

.sidebar{
	
	width:200px;
	padding:10px;
	margin:10px;
	border:1px solid red;
}
#content{
	float:left;
	width:600px;
	margin:10px;
	border:1px solid red;
}
#image{

	min-height:110px;
}
input{
display:block;
margin:10px;
}
select{
display:block;
margin:10px;
}
</style>
</head>
<body>
<div id="side">
<?
$imgBackground = new Sidebar('Kolor tła');
$imgBackgroundContent = new FormInput();
$imgBackgroundContent->setName('imgBackground');
$imgBackgroundContent->setClass('color {pickerMode:\'HSV\'}');
$imgBackgroundContent->setonChange('getColor(this)');
$imgBackground->setContent($imgBackgroundContent->display());
$imgBackground->displaySidebar();



$fontColor = new Sidebar('Kolor tekstu');
$fontColorContent = new FormInput();
$fontColorContent->setName('fontColor');
$fontColorContent->setClass('color {pickerMode:\'HSV\'}');
$fontColorContent->setonChange('getColor(this)');
$fontColor->setContent($fontColorContent->display());
$fontColor->displaySidebar();


$imgShadow = new Sidebar('Kolor cienia obrazu');
$imgShadowContent = new FormInput();
$imgShadowContent->setName('imgShadow');
$imgShadowContent->setClass('color {pickerMode:\'HSV\'}');
$imgShadowContent->setonChange('getColor(this)');
$imgShadow->setContent($imgShadowContent->display());
$imgShadow->displaySidebar();



$tmpBackground = new Sidebar('Kolor podkładki');
$tmpBackgroundContent = new FormInput();
$tmpBackgroundContent->setName('tmpBackground');
$tmpBackgroundContent->setClass('color {pickerMode:\'HSV\'}');
$tmpBackgroundContent->setonChange('getColor(this)');
$tmpBackground->setContent($tmpBackgroundContent->display());
$tmpBackground->displaySidebar();




$fontShadow = new Sidebar('Kolor cienia tekstu');
$fontShadowContent = new FormInput();
$fontShadowContent->setName('fontShadow');
$fontShadowContent->setClass('color {pickerMode:\'HSV\'}');
$fontShadowContent->setonChange('getColor(this)');
$fontShadow->setContent($fontShadowContent->display());
$fontShadow->displaySidebar();
?>
</div>
<div id="content">
<div id="image">wybierz </div>
<div id="options">

<?
$text = new FormInputLabel();
$text->setName('text');
$text->setValue('Wpisz tekst');
$text->setId('text');
$text->setLabel('Tekst do wygenerowania');
$text->setonkeyUp('sendRequest()');
echo $text->display();


$font_size = new FormInputLabel();
$font_size->setName('fontSize');
$font_size->setValue('21');
$font_size->setId('fontSize');
$font_size->setLabel('Rozmiar tekstu');;
$font_size->setonkeyUp('sendRequest()');
echo $font_size->display();

$font_family = new FormSelect();
$font_family->setName('fontFamily');
$font_family->setId('fontFamily');
$font_family->setOptions();
$font_family->setonChange('sendRequest()');
echo $font_family->display();

$x_padding_left = new FormInputLabel();
$x_padding_left->setName('xPaddingLeft');
$x_padding_left->setValue('20');
$x_padding_left->setId('xPaddingLeft');
$x_padding_left->setLabel('odleglosc od lewej');
$x_padding_left->setonkeyUp('sendRequest()');
echo $x_padding_left->display();


$y_padding_top = new FormInputLabel();
$y_padding_top->setName('yPaddingTop');
$y_padding_top->setValue('10');
$y_padding_top->setId('yPaddingTop');
$y_padding_top->setLabel('odleglosc od góry');
$y_padding_top->setonkeyUp('sendRequest()');
echo $y_padding_top->display();


?>


</div>
</div>


<script type="text/javascript">
var imgBackground;
var fontColor;
var imgShadow;
var tmpBackground;
var fontShadow;
var options = new Array('imgBackground', 'fontColor', 'imgShadow', 'tmpBackground', 'fontShadow');
var values = new Array();

var myPicker = document.getElementsByClassName('color');
	
		piker0 = new jscolor.color(myPicker[0], {});
		piker0.fromString('2BA3FF');
		values[0] = '2BA3FF';

		piker1 = new jscolor.color(myPicker[1], {});
		piker1.fromString('ffffff');
		values[1] = 'ffffff';

		piker2 = new jscolor.color(myPicker[2], {});
		piker2.fromString('000000');
		values[2] = '000000';

		piker3 = new jscolor.color(myPicker[3], {});
		piker3.fromString('ffffff');
		values[3] = 'ffffff';
		
		piker4 = new jscolor.color(myPicker[4], {});
		piker4.fromString('FF8533');
		values[4] = 'FF8533';

	


//new jscolor.color(document.getElementById('myField1'), {})
//myPicker.fromString('99FF33')




var url;
function getColor(ten){

	for(var i in options){
		if (options[i] == ten.name){
			values[i] = ten.value;
		}
	}
	
	sendRequest();
}

function sendRequest(){
	var ts1 = HexToR(values[4]);
	var ts2 = HexToG(values[4]);
	var ts3 = HexToB(values[4]);

	var im1 = HexToR(values[0]);
	var im2 = HexToG(values[0]);
	var im3 = HexToB(values[0]);
	
	var te1 = HexToR(values[1]);
	var te2 = HexToG(values[1]);
	var te3 = HexToB(values[1]);

	var is1 = HexToR(values[2]);
	var is2 = HexToG(values[2]);
	var is3 = HexToB(values[2]);
	
	var text = document.getElementById('text').value;
	var fontFamily = document.getElementById('fontFamily').value;
	var fontSize = document.getElementById('fontSize').value;
	var xPaddingLeft = document.getElementById('xPaddingLeft').value;
	var yPaddingTop = document.getElementById('yPaddingTop').value;
	
	
	url = 'phpimagebank.php?fontSize='+fontSize+'&text='+text+'&ts1='+ts1+'&ts2='+ts2+'&ts3='+ts3+'&im1='+im1+'&im2='+im2+'&im3='+im3+'&te1='+te1+'&te2='+te2+'&te3='+te3+'&is1='+is1+'&is2='+is2+'&is3='+is3+'&fontFamily='+fontFamily+'&xPaddingLeft='+xPaddingLeft+'&yPaddingTop='+yPaddingTop;

	startGETRequest(url, onComplete, onEnd);
}
function onComplete(){
document.getElementById('image').innerHTML = '<img src="'+url+'" />';
}
function onEnd(){
	
}

function HexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
function HexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
function HexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}

window.onload = sendRequest;
</script>
</body>
</html>



