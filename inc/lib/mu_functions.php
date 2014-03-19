<?php


function is_child_site() {
	global $blog_id;
	if ( $blog_id == 1 ) {
		return false;
	}

	return true;
}




/*
============================================

switch_to_blogしたときのパーマリンク修正

============================================
*/

/**
 * switch_site_rewrite ()
 *
 * Deal with permalinks and cat and tag base structures
 *
 * @global object $wp_rewrite
 */
function switch_site_rewrite () {
	global $wp_rewrite;
	if ( is_object( $wp_rewrite ) ) {

		$permalink_structure = get_option( 'permalink_structure' );
		$front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
		$wp_rewrite->front = $front;
		if ( !empty( $permalink_structure ) ) {
			$wp_rewrite->permalink_structure = $permalink_structure;
		}

		$category_base = get_option( 'category_base' );

		if ( empty( $category_base ) ) {
			$category_base = "category";
		}

		$wp_rewrite->set_category_base( $category_base );


		$tag_base = get_option('tag_base');

		if ( empty( $tag_base ) ) {
			$category_base = "tag";
		}
		$wp_rewrite->set_tag_base( $tag_base );

/*
		$post_types = get_post_types( array('_builtin'=>false, 'publicly_queryable'=>true,'show_ui' => true) );
		foreach ( $post_types as $post_type ){
			$permalink_structure = get_option( $post_type.'_structure' );

			$wp_rewrite->extra_permastructs[$post_type]["struct"] = $permalink_structure;

		}
*/
	}
}




/*
============================================

Body Class にサイトパスを表示

============================================
*/


class MU_Body_Class {

	public function __construct() {
		add_filter( "body_class", array( $this, "body_class" ) );
	}

	public function get_blog_path() {
		$blog_details = get_blog_details( $GLOBALS['blog_id'] );
		$blog_path = trim( $blog_details->path, "/" );
		return $blog_path;
	}

	public function body_class( $classes ) {
		$blog_path = $this->get_blog_path();
		if ( !$blog_path ) {
			$blog_path = "main";
		}
		$classes[] = "site-".$blog_path;
		return $classes;
	}
}


?>