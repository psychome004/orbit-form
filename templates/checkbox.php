<ul class="list-inline">
  <?php foreach( $options as $option ):?>
  <li class="checkbox">
    <label>
      <input type="checkbox" name="<?php _e( $field['name'] );?>[]" value="<?php _e( $option['slug'] )?>" />&nbsp;<?php _e( $option['name'] );?>
    </label>
  </li>
  <?php endforeach;?>
</ul>
