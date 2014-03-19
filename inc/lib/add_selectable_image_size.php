<?php

Class Add_Selectable_Image_Size {

	public $image_sizes = array();

	public function __construct() {
		add_action( 'after_setup_theme', array(&$this, 'add_image_sizes') );
		add_filter( 'image_size_names_choose', array(&$this, 'add_image_size_select') );
	}

	public function add_image_size( $name, $width, $height, $crop, $label, $selectable ) {
		$this->image_sizes[$name] = array(
			'name'       => $label, // 選択肢のラベル名
			'width'      => $width,    // 最大画像幅
			'height'     => $height,    // 最大画像高さ
			'crop'       => $crop,  // 切り抜きを行うかどうか
			'selectable' => $selectable   // 選択肢に含めるかどうか
		);
	}

	public function add_image_sizes() {

		foreach ( $this->image_sizes as $slug => $size ) {
			add_image_size( $slug, $size['width'], $size['height'], $size['crop'] );
		}
	}

	public function add_image_size_select( $size_names ) {
		$custom_sizes = get_intermediate_image_sizes();
		foreach ( $custom_sizes as $custom_size ) {
			if ( isset( $this->image_sizes[$custom_size]['selectable'] ) && $this->image_sizes[$custom_size]['selectable'] ) {
				$size_names[$custom_size] = $this->image_sizes[$custom_size]['name'];
			}
		}
		return $size_names;
	}
}


function add_selectable_image_size( $name, $width = 0, $height = 0, $crop = false, $label = "" ) {
	$asis = new Add_Selectable_Image_Size();
	if( $label == "" ) {
		$label = $name;
	}
	$asis->add_image_size( $name, $width, $height, $crop , $label, true );
}
?>