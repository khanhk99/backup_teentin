<?php $site_users_only = (class_exists("WPQA")?wpqa_site_users_only():"");
$under_construction = (class_exists("WPQA")?wpqa_under_construction():"");
$wp_page_template = discy_post_meta("_wp_page_template","",false);?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo ($site_users_only == "yes" || $under_construction == "on" || $wp_page_template == "template-landing.php"?"dicsuss-html-login ":"")?>no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="manifest" href="/manifest.json">
	<meta name="theme-color" content="#de751f">
	<?php wp_head();?>
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-167219446-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-167219446-1');
</script>
	
</head>
<body <?php body_class();echo (is_singular('question')?' itemscope itemtype="https://schema.org/QAPage"':'')?>>
	<?php $logo_display = discy_options("logo_display");
	$logo_img    = discy_image_url_id(discy_options("logo_img"));
	$retina_logo = discy_image_url_id(discy_options("retina_logo"));
	$logo_height = discy_options("logo_height");
	$logo_width  = discy_options("logo_width");
	if ($site_users_only == "yes" || $under_construction == "on" || $wp_page_template == "template-landing.php") {
		include locate_template("includes/login-page.php");
		get_footer();
		die();
	}else {
		include locate_template("includes/header-code.php");
	}?>