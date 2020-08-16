<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly
}

do_action("wpqa_before_subscriptions");?>

<div class='wpqa-templates wpqa-subscriptions-template'>
	<div class="page-sections">
		<div class="page-section">
			<?php $subscriptions_payment = wpqa_options("subscriptions_payment");
			if ($subscriptions_payment == "on") {
				$subscriptions_options = wpqa_options("subscriptions_options");
				$user_id = get_current_user_id();
				$default_group = wpqa_options("default_group");
				$subscriptions_group = wpqa_options("subscriptions_group");
				$roles = wpqa_options("roles");
				$currency_code = wpqa_options("currency_code");
				$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");
				wpqa_free_subscriptions($user_id);
				$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
				if ($package_subscribe != "") {
					echo '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("You have a paid membership already.","wpqa").'</p></div>';
				}
				echo '<div class="page-wrap-content">
					<h2 class="post-title-3"><i class="icon-basket"></i>'.esc_html__("Subscriptions","wpqa").'</h2>
					<div class="points-section buy-points-section subscriptions-section">
						<ul class="row">';
							$array = array(
								"free"    => array("key" => "free","name"    => esc_html__("Free membership","wpqa")),
								"monthly" => array("key" => "monthly","name" => esc_html__("Monthly membership","wpqa")),
								"3months" => array("key" => "3months","name" => esc_html__("3 Months","wpqa")),
								"6months" => array("key" => "6months","name" => esc_html__("6 Months","wpqa")),
								"yearly"  => array("key" => "yearly","name"  => esc_html__("Yearly","wpqa")),
							);
							$roles_array = array(
								"ask_question"           => esc_html__("Can ask a question.","wpqa"),
								"show_question"          => esc_html__("Can show questions.","wpqa"),
								"add_answer"             => esc_html__("Can add an answer.","wpqa"),
								"show_answer"            => esc_html__("Can show answers.","wpqa"),
								"add_post"               => esc_html__("Can add a post.","wpqa"),
								"add_category"           => esc_html__("Can add a category.","wpqa"),
								"send_message"           => esc_html__("Can send message.","wpqa"),
								"upload_files"           => esc_html__("Can can upload files.","wpqa"),
								"approve_question"       => esc_html__("Can get approve to your questions auto.","wpqa"),
								"approve_answer"         => esc_html__("Can get approve to your answers auto.","wpqa"),
								"approve_post"           => esc_html__("Can get approve to your posts auto.","wpqa"),
								"approve_comment"        => esc_html__("Can get approve to your comments auto.","wpqa"),
								"approve_question_media" => esc_html__("Can get approve to your questions auto when have media attached.","wpqa"),
								"approve_answer_media"   => esc_html__("Can get approve to your answers auto when have media attached.","wpqa"),
								"without_ads"            => esc_html__("You will not see any ads at the site.","wpqa"),
							);
							foreach ($array as $key => $value) {
								if ($package_subscribe == $key || ($package_subscribe == "" && ($key == "free" || (isset($subscriptions_options[$key]) && $subscriptions_options[$key] !== "0")))) {
									$price = (int)wpqa_options("subscribe_".$key);
									echo '<li id="li-subscribe-'.$key.'" class="col col12">
										<div class="point-section subscribe-section'.(is_user_logged_in()?' subscribe-'.$key:'').($package_subscribe != ""?" paid-subscribe":"").'">
											<div class="point-div">
												<span>'.$value["name"].'</span>'.($key == "free"?esc_html__("Free","wpqa"):esc_html__("Paid","wpqa")).'
												<span class="points-price">'.floatval(($key == "free"?0:$price)).' '.$currency_code.'</span>
											</div>
											<ul>';
												if ($key == "free") {
													$roles_can = $roles[$default_group];
												}else {
													$roles_can = $roles[$subscriptions_group];
												}
												foreach ($roles_can as $roles_key => $roles_value) {
													if ($roles_value == "on" && isset($roles_array[$roles_key])) {
														echo '<li'.($key == "free"?"":" class='paid-membership'").'>'.$roles_array[$roles_key].'</li>';
													}
												}
											echo '</ul>
											<div class="buy-points-content">';
											if ($key == "free") {
												if (!is_user_logged_in()) {
													echo '<a href="'.wpqa_signup_permalink().'" class="button-default signup-panel '.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','').'">'.esc_html__("Sign Up","wpqa").'</a>';
												}
											}else if ($package_subscribe == "") {
												if (!is_user_logged_in()) {
													echo '<a data-subscribe="'.$key.'" href="'.wpqa_signup_permalink().'" class="button-default signup-panel subscribe-signup '.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','').'">'.esc_html__("Subscribe","wpqa").'</a>';
												}else {
													echo '<a href="#" class="wpqa-open-click button-default">'.esc_html__("Subscribe","wpqa").'</a>
													<div class="clearfix"></div>
													<div class="buy-points-div wpqa-open-div'.(isset($_POST["add_coupon"]) && $_POST["add_coupon"] == "submit" && isset($_POST["package_subscribe"]) && $_POST["package_subscribe"] == $key?"":" wpqa_hide").'">';
														if ($price > 0) {
															echo wpqa_get_payment_coupons($user_id,false,0,0,"subscribe",0,$price,$key,esc_html__("Please make a payment to buy paid membership %s.","wpqa"));
														}
													echo '</div>
													<div class="clearfix"></div>';
												}
											}
											echo '</div>
										</div>
									</li>';
								}
							}
						echo '</ul>
					</div><!-- End subscriptions-section -->
				</div><!-- End page-wrap-content -->';
			}else {
				echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, this page is not available.","wpqa").'</p></div>';
			}?>
		</div><!-- End page-section -->
	</div><!-- End page-sections -->
</div><!-- End wpqa-subscriptions-template -->

<?php do_action("wpqa_after_subscriptions");?>