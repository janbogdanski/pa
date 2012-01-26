{{ if user:logged_in }}

<?php echo form_open(base_url('images/upload'), 'id="multiForm" enctype="multipart/form-data"'); ?>

<div class="form_inputs" id="blog-content-tab">

    <fieldset>
                <div class="input">
                    <?php echo form_upload(array('name' => 'image[]', 'id' => 'image_input')); ?>
                </div>

        <br style="clear:both"/>

    </fieldset>

</div>
    
    <?php echo form_submit('send', lang('images_form_send')); ?>
<?php echo form_close(); ?>

<div class="clear"></div>
<div class="ajax-loader"></div>
    
<br style="clear:both"/>
    
<?php if ( ! empty($images)): ?>
    
    <div id="galeriaObrazow">
    <ul class="containerThumbnails">
            
<?php foreach ($images as $image): ?>

        <li>
            <div>
                <p>
                    <?php echo anchor(uri_string().'#', lang('images_link_edit'), array('class' => 'fotoflexer_image', 'id' => "link_edit_{$image->image_id}")); ?>
                    | 
                    <?php echo anchor('images/delete/'.$image->image_id, lang('images_link_delete')); ?>
                 </p>
                <a href="<?php echo $image->image_link; ?>">
                    <img src="<?php echo $image->image_thumb_link; ?>"></a>
                <p>
                    <?php echo form_label(lang('images_link_label'), "link{$image->image_id}"); ?>
                    <?php echo form_input(array('onclick' => 'this.focus();this.select();', 'readonly' => '', 'value' => $image->image_link, 'id' => "link{$image->image_id}")); ?>
                </p>
            </div>
        </li>
<?php endforeach; ?>
    </ul>
        </div>
<div class="clear"></div>

<?php echo $pagination['links']; ?>

<?php else: ?>
	<p><?php echo lang('blog_currently_no_posts');?></p>
<?php endif; ?>

{{ else }}
    zaloguj
{{ endif }}