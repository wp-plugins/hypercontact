<?php 
	/*
	 * Adds admin page and help. Uses a tab system for different views
	*/
	add_action('admin_menu', 'hypercontact_add_admin_menu');
	
	function hypercontact_add_admin_menu(){
		$page = add_users_page('HyperContact', 'HyperContact', 'add_users', 'hypercontact-config', 'hypercontact_page');
		$help = '<p>'.__('Add custom fields to users', 'HyperContact').'.</p>';
		$help .= '<p>'.__('To add a new field use the form', 'HyperContact').'</p>';
		$help .= '<p>'.__('field: the field type. skype, facebook or anything you want (small letters only)', 'HyperContact').'<br/>'.__('Name: The name of the field displayed in theme', 'HyperContact').'</p>';
		$help .= '<p>'.__('Use the form to allow fields to be used in widget', 'HyperContact').'</p>';
		$help .= '<p>'.__('See help tab or plugin page for more info', 'HyperContact').'</p>';
		add_contextual_help( $page, $help );
	}

	/*
	 * Displays admin page
	*/

	//tabs
	function hypercontact_admin_tabs( $current = 'homepage' ) {
	    $tabs = array( 'homepage' => __('Fields', 'HyperContact'), 'css' => __('CSS', 'HyperContact'), 'script' => __('Add script', 'HyperContact'), 'help' => __('Help', 'HyperContact') );
	    echo '<div id="icon-themes" class="icon32"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=hypercontact-config&tab=$tab'>$name</a>";
	    }
	    echo '</h2>';
	}

	//admin page
	function hypercontact_page(){
		global $pagenow;
	?>

		<div class="wrap">
			<h2>HyperContact</h2>
			<?php 
				if ( isset ( $_GET['tab'] ) ) 
					hypercontact_admin_tabs($_GET['tab']); 
				else 
					hypercontact_admin_tabs('homepage'); 

				if ( $pagenow == 'users.php' && $_GET['page'] == 'hypercontact-config' ){ 
				
					if ( isset ( $_GET['tab'] ) ) 
						$tab = $_GET['tab']; 
					else 
						$tab = 'homepage';

					switch ( $tab ){ 
				        case 'homepage' :
							hc_print_home();
						break; 
				        case 'css' : 
							hc_print_css();
						break;
						case 'script' : 
							hc_print_footer();
						break;
						case 'help' : 
							hc_print_help();
						break;
				    }

				}
			?>

			</div>
	<?php
	}

	/**
	* Print tab content
	*/

	function hc_print_home(){
		$json = get_option('hc_extra_fields');
		$json = json_decode($json);
		?>
			<div class="subsubsub">
				<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>" class="add:users: validate">
					<table class="form-table">
						<tbody>
							<tr class="form-field form-required">
								<th scope="row">
									<label for="field"><?php _e('Field', 'HyperContact'); ?> <span class="description">(<?php _e('required', 'HyperContact') ?>)</span> </label>
								</th>
								<td>
									<input type="text" name="field" id="field" aria-required="true" class="required"/>
								</td>
							</tr>

							<tr class="form-field form-required">
								<th scope="row">
									<label for="fieldname"><?php _e('Name', 'HyperContact'); ?></label>
								</th>
								<td>
									<input type="text" name="fieldname" id="fieldname"/>
								</td>
							</tr>
							<tr>
								<td>
									<input type="submit" id="new">
									<input type="hidden" name="hc_save_form" value="newfield">
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
			<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<div class="tablenav top">
					<div class="alignleft actions">
						<select name="action">
							<option value="-1" selected="selected"><?php _e('Bulk actions', 'HyperContact') ?></option>
							<option value="hc_delete"><?php _e('Delete selected', 'HyperContact') ?></option>
							<option value="hc_show_widget"><?php _e('Display in widget', 'HyperContact') ?></option>
							<option value="hc_hide_widget"><?php _e('Don`t display in widget', 'HyperContact') ?></option>
						</select>
						<input type="submit" name="" id="doaction" class="button-secondary action" value="Apply">
					</div>
				</div>
				<table class="wp-list-table widefat fixed users">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column"><input type="checkbox" name="" value=""></th>
							<th><?php _e('Field', 'HyperContact'); ?></th>
							<th><?php _e('Name', 'HyperContact'); ?></th>
							<th><?php _e('Display in widget', 'HyperContact'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th class="manage-column column-cb check-column"><input type="checkbox" name="" value=""></th>
							<th><?php _e('Field', 'HyperContact'); ?></th>
							<th><?php _e('Name', 'HyperContact'); ?></th>
							<th><?php _e('Display in widget', 'HyperContact'); ?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php foreach ($json as $key => $value): ?>
							<tr>
							<th class="manage-column column-cb check-column">
								<input class="" type="checkbox" name="mark[]" value="<?php echo $value->field ?>">
							</th>
							<td>
								<?php echo $value->field ?>
							</td>
							<td>
								<?php echo $value->fieldname ?>
							</td>
							<td>
								<?php if ($value->widget): 
									echo __('Yes', 'HyperContact');
									else:
									echo __('No', 'HyperContact');
								 endif ?>
							</td>
						</tr>
						<?php endforeach ?>
						
					</tbody>
				</table>
				<div class="tablenav bottom">
					<div class="alignleft actions">
						<select name="action2">
							<option value="-1" selected="selected"><?php _e('Bulk actions', 'HyperContact') ?></option>
							<option value="hc_delete"><?php _e('Delete selected', 'HyperContact') ?></option>
							<option value="hc_show_widget"><?php _e('Display in widget', 'HyperContact') ?></option>
							<option value="hc_hide_widget"><?php _e('Don`t display in widget', 'HyperContact') ?></option>
						</select>
						<input type="submit" name="" id="doaction2" class="button-secondary action" value="Apply">
						<input type="hidden" name="hc_save_form" value="changefields">
					</div>
				</div>
			</form>
		<?php
	}

	function hc_print_css(){
		$checked = '';
		$script = get_option('hc_use_css');
			if($script == 'usecss')
				$checked = 'checked="checked"';
		$cssSaved = stripslashes(get_option('hc_css'));

		?>
			<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<h2><?php _e('Change css:', 'HyperContact') ?></h2>
				<table class="form-table">
					<tr>
						<th class="form-field">
							<label for="hccss"><?php _e('Custom css:', 'HyperContact'); ?></label>
						</th>
						<td>
							<textarea name="hccss" cols="50" rows="20" placeholder="<?php _e('Add your css here', 'HyperContact'); ?>" resize="false"><?php echo stripslashes($cssSaved); ?></textarea>
						</td>
						<td>
							<span class="description">
								<p><?php _e('Overide the css for this plugin. These are the selectors you can use if you haven`t modified the templates files:', 'HyperContact'); ?></p>
								<h3><?php _e('Widget:', 'HyperContact'); ?></h3>
								<p>ul.hc_user_list</p>
								<h3><?php _e('Author:', 'HyperContact'); ?></h3>
								<p>div.hc-author-box</p>
								<p>h3.hc-admin-name</p>
								<p>div.hc-author-image img</p>
								<p>div.hc-author-info</p>
								<p>div.hc-author-description</p>
								<p>ul.hc-author-info-list</p>
							</span>
						</td>
					</tr>
					<tr>
						<th class="form-field">
							<label for="hcusecss"><?php _e('Use this css?:', 'HyperContact'); ?></label>
							
						</th>
						<td>
							<input type="checkbox" value="usecss" <?php echo $checked; ?> name="hcusecss">
						</td>
						<td>
							<input type="submit" value="Save"/>
							<input type="hidden" name="hc_save_form" value="savecss">
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	function hc_print_footer(){
		$checked = '';
		$script = get_option('hc_use_script');
			if($script == 'hcusescript')
				$checked = 'checked="checked"';
		$scriptSaved = stripslashes(get_option('hc_scripts'));
		?>
			<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<h2><?php _e('Add script to footer', 'HyperContact'); ?></h2>
				<table class="form-table">
					<tr>
						<th class="form-field">
							<label for="hcscripts"><?php _e('Scripts', 'HyperContact'); ?></label>
						</th>
						<td>
							<textarea name="hcscripts" cols="50" rows="20" placeholder="add your scripts here" resize="false"><?php echo stripslashes($scriptSaved); ?></textarea>
						</td>
						<td>
							<span class="description">
								<p><?php _e('Here you can paste scripts that can be used with userfields.', 'HyperContact'); ?></p>
								<p><?php _e('eg. skype check, facebook scripts and twitter scripts', 'HyperContact'); ?></p>
								<p><?php _e('You can only save this is you have the right to save html to the database.', 'HyperContact'); ?></p>
							</span>
						</td>
					</tr>
					<tr>
						<th class="form-field">
							<label for="hcusescript"><?php _e('Use these scripts?', 'HyperContact'); ?></label>
							
						</th>
						<td>
							<input type="checkbox" value="hcusescript" <?php echo $checked; ?> name="hcusescript">
						</td>
						<td>
							<input type="submit" value="Save"/>
							<input type="hidden" name="hc_save_form" value="savescript">
						</td>
					</tr>
				</table>
			</form>
		<?php
	}

	function hc_print_help(){
		?>
		<h2>HyperContact <?php _e('help', 'HyperContact'); ?></h2>
		<p><?php _e('There are several ways you can use this plugin. There is theme functions, shortcodes and two widgets you can use. All these options has several arguments for you to use.', 'HyperContact'); ?></p>
		<h3><?php _e('Widgets', 'HyperContact'); ?></h3>
		<p><?php _e('The first widget is the simplest to use. Just drag it to a siderbar and fill in the options. Remember to allow the fields in options page first. To change the apperance of the widget copy the file', 'HyperContact'); ?> <pre>"hypercontact/templates/widget.php"</pre> <?php _e('to', 'HyperContact'); ?> <pre>"your_theme_folder/plugins/hypercontact/templates/widget.php"</pre> <?php _e('and change the layout.', 'HyperContact'); ?></p>

		<p><?php _e('The second widget is a html widget where you can add shortcodes. More on shortcodes below', 'HyperContact'); ?></p>
		<h3><?php _e('Shortcodes', 'HyperContact'); ?></h3>
		<p><?php _e('In post and pages (and in HyperContact html widget) you can use shortcodes to show fields for a user. The are currently three different shortcodes:', 'HyperContact'); ?></p>
		<p><pre>[hc_user_field user="" field=""]</pre>user <?php _e('is the user you want to display and field are the field (for field names look under fields tab). You can use it like this ', 'HyperContact'); ?> <pre>[hc_user_field user="johndoe" field="skype"]</pre><?php _e('It will display the skype field for the user ', 'HyperContact'); ?>"johndoe"</p>
		<p><pre>[hc_author_field field=""]</pre> <?php _e('Displays a field from post/page author. VARNING use only in posts or pages', 'HyperContact'); ?></p>
		<h3><?php _e('Theme functions', 'HyperContact'); ?></h3>
		<p><?php _e('If you know how to edit themes and knows a little php these are the functions for you. You can add these functions to any site. One of these function does use a templatefile. Read more about it below', 'HyperContact'); ?> </p>
		<p><pre>hc_get_users(array $args)</pre><?php _e('Default', 'HyperContact'); ?> $args:
<pre>
$args = array(
	'gravatarsize'	=> '32', //size of the gravatar
	'single'		=> false, //single user
	'username'		=> '', //username for single user or comma seperated list of users
	'group'			=> '', //select group
	'fields'		=> '' // comma seperated list of fields if not set, display all
);
</pre>
<?php _e('Returns a array with object that contains the user fields', 'HyperContact'); ?>
</p>
		<p><pre>hc_users($args)</pre> <?php _e('Default $args is same as above.', 'HyperContact'); ?><br /><?php _e('Echoes an unordered list', 'HyperContact'); ?></p>

		<p><pre>hc_user_field($user, $field)</pre> <?php _e('Displays a single field for a user', 'HyperContact'); ?>. $username = <?php _e('the username of the user you want to display', 'HyperContact'); ?>. $field = <?php _e('the field you want to display.', 'HyperContact'); ?><br /><?php _e('Echoes field', 'HyperContact'); ?></p>

		<p><pre>hc_get_the_author($gravatarSize = '')</pre> <?php _e('Get information about the author.', 'HyperContact'); ?> $gravatar <?php _e('defaults to 32', 'HyperContact'); ?><br / >Returns a array with object that contains the user fields</p>
		<p><pre>hc_get_author_field($field , $gravatarSize = '')</pre> <?php _e('Get a field for author.', 'HyperContact'); ?></p>
		<p><pre>hc_author_field($field , $gravatarSize = '')</pre> <?php _e('Display a field for the author.', 'HyperContact'); ?></p>
		<p><pre>hc_get_field_name($field)</pre> <?php _e('Get fieldname for a field', 'HyperContact'); ?></p>
		<p><pre>hc_field_name($field)</pre> <?php _e('Display a fieldname for a field', 'HyperContact'); ?></p>
		<p><pre>hc_the_author($gravatarSize = '')</pre> <?php _e('Display author info. Uses a template file. For more info look below.', 'HyperContact'); ?></p>

		
		<h3><?php _e('Templates and css', 'HyperContact'); ?></h3>
		<p><?php _e('To style and change apperance of the display, use the template files and the css file from this plugin', 'HyperContact'); ?>. <?php _e('Copy the template folder from this plugin folder to', 'HyperContact'); ?> "your_theme/plugins/hypercontact/templates". <?php _e('In that folder you have a template file for author info and widget.', 'HyperContact'); ?> <?php _e('You also has a css file to use for styling.', 'HyperContact'); ?> <?php _e('Note: if you have added css in this options page the css from the file will not be displayed', 'HyperContact'); ?></p>

		<p><?php _e('If you have any questions go to the plugin page', 'HyperContact'); ?></p>
		<?php
	}

	/*
	 * Save, delete, hide or show
	*/
	if (isset($_POST['hc_save_form'])){
		$func = $_POST['hc_save_form'];
		switch ($func) {
			case 'newfield':
				hc_saveFields();
				break;

			case 'changefields':
				hc_check_fields();
				break;

			case 'savecss':
				hc_save_css();
				break;
				
			case 'savescript':
				hc_save_script();
				break;
				
			default:
				break;
		}
	}

	function hc_check_fields(){
		if((isset($_POST['action2']) || isset($_POST['action']))){
			$action = $_POST['action'];
			$actiontwo = $_POST['action2'];
			if ($actiontwo == 'hc_delete' || $action == 'hc_delete') {
				hc_deleteFields();
			}elseif($actiontwo == 'hc_show_widget' || $action == 'hc_show_widget'){
				hc_display_in_widget();
			}elseif($actiontwo == 'hc_hide_widget' || $action == 'hc_hide_widget'){
				hc_hide_in_widget();
			}
		}
	}

	function hc_saveFields(){
		if (!isset($_POST['field']))
			return;
		$json 		= get_option('hc_extra_fields');
		$field 		= strip_tags($_POST['field']);
		$fieldName 	= strip_tags($_POST['fieldname']);
	 	$special 	= urlencode( $special );
		if ($json == '') {
			$arr 	= array('field' => $field, 'fieldname' => $fieldName, 'widget' => 0 );
			$json 	= json_encode($arr);
			update_option('hc_extra_fields', $json);
		}else{
			$arr = json_decode($json);
			$arr[] = array('field' => $field,'fieldname' => $fieldName, 'widget' => 0);
			
			//encode and save
			$arr = json_encode($arr);
			update_option('hc_extra_fields', $arr);
		}
	}

	function hc_deleteFields(){
		$json  = get_option('hc_extra_fields');
		$arr = json_decode($json);
		$delArr = $_POST['mark'];
		$defaultArr = array('user_email','user_url','gravatar','display_name','description');
		foreach ($delArr as $key => $value) {
			foreach ($arr as $number => $val) {
				if ($val->field == $value && !in_array($val->field, $defaultArr)) {
					unset($arr[$number]);
				}
			}
		}

		$arr = array_values($arr);
		$arr = json_encode($arr);
		update_option('hc_extra_fields', $arr);
	}

	function hc_display_in_widget(){
		$json = get_option('hc_extra_fields');
		$arr = json_decode($json);
		$update = $_POST['mark'];
		foreach ($arr as $key => $value) {
			foreach ($update as $index => $val) {
				if ($value->field == $val) {
					$value->widget = 1;
				}
			}
		}
		$json = json_encode($arr);
		update_option('hc_extra_fields', $json);
	}

	function hc_hide_in_widget(){
		$json = get_option('hc_extra_fields');
		$arr = json_decode($json);
		$update = $_POST['mark'];
		foreach ($arr as $key => $value) {
			foreach ($update as $index => $val) {
				if ($value->field == $val) {
					$value->widget = 0;
				}
			}
		}
		$json = json_encode($arr);
		update_option('hc_extra_fields', $json);
	}

	function hc_save_css(){
		update_option('hc_css', strip_tags($_POST['hccss']));
		$useCss = get_option('hc_use_css');
		if (isset($_POST['hcusecss'])) {
			update_option('hc_use_css', 'usecss');
		}else{
			update_option('hc_use_css', '');
		}
	}

	function hc_save_script(){
		if (!current_user_can('unfiltered_html')) {
			return false;
		}
		$script = addslashes($_POST['hcscripts']);
		update_option('hc_scripts', $script);
		$useCss = get_option('hc_use_script');
		if (isset($_POST['hcusescript'])) {
			update_option('hc_use_script', 'hcusescript');
		}else{
			update_option('hc_use_script', '');
		}
	}

?>