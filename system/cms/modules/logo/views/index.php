<?php

echo '<h2>'.lang('logo_header').'</h2>';

echo '<div id="image">';
echo img(array('src' => 'logo/image', 'id' => 'imagesrc'), true);
echo '</div>';
echo '<div class="ajax-loader"></div>';

echo form_open(base_url('logo/image'), 'id="logo_form" method="post"');


echo '<div id="formularz">';
echo '<div class="textCol">';
echo '<div>';
echo lang('logo_field_text');
echo form_input('text','Wpisz tekst', 'class="key" id="text"');
echo '</div>';

echo '<div>';
echo lang('logo_field_fontSize');
echo form_input('fontSize','17', 'class="key" id="fontSize"');
echo '</div>';

echo '<div>';
echo lang('logo_field_fontFamily');
echo form_dropdown('fontFamily', $fonts, 'ALAMAKOT.TTF', 'class="change" id="fontFamily"');
echo '</div>';

echo '<div>';
echo lang('logo_field_xPaddingLeft');
echo form_input('xPaddingLeft','10', 'class="key" id="xPaddingLeft"');
echo '</div>';

echo '<div>';
echo lang('logo_field_yPaddingTop');

echo form_input('yPaddingTop','5', 'class="key" id="yPaddingTop"');
echo '</div>';
echo '</div>'; //leftcol

echo '<div class="colorCol">';
echo '<div>';
echo lang('logo_field_imgBackground');

echo form_input('imgBackground','080808', 'class="color, change" id="imgBackground" size="7" maxlength="6"');
echo '</div>';

echo '<div>';
echo lang('logo_field_fontColor');

echo form_input('fontColor','CCCCCC', 'class="color, change" id="fontColor" size="7" maxlength="6"');
echo '</div>';

echo '<div>';
echo lang('logo_field_fontShadow');

echo form_input('fontShadow','FF0000', 'class="color, change" id="fontShadow" size="7" maxlength="6"');
echo '</div>';
echo '</div>'; //colorCol
echo '<div class="clear"></div>';
echo '</div>';
echo '<div class="clear"></div>';

echo form_button('reload_logo', lang('logo_reload'), 'id="reload_logo"');?>
{{ if user:logged_in }}

<?php
echo form_submit('save_logo', lang('logo_save'));
?>
{{ else }}
<?php
echo lang('logo_log_in');
?>
{{ endif }}
<?php
echo form_close();
