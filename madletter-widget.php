<?php
/*
  Plugin Name: Madletter subscriber list
  Plugin URI: http://madletter.com
  Description: The most simple WordPress plugin for creating subscriber lists for your blog or small business.
  Version: 0.1
  Author: omnify
  Author URI: https://profiles.wordpress.org/omnify
*/

class madletter_widget_subscribe extends WP_Widget {
  function __construct() {
    parent::__construct(false, $name = __('Madletter Widget'));
  }

  function widget($args, $instance) {
    ?>
    <style media="screen">
      .madletter_widget{
        font-weight: normal;
      }
      .madletter_input_field {
        width: 90%;
        border: 0.5px solid #EAEAEA;
        border-radius: 4px;
        padding: 4px;
        background-color: #ecf0f1;
        margin-bottom: 5px;
        margin-top: 5px;
        font-weight: normal;
      }
      .madletter_button{
        background-color: #3498db;
        border-radius: 5px;
        border: none;
        padding: 10px;
        color: white;
        font-size: 14px;
        font-weight: normal;
        padding-left: 15px;
        padding-right: 15px;
        margin-top: 15px;
      }
      .madletter_button:hover{
        background-color: #2980b9;
      }
    </style>
      <div class='madletter_widget'>
        <form class="" action="" method="post">

          <label for="madletter_email_field">Email Address*</lable>
          <input class="madletter_input_field" type="email" id="madletter_email_field" name="madletter_email_field" value=""   required/>

          <label for="madletter_fname_field">First Name</lable>
          <input class="madletter_input_field" type="text" id="madletter_fname_field" name="madletter_fname_field" value=""   />

          <label for="madletter_lname_field">Last Name</lable>
          <input class="madletter_input_field" type="text" id="madletter_lname_field" name="madletter_lname_field" value=""   />

          <input class="madletter_button" type="submit" name = "madletter_subscribe" value = "Subscribe">
        </form>

      </div>
    <?php

      if (isset($_POST['madletter_subscribe'])) {
          if($_POST['madletter_email_field'] != "") {
          	$email = sanitize_email($_POST['madletter_email_field']);
            $fname = "";
            $lname = "";
            if($_POST['madletter_fname_field'] != "") {$fname = sanitize_text_field($_POST['madletter_fname_field']);}
            if($_POST['madletter_lname_field'] != "") {$lname = sanitize_text_field($_POST['madletter_lname_field']);}
            if(get_option('madletter_secure_token')) {
              $token = get_option('madletter_secure_token');
            } else {
              $token="";
            }
            $body = array(
                'token' => $token,
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email
            );

            $args = array(
                'body' => $body
            );
            $response = wp_remote_post( 'http://madletter.com/api/connect', $args );

            echo json_decode($response['body'])->message;

          }
    }
}
}
add_action('widgets_init', function(){
  register_widget('madletter_widget_subscribe');
});

add_action('admin_menu', 'madletter_plugin_page');
add_action('admin_init', 'madletter_plugin_admin_init') ;


function madletter_plugin_page() {
    add_menu_page('Madletter', 'Madletter', 'manage_options', 'omnify-madletter', 'madletter_page'); //last function
  }
  function madletter_page() { ?>
    <div>
      <h1>Madletter</h1>

      <form action="options.php" method="post">
        <?php settings_fields('madletter_secure_token'); ?>
        <?php do_settings_sections('madletter_plugin'); ?>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }


  function madletter_plugin_admin_init(){
    register_setting( 'madletter_secure_token', 'madletter_secure_token', 'plugin_options_validate' );
    add_settings_section('madletter_plugin_main', 'Settings', 'madletter_plugin_section_text', 'madletter_plugin');
    add_settings_field('madletter_plugin_text_string', 'Token: ', 'madletter_plugin_setting_string', 'madletter_plugin', 'madletter_plugin_main');
  }

  function madletter_plugin_section_text() {
    echo '<p>Insert token</p>';
    echo "Get token at <a target='_blank' href='http://madletter.com'>Madletter.com</a>";
  }

  function madletter_plugin_setting_string() {
    $options = get_option('madletter_secure_token');
    echo "<input id='madletter_plugin_text_string' name='madletter_secure_token' size='40' type='text' value='{$options}' />";
  }

?>
