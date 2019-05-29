<?php
/*
Plugin Name: Orbit Fep
Description: Creates a form using repeater functionality inherited from orbit-bundle
Version: 1.0
Author: Sputznik
*/


// THIS CONDITION CHECKS IF THE ORBIT-BUNDLE HAS BEEN LOADED FIRST, IF NOT THEN WAIT FOR IT TO LOAD COMPLETELY
if( class_exists('ORBIT_BASE') ){

  include('class-orbit-fep.php');
}
else{
  add_action('orbit-bundle-loaded', function(){
    include('class-orbit-fep.php');
  });
}
