<?php

status_header( '200' ); 
header( 'Content-Type: text/xml; charset=' . get_bloginfo( 'charset' ), true );

echo '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . 
	 '"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

include 'init/udinra_init_image.php';
include 'img/udinra_img.php';
include 'exit/udinra_ping_image.php';

echo $udinra_xml_image;


?>