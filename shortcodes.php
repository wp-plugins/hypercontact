<?php  
/**
 * display a field in post
 * @param array. Arguments to display
 * user="". the user to display
 * field="". the field to display
 * [hc_user_field user="" field=""]
*/
function hc_sc_user_field( $atts ) {
	extract( shortcode_atts( array(
		'user' => '',
		'field' => '',
	), $atts ) );
	$args = array(
				'single' => true,
				'username' => $user,
				'fields' => $field
			);
		$userObject = hc_get_users($args);
		return $userObject[0]->$field;
}
add_shortcode( 'hc_user_field', 'hc_sc_user_field' );

/**
 * display a field in post
 * @param array. Arguments to display
 * field="". the field to display
 * [hc_author_field field=""]
*/
function hc_sc_author_field( $atts ) {
	extract( shortcode_atts( array(
		'field' => ''
	), $atts ) );
	global $post;
	$username = get_user_by('id',$post->post_author);
	$username = $username->user_login;
	$args = array(
				'single' => true,
				'username' => $username,
				'fields' => $field
			);
		$userObject = hc_get_users($args);
		return $userObject[0]->$field;
}
add_shortcode( 'hc_author_field', 'hc_sc_author_field' );

/**
 * display a fieldname
 * @param array. Arguments to display
 * field="". the fieldname to display
 * [hc_field_name field=""]
*/
function hc_sc_field_name( $atts ) {
	extract( shortcode_atts( array(
		'field' => ''
	), $atts ) );
	return hc_get_field_name($field);
}
add_shortcode( 'hc_field_name', 'hc_sc_field_name' );

?>