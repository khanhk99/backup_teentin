<?php

/* @author    2codeThemes
*  @package   WPQA/payments
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly
}

/* Payments */
if (!function_exists('wpqa_get_payment_coupons')) :
	function wpqa_get_payment_coupons($user_id,$popup = false,$item_id = 0,$days_sticky = 0,$kind_of_payment = "ask_question",$points = 0,$price = 0,$package_name = "",$message = "",$number_vars = 1) {
		$output = '';
		$active_coupons = wpqa_options("active_coupons");
		$coupons = wpqa_options("coupons");
		$free_coupons = wpqa_options("free_coupons");
		$currency_code = wpqa_options("currency_code");
		$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");
		if ($kind_of_payment == "answer") {
			$payment_option = "pay_answer_payment";
			$payment_by_points = "payment_type_answer";
			$points_price = "answer_payment_points";
			$payment_description = esc_attr__("Pay to add answer","wpqa");
			$item_number = "pay_answer";
			$return_url = get_the_permalink();
			$item_process = "answer";
			$custom_buttom = $item_id;
		}else if ($kind_of_payment == "sticky") {
			$payment_option = "pay_sticky_payment";
			$payment_by_points = "payment_type_sticky";
			$points_price = "sticky_payment_points";
			$payment_description = esc_attr__("Pay to make question sticky","wpqa");
			$item_number = "pay_sticky";
			$return_url = get_the_permalink();
			$item_process = "sticky";
			$custom_buttom = $item_id;
		}else if ($kind_of_payment == "buy_points") {
			$payment_option = $payment_by_points = $points_price = "";
			$payment_description = esc_attr__("Buy points","wpqa").($package_name != ""?" - ".$package_name:"");
			$item_number = "buy_points";
			$return_url = wpqa_buy_points_permalink();
			$item_process = "points";
			$custom_buttom = $points;
		}else if ($kind_of_payment == "subscribe") {
			$array = array(
				"free"    => array("key" => "free","name" => esc_html__("Free membership","wpqa")),
				"monthly" => array("key" => "monthly","name" => esc_html__("Monthly membership","wpqa")),
				"3months" => array("key" => "3months","name" => esc_html__("3 Months membership","wpqa")),
				"6months" => array("key" => "6months","name" => esc_html__("6 Months membership","wpqa")),
				"yearly"  => array("key" => "yearly","name" => esc_html__("Yearly membership","wpqa")),
			);
			$payment_option = $payment_by_points = $points_price = "";
			$payment_description = esc_attr__("Paid membership","wpqa").(isset($array[$package_name]["name"]) && $array[$package_name]["name"] != ""?" - ".$array[$package_name]["name"]:"");
			$plan_name = (isset($array[$package_name]["key"]) && $array[$package_name]["key"] != ""?$array[$package_name]["key"]:"");
			$item_number = "subscribe";
			$return_url = wpqa_subscriptions_permalink();
			$item_process = "subscribe";
			$custom_buttom = $plan_name;
		}else {
			$payment_option = "pay_ask_payment";
			$payment_by_points = "payment_type_ask";
			$points_price = "ask_payment_points";
			$wpqa_add_question_user = wpqa_add_question_user();
			if ($wpqa_add_question_user != "") {
				$author_display_name = get_the_author_meta("display_name",$wpqa_add_question_user);
			}
			$payment_description = esc_attr__("Ask a new question","wpqa").($wpqa_add_question_user != ""?" ".esc_attr__("for","wpqa")." ".$author_display_name:"");
			$item_number = "ask_question";
			$return_url = ($wpqa_add_question_user != ""?wpqa_add_question_permalink("user",$wpqa_add_question_user):wpqa_add_question_permalink());
			$item_process = "ask";
			$custom_buttom = ($wpqa_add_question_user != ""?$wpqa_add_question_user:"");
		}

		$payment_option = apply_filters("wpqa_filter_payment_option",$payment_option);
		$payment_by_points = apply_filters("wpqa_filter_payment_by_points",$payment_by_points);
		$points_price = apply_filters("wpqa_filter_points_price",$points_price);
		$payment_description = apply_filters("wpqa_filter_payment_description",$payment_description);
		$item_number = apply_filters("wpqa_filter_item_number",$item_number);
		$return_url = apply_filters("wpqa_filter_return_url",$return_url);
		$item_process = apply_filters("wpqa_filter_item_process",$item_process);

		$payment_by_points = wpqa_options($payment_by_points);
		$payment_methodes = wpqa_options("payment_methodes");
		foreach ($payment_methodes as $key => $value) {
			if ($value["value"] !== '0') {
				$payment_methodes_activated = true;
			}
		}
		if ($payment_by_points == "points" || !isset($payment_methodes_activated)) {
			$points_price = floatval(wpqa_options($points_price));
			if ($kind_of_payment == "sticky") {
				$output .= '<div class="alert-message success"><i class="icon-check"></i><p>'.sprintf(esc_html__("Please pay by points to allow to be able to sticky the question %s For %s days.","wpqa"),' "'.$points_price." ".esc_html__("points","wpqa").'"',$days_sticky).'</p></div>';
			}else if ($kind_of_payment == "answer") {
				$output .= '<div class="alert-message success"><i class="icon-check"></i><p>'.sprintf(esc_html__("Please pay by points to allow to be able to add a answer %s.","wpqa"),' "'.$points_price." ".esc_html__("points","wpqa").'"').'</p></div>';
			}else {
				$output .= '<div class="alert-message success"><i class="icon-check"></i><p>'.sprintf(esc_html__("Please pay by points to allow to be able to ask a question %s.","wpqa"),' "'.$points_price." ".esc_html__("points","wpqa").'"').'</p></div>';
			}
			if ($user_id > 0) {
				$points_user = (int)get_user_meta($user_id,"points",true);
				if ($points_price <= $points_user) {
					$output .= '<div class="process_area">
						<form method="post" action="'.$return_url.'">
							<input type="submit" class="button" value="'.esc_attr__("Process","wpqa").'">
							<input type="hidden" name="process" value="'.$item_process.'">
							<input type="hidden" name="points" value="'.$points_price.'">';
							if ($item_id > 0) {
								$output .= '<input type="hidden" name="post_id" value="'.$item_id.'">';
							}
						$output .= '</form>
					</div>';
				}else {
					$buy_points_payment = wpqa_options("buy_points_payment");
					$output .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you haven't enough points","wpqa").($buy_points_payment == "on"?', <a href="'.wpqa_buy_points_permalink().'">'.esc_html__("You can buy the points from here.","wpqa").'</a>':'.').'</p></div>';
				}
			}
		}else {
			$pay_payment = $last_payment = floatval($price > 0?$price:wpqa_options($payment_option));
			if ($active_coupons == "on") {
				if (isset($_POST["add_coupon"]) && $_POST["add_coupon"] == "submit") {
					$coupon_name = (isset($_POST["coupon_name"])?esc_attr($_POST["coupon_name"]):"");
					$coupons_not_exist = "no";
					
					if ($coupon_name == "") {
						$output .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Please enter your coupon.","wpqa").'</p></div>';
					}else if (isset($coupons) && is_array($coupons)) {
						foreach ($coupons as $coupons_k => $coupons_v) {
							if (is_array($coupons_v) && in_array($coupon_name,$coupons_v)) {
								$coupons_not_exist = "yes";
								
								if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] != "") {
									$coupons_v["coupon_date"] = !is_numeric($coupons_v["coupon_date"]) ? strtotime($coupons_v["coupon_date"]):$coupons_v["coupon_date"];
								}
								
								if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] != "" && current_time( 'timestamp' ) > $coupons_v["coupon_date"]) {
									$output .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon has expired.","wpqa").'</p></div>';
								}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent" && (int)$coupons_v["coupon_amount"] > 100) {
									$output .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon is not valid.","wpqa").'</p></div>';
								}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount" && (int)$coupons_v["coupon_amount"] > $pay_payment) {
									$output .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon is not valid.","wpqa").'</p></div>';
								}else {
									if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent") {
										$the_discount = ($pay_payment*$coupons_v["coupon_amount"])/100;
										$last_payment = $pay_payment-$the_discount;
									}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount") {
										$last_payment = $pay_payment-$coupons_v["coupon_amount"];
									}
									$output .= '<div class="alert-message success"><i class="icon-check"></i><p>'.sprintf(esc_html__('Coupon "%s" applied successfully.','wpqa'),$coupon_name).'</p></div>';
									
									update_user_meta($user_id,$user_id."_coupon",esc_attr($coupons_v["coupon_name"]));
									update_user_meta($user_id,$user_id."_coupon_value",($last_payment <= 0?"free":$last_payment));
								}
							}
						}
						
						if ($coupons_not_exist == "no") {
							$output .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Coupon does not exist!.","wpqa").'</p></div>';
						}else if ($coupons_not_exist == "no") {
							$output .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.sprintf(esc_html__('Coupon "%s" does not exist!.','wpqa'),$coupon_name).'</p></div>';
						}
					}
				}else {
					delete_user_meta($user_id,$user_id."_coupon");
					delete_user_meta($user_id,$user_id."_coupon_value");
				}
			}

			$payment_with_currency = ' "'.$last_payment." ".$currency_code.'"';
			if ($number_vars == 2) {
				if ($kind_of_payment == "sticky") {
					$last_message = sprintf($message,$payment_with_currency,$days_sticky);
				}else {
					$last_message = sprintf($message,$points,$payment_with_currency);
				}
			}else {
				$last_message = sprintf($message,$payment_with_currency);
			}
			//$message = sprintf(esc_html__("Please make a payment to allow to be able to add a annswer %s.","wpqa"),' "'.$last_payment." ".$currency_code.'"');
			$output .= '<div class="alert-message success"><i class="icon-check"></i><p>'.$last_message.'</p></div>';
			
			if (isset($coupons) && is_array($coupons) && $free_coupons == "on" && $active_coupons == "on") {
				foreach ($coupons as $coupons_k => $coupons_v) {
					$pay_payments = $last_payments = floatval($price > 0?$price:wpqa_options($payment_option));
					if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent") {
						$the_discount = ($pay_payments*$coupons_v["coupon_amount"])/100;
						$last_payments = $pay_payments-$the_discount;
					}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount") {
						$last_payments = $pay_payments-$coupons_v["coupon_amount"];
					}
					
					if ($last_payments <= 0) {
						if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] != "") {
							$coupons_v["coupon_date"] = !is_numeric($coupons_v["coupon_date"]) ? strtotime($coupons_v["coupon_date"]):$coupons_v["coupon_date"];
						}
						if (isset($coupons_v["coupon_type"]) && ($coupons_v["coupon_type"] == "percent" && (int)$coupons_v["coupon_amount"] >= 100  && (isset($coupons_v["coupon_date"]) && ($coupons_v["coupon_date"] == "" || ($coupons_v["coupon_date"] != "" && strtotime(date("M j, Y") <= $coupons_v["coupon_date"]))))) || ($coupons_v["coupon_type"] == "discount" && (int)$coupons_v["coupon_amount"] >= $pay_payments && (isset($coupons_v["coupon_date"]) && ($coupons_v["coupon_date"] == "" || ($coupons_v["coupon_date"] != "" && strtotime(date("M j, Y") <= $coupons_v["coupon_date"])))))) {
							$output .= '<div class="alert-message"><i class="icon-lamp"></i><p>'.sprintf(esc_html__("Take it free? Add this coupon %s.","wpqa"),' "'.$coupons_v["coupon_name"].'"').'</p></div>';
						}
					}
				}
			}
			
			if ($active_coupons == "on") {
				$output .= '<div class="coupon_area">
					<form method="post">
						<input type="text" name="coupon_name" class="coupon_name" value="" placeholder="'.esc_attr__("Coupon code","wpqa").'">
						<input type="submit" class="button-default" value="'.esc_attr__("Apply Coupon","wpqa").'">';
						if ($kind_of_payment == "buy_points") {
							$output .= '<input type="hidden" name="package_points" value="'.esc_attr($points).'">';
						}else if ($kind_of_payment == "subscribe") {
							$output .= '<input type="hidden" name="package_subscribe" value="'.esc_attr($plan_name).'">';
						}
						if ($popup == "popup") {
							$output .= '<input type="hidden" name="form_type" value="add_question">
							<input type="hidden" name="question_popup" value="popup">';
						}
						$output .= '<input type="hidden" name="add_coupon" value="submit">
					</form>
				</div>';
			}
			
			$output .= '<div class="clearfix"></div>';
			if ($last_payment > 0 && isset($payment_methodes_activated)) {				
				if ($kind_of_payment == "subscribe") {
					$interval_count = ($plan_name == "monthly" || $plan_name == "yearly"?1:($plan_name == "3months"?3:6));
				}
				$rand = rand(1,1000);
				$inputs = (isset($coupon_name) && $coupon_name != ''?'<input type="hidden" name="coupon" value="'.$coupon_name.'">':'').'
				<input type="hidden" name="custom" value="wpqa_'.$item_number.'-'.$custom_buttom.'">
				<input type="hidden" name="item_name" value="'.$payment_description.'">
				<input type="hidden" name="item_number" value="'.$item_number.'">
				<button type="submit" class="button-default pay-button">'.esc_html__("Pay","wpqa").' '.$last_payment.' '.$currency_code.'</button>';
				
				$paypal_inputs = '<input type="hidden" name="key" value="'.md5(date("Y-m-d:").rand()).'">
				<input type="hidden" name="rm" value="2">
				<input type="hidden" name="currency_code" value="'.$currency_code.'">
				<input type="hidden" name="business" value="'.wpqa_options('paypal_email').'">
				<input type="hidden" name="return" value="'.esc_url(home_url('/')).'?action=success">
				<input type="hidden" name="cancel_return" value="'.esc_url(home_url('/')).'?action=cancel">
				<input type="hidden" name="notify_url" value="'.esc_url(home_url('/')).'?action=ipn">
				<input type="hidden" name="cpp_header_image" value="'.plugin_dir_url(dirname(__FILE__)).'images/payment.png">
				<input type="hidden" name="image_url" value="'.plugin_dir_url(dirname(__FILE__)).'images/payment.png">
				<input type="hidden" name="cpp_logo_image" value="'.plugin_dir_url(dirname(__FILE__)).'images/payment.png">';

				$output .= '<div class="payment-methods">';
					if (is_array($payment_methodes) && !empty($payment_methodes)) {
						$output .= '<h3 class="post-title-3"><i class="icon-credit-card"></i>'.esc_html__("Select Payment Method","wpqa").'</h3>';
						$k = 0;
						foreach ($payment_methodes as $key => $value) {
							if ($value["value"] !== '0') {
								$k++;
								if ($key == "paypal") {
									$icon = '<i class="icon-paypal"></i>';
								}else if ($key == "stripe") {
									$icon = '<i class="icon-credit-card"></i>';
								}else {
									$icon = '<i class="icon-paypal"></i>';
								}
								$output .= '<a href="payment-'.$key.'" class="'.($k == 1?"button-default-3":"button-default-2").'">'.$icon.$value["sort"].'</a>';
							}
						}
						$k = 0;
						foreach ($payment_methodes as $key => $value) {
							if ($value["value"] !== '0') {
								$k++;
							}
							if ($key == "paypal" && $payment_methodes["paypal"]["value"] == "paypal") {
								$paypal_sandbox = wpqa_options('paypal_sandbox');
								if ($paypal_sandbox == "on") {
									$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
								}else {
									$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
								}
								if ($kind_of_payment == "subscribe") {
									$interval = ($plan_name == "yearly"?"Y":"M");
									//$interval = "D";
								}

								$output .= '<div class="payment-method payment_area'.($k == 1?"":" wpqa_hide").'" data-hide="payment-'.$key.'">
									<form method="post" action="'.$paypal_url.'">';
										if ($kind_of_payment == "subscribe") {
											$output .= '<input type="hidden" name="cmd" value="_xclick-subscriptions">
											<input type="hidden" name="bn" value="PP-SubscriptionsBF:btn_subscribeCC_LG.gif:NonHostedGuest">
											<input type="hidden" name="no_note" value="1">
											<input type="hidden" name="src" value="1">
											<input type="hidden" name="p3" value="'.$interval_count.'">
											<input type="hidden" name="a3" value="'.$last_payment.'">
											<input type="hidden" name="t3" value="'.$interval.'">
											'.$inputs;
										}else {
											$output .= '<input type="hidden" name="no_shipping" value="1">
	    									<input type="hidden" name="cmd" value="_xclick">
											<input type="hidden" name="quantity" value="1">
											<input type="hidden" name="amount" value="'.$last_payment.'">
											'.$inputs;
										}
										$output .= $paypal_inputs.'
									</form>
								</div>';
							}else if ($key == "stripe" && $payment_methodes["stripe"]["value"] == "stripe") {
								if ($kind_of_payment == "subscribe") {
									$interval = ($plan_name == "yearly"?"year":"month");
									//$interval = "day";
								}
								$stripe_address = wpqa_options("stripe_address");
								$stripe_inputs = '';
								if ($stripe_address == "on") {
									$get_countries = apply_filters('wpqa_get_countries',false);
									$line1 = get_the_author_meta("line1",$user_id);
									$postal_code = get_the_author_meta("postal_code",$user_id);
									$country = get_the_author_meta("country",$user_id);
									$city = get_the_author_meta("city",$user_id);
									$state = get_the_author_meta("state",$user_id);
									$stripe_inputs = '
									<div class="row">
										<div class="col col8">
											<p class="line1_field">
												<label for="line1_'.$rand.'">'.esc_html__("Address","wpqa").'</label>
												<input type="text" value="'.esc_attr($line1).'" id="line1_'.$rand.'" name="line1">
												<i class="icon-direction"></i>
											</p>
										</div>
										<div class="col col4">
											<p class="postal_code_field">
												<label for="postal_code_'.$rand.'">'.esc_html__("ZIP","wpqa").'</label>
												<input type="text" value="'.esc_attr($postal_code).'" id="postal_code_'.$rand.'" name="postal_code">
												<i class="icon-box"></i>
											</p>
										</div>
									</div>
									<div class="row">
										<div class="col col4">
											<p class="country_field">
												<label for="country_'.$rand.'">'.esc_html__("Country","wpqa").'</label>
												<span class="styled-select">
													<select name="country" id="country_'.$rand.'">
														<option value="">'.esc_html__( 'Select a country&hellip;', 'wpqa' ).'</option>';
															foreach( $get_countries as $key_country => $value_country ) {
																$stripe_inputs .= '<option value="' . esc_attr( $key_country ) . '"' . selected(esc_attr($country), esc_attr( $key_country ), false ) . '>' . esc_attr( $value_country ) . '</option>';
															}
													$stripe_inputs .= '</select>
												</span>
												<i class="icon-location"></i>
											</p>
										</div>
										<div class="col col4">
											<p class="city_field">
												<label for="city_'.$rand.'">'.esc_html__("City","wpqa").'</label>
												<input type="text" value="'.esc_attr($city).'" id="city_'.$rand.'" name="city">
												<i class="icon-address"></i>
											</p>
										</div>
										<div class="col col4">
											<p class="state_field">
												<label for="state_'.$rand.'">'.esc_html__("State","wpqa").'</label>
												<input type="text" value="'.esc_attr($state).'" id="state_'.$rand.'" name="state">
												<i class="icon-direction"></i>
											</p>
										</div>
									</div>';
								}
								$output .= '<div class="payment-method payment-stripe'.($k == 1?"":" wpqa_hide").'" data-hide="payment-'.$key.'">
									<form action="" method="post" class="wpqa-stripe-payment-form wpqa_form" data-id="payment-stripe'.$rand.'">
										<div class="wpqa_error"></div>
										'.$stripe_inputs.'
										<div class="wpqa-stripe-payment" id="payment-stripe'.$rand.'" data-id="payment-stripe'.$rand.'"></div>
										<div class="form-submit">
											<span class="load_span"><span class="loader_2"></span></span>
											'.$inputs.'
											<input type="hidden" value="'.get_the_author_meta("display_name",$user_id).'" name="name" class="name" required="" autofocus="">
											<input type="hidden" value="'.get_the_author_meta("user_email",$user_id).'" name="email" class="email" required="">
											<input type="hidden" name="payment" value="'.$last_payment.'">
											<input type="hidden" name="action" value="wpqa_stripe_payment">
											<input type="hidden" name="wpqa_stripe_nonce" value="'.wp_create_nonce("wpqa_stripe_nonce").'">
										</div>
									</form>
								</div>';
							}
						}
					}
				$output .= '</div>';
			}else {
				$wpqa_find_coupons = wpqa_find_coupons($coupons,(isset($_POST["coupon_name"])?esc_html($_POST["coupon_name"]):""));
				$output .= '<div class="process_area">
					<form method="post" action="'.$return_url.'">
						<input type="submit" class="button" value="'.esc_attr__("Process","wpqa").'">
						<input type="hidden" name="process" value="'.$item_process.'">';
						if ($item_process == "points" && isset($_POST["package_points"])) {
							$output .= '<input type="hidden" name="package_points" value="'.esc_attr($_POST["package_points"]).'">';
						}else if ($item_process == "subscribe" && isset($_POST["package_subscribe"])) {
							$output .= '<input type="hidden" name="package_subscribe" value="'.esc_attr($_POST["package_subscribe"]).'">';
						}
						if ($wpqa_find_coupons != "" && $active_coupons == "on" && isset($_POST["coupon_name"])) {
							$output .= '<input type="hidden" name="coupon" value="'.esc_attr($_POST["coupon_name"]).'">';
						}
					$output .= '</form>
				</div>';
			}
		}
		return $output;
	}
