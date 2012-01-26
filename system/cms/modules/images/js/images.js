
    $(document).ready(function(){
        $("#image_input").MultiFile({
            accept:'jpg|jpeg|png',
            max:3,
            STRING: {
                remove:'Usuń',
                selected:'Wybrano: $file',
                denied:'Zły format pliku: $ext!',
                duplicate:'Podany plik został już dodany:\n$file!'
            }
        });


        $('#multiForm').ajaxForm({
            dataType: 'json',
            beforeSubmit: function(a,f,o) {
                o.dataType = 'html';
                $(".ajax-loader").fadeIn('slow');
                // $('#uploadOutput').html('Submitting...');
            },
            complete: function(){
                $(".ajax-loader").fadeOut('slow');
            },
            success: function(data) {
//                data = eval(data);
//                alert(data);


                var $out = $('#uploadOutput');
                // $out.html('Form success handler received: <strong>' + typeof data + '</strong>');
                if (typeof data == 'object' && data.nodeType)
                    data = elementToString(data.documentElement, true);
                else if (typeof data == 'object')
                    data = objToString(data);
//                $out.append(data);
            }
        });
        
        $(".fotoflexer_image").click(function(){
            
            var id = $(this).attr('id').match(/\d{1,}/i);
            if(id != "undefined"){

                var ff_image_url = $(this).parent("p").next("a").attr('href');
                var ff_callback_url = BASE_URL + 'images/fotoflexer_success/' + id;
                var ff_cancel_url = BASE_URL + 'images/fotoflexer_cancel';
                var ff_lang = 'pl-PL';
//            alert(ff_image_url + ' '+ ff_callback_url  + ' '+ ff_cancel_url + ' '+ ff_lang);

                window.location="http://fotoflexer.com/API/API_Loader_v1_01.php?ff_image_url="+ff_image_url+"&ff_callback_url="+ff_callback_url+
                    "&ff_cancel_url="+ff_cancel_url+"&ff_lang="+ff_lang;
            }
        });
        
    });


