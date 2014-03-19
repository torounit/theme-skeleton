<?php

/*
 * ======================================================================================
 *
 *  Body Class
 *
 * ======================================================================================
 * */


Class Body_Class extends Singleton {


	protected function initialize() {
		add_filter( "body_class", array($this, "add_page_slug"), 10 ,1);
		add_filter( "body_class", array($this, "add_post_archive_slug"), 10 ,1);
		add_filter( "body_class", array($this, "add_post_type_slug"), 10 ,1);
	}

	public function add_page_slug($classes) {
		global $post;
		if( is_page() ) {
			$classes[] = $post->post_name;
		}

		return $classes;
	}

	public function add_post_archive_slug($classes) {
		if(get_post_type() == "post" and !is_front_page()) {
			if($page_id = get_option("page_for_posts")) {
				$posts_page = get_page($page_id);
				$classes[] = $posts_page->post_name;
			}
		}

		return $classes;

	}

	function add_post_type_slug($classes) {
		global $post;
		if(is_singular()) {
			$classes[] = $post->post_type."-slug-".$post->post_name;
		}
		return $classes;
	}

}
