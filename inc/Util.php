<?php



/*
============================================

ヘッダからいらないものを削除

============================================
*/

add_action( "init", "remove_from_wp_head");
function remove_from_wp_head() {
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_generator');
}




/*
============================================

コメント・ディスカッション機能を無効化

============================================
*/


add_action( "init", "set_option_values");
function set_option_values() {
	update_option( "default_ping_status", false );
	update_option( "default_pingback_flag", false );
	update_option( "default_comment_status", false );
}



/*
============================================

カスタム投稿タイプ・タクソノミーを追加する関数

============================================
*/


$mw_manage_custom = new MW_Manage_Custom();

add_action( 'init', function(){
	global $mw_manage_custom;
	$mw_manage_custom->init();
},1);

function create_post_type( $name, $slug, $supports = array(), $options = array() ) {
	global $mw_manage_custom;
	$mw_manage_custom->custom_post_type( $name, $slug, $supports, $options );
}

function create_taxonomy( $name, $slug, $post_type, $options  = array()) {
	global $mw_manage_custom;
	$mw_manage_custom->custom_taxonomy( $name, $slug, $post_type, $options);
}



/*
============================================

テーマ内に admin.cssが存在すれば、
それを管理画面に適用

============================================
*/


add_action( "admin_init", "my_admin_css" );
function my_admin_css() {

	if (  TEMPLATEPATH != STYLESHEETPATH && file_exists( TEMPLATEPATH."/admin.css" ) ) {
		wp_enqueue_style( "admin_css", get_bloginfo( "template_directory" ) ."/admin.css" );
	}

	if ( file_exists( STYLESHEETPATH."/admin.css" ) ) {
		wp_enqueue_style( "admin_css", get_bloginfo( "stylesheet_directory" ) ."/admin.css" );
	}

}


/*
============================================

子ページの存在判定

============================================
*/


function has_children( $child_of = null ) {
	if ( is_null( $child_of ) ) {
		global $post;
		$child_of = ( $post->post_parent != '0' ) ? $post->post_parent : $post->ID;
	}
	return ( wp_list_pages( "child_of=$child_of&echo=0" ) ) ? true : false;
}


/*
 * ======================================================================================
 *
 *  記事内の最初の画像を持ってくる
 *
 * ======================================================================================
 * */


if( !function_exists("the_content_image") ) {

	function the_content_image( $size = 'thumbnail', $align = "" ) {
		global $post;
		$output = preg_match('/<img.+class=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if( $output ) {
			$first = $matches;
			preg_match("/wp-image-([0-9]+)/", $first[1], $matches);
			$attr = "";
			if( $align ) {
				$attr = array( "class" => $align );
			}
			echo wp_get_attachment_image( $matches[1], $size , false , $attr);

		} else {
			//$output = preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			//echo $matches[0];
		}
		return false;

	}

}



