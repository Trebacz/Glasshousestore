<?php

switch ($_POST['selfreq']) {
	case "daily":
		update_option('udinra_image_sitemap_freq',1);
		break;
	case "always":
		update_option('udinra_image_sitemap_freq',2);
		break;
	default:
		update_option('udinra_image_sitemap_freq',3);
		break;
}

switch ($_POST['pingfreq']) {
	case "disable":
		update_option('udinra_image_sitemap_ping',2);
		break;
	default:
		update_option('udinra_image_sitemap_ping',1);
		break;
}

switch ($_POST['indexflag']) {
	case "disable":
		update_option('udinra_image_sitemap_index',2);
		break;
	default:
		update_option('udinra_image_sitemap_index',1);
		break;
}

update_option('udinra_image_sitemap_count',intval($_POST['urlcount']));
?>