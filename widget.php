<?php 
	/*
		The widget.
		This widget displays information from the user table.
	*/

	class HyperContact extends WP_Widget {
		function HyperContact() {
			//set widget
			$widget_ops = array('classname' => 'hyper_contact_widget', 'description' => __('Widget for HyperContact. Displays selected users in a widget', 'hypercontact') );
    		parent::WP_Widget( false, 'Hyper Contact' , $widget_ops);
		}

		function form($instance) {
			//create form for widget options
			$instance 	= wp_parse_args( (array) $instance, array( 
															'title'			=> '', 
															'group'			=> '', 
															'single'		=> '',
															'username'		=> '',
															'gravatar'		=> '',
															'gravatarsize'	=> '',
															));
			//Get options
    		$title 			= strip_tags($instance['title']);
    		$group 			= strip_tags($instance['group']);
    		$single 		= strip_tags($instance['single']);
    		$hideMail 		= strip_tags($instance['hidemail']);
    		$username 		= strip_tags($instance['username']);
    		$gravatarsize	= strip_tags($instance['gravatarsize']);

    		// Holds all roles for use when displaying from group
    		$roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber')

    		?>
    			<?php 
    					/*
    					 * TITLE
    					 * Choose a title for the widget
    					*/
    			 ?>
		      <p>
		        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'hypercontact') ?>: <em><?php _e('The widget title', 'hypercontact') ?></em>
		          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		        </label>
		      </p>

		      <?php 
    					/*
    					 * GROUP
    					 * Choose group to display
    					*/
    			 ?>
		      <p>
		        <label for="<?php echo $this->get_field_id('group'); ?>"><?php _e('Group','hypercontact') ?>: <em><?php _e('The desired listed group', 'hypercontact'); ?></em>
		          <select id="<?php echo $this->get_field_id('group'); ?>" name="<?php echo $this->get_field_name('group'); ?>" type="text" value="<?php echo attribute_escape($group); ?>" >
		        	<?php foreach ($roles as $key => $role): ?>
		        		<option value="<?php echo $role; ?>" <?php if(attribute_escape($group) == $role) {echo 'selected="selected"';} ?>><?php echo $role; ?></option>
		        	<?php endforeach ?>
		          </select>
		        </label>
		      </p>

		      <?php 
    					/*
    					 * SINGLE
    					 * Show only a single user?
    					*/
    			 ?>
		      <p>
		        <label for="<?php echo $this->get_field_id('single'); ?>"><?php _e('Select users', 'hypercontact') ?>: <em><?php _e('Only display the users you want', 'hypercontact'); ?></em>
		          <input class="widefat" id="<?php echo $this->get_field_id('single'); ?>" name="<?php echo $this->get_field_name('single'); ?>" type="checkbox" value="single" <?php if($single == 'single'){echo 'checked="checked"';} ?>/>
		        </label>
		      </p>
		      	
		      	<?php 
    					/*
    					 * USERNAME
    					 * The username if single
    					*/
    			 ?>
		      <p>
		        <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Usernames', 'hypercontact') ?>: <em><?php _e('usernames in comma seperated list', 'hypercontact'); ?></em>
		          <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo attribute_escape($username); ?>" />
		        </label>
		      </p>

		      <?php 
    					/*
    					 * GRAVATARSIZE
    					 * Gravtar size
    					*/
    			 ?>
		      <p>
		        <label for="<?php echo $this->get_field_id('gravatarsize'); ?>"><?php _e('Gravatar size', 'hypercontact') ?>: <em><?php _e('The size for the Gravatar', 'hypercontact') ?></em>
		          <input class="widefat" id="<?php echo $this->get_field_id('gravatarsize'); ?>" name="<?php echo $this->get_field_name('gravatarsize'); ?>" type="text" value="<?php echo attribute_escape($gravatarsize); ?>" />
		        </label>
		      </p>


		    <?php
		}

		function update($new_instance, $old_instance) {
			// Save options
			$instance = $old_instance;
    		$instance['title'] = strip_tags($new_instance['title']);
    		$instance['group'] = strip_tags($new_instance['group']);
    		$instance['single'] = strip_tags($new_instance['single']);
    		$instance['username'] = strip_tags($new_instance['username']);
    		$instance['gravatarsize'] = strip_tags($new_instance['gravatarsize']);
    		return $instance;
		}

		function widget($args, $instance) {
			global $wpdb;
			extract($args, EXTR_SKIP);
			echo $before_widget;
			echo $before_title;
			echo $instance['title'];
			echo $after_title;
			
			$hc_users = hc_get_users($instance, true);
			if ( is_wp_error($hc_users) ){
				echo $hc_users->get_error_message();
			}else{
				foreach ($hc_users as $key => $user) {
					if(file_exists(get_bloginfo('stylesheet_directory').'plugins/hypercontact/templates/widget.php')){
						include(get_bloginfo('stylesheet_directory').'plugins/hypercontact/templates/widget.php');
					}else{
						include('templates/widget.php');
					}
				}
					
			}
   				
			

			
			
			echo $after_widget;
		}

	}

	class HyperContactText extends WP_Widget {

	function HyperContactText() {
		$widget_ops = array('classname' => 'hyper_contact_textwidget', 'description' => __('Textwidget for HyperContact. You can use html and shortcodes', 'hypercontact'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct(false, 'Hyper Contact text', $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = do_shortcode(apply_filters( 'widget_text', $instance['text'], $instance ));
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'hypercontact'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', 'hypercontact'); ?></label></p>
		<p>
			<?php _e('These are the shortcodes you can use:', 'hypercontact'); ?>
			<pre>[hc_user_field user="" field=""] 
			*<?php _e('user and field are required', 'hypercontact'); ?></pre>
			<pre>[hc_author_field field=""] 
			*<?php _e('field are required', 'hypercontact'); ?></pre>
			<pre>[hc_field_name field=""] 
			*<?php _e('field are required', 'hypercontact'); ?></pre>
		</p>

<?php
	}
}
	//register widget
	add_action('widgets_init',function(){
		register_widget('HyperContact');
		register_widget('HyperContactText');
	});
 ?>