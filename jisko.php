<?php

/*
Plugin Name: Jisko for Wordpress
Version: 1.0
Plugin URI: http://rick.jinlabs.com/code/jisko
Description: Displays your public Jisko notes for all to read. Based on <a href="http://cavemonkey50.com/code/pownce/">Pownce for Wordpress</a> by <a href="http://cavemonkey50.com/">Cavemonkey50</a>.
Author: Ricardo Gonz&aacute;lez
Author URI: http://rick.jinlabs.com/
*/

/*  Copyright 2007  Ricardo González Castro (rick[in]jinlabs.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


define('MAGPIE_CACHE_AGE', 120);
define('MAGPIE_INPUT_ENCODING', 'UTF-8');

$jisko_options['widget_fields']['title'] = array('label'=>'Title:', 'type'=>'text', 'default'=>'');
$jisko_options['widget_fields']['username'] = array('label'=>'Username:', 'type'=>'text', 'default'=>'');
$jisko_options['widget_fields']['num'] = array('label'=>'Number of links:', 'type'=>'text', 'default'=>'5');
$jisko_options['widget_fields']['update'] = array('label'=>'Show timestamps:', 'type'=>'checkbox', 'default'=>true);
$jisko_options['widget_fields']['linked'] = array('label'=>'Linked:', 'type'=>'text', 'default'=>'#');
$jisko_options['widget_fields']['hyperlinks'] = array('label'=>'Discover Hyperlinks:', 'type'=>'checkbox', 'default'=>true);
$jisko_options['widget_fields']['jisko_users'] = array('label'=>'Discover @replies:', 'type'=>'checkbox', 'default'=>true);
$jisko_options['widget_fields']['encode_utf8'] = array('label'=>'UTF8 Encode:', 'type'=>'checkbox', 'default'=>false);


$jisko_options['prefix'] = 'jisko';

// Display Jisko messages
function jisko_messages($username = '', $num = 5, $list = true, $update = true, $linked  = '#', $hyperlinks = true, $jisko_users = true, $encode_utf8 = false) {

	global $jisko_options;
	include_once(ABSPATH . WPINC . '/rss.php');
	
	$messages = fetch_rss('http://jisko.net/rss/profile?u='.$username);

	if ($list) echo '<ul class="jisko">';
	
	if ($username == '') {
		if ($list) echo '<li>';
		echo 'RSS not configured';
		if ($list) echo '</li>';
	} else {
			if ( empty($messages->items) ) {
				if ($list) echo '<li>';
				echo 'No public Jisko messages.';
				if ($list) echo '</li>';
			} else {
				foreach ( $messages->items as $message ) {
					$msg = " ".$message['description']." ";
					if($encode_utf8) $msg = utf8_encode($msg);
					$link = $message['link'];
				
					if ($list) echo '<li class="jisko-item">'; elseif ($num != 1) echo '<p class="jisko-message">';
					
					if ($linked != '' || $linked != false) {
            if($linked == 'all')  { 
              $msg = '<a href="'.$link.'" class="jisko-link">'.$msg.'</a>';  // Puts a link to the status of each tweet 
            } else {
              $msg = $msg . '<a href="'.$link.'" class="jisko-link">'.$linked.'</a>'; // Puts a link to the status of each tweet
              if ($hyperlinks) { $msg = jisko_hyperlinks($msg); }
              if ($jisko_users)  { $msg = jisko_users($msg); }
            }
          } 
          echo $msg;
          
          
        if($update) {				
          $time = strtotime($message['pubdate']);
          
          if ( ( abs( time() - $time) ) < 86400 )
            $h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
          else
            $h_time = date(__('Y/m/d'), $time);

          echo sprintf( __('%s', 'jisko-for-wordpress'),' <span class="jisko-timestamp"><abbr title="' . date(__('Y/m/d H:i:s'), $time) . '">' . $h_time . '</abbr></span>' );
         }          
                  
					if ($list) echo '</li>'; elseif ($num != 1) echo '</p>';
				
					$i++;
					if ( $i >= $num ) break;
				}
			}
		}
		if ($list) echo '</ul>';
	}

// Link discover stuff

function jisko_hyperlinks($text) {
    // match protocol://address/path/file.extension?some=variable&another=asf%
    $text = preg_replace("/\s([a-zA-Z]+:\/\/[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i"," <a href=\"$1\" class=\"jisko-link\">$1</a>$2", $text);
    // match www.something.domain/path/file.extension?some=variable&another=asf%
    $text = preg_replace("/\s(www\.[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i"," <a href=\"http://$1\" class=\"jisko-link\">$1</a>$2", $text);      
    // match name@address
    $text = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto://$1\" class=\"jisko-link\">$1</a>$2", $text);    
    return $text;
}

function jisko_users($text) {
       $text = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://jisko.net/$2\" class=\"jisko-user\">@$2</a>$3 ", $text);
       return $text;
}     

// Jisko widget stuff
function widget_jisko_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;
	
	$check_options = get_option('widget_jisko');
  if ($check_options['number']=='') {
    $check_options['number'] = 1;
    update_option('widget_jisko', $check_options);
  }
  
	function widget_jisko($args, $number = 1) {

		global $jisko_options;
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		include_once(ABSPATH . WPINC . '/rss.php');
		$options = get_option('widget_jisko');
		
		// fill options with default values if value is not set
		$item = $options[$number];
		foreach($jisko_options['widget_fields'] as $key => $field) {
			if (! isset($item[$key])) {
				$item[$key] = $field['default'];
			}
		}
		
		$messages = fetch_rss('http://jisko.net/rss/profile?user='.$username.'/');


		// These lines generate our output.
		echo $before_widget . $before_title . $item['title'] . $after_title;
		jisko_messages($item['username'], $item['num'], true, $item['update'], $item['linked'], $item['hyperlinks'], $item['jisko_users'], $item['encode_utf8']);
		echo $after_widget;
				
	}

	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_jisko_control($number) {
	
		global $jisko_options;

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_jisko');
		if ( isset($_POST['jisko-submit']) ) {

			foreach($jisko_options['widget_fields'] as $key => $field) {
				$options[$number][$key] = $field['default'];
				$field_name = sprintf('%s_%s_%s', $jisko_options['prefix'], $key, $number);

				if ($field['type'] == 'text') {
					$options[$number][$key] = strip_tags(stripslashes($_POST[$field_name]));
				} elseif ($field['type'] == 'checkbox') {
					$options[$number][$key] = isset($_POST[$field_name]);
				}
			}

			update_option('widget_jisko', $options);
		}

		foreach($jisko_options['widget_fields'] as $key => $field) {
			
			$field_name = sprintf('%s_%s_%s', $jisko_options['prefix'], $key, $number);
			$field_checked = '';
			if ($field['type'] == 'text') {
				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);
			} elseif ($field['type'] == 'checkbox') {
				$field_value = 1;
				if (! empty($options[$number][$key])) {
					$field_checked = 'checked="checked"';
				}
			}
			
			printf('<p style="text-align:right;" class="jisko_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',
				$field_name, __($field['label']), $field_name, $field_name, $field['type'], $field_value, $field['type'], $field_checked);
		}

		echo '<input type="hidden" id="jisko-submit" name="jisko-submit" value="1" />';
	}
	
	function widget_jisko_setup() {
		$options = $newoptions = get_option('widget_jisko');
		
		if ( isset($_POST['jisko-number-submit']) ) {
			$number = (int) $_POST['jisko-number'];
			$newoptions['number'] = $number;
		}
		
		if ( $options != $newoptions ) {
			update_option('widget_jisko', $newoptions);
			widget_jisko_register();
		}
	}
	
	
	function widget_jisko_page() {
		$options = $newoptions = get_option('widget_jisko');
	?>
		<div class="wrap">
			<form method="POST">
				<h2><?php _e('Jisko Widgets'); ?></h2>
				<p style="line-height: 30px;"><?php _e('How many Jisko widgets would you like?'); ?>
				<select id="jisko-number" name="jisko-number" value="<?php echo $options['number']; ?>">
	<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
				</select>
				<span class="submit"><input type="submit" name="jisko-number-submit" id="jisko-number-submit" value="<?php echo attribute_escape(__('Save')); ?>" /></span></p>
			</form>
		</div>
	<?php
	}
	
	
	function widget_jisko_register() {
		
		$options = get_option('widget_jisko');
		$dims = array('width' => 300, 'height' => 300);
		$class = array('classname' => 'widget_jisko');

		for ($i = 1; $i <= 9; $i++) {
			$name = sprintf(__('Jisko #%d'), $i);
			$id = "jisko-$i"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, $i <= $options['number'] ? 'widget_jisko' : /* unregister */ '', $class, $i);
			wp_register_widget_control($id, $name, $i <= $options['number'] ? 'widget_jisko_control' : /* unregister */ '', $dims, $i);
		}
		
		add_action('sidebar_admin_setup', 'widget_jisko_setup');
		add_action('sidebar_admin_page', 'widget_jisko_page');
	}

	widget_jisko_register();
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_jisko_init');

?>