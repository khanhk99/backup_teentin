<?php register_nav_menus(array(
	'discy_explore'       => 'Menu left - not logged',
	'discy_explore_login' => 'Menu left - logged',
	'header_menu'         => 'Header menu - not logged',
	'header_menu_login'   => 'Header menu - logged',
));
function discy_nav_fallback() {
	echo '<div class="menu-alert">'.esc_html__('You can use WP menu builder to build menus',"discy").'</div>';
}
function discy_empty_fallback() {
	echo '';
}?>