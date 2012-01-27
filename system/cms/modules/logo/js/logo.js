var url;
var imgBackground;
var fontColor;
var imgShadow;
var fontShadow;

var options = new Array('imgBackground', 'fontColor', 'fontShadow');
var values = new Array();


$(document).ready(function(){

    values[0] = $("#imgBackground").val();//'FFACFA';
    values[1] = $("#fontColor").val();
    values[2] = $("#fontShadow").val();

    //klik w button odswieza logo
    $("#reload_logo").click(function(){

        $(".ajax-loader").show();
        sendRequest();
        $("#imagesrc").attr('src', url);
        $(".ajax-loader").hide();
        return false;
    });

//    sendRequest();
    $("#reload_logo").click();

    $(".key").keyup(function(){

        $(".ajax-loader").show();
        sendRequest();
        $("#imagesrc").attr('src', url);
        $(".ajax-loader").hide();

        return false;
    });

    $(".change").change(function(){
        for(var i in options){
            if (options[i] == $(this).attr('name')){
                values[i] =  $(this).val();
            }
        }
        $(".ajax-loader").show();
        sendRequest();
        $("#imagesrc").attr('src', url);
        $(".ajax-loader").hide();

        return false;
    });
});


function sendRequest(){

    var im1 = HexToR(values[0]);
    var im2 = HexToG(values[0]);
    var im3 = HexToB(values[0]);

    var te1 = HexToR(values[1]);
    var te2 = HexToG(values[1]);
    var te3 = HexToB(values[1]);

    var is1 = HexToR(values[2]);
    var is2 = HexToG(values[2]);
    var is3 = HexToB(values[2]);

    var text = $('#text').val();
    var fontFamily = $('#fontFamily').val();
    var fontSize = $('#fontSize').val();
    var xPaddingLeft = $('#xPaddingLeft').val();
    var yPaddingTop = $('#yPaddingTop').val();

    url = BASE_URL +'logo/image/?fontSize='+fontSize+'&text='+text+'&im1='+im1+'&im2='+im2+'&im3='+im3+'&te1='+te1+'&te2='+te2+'&te3='+te3+'&is1='+is1+'&is2='+is2+'&is3='+is3+'&fontFamily='+fontFamily+'&xPaddingLeft='+xPaddingLeft+'&yPaddingTop='+yPaddingTop;

    return url;
}

function HexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
function HexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
function HexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}