endif;
/* Payment succeed */
function wpqa_payment_succeeded($user_id,$response) {
	$item_transaction = get_user_meta($user_id,"item_transaction",true);
	if ($item_transaction != $response['item_transaction']) {
		if (isset($response["free"]) || (!isset($response["renew"]) && !isset($response["free"]))) {
			/* Coupon */
			$_coupon = get_user_meta($user_id,$user_id."_coupon",true);
			$_coupon_value = get_user_meta($user_id,$user_id."_coupon_value",true);
		}

		/* Number of my payments */
		$_payments = get_user_meta($user_id,$user_id."_payments",true);
		if ($_payments == "" || !$_payments) {
			$_payments = 0;
		}
		$_payments++;
		update_user_meta($user_id,$user_id."_payments",$_payments);

		add_user_meta($user_id,$user_id."_payments_".$_payments,
			array(
				"date_years" => date_i18n('Y/m/d',current_time('timestamp')),
				"date_hours" => date_i18n('g:i a',current_time('timestamp')),
				"item_number" => $response['item_no'],
				"item_name" => $response['item_name'],
				"item_price" => $response['item_price'],
				"item_currency" => $response['item_currency'],
				"item_transaction" => $response['item_transaction'],
				"payer_email" => $response['payer_email'],
				"first_name" => $response['first_name'],
				"last_name" => $response['last_name'],
				"user_id" => $user_id,
				"sandbox" => $response['sandbox'],
				"time" => current_time('timestamp'),
				"coupon" => (isset($_coupon)?$_coupon:''),
				"coupon_value" => (isset($_coupon_value)?$_coupon_value:''),
				"id" => (isset($response['id'])?$response['id']:''),
				"current_period_start" => (isset($response['current_period_start'])?$response['current_period_start']:''),
				"current_period_end" => (isset($response['current_period_end'])?$response['current_period_end']:''),
				"customer" => (isset($response['customer'])?$response['customer']:''),
				"payment" => (isset($response['payment'])?$response['payment']:''),
			)
		);

		/* New */
		$new_payments = get_option("new_payments");
		if ($new_payments == "" || !$new_payments) {
			$new_payments = 0;
		}
		$new_payments++;
		$update = update_option('new_payments',$new_payments);

		/* Money i'm paid */
		$_all_my_payment = get_user_meta($user_id,$user_id."_all_my_payment_".$response['item_currency'],true);
		if($_all_my_payment == "" || $_all_my_payment == 0 || !$_all_my_payment) {
			$_all_my_payment = 0;
		}
		update_user_meta($user_id,$user_id."_all_my_payment_".$response['item_currency'],$_all_my_payment+$response['item_price']);

		update_user_meta($user_id,$user_id."_last_payments",$response['item_transaction']);

		/* All the payments */
		$payments_option = get_option("payments_option");
		if ($payments_option == "" && !$payments_option) {
			$payments_option = 0;
		}
		$payments_option++;
		update_option("payments_option",$payments_option);

		add_option("payments_".$payments_option,
			array(
				"date_years" => date_i18n('Y/m/d',current_time('timestamp')),
				"date_hours" => date_i18n('g:i a',current_time('timestamp')),
				"item_number" => $response['item_no'],
				"item_name" => $response['item_name'],
				"item_price" => $response['item_price'],
				"item_currency" => $response['item_currency'],
				"item_transaction" => $response['item_transaction'],
				"payer_email" => $response['payer_email'],
				"first_name" => $response['first_name'],
				"last_name" => $response['last_name'],
				"user_id" => $user_id,
				"sandbox" => $response['sandbox'],
				"time" => current_time('timestamp'),
				"coupon" => (isset($_coupon)?$_coupon:''),
				"coupon_value" => (isset($_coupon_value)?$_coupon_value:''),
				"payment_new" => 1,
				"payment_item" => $payments_option,
				"id" => (isset($response['id'])?$response['id']:''),
				"current_period_start" => (isset($response['current_period_start'])?$response['current_period_start']:''),
				"current_period_end" => (isset($response['current_period_end'])?$response['current_period_end']:''),
				"customer" => (isset($response['customer'])?$response['customer']:''),
				"payment" => (isset($response['payment'])?$response['payment']:''),
			)
		);

		if (!isset($response["renew"])) {
			delete_user_meta($user_id,$user_id."_coupon",true);
			delete_user_meta($user_id,$user_id."_coupon_value",true);
		}

		/* All money */
		$all_money = get_option("all_money_".$response['item_currency']);
		if($all_money == "" || !$all_money || $all_money == 0) {
			$all_money = 0;
		}
		update_option("all_money_".$response['item_currency'],$all_money+$response['item_price']);

		/* The currency */
		$the_currency = get_option("the_currency");
		if (is_string($the_currency) || (is_array($the_currency) && empty($the_currency))) {
			delete_option("the_currency");
			add_option("the_currency",array("USD"));
			$the_currency = get_option("the_currency");
		}
		$the_currency = (is_array($the_currency)?$the_currency:array());
		if (!in_array($response['item_currency'],$the_currency)) {
			array_push($the_currency,$response['item_currency']);
		}
		update_option("the_currency",$the_currency);

		$custom = esc_html(isset($response['custom'])?$response['custom']:'');
		$str_replace = str_replace("wpqa_".$response['item_no']."-","",$custom);
		$another_way_payment_filter = apply_filters("wpqa_another_way_payment_filter",true,array("user_id" => $user_id,"item_transaction" => $response['item_transaction'],"item_price" => $response['item_price'],"item_currency" => $response['item_currency'],"payer_email" => $response['payer_email'],"first_name" => $response['first_name'],"last_name" => $response['last_name']));
		if ($another_way_payment_filter == true) {
			if (strpos($custom,'wpqa_pay_answer-') !== false) {
				/* Number allow to add answer */
				$_allow_to_answer = (int)get_user_meta($user_id,$user_id."_allow_to_answer",true);
				if ($_allow_to_answer == "" || $_allow_to_answer < 0) {
					$_allow_to_answer = 0;
				}
				$_allow_to_answer++;
				update_user_meta($user_id,$user_id."_allow_to_answer",$_allow_to_answer);
				
				/* Paid answer */
				update_user_meta($user_id,"_paid_answer","paid");
			}else if (strpos($custom,'wpqa_pay_sticky-') !== false) {
				update_post_meta($str_replace,"sticky",1);
				$sticky_questions = get_option('sticky_questions');
				if (is_array($sticky_questions)) {
					if (!in_array($str_replace,$sticky_questions)) {
						$array_merge = array_merge($sticky_questions,array($str_replace));
						update_option("sticky_questions",$array_merge);
					}
				}else {
					update_option("sticky_questions",array($str_replace));
				}
				$sticky_posts = get_option('sticky_posts');
				if (is_array($sticky_posts)) {
					if (!in_array($str_replace,$sticky_posts)) {
						$array_merge = array_merge($sticky_posts,array($str_replace));
						update_option("sticky_posts",$array_merge);
					}
				}else {
					update_option("sticky_posts",array($str_replace));
				}
				$days_sticky = (int)wpqa_options("days_sticky");
				$days_sticky = ($days_sticky > 0?$days_sticky:7);
				update_post_meta($str_replace,"start_sticky_time",strtotime(date("Y-m-d")));
				update_post_meta($str_replace,"end_sticky_time",strtotime(date("Y-m-d",strtotime(date("Y-m-d")." +$days_sticky days"))));
			}else if (strpos($custom,'wpqa_buy_points-') !== false) {
				wpqa_add_points($user_id,$str_replace,"+","buy_points");
			}else if (strpos($custom,'wpqa_subscribe-') !== false) {
				$subscriptions_group = wpqa_options("subscriptions_group");
				$subscriptions_group = ($subscriptions_group != ""?$subscriptions_group:"author");
				wp_update_user(array('ID' => $user_id,'role' => $subscriptions_group));
				$interval = ($str_replace == "yearly"?"year":"month");
				$interval_count = ($str_replace == "monthly" || $str_replace == "yearly"?1:($str_replace == "3months"?3:6));
				update_user_meta($user_id,"stripe_latest_invoice",$response["item_transaction"]);
				update_user_meta($user_id,"start_subscribe_time",strtotime(date("Y-m-d H:i:s")));
				update_user_meta($user_id,"end_subscribe_time",strtotime(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +$interval_count $interval +7 hour"))));
				update_user_meta($user_id,"subscribe_renew_id",$response["customer"]);
				update_user_meta($user_id,"package_subscribe",$str_replace);
			}else {
				/* Number allow to ask question */
				$_allow_to_ask = (int)get_user_meta($user_id,$user_id."_allow_to_ask",true);
				if ($_allow_to_ask == "" || $_allow_to_ask < 0) {
					$_allow_to_ask = 0;
				}
				$_allow_to_ask++;
				update_user_meta($user_id,$user_id."_allow_to_ask",$_allow_to_ask);
				
				/* Paid question */
				update_user_meta($user_id,"_paid_question","paid");
			}
			
			if ($response['item_no'] == "pay_sticky") {
				update_post_meta($str_replace,'item_transaction_sticky',$response['item_transaction']);
				if ($response['sandbox'] == "on") {
					update_post_meta($str_replace,'paypal_sandbox_sticky','sandbox');
				}
			}else {
				update_user_meta($user_id,"item_transaction",$response['item_transaction']);
				if ($response['sandbox'] == "on") {
					update_user_meta($user_id,"paypal_sandbox","sandbox");
				}
			}

			if (!isset($response["renew"])) {
				if (strpos($custom,'wpqa_pay_answer-') !== false) {
					$payment_success = esc_html__("Thank you for your payment you can now add a new answer","wpqa");
				}else if (strpos($custom,'wpqa_pay_sticky-') !== false) {
					$payment_success = esc_html__("Thank you for your payment, Your question now is sticky","wpqa");
				}else if (strpos($custom,'wpqa_buy_points-') !== false) {
					$payment_success = esc_html__("Thank you for your payment, Your points was added now","wpqa");
				}else if (strpos($custom,'wpqa_subscribe-') !== false) {
					$payment_success = esc_html__("Thank you for your payment, your membership was upgraded","wpqa");
				}else {
					$payment_success = esc_html__("Thank you for your payment you can now add a new question","wpqa");
				}
				
				if ($response['payment'] == "paypal") {
					echo '<div class="alert-message success"><i class="icon-check"></i><p>'.$payment_success.'.</p></div>';
				}
			}
			$send_text = wpqa_send_email(wpqa_options("email_new_payment"),"","","","","",$response['item_price'],$response['item_currency'],$response['payer_email'],$response['first_name'],$response['last_name'],$response['item_transaction'],date('m/d/Y'),date('g:i A'));
			$last_message_email = wpqa_email_code($send_text);
			$email_title = wpqa_options("title_new_payment");
			$email_title = ($email_title != ""?$email_title:esc_html__("Instant Payment Notification - Received Payment","wpqa"));
			$email_template = wpqa_options("email_template");
			$mail_smtp = wpqa_options("mail_smtp");
			$email_template = ($mail_smtp == "on"?wpqa_options("mail_username"):$email_template);
			wpqa_sendEmail($email_template,get_bloginfo('name'),wpqa_options("email_template_to"),$response['first_name'],$email_title,$last_message_email);
			if ($response['payer_email'] != $email_template) {
				$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$user_id);
				$new_payment_mail = get_the_author_meta('new_payment_mail',$user_id);
				if ($unsubscribe_mails != "on" && $new_payment_mail == "on") {
					wpqa_sendEmail($email_template,get_bloginfo('name'),$response['payer_email'],$response['first_name'],$email_title,$last_message_email);
				}
			}
			if (!isset($response["renew"])) {
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.$payment_success.', '.sprintf(esc_html__("Your transaction id %s, Please copy it.","wpqa"),$response['item_transaction']).'</p></div>','wpqa_session');
			}
		}
	}
}
/* Stripe payment */
add_action('wp_ajax_wpqa_stripe_payment','wpqa_stripe_payment');
add_action('wp_ajax_nopriv_wpqa_stripe_payment','wpqa_stripe_payment');
function wpqa_stripe_payment() {
	$result      = array();
	$user_id     = get_current_user_id();
	$custom      = (isset($_POST['custom'])?esc_html($_POST['custom']):"");
	$item_name   = esc_html($_POST['item_name']);
	$item_number = esc_html($_POST['item_number']);
	$name        = esc_html($_POST['name']);
	$payer_email = esc_html($_POST['email']);
	$line1       = (isset($_POST['line1'])?esc_html($_POST['line1']):'');
	$postal_code = (isset($_POST['postal_code'])?esc_html($_POST['postal_code']):'');
	$country     = (isset($_POST['country'])?esc_html($_POST['country']):'');
	$city        = (isset($_POST['city'])?esc_html($_POST['city']):'');
	$state       = (isset($_POST['state'])?esc_html($_POST['state']):'');
	$payment     = (int)$_POST['payment'];
	$item_price  = ($payment*100);
	$currency_code = wpqa_options("currency_code");
	$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");

	if ($line1 != "") {
		update_user_meta($user_id,"line1",$line1);
	}
	if ($postal_code != "") {
		update_user_meta($user_id,"postal_code",$postal_code);
	}
	if ($country != "") {
		update_user_meta($user_id,"country",$country);
	}
	if ($city != "") {
		update_user_meta($user_id,"city",$city);
	}
	if ($state != "") {
		update_user_meta($user_id,"state",$state);
	}
	
	require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
	\Stripe\Stripe::setApiKey(wpqa_options("secret_key"));
	try {
		if (isset($_POST["payment-intent-id"]) && $_POST["payment-intent-id"] != "") {
			$charge = \Stripe\PaymentIntent::retrieve(esc_html($_POST["payment-intent-id"]));
			wpqa_finish_stripe_payment($charge->payment_method,$charge->customer);
			if (isset($charge->status) && ($charge->status == "active" || $charge->status == "paid" || $charge->status == "succeeded")) {
				$success = true;
			}else {
				$result['success'] = 0;
				$result['error']   = esc_html__("Transaction has been failed.","wpqa");
			}
		}else if (isset($_POST["payment-method-id"]) && $_POST["payment-method-id"] != "") {
			if (!isset($_POST['wpqa_stripe_nonce']) || !wp_verify_nonce($_POST['wpqa_stripe_nonce'],'wpqa_stripe_nonce')) {
				$result['success'] = 0;
				$result['error']   = esc_html__("There is an error, Please reload the page and try again.","wpqa");
			}else {
				$payment_method_id = esc_html($_POST["payment-method-id"]);
				$args = array(
					'payment_method'   => $payment_method_id,
					'name'             => $name,
					'email'            => $payer_email,
					'invoice_settings' => array(
						'default_payment_method' => $payment_method_id
					)
				);
				$customer_address = array();
				if ($line1 != "") {
					$customer_address['line1'] = $line1;
				}
				if ($country != "") {
					$customer_address['country'] = $country;
				}
				if ($city != "") {
					$customer_address['city'] = $city;
				}
				if ($state != "") {
					$customer_address['state'] = $state;
				}
				if ($postal_code != "") {
					$customer_address['postal_code'] = $postal_code;
				}
				if (isset($customer_address) && !empty($customer_address)) {
					$args['address'] = $customer_address;
				}
				$customer_description = $item_name;
				if (isset($customer_description) && $customer_description != "") {
					$args['description'] = $customer_description;
				}
				if (isset($customer_metadata)) {
					$args['metadata'] = $customer_metadata;
				}
				$customer = \Stripe\Customer::create($args);
				update_user_meta($user_id,"wpqa_stripe_customer",$customer->id);
				if (strpos($custom,'wpqa_subscribe-') !== false) {
					$product = \Stripe\Product::create([
						'name' => $item_name,
						'type' => 'service',
					]);

					$str_replace = str_replace("wpqa_".$item_number."-","",$custom);

					$interval = ($str_replace == "yearly"?"year":"month");
					$interval_count = ($str_replace == "monthly" || $str_replace == "yearly"?1:($str_replace == "3months"?3:6));
					//$interval = "day";

					$plan = \Stripe\Plan::create([
						'currency'       => $currency_code,
						'interval'       => $interval,
						'interval_count' => $interval_count,
						'product'        => $product->id,
						'nickname'       => $item_name,
						'amount'         => $item_price,
					]);

					$charge = \Stripe\Subscription::create([
						"customer" => $customer->id,
						"items"    => [["plan" => $plan->id]],
						'metadata' => ['order_id' => $item_number],
						'expand'   => ['latest_invoice.payment_intent']
					]);
				}else {
					$wpqa_stripe_customer = get_user_meta($user_id,"wpqa_stripe_customer",true);
					$args = array(
						'amount'              => $item_price,
						'currency'            => $currency_code,
						'confirmation_method' => 'automatic',
						'confirm'             => true,
						'customer'            => $wpqa_stripe_customer,
						'payment_method'      => $payment_method_id,
					);
					if (isset($payment_metadata) && !empty($payment_metadata)) {
						$args['metadata'] = $payment_metadata;
					}
					$payment_description = $item_name;
					if (isset($payment_description) && $payment_description != "") {
						$args['description'] = $payment_description;
					}
					$charge = \Stripe\PaymentIntent::create($args);
				}
				if (isset($charge->status) && (($charge->status == 'requires_action' && $charge->next_action->type == 'use_stripe_sdk') || $charge->status == 'incomplete')) {
					if ($charge->status == 'incomplete' && strpos($custom,'wpqa_subscribe-') !== false && isset($payment_method_id)) {
						wpqa_finish_stripe_payment($payment_method_id,$charge->customer);
					}
					$result['confirm_card']   = 1;
					$result['success']        = 0;
					$result['client_secret']  = (isset($charge->client_secret)?esc_html($charge->client_secret):(isset($charge->latest_invoice->payment_intent->client_secret)?esc_html($charge->latest_invoice->payment_intent->client_secret):""));
					$result['payment_method'] = $charge->id;
				}else if ($charge->status == "active" || $charge->status == "paid" || $charge->status == "succeeded") {
					$success = true;
				}else {
					$result['success'] = 0;
					$result['error']   = esc_html__("Transaction has been failed.","wpqa");
				}
			}
		}else {
			$result['success'] = 0;
			$result['error']   = esc_html__("Transaction has been failed.","wpqa");
		}
		if (isset($success) && $success == true) {
			$response = $charge->jsonSerialize();
			$str_replace = str_replace("wpqa_".$item_number."-","",$custom);
			if (strpos($custom,'wpqa_ask_question-') !== false) {
				if (is_numeric($str_replace)) {
					$redirect_to = esc_url(wpqa_add_question_permalink("user",$str_replace));
				}else {
					$redirect_to = esc_url(wpqa_add_question_permalink());
				}
			}else if (strpos($custom,'wpqa_pay_answer-') !== false) {
				$redirect_to = esc_url(get_the_permalink($str_replace));
			}else if (strpos($custom,'wpqa_pay_sticky-') !== false) {
				$redirect_to = esc_url(get_the_permalink($str_replace));
			}else if (strpos($custom,'wpqa_buy_points-') !== false) {
				$redirect_to = esc_url(wpqa_get_profile_permalink($user_id,"points"));
			}else if (strpos($custom,'wpqa_subscribe-') !== false) {
				$redirect_to = esc_url(wpqa_profile_url($user_id));
			}else {
				$redirect_to = esc_url(home_url('/'));
			}
			$result['success']  = 1;
			$result['redirect'] = $redirect_to;
			$array = array (
				'item_no' => $item_number,
				'item_name' => $item_name,
				'item_price' => $payment,
				'item_currency' => $currency_code,
				'item_transaction' => (isset($response['charges']['data'][0]['balance_transaction'])?$response['charges']['data'][0]['balance_transaction']:(isset($response['latest_invoice']['payment_intent']['charges']['data'][0]['balance_transaction'])?$response['latest_invoice']['payment_intent']['charges']['data'][0]['balance_transaction']:"")),
				'payer_email' => $payer_email,
				'first_name' => get_the_author_meta("first_name",$user_id),
				'last_name' => get_the_author_meta("last_name",$user_id),
				'custom' => $custom,
				'sandbox' => '',
				'payment' => 'Stripe',
				"id" => $response['id'],
				"current_period_start" => (isset($response['current_period_start'])?$response['current_period_start']:''),
				"current_period_end" => (isset($response['current_period_end'])?$response['current_period_end']:''),
				"customer" => $response['customer'],
			);
			wpqa_payment_succeeded($user_id,$array);
		}else if (!isset($result['confirm_card'])) {
			$result['success'] = 0;
			$result['error']   = esc_html__("Transaction has been failed.","wpqa");
		}
	}catch ( \Stripe\Exception\CardException $e ) {
		$result['success'] = 0;
		$result['error']   = $e->getError()->message;
	}catch ( Exception $e ) {
		$result['success'] = 0;
		$result['error']   = $e->getMessage();
	}
	echo json_encode(apply_filters('wpqa_stripe_payment',$result));
	die();
}
/* Finish stripe payment */
function wpqa_finish_stripe_payment($payment_method_id,$get_the_customer_id) {
	require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
	\Stripe\Stripe::setApiKey(wpqa_options("secret_key"));
	$payment_method = \Stripe\PaymentMethod::retrieve($payment_method_id);
	$payment_method->attach(['customer' => $get_the_customer_id]);
	$update_customer = \Stripe\Customer::update(
		$get_the_customer_id,[
			'invoice_settings' => [
				'default_payment_method' => $payment_method_id,
			],
		]
	);
}
/* Do payments */
add_action("wpqa_do_payments","wpqa_do_payments");
if (!function_exists('wpqa_do_payments')):
	function wpqa_do_payments() {
		$pay_ask = wpqa_options('pay_ask');
		$pay_to_sticky = wpqa_options('pay_to_sticky');
		$buy_points_payment = wpqa_options('buy_points_payment');
		$pay_to_answer = wpqa_options('pay_to_answer');
		$pay_to_anything = apply_filters("wpqa_filter_pay_to_anything",false);
		if ($pay_ask == "on" || $pay_to_sticky == "on" || $buy_points_payment == "on" || $pay_to_answer == "on" || $pay_to_anything == true) {
			$paypal_sandbox = wpqa_options('paypal_sandbox');
			if ($paypal_sandbox == "on") {
				$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			}else {
				$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
			}
			$user_id   = get_current_user_id();
			switch ((isset($_REQUEST['action'])?$_REQUEST['action']:"")) {
				case 'success':
					if ((isset($_REQUEST['txn_id']) && $_REQUEST['txn_id'] != "") || isset($_REQUEST['tx']) && $_REQUEST['tx'] != "") {
						$data = wp_remote_post($paypal_url.'?cmd=_notify-synch&tx='.(isset($_REQUEST['tx'])?$_REQUEST['tx']:(isset($_REQUEST['txn_id'])?$_REQUEST['txn_id']:'')).'&at='.wpqa_options("identity_token"));
						if (!is_wp_error($data)) {
							$data = $data['body'];
							$response = substr($data, 7);
							$response = urldecode($response);
							
							preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
							$response = array_combine($m[1], $m[2]);
							
							if (isset($response['charset']) && strtoupper($response['charset']) !== 'UTF-8') {
								foreach ($response as $key => &$value) {
									$value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
								}
								$response['charset_original'] = $response['charset'];
								$response['charset'] = 'UTF-8';
							}
							
							ksort($response);
						}else {
							wp_safe_redirect(esc_url(home_url('/')));
							die();
						}
						
						$item_transaction = (isset($response['txn_id'])?esc_attr($response['txn_id']):"");
						$last_payments    = get_user_meta($user_id,$user_id."_last_payments",true);
						if ($item_transaction != "") {
							if (isset($last_payments) && $last_payments == $item_transaction) {
								wp_safe_redirect(esc_url(home_url('/')));
								die();
							}else {
								$item_no       = (isset($response['item_number'])?esc_attr($response['item_number']):"");
								$item_price    = (isset($response['mc_gross'])?esc_attr($response['mc_gross']):"");
								$item_currency = (isset($response['mc_currency'])?esc_attr($response['mc_currency']):"");
								$payer_email   = (isset($response['payer_email'])?esc_attr($response['payer_email']):"");
								$first_name    = (isset($response['first_name'])?esc_attr($response['first_name']):"");
								$last_name     = (isset($response['last_name'])?esc_attr($response['last_name']):"");
								$item_name     = (isset($response['item_name'])?esc_attr($response['item_name']):"");
								if (isset($response["subscr_id"])) {
									update_user_meta($user_id,"subscr_id",$response["subscr_id"]);
								}
								$array = array (
									'item_no' => $item_no,
									'item_name' => $item_name,
									'item_price' => $item_price,
									'item_currency' => $item_currency,
									'item_transaction' => $item_transaction,
									'payer_email' => $payer_email,
									'first_name' => $first_name,
									'last_name' => $last_name,
									'sandbox' => ($paypal_sandbox == 'on'?'sandbox':''),
									'payment' => 'PayPal',
									'custom' => $response['custom'],
									"customer" => (isset($response['subscr_id'])?$response['subscr_id']:''),
								);
								wpqa_payment_succeeded($user_id,$array);
								$custom = $response['custom'];
								$str_replace = str_replace("wpqa_".$item_no."-","",$custom);
								if (strpos($custom,'wpqa_ask_question-') !== false) {
									if (is_numeric($str_replace)) {
										$redirect_to = esc_url(wpqa_add_question_permalink("user",$str_replace));
									}else {
										$redirect_to = esc_url(wpqa_add_question_permalink());
									}
								}else if (strpos($custom,'wpqa_pay_answer-') !== false) {
									$redirect_to = esc_url(get_the_permalink($str_replace));
								}else if (strpos($custom,'wpqa_pay_sticky-') !== false) {
									$redirect_to = esc_url(get_the_permalink($str_replace));
								}else if (strpos($custom,'wpqa_buy_points-') !== false) {
									$redirect_to = esc_url(wpqa_get_profile_permalink($user_id,"points"));
								}else if (strpos($custom,'wpqa_subscribe-') !== false) {
									$redirect_to = esc_url(wpqa_profile_url($user_id));
								}else {
									$redirect_to = esc_url(home_url('/'));
								}
								wp_safe_redirect($redirect_to);
								die();
							}
						}else {
							echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("The payment was failed!","wpqa").'</p></div>';
						}
					}else {
						wp_safe_redirect(esc_url(home_url('/')));
						die();
					}
				break;
				case 'cancel':
					echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("The payment was canceled!","wpqa").'</p></div>';
				break;
				case 'ipn':
					$raw_post_data = file_get_contents('php://input');
					$raw_post_array = explode('&', $raw_post_data);
					$myPost = array();
					foreach ($raw_post_array as $keyval) {
						$keyval = explode ('=', $keyval);
						if (count($keyval) == 2)
							$myPost[$keyval[0]] = urldecode($keyval[1]);
					}

					$req = 'cmd=_notify-validate';
					if (function_exists('get_magic_quotes_gpc')) {
						$get_magic_quotes_exists = true;
					}
					foreach ($myPost as $key => $value) {
						if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
							$value = urlencode(stripslashes($value));
						}else {
							$value = urlencode($value);
						}
						$req .= "&$key=$value";
					}

					$paypal_sandbox = wpqa_options('paypal_sandbox');
					if ($paypal_sandbox == "on") {
						$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
					}else {
						$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
					}
					$ch = curl_init($paypal_url);
					if ($ch == FALSE) {
						return FALSE;
					}
					curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
					curl_setopt($ch, CURLOPT_SSLVERSION, 6);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: company-name'));
					$res = curl_exec($ch);

					$tokens = explode("\r\n\r\n", trim($res));
					$res = trim(end($tokens));
					if (strcmp($res, "VERIFIED") == 0 || strcasecmp($res, "VERIFIED") == 0) {
						if (isset($_POST['payment_status']) && ($_POST['payment_status'] == "Completed" || $_POST['payment_status'] == "Processed" || $_POST['payment_status'] == "Created" || $_POST['payment_status'] == "Pending")) {
							$user = reset(
								get_users(
									array(
										'meta_key'    => "subscribe_renew_id",
										'meta_value'  => $_POST['subscr_id'],
										'number'      => 1,
										'count_total' => false
									)
								)
							);
							$user_id = (isset($user->ID)?$user->ID:0);
							if ($user_id > 0) {
								$item_transaction = (isset($_POST['txn_id'])?esc_attr($_POST['txn_id']):"");
								if ($item_transaction != "") {
									$item_no       = (isset($_POST['item_number'])?esc_attr($_POST['item_number']):"");
									$item_price    = (isset($_POST['mc_gross'])?esc_attr($_POST['mc_gross']):"");
									$item_currency = (isset($_POST['mc_currency'])?esc_attr($_POST['mc_currency']):"");
									$payer_email   = (isset($_POST['payer_email'])?esc_attr($_POST['payer_email']):"");
									$first_name    = (isset($_POST['first_name'])?esc_attr($_POST['first_name']):"");
									$last_name     = (isset($_POST['last_name'])?esc_attr($_POST['last_name']):"");
									$item_name     = (isset($_POST['item_name'])?esc_attr($_POST['item_name']):"");
									$custom        = (isset($_POST['custom'])?esc_attr($_POST['custom']):"");
									$array = array (
										'item_no' => $item_no,
										'item_name' => $item_name." ".esc_html__("(Renew)","WPQA"),
										'item_price' => $item_price,
										'item_currency' => $item_currency,
										'item_transaction' => $item_transaction,
										'payer_email' => $payer_email,
										'first_name' => $first_name,
										'last_name' => $last_name,
										'sandbox' => ($paypal_sandbox == 'on'?'sandbox':''),
										'payment' => 'PayPal',
										'renew' => 'subscribe',
										"customer" => $_POST['subscr_id'],
										'custom' => $custom,
									);
									wpqa_payment_succeeded($user_id,$array);
								}
							}
						}
					}
				break;
			}
		}
	}
