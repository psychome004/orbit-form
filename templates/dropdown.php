<select name="<?php _e( $field['name'] );?>" class="<?php _e( $field['class'] );?>">
  <?php //if( isset( $field['placeholder'] ) ):?>
  <option value="0">Select</option>
  <?php //endif;?>
  <?php foreach( $options as $option ):?>
  <option value="<?php echo $option['slug'] ;?>"><?php echo $option['name'] ;?></option>
  <?php endforeach;?>
</select>
