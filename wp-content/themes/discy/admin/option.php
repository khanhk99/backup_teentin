<?php
/* Save default options */
$discy_admin_options = new discy_admin_options;
$default_options = $discy_admin_options->get_default_values();
if (!get_option(discy_options)) {
	add_option(discy_options,$default_options);
}
/* Theme version */
$get_theme_version = get_option("discy_theme_version");
$discy_old_version = get_option("discy_old_version");
if ($get_theme_version == "") {
	add_option("discy_old_version",discy_theme_version);
}
if (isset($get_theme_version) && $get_theme_version != "") {
	if ($get_theme_version != discy_theme_version) {
		update_option("discy_old_version",$get_theme_version);
		update_option("discy_theme_version",discy_theme_version);
	}
}else {
	add_option("discy_theme_version",discy_theme_version);
}?>