endif;
/* Coupon valid */
if (!function_exists('wpqa_coupon_valid')) :
	function wpqa_coupon_valid ($coupons,$coupon_name,$coupons_not_exist,$pay_ask_payment,$what_return = '') {
		if (isset($coupons) && is_array($coupons)) {
			foreach ($coupons as $coupons_k => $coupons_v) {
				if (is_array($coupons_v) && in_array($coupon_name,$coupons_v)) {
					if ($what_return == "coupons_not_exist") {
						return "yes";
					}
					if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] !="" && $coupons_v["coupon_date"] < date_i18n('m/d/Y',current_time('timestamp'))) {
						return '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon has expired.","wpqa").'</p></div>';
					}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent") {
						if ((int)$coupons_v["coupon_amount"] > 100) {
							return '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon is not valid.","wpqa").'</p></div>';
						}else {
							$the_discount = ($pay_ask_payment*$coupons_v["coupon_amount"])/100;
							$last_payment = $pay_ask_payment-$the_discount;
							if ($what_return == "last_payment") {
								return $last_payment;
							}
						}
					}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount") {
						if ((int)$coupons_v["coupon_amount"] > $pay_ask_payment) {
							return '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon is not valid.","wpqa").'</p></div>';
						}else {
							$last_payment = $pay_ask_payment-$coupons_v["coupon_amount"];
							if ($what_return == "last_payment") {
								return $last_payment;
							}
						}
					}else {
						return '<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Coupon code applied successfully.","wpqa").'</p></div>';
					}
				}
			}
		}
	}
