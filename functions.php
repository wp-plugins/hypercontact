<?php 
	/**
	 * Get users based on usernames or group.
	 * @param  array. Acceptet fields are:
	 * gravatarsize - int - the size of the gravatar
	 * single - bool - is single user
	 * username - string - a single user or multiple users in comma seperated list
	 * fields - string - a string that contains a field to sow. or multiple fields in comma seperated list
	 * @param bool. Use widget options
	 * 
	*/
	function hc_get_users($args, $widget = false){
		//set gravatar size
		if (!isset($args['gravatarsize']) || $args['gravatarsize'] == '') {
			$gravatarSize = '32';
		}else{
			$gravatarSize = $args['gravatarsize'];
		}

		//display group or single
		if(isset($args['single']) && ($args['single'] == 'single' || $args['single'] == true)){
			$hc_users = array();
			$user = $args['username'];
			$userArr = explode(',',$user);
			foreach ($userArr as $key => $username) {
				$hc_users[] = get_user_by('login', $username);
			}
		}else{
			//args for getting users
			if (empty($args['group']))
				return new WP_Error('No users', __("No group or user specified", 'hypercontact'));
				
			$roles = array(
						'role'		=> $args['group'],
						'fields'	=> array('ID')
					);

			// Create a new user query
			$wp_user_query = new WP_User_Query($roles);
			
			// Get user meta
			$groupId = $wp_user_query->get_results();
			$hc_users = array();
			foreach ($groupId as $key => $value) {
				$hc_users[] = get_user_by('id', $value->ID);
			}
		}
		
		foreach ($hc_users as $index => $user) {
			$user->gravatar = get_avatar( $user->user_email, $gravatarSize);
			$user = hc_filter($user, $widget);
		}

		if(isset($args['fields'])){
			$fieldsString = $args['fields'];
			$fieldsArray = explode(',',$fieldsString);
			$fieldsArray = array_map('trim', $fieldsArray);
			foreach ($hc_users as $key => $user){
				foreach ($user as $field => $value) {
					if (!in_array($field, $fieldsArray)) {
						unset($user->$field);
					}
				}
			}
		}
		

		if (!empty($hc_users) && (isset($hc_users[0]) && !empty($hc_users[0]))){
			return $hc_users;
		}else{
			return new WP_Error('No info', __("No user info could be found", 'hypercontact'));
		}
	}

	/**
	 * Displays a single filed drom a single user
	 * @param string. Username
	 * @param string. the field to show
	*/
	function hc_user_field($user, $field){
		$args = array(
				'single' => true,
				'username' => $user,
				'fields' => $field
			);
		$userObject = hc_get_users($args);
		echo $userObject[0]->$field;
	}

	/**
	 * Display a unordered list
	 * @param array. Arguments for display
	*/
	function hc_users($args){
		$users = hc_get_users($args);
		if ( is_wp_error($users) ){
				echo $users->get_error_message();
				return false;
		}
		foreach ($users as $key => $user) {
			echo '<ul class="hc_user_list">';
			echo '<li class="hc_user_name">'.$user->display_name.'</li>';
			echo '<li class="hc_user_gravatar">'.$user->gravatar.'</li>';
			echo '<ul class="hc_user_info">';
				foreach ($user as $field => $value) {
					if (($field !=' display_name' && $field != 'gravatar') && $value != '') {
						if ($field == 'user_url') {
							echo '<li><a href="'.$value.'">'.$value.'</a></li>';
						}else{
							echo '<li>'.$value.'</li>';
						}
						
					}
				}
			echo '</ul>';
			echo '</ul>';
		}
	}

	/**
	 * Get information about author
	 * @param string. Size of gravatar
	*/
	function hc_get_the_author($gravatarSize = '', $field = ''){
		global $post;
		$username = get_user_by('id',$post->post_author);
		$username = $username->user_login;
		$args = array('single' => true, 'username' => $username, 'gravatarsize' => $gravatarSize);
		if($field != '')
			$args['fields'] = $field;
		$users = hc_get_users($args);
		if ( is_wp_error($users) ){
				echo $users->get_error_message();
				return false;
		}
		return $users;
	}

	/**
	 * Dsiplay information about the author
	 * @param string. size of gravatar
	*/
	function hc_the_author($gravatarSize = ''){
		$info = hc_get_the_author($gravatarSize);
		foreach ($info as $index => $author) {
			if(file_exists(get_bloginfo('stylesheet_directory').'plugins/hypercontact/templates/author.php')){
				include(get_bloginfo('stylesheet_directory').'plugins/hypercontact/templates/author.php');
			}else{
				include('templates/author.php');
			}
		}

	}

	/**
	 * return field from the author
	 * @param string. the field
	 * @param string. size of gravatar
	*/
	function hc_get_author_field($field , $gravatarSize = ''){
		$info = hc_get_the_author($gravatarSize, $field);
		$author = $info[0];
		$field = $author->$field;
		return $field;
	}

	/**
	 * display field from the author
	 * @param string. the field
	 * @param string. size of gravatar
	*/
	function hc_author_field($field , $gravatarSize = ''){
		$info = hc_get_author_field($field , $gravatarSize);
		echo $info;
	}

	/**
	 * Filters output to only display allowed fields
	 * @param object. The user object to filter
	 * @param bool. Use widget options
	*/
	function hc_filter($user, $widget = false){
		$json = get_option('hc_extra_fields');
		$json = json_decode($json);
		foreach ($user as $key => $info) {
				$display = 0;
				foreach ($json as $fields => $field) {
					if ($widget) {
						if ($field->field == $key && $field->widget) {
							$display = 1;
						}
					}else{
						if ($field->field == $key) {
							$display = 1;
						}
					}
					
				}
				if (!$display) {
					unset($user->$key);
				}
			}
		return $user;
	}
	/**
	 * return field name for a field
	 * @param string. the field
	*/
	function hc_get_field_name($field){
		$json = get_option('hc_extra_fields');
		$json = json_decode($json);
		foreach ($json as $key => $value) {
			if ($value->field == $field) {
				return $value->fieldname;
			}
		}
		return false;
	}
	/**
	 * Display field name for a field
	 * @param string. the field
	*/
	function hc_field_name($field){
		echo hc_get_field_name($field);
	}
?>