<?php
/*
Plugin Name: Wpdojoloader Sidebar Widget
Plugin URI:  http://wpdojoloader.berlios.de
Description: sidebarwidget to add dojo content to the wordpress sidebar
Version: 0.0.1
Author URI: http://wpdojoloader.berlios.de
*/
 
require_once(dirname(__FILE__). '/wpdojoloader.php');

class WpDojoLoaderSidebar Extends WP_Widget {
  
  var $dl_dojoLoader;

  //constructor
  function WpDojoLoaderSidebar(){
    $this->WP_Widget ( False, 'Dojoloader Sidebar!');
	//create a WpDojoLoader instance
    $this->dl_dojoLoader = new WpDojoLoader();
  }

  /*
   * display the sidebar
  */
  function widget ($args, $settings){
       
    echo $args['before_widget'];
 
      //display the title
     if (($settings['headline'] != null) && ($settings['headline'] != "")) { 
     	echo $args['before_title'];
        echo $settings['headline'];  
      	echo $args['after_title'];
     }
 
      //display the content
      if (($settings['text'] != null) && ($settings['text'] != "")) { 
	 	  $rawdata  = "[dojocontent]".$settings['text']."[/dojocontent]";
	      $content = $this->dl_dojoLoader->addContent($rawdata);		        
		  echo $content;
      }	      
      
      echo $args['after_widget'];
  }

  /**
   * the admin settings
   */
  function form ($settings){
    echo '<h3>Settings</h3>';
    
    echo 'Headline:&nbsp; <input name="'.$this->get_field_name('headline').'" type="text" value="'.$settings['headline'].'" />';
	echo '<br/><br/>';
    echo 'Dojocontent:&nbsp; <textarea name="'.$this->get_field_name('text').'" cols="45" rows="10">'.$settings['text'].'</textarea>';        
  }
 
  /**
   * store the admin settings
   */
  function update ($new_settings, $old_settings){
    return $new_settings;
  }
}
 
// register the wpdojoloader sidebar widget
add_action ('widgets_init', create_function('', 'Register_Widget("WpDojoLoaderSidebar");'));

?>