endif;
/* Find coupons */
if (!function_exists('wpqa_find_coupons')) :
	function wpqa_find_coupons($coupons,$coupon_name) {
		if (isset($coupons) && is_array($coupons)) {
			foreach ($coupons as $coupons_k => $coupons_v) {
				if (is_array($coupons_v) && ((isset($coupons_v["coupon_name"]) && $coupons_v["coupon_name"] == $coupon_name)) || in_array($coupon_name,$coupons_v)) {
					return $coupons_k;
				}
			}
		}
		return false;
	}
endif;
/* Payments */
if (!function_exists('wpqa_add_admin_page_payments')) :
	function wpqa_add_admin_page_payments() {
		$pay_ask = wpqa_options('pay_ask');
		$payment_type_ask = wpqa_options('payment_type_ask');
		$pay_to_sticky = wpqa_options('pay_to_sticky');
		$payment_type_sticky = wpqa_options('payment_type_sticky');
		$subscriptions_payment = wpqa_options('subscriptions_payment');
		$buy_points_payment = wpqa_options('buy_points_payment');
		$pay_to_answer = wpqa_options('pay_to_answer');
		$payment_type_answer = wpqa_options('payment_type_answer');
		$pay_to_anything = apply_filters("wpqa_filter_pay_to_anything",false);
		if (($pay_ask == "on" && $payment_type_ask != "points") || ($pay_to_sticky == "on" && $payment_type_sticky != "points") || $subscriptions_payment == "on" || $buy_points_payment == "on" || ($pay_to_answer == "on" && $payment_type_answer != "points") || $pay_to_anything == true) {
			$new_payments = (int)get_option("new_payments");
			add_menu_page(esc_html__('Payments','wpqa'),esc_html__('Payments','wpqa').' <span class="count_report_new awaiting-mod count-'.$new_payments.'"><span class="count_lasts">'.$new_payments.'</span></span>','manage_options','wpqa_payments','wpqa_payments','dashicons-cart');
		}
	}
