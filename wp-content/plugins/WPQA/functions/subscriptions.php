<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Paid subscriptions button */
if (!function_exists('wpqa_paid_subscriptions')) :
	function wpqa_paid_subscriptions($show = '') {
		$subscriptions_payment = wpqa_options("subscriptions_payment");
		if ($subscriptions_payment == "on") {
			$out = ($show == true?'<div class="pop-footer pop-footer-subscriptions">':'').'<a class="subscriptions-link" href="'.wpqa_subscriptions_permalink().'" target="_blank">'.esc_html__("Get the paid membership","wpqa").'<i class="icon-sound"></i></a>'.($show == true?'</div>':'');
			return $out;
		}
	}
endif;
/* Cookie for content */
add_action('parse_query','wpqa_cookie_content');
if (!function_exists('wpqa_cookie_content')) :
	function wpqa_cookie_content() {
		$activate_need_to_login = wpqa_options("activate_need_to_login");
		if ($activate_need_to_login == "on" && !is_user_logged_in()) {
			$need_login_pages = (int)wpqa_options("need_login_pages");
			$wpqa_locked_content = wpqa_options("uniqid_cookie").'wpqa_locked_content';
			if (!is_home() && !is_front_page() && (is_page() || is_single()) && !isset($_COOKIE[$wpqa_locked_content])) {
				$wpqa_locked_count = wpqa_options("uniqid_cookie").'wpqa_locked_count';
				$count = 1;
				if (isset($_COOKIE[$wpqa_locked_count])) {
					$count = ($_COOKIE[$wpqa_locked_count])+1;
					setcookie($wpqa_locked_count,$count,time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}else {
					setcookie($wpqa_locked_count,$count,time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}
				if ($need_login_pages > 0 && isset($_COOKIE[$wpqa_locked_count]) && $_COOKIE[$wpqa_locked_count] == $need_login_pages) {
					setcookie($wpqa_locked_content,'wpqa_locked_content',time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}
			}
		}
	}
endif;
/* Paid subscriptions button */
add_action("wpqa_init","wpqa_check_get_subscriptions");
function wpqa_check_get_subscriptions() {
	$subscriptions_payment = wpqa_options("subscriptions_payment");
	if ($subscriptions_payment == "on") {
		/* Stripe webhooks */
		wpqa_stripe_data_webhooks();
		/* Check the subscription */
		wpqa_check_subscription();
	}
}
/* Check the subscription */
function wpqa_check_subscription() {
	if (is_user_logged_in()) {
		$user_id = get_current_user_id();
		//update_user_meta($user_id,"end_subscribe_time",strtotime(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -6 days"))));
		$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time",true);
		if (!is_super_admin($user_id) && $end_subscribe_time != "" && $end_subscribe_time < strtotime(date("Y-m-d H:i:s"))) {
			$default_group = wpqa_options("default_group");
			$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
			wp_update_user(array('ID' => $user_id,'role' => $default_group));
			delete_user_meta($user_id,"start_subscribe_time");
			delete_user_meta($user_id,"end_subscribe_time");
			delete_user_meta($user_id,"package_subscribe");
		}
	}
}
/* Check the subscription */
function wpqa_check_if_user_subscribe($user_id = 0) {
	$return = false;
	if (is_user_logged_in()) {
		$subscriptions_payment = wpqa_options("subscriptions_payment");
		if ($user_id == 0) {
			$user_id = get_current_user_id();
		}
		if ($subscriptions_payment == "on") {
			$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time",true);
			if (!is_super_admin($user_id) && $end_subscribe_time != "" && $end_subscribe_time < strtotime(date("Y-m-d H:i:s"))) {
				$return = false;
			}else if (!is_super_admin($user_id) && $end_subscribe_time != "" && $end_subscribe_time >= strtotime(date("Y-m-d H:i:s"))) {
				$return = true;
			}
		}
	}
	return $return;
}
/* Stripe webhooks */
function wpqa_stripe_data_webhooks() {
	if (isset($_GET["action"]) && $_GET["action"] == "stripe") {
		$input = @file_get_contents('php://input');
		$response = json_decode($input);
		if (isset($response->data->object)) {
			$response = $response->data->object;
			$user = reset(
				get_users(
					array(
						'meta_key'    => "subscribe_renew_id",
						'meta_value'  => $response->customer,
						'number'      => 1,
						'count_total' => false
					)
				)
			);
			$user_id = (isset($user->ID)?$user->ID:0);
			$status = (isset($response->status)?$response->status:"");
			if (($status == "active" || $status == "paid" || $status == "succeeded") && $user_id > 0) {
				$currency_code = wpqa_options("currency_code");
				$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");
				$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
				$array = array(
					"free"    => array("key" => "free","name" => esc_html__("Free membership","wpqa")),
					"monthly" => array("key" => "monthly","name" => esc_html__("Monthly membership","wpqa")),
					"3months" => array("key" => "3months","name" => esc_html__("3 Months membership","wpqa")),
					"6months" => array("key" => "6months","name" => esc_html__("6 Months membership","wpqa")),
					"yearly"  => array("key" => "yearly","name" => esc_html__("Yearly membership","wpqa")),
				);
				$payment_description = esc_html__("Paid membership","wpqa").(isset($array[$package_subscribe]["name"]) && $array[$package_subscribe]["name"] != ""?" - ".$array[$package_subscribe]["name"]:"")." ".esc_html__("(Renew)","WPQA");
				$array = array (
					'item_no' => 'subscribe',
					'item_name' => $payment_description,
					'item_price' => ($response->amount/100),
					'item_currency' => $currency_code,
					'item_transaction' => (isset($response->balance_transaction) && $response->balance_transaction != ""?$response->balance_transaction:(isset($response->invoice) && $response->invoice != ""?$response->invoice:'')),
					'payer_email' => $user->user_email,
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'sandbox' => '',
					'payment' => 'Stripe',
					"customer" => $response->customer,
					'renew' => 'subscribe',
					'custom' => 'wpqa_subscribe-'.$package_subscribe,
				);
				wpqa_payment_succeeded($user_id,$array);
			}
			http_response_code(200);
			die();
		}
	}
}
/* Free subscriptions */
function wpqa_free_subscriptions($user_id) {
	if ($user_id > 0 && isset($_POST["process"]) && $_POST["process"] == "subscribe" && isset($_POST["package_subscribe"])) {
		$package_subscribe = esc_html($_POST["package_subscribe"]);
		$currency_code = wpqa_options("currency_code");
		$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");
		$array = array (
			'item_no' => 'subscribe',
			'item_name' => esc_html__("Paid membership","wpqa")." - ".$package_subscribe." ".esc_html__("membership","wpqa"),
			'item_price' => 0,
			'item_currency' => $currency_code,
			'item_transaction' => '',
			'payer_email' => '',
			'first_name' => get_the_author_meta("first_name",$user_id),
			'last_name' => get_the_author_meta("last_name",$user_id),
			'question_answer' => '',
			'question_sticky' => '',
			'payment_item' => '',
			'subscribe' => $package_subscribe,
			'buy_points' => '',
			'sandbox' => '',
			'payment' => '',
			'renew' => 'subscribe',
			'custom' => 'wpqa_subscribe-'.$package_subscribe,
			'free' => 'payment',
		);
		wpqa_payment_succeeded($user_id,$array);
		wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("You have got a new free membership.","wpqa").'</p></div>','wpqa_session');
		wp_safe_redirect(esc_url(wpqa_profile_url($user_id)));
		die();
	}
}
?>