<?php
	/*
	 *	Plugin Name: HyperContact
	 *	Plugin URI: http://wordpress.org/extend/plugins/hypercontact/
	 *	Description: This plugin creates a widget that display users contact forms
	 *	Author: Johan Ahlbäck @ www.hypernode.se
	 *	Version: 1.4.3
	 *	Author URI: http://www.hypernode.se/
	*/
?>
<?php
	/*
	 *	Language
	*/
		$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		load_plugin_textdomain('HyperContact', $dir);
	/*
	 * Adds options to table. 
	*/
	register_activation_hook( __FILE__, 'hc_activate' );
	function hc_activate(){
		add_option('hc_extra_fields', false);
		add_option('hc_use_css', false);
		add_option('hc_css', false);
		add_option('hc_scripts', false);
		add_option('hc_use_script', false);
		$arr = array();
		$arr[] = array('field' => 'user_email', 'fieldname' => __('Email','HyperContact'), 'widget' => 0 );
		$arr[] = array('field' => 'user_url', 'fieldname' => __('Website', 'HyperContact'), 'widget' => 0 );
		$arr[] = array('field' => 'gravatar', 'fieldname' => __('gravatar', 'HyperContact'), 'widget' => 0 );
		$arr[] = array('field' => 'display_name', 'fieldname' => __('Display name','HyperContact'), 'widget' => 0 );
		$arr[] = array('field' => 'description', 'fieldname' => __('Description', 'HyperContact'), 'widget' => 0 );
		$arr = json_encode($arr);
		update_option('hc_extra_fields', $arr);
	}
	
	/*
	 * Add fields to user profile
	*/
	add_filter('user_contactmethods','hc_user_fields',10,2);

	function hc_user_fields( $contactmethods ) {
		$contactmethods = array();
		$json = get_option('hc_extra_fields');
		$arr = json_decode($json);
		$defaultArr = array('user_email','user_url','gravatar','display_name','description');
		foreach ($arr as $key => $value) {
			if (!in_array($value->field, $defaultArr)) {
				$contactmethods[$value->field] = $value->fieldname;
			}
		}
	  return $contactmethods;
	}

	/*
	 * Includes all files in plugin
	*/
	require_once('adminpage.php');
	require_once('widget.php');
	require_once('functions.php');
	require_once('shortcodes.php');
	
	/**
	 * Adds style
	*/
	
	$useCss = get_option('hc_use_css');
	if ($useCss == 'usecss') {
		add_action('wp_head', 'hc_add_custom_css');
	}else {
		add_action('wp_print_styles', 'hc_add_css');
	}

	function hc_add_custom_css(){
		if (!is_admin()) {
			$css = get_option('hc_css');
			echo '<style type="text/css">'.stripslashes($css).'</style>';
		}
	}

	function hc_add_css(){
			if (!is_admin() ) {
				if (file_exists(get_bloginfo('stylesheet_directory').'plugins/hypercontact/css/hc_style.css')) {
					wp_enqueue_style( 'hc_style', get_bloginfo('stylesheet_directory').'plugins/hypercontact/css/hc_style.css');
				}else{
					wp_enqueue_style( 'hc_style', plugins_url('hc_style.css', __FILE__ ));	
				} 
			}
		}
	
	/**
	 * Adds script
	*/
	$useScript = get_option('hc_use_script');
	if ($useScript == 'hcusescript') {
		add_action('wp_footer', 'hc_add_script');
	}
	function hc_add_script(){
		if (!is_admin()) {
			$script = stripslashes(get_option('hc_scripts'));
			echo '<script type="text/javascript">'.stripslashes($script).'</script>';
		}
		
	}

	/**
	 * Add links to plugin page
	*/
	add_action('init', 'hc_add_links');
	function hc_add_links(){
		add_action('plugin_action_links_' . plugin_basename(__FILE__), 'hc_add_plugin_actions');
	}
	function hc_add_plugin_actions($all){
		$links = array();
		 $links[ ] = '<a href="users.php?page=hypercontact-config">' . __('Settings') . '</a>';

	    return array_merge($links, $all);
	}

?>