endif;
add_action('admin_menu','wpqa_add_admin_page_payments');
if (!function_exists('wpqa_payments')) :
	function wpqa_payments () {?>
		<div class="wrap">
			<h1><?php esc_html_e("Payments","wpqa")?></h1>
			<?php $the_currency = get_option("the_currency");
			if (isset($the_currency) && is_array($the_currency)) {
				echo "<br>".esc_html__("All my money","wpqa")."<br>";
				foreach ($the_currency as $key => $currency) {
					if (isset($currency) && $currency != "") {
						$all_money = get_option("all_money_".$currency);
						echo "<br>".(isset($all_money) && $all_money != ""?$all_money:0)." ".$currency."<br>";
						//$_all_my_payment = get_user_meta(get_current_user_id(),get_current_user_id()."_all_my_payment_".$currency,true);
						//echo " all my payment ".(isset($_all_my_payment) && $_all_my_payment != ""?$_all_my_payment:0)." ".$currency."<br>";
					}
				}
				echo "<br>";
			}
			do_action("wpqa_action_after_all_money")?>
			
			<div class="payments-table-items">
				<?php $_payments = get_option("payments_option");
				$rows_per_page = get_option("posts_per_page");
				for ($payments = 1; $payments <= $_payments; $payments++) {
					$payment_one[] = get_option("payments_".$payments);
				}
				if (isset($payment_one)) {
					update_option("new_payments",0);
					$payment = array_reverse($payment_one);
					$paged = (isset($_GET["paged"])?(int)$_GET["paged"]:1);
					$current = max(1,$paged);
					$pagination_args = array(
						'base'      => esc_url_raw(add_query_arg('paged','%#%')),
						'total'     => ceil(sizeof($payment)/$rows_per_page),
						'current'   => $current,
						'show_all'  => false,
						'prev_text' => '&laquo; Previous',
						'next_text' => 'Next &raquo;',
					);
						
					$start = ($current - 1) * $rows_per_page;
					$end = $start + $rows_per_page;
					$end = (sizeof($payment) < $end) ? sizeof($payment) : $end;
				}
				
				if (isset($payment_one) && is_array($payment_one) && isset($pagination_args["total"]) && $pagination_args["total"] > 1) {?>
					<div class="tablenav top">
						<div class="tablenav-pages">
							<span class="displaying-num"><?php echo count($payment_one)?> <?php esc_html_e("Payments","wpqa")?></span>
							<span class="pagination-links">
								<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
							</span>
						</div>
						<br class="clear">
					</div>
				<?php }else {
					echo "<br>";
				}?>
				
				<table class="wp-list-table widefat fixed striped ">
					<thead>
						<tr>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Price","wpqa")?> - (<?php esc_html_e("coupon","wpqa")?>)</span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Author","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Item","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Date","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Transaction","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Payer email","wpqa")?> - (<?php esc_html_e("sandbox","wpqa")?>)</span></th>
						</tr>
					</thead>
					
					<tbody class="payment-table">
						<?php if (isset($payment_one) && is_array($payment_one) && !empty($payment_one)) {
							for ($i = $start; $i < $end; ++$i) {
								$payment_result = $payment[$i];
								$date_years = (isset($payment_result["date_years"]) && $payment_result["date_years"] != ""?$payment_result["date_years"]:"");
								$date_hours = (isset($payment_result["date_hours"]) && $payment_result["date_hours"] != ""?$payment_result["date_hours"]:"");
								$item_number = (isset($payment_result["item_number"]) && $payment_result["item_number"] != ""?$payment_result["item_number"]:"");
								$item_price = (isset($payment_result["item_price"]) && $payment_result["item_price"] != ""?$payment_result["item_price"]:"");
								$item_currency = (isset($payment_result["item_currency"]) && $payment_result["item_currency"] != ""?$payment_result["item_currency"]:"");
								$item_transaction = (isset($payment_result["item_transaction"]) && $payment_result["item_transaction"] != ""?$payment_result["item_transaction"]:"");
								$payment_method = (isset($payment_result["payment"]) && $payment_result["payment"] != ""?$payment_result["payment"]:"");
								$payer_email = (isset($payment_result["payer_email"]) && $payment_result["payer_email"] != ""?$payment_result["payer_email"]:"");
								$first_name = (isset($payment_result["first_name"]) && $payment_result["first_name"] != ""?$payment_result["first_name"]:"");
								$last_name = (isset($payment_result["last_name"]) && $payment_result["last_name"] != ""?$payment_result["last_name"]:"");
								$user_id = (isset($payment_result["user_id"]) && $payment_result["user_id"] != ""?$payment_result["user_id"]:"");
								$sandbox = (isset($payment_result["sandbox"]) && $payment_result["sandbox"] != ""?$payment_result["sandbox"]:"");
								$time = (isset($payment_result["time"]) && $payment_result["time"] != ""?human_time_diff($payment_result["time"],current_time('timestamp'))." ago":"");
								$coupon = (isset($payment_result["coupon"]) && $payment_result["coupon"] != ""?$payment_result["coupon"]:"");
								$coupon_value = (isset($payment_result["coupon_value"]) && $payment_result["coupon_value"] != ""?$payment_result["coupon_value"]:"");
								$item_name = (isset($payment_result["item_name"]) && $payment_result["item_name"] != ""?$payment_result["item_name"]:"---");?>
								
								<tr<?php echo (isset($payment_result["payment_new"]) && $payment_result["payment_new"] == 1?' class="unapproved"':'')?>>
									<td><?php echo ($item_price > 0?esc_html($item_price)." ".$item_currency:esc_html__("Free","wpqa")).(isset($coupon) && $coupon != ""?" - (".$coupon.")":"")?></td>
									<td><a href="<?php echo wpqa_profile_url((int)$user_id);?>"><strong><?php echo get_the_author_meta("display_name",(int)$user_id)?></strong></a></td>
									<td><?php echo esc_html($item_name)?></td>
									<td><?php echo esc_html($time)?></td>
									<td><?php echo ($item_transaction != ""?esc_html($item_transaction):"---").($payment_method != ""?" - ".esc_html($payment_method):"")?></td>
									<td><?php echo ($payer_email != ""?esc_html($payer_email).(isset($sandbox) && $sandbox != ""?" - (".$sandbox.")":""):"---")?></td>
								</tr>
								<?php if (isset($payment_result["payment_new"]) && $payment_result["payment_new"] == 1 && isset($payment_result["payment_item"])) {
									$payment_result["payment_new"] = 0;
									update_option("payments_".$payment_result["payment_item"],$payment_result);
								}
							}
						}else {
							echo '<tr class="no-items"><td class="colspanchange" colspan="6">There are no payments yet.</td></tr>';
						}?>
					</tbody>
				
					<tfoot>
						<tr>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Price","wpqa")?> - (<?php esc_html_e("coupon","wpqa")?>)</span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Author","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Item","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Date","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Transaction","wpqa")?></span></th>
							<th class="manage-column" scope="col"><span><?php esc_html_e("Payer email","wpqa")?> - (<?php esc_html_e("sandbox","wpqa")?>)</span></th>
						</tr>
					</tfoot>
				</table>
					
				<?php if (isset($payment_one) && is_array($payment_one) && isset($pagination_args["total"]) && $pagination_args["total"] > 1) {?>
					<div class="tablenav bottom">
						<div class="tablenav-pages">
							<span class="displaying-num"><?php echo count($payment_one)?> <?php esc_html_e("Payments","wpqa")?></span>
							<span class="pagination-links">
								<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
							</span>
						</div>
						<br class="clear">
					</div>
				<?php }?>
			</div>
		</div>
	<?php }
endif;
?>