<?php

function src($url = "") {
	echo 'src="'.get_template_directory_uri().$url.'"';
}