<?php

Class Assets_Init extends Singleton {

	public $styles;
	public $scripts;
	public $prefetch;

	protected function initialize() {

		$styles = array();
		$scripts = array();
		$prefetch = array();

		$this->add_hook();
	}

	private function add_hook() {
		add_action( "wp_enqueue_scripts", array( $this, "enqueue_styles") );
		add_action( "wp_enqueue_scripts", array( $this, "enqueue_scripts") );
		add_action( "wp_head", array($this, "dns_prefetch"),1);
	}

	/**
	 *
	 * Add DNS prefetch.
	 *
	 * */

	public function dns_prefetch() {
		if( !empty($this->prefetch) ):
		?>
			<meta http-equiv="x-dns-prefetch-control" content="on">
			<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
			<link rel="dns-prefetch" href="//code.jquery.com">
			<?php
			foreach($this->prefetch as $url):?>
				<link rel="dns-prefetch" href="//<?php echo $url;?>">
			<?php
			endforeach;
		endif;
	}

	/**
	 *
	 * Enqueue CSS.
	 *
	 * */

	public function add_prefetch($url) {
		$url = preg_replace("/^\/\//", "http://", $url);
		$param = parse_url($url);
		$host = $param["host"];
		$myhost = parse_url(home_url());
		$myhost = $myhost["host"];
		if( empty($this->prefetch) or in_array($url, $this->prefetch) ) {
			$this->prefetch[] = $host;
		}

	}

	public function add_style($name, $url) {
		$this->styles[] = array("name" => $name, "url" => $url);
		$this->add_prefetch($url);
	}

	public function enqueue_styles() {

		if(!empty($this->styles)) {
			foreach ($this->styles as $style ) {
				wp_enqueue_style( $style["name"], $style["url"], array(), "1" );
			}
		}

	}


	/**
	 *
	 * Enqueue JavaScript.
	 *
	 * */

	public function add_script($name,$url) {
		$this->scripts[] = array("name" => $name, "url" => $url);
		$this->add_prefetch($url);
	}

	public function enqueue_scripts() {

		if(!empty($this->scripts)) {
			foreach ( $this->scripts as $script ) {
				wp_deregister_script($script["name"]);
				wp_enqueue_script($script["name"], $script["url"] );
			}
		}

	}
}