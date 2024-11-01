<?php
/*
Plugin Name: Test it Yourself
Plugin URI: http://blog.timersys.com/plugins/test-it-yourself-plugin-para-wordpress/
Description: Adds a editor to test code snippets online
Version: 1.0
Author: Damian Logghe
Author URI: http://blog.timersys.com
License: GPL2
*/

//*************** Admin function ***************
function testityourself_admin() {
	include('tiy_admin-settings.php');
}

function testityourself_admin_actions() {
    add_options_page("Test it Yourself", "Test it Yourself",1, "Test-it-Yourself", "testityourself_admin");
}
 
// Only all the admin options if the user is an admin
if(is_admin()){
    add_action('admin_menu', 'testityourself_admin_actions');
 
}

//****************Shortcode handler*********************
function testiy_shortcode_handler($atts, $content=null, $code="") {
 
  extract( shortcode_atts( array(
      'id' => '_tiy_0',
          ), $atts ) );

if($id != '_tiy_0')$id= '_tiy_'.$id;
  

   $output='
   <script type="text/javascript">
$(document).ready(function () {
	$("#clear_'.$id.'").click( function() {
		$("#input'.$id.'").val("");
		$("#output'.$id.'").attr("src","about:blank");
	});
});
</script>
   <div class="testiy" style="float:left;">
   <form id="tester'.$id.'" target="output'.$id.'" method="post" action="'.plugins_url("test-it-yourself/").'show.php">


<textarea id="input'.$id.'" wrap="logical" cols="90" rows="12" name="input" class="input">'.clean_pre($content).'
</textarea>

	<p>
		<input class="inputbutton" id="exec'.$id.'" value="Test It" type="submit" /> <input class="inputbutton" id="clear_'.$id.'" value="Clear All" type="submit" />
	</p>
	
	<iframe id="output'.$id.'" src="about:blank" name="output'.$id.'" class="output" frameborder="0"> </iframe>
	</form>	
   
   
   
   </div>';
   
	return  $output;
}


add_shortcode('testiy', 'testiy_shortcode_handler');

add_filter('the_posts', 'conditionally_add_scripts_and_styles'); // the_posts gets triggered before wp_head

function conditionally_add_scripts_and_styles($posts){
	if (empty($posts)) return $posts;
 
	$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
	foreach ($posts as $post) {
		if (stripos($post->post_content, '[testiy]')) {
			$shortcode_found = true; // bingo!
			break;
		}
	}
 
	if ($shortcode_found) {
		// enqueue here
		wp_enqueue_style('test-it-style', plugins_url("test-it-yourself/").'test-it-style.css');
		//wp_enqueue_script('my-script', '/script.js');
	}
 
	return $posts;
}
