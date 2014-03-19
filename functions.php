<?php

require_once "inc/Singleton.php";
require_once 'inc/Helper.php';
require_once 'inc/Body_Class.php';
require_once 'inc/Assets_Init.php';
require_once 'inc/Util.php';

//ライブラリの読み込み
require_once dirname( __File__ ). "inc/lib/MW_Manage_Custom.class.php";
require_once dirname( __File__ ). "inc/lib/add_selectable_image_size.php";
require_once dirname( __File__ ). "inc/lib/Default_Term.class.php";


if(!is_admin()) {
	$assets = Assets_Init::getInstance();
	$assets->add_style("all", get_template_directory_uri() ."/assets/stylesheets/all.css");

	$assets->add_script("modernizr", "//cdnjs.cloudflare.com/ajax/libs/modernizr/2.7.1/modernizr.min.js");
	$assets->add_script("jquery", "//code.jquery.com/jquery-2.1.0.min.js");

	if( WP_DEBUG ) {
		$assets->add_script("all", get_template_directory_uri() ."/assets/javascripts/all.js");
	}else {
		$assets->add_script("all", get_template_directory_uri() ."/assets/javascripts/all.min.js");
	}

	Body_Class::getInstance();
}


//カスタム投稿タイプのデフォルトのタクソノミーを指定。
new Default_Term();


if( is_multisite() ) {
	require_once dirname( __File__ ). "/lib/mu_functions.php";
	add_action( 'switch_blog', 'switch_site_rewrite' );
	new MU_Body_Class();
}




