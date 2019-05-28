<?php
/*
Plugin Name: Orbit Fep
Description: Creates a form using repeater functionality inherited from orbit-bundle
Version: 1.0
Author: Sputznik
*/

add_action('orbit-bundle-loaded', function(){
  include('class-orbit-fep.php');
});
