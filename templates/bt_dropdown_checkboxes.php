<!-- ASSUMES THAT THE BOOTSTRAP DROPDOWN IS BEING USED -->
<div class="dropdown" data-behaviour="bt-dropdown-checkboxes">
  <button class="btn btn-primary dropdown-toggle" id="menu1" type="button">Select
  <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
    <?php foreach( $options as $option ):?>
      <li class="checkbox">
    		<label>
    			<input type="checkbox"  name="<?php _e( $field['name'] );?>[]" value="<?php echo $option['name'];?>" />
          <span><?php echo $option['name'];?></span>
    		</label>
    	</li>
  <?php endforeach;?>
  </ul>
</div>
