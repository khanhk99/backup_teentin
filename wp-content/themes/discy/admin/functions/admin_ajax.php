<?php /* Update options */
function discy_update_options() {
	do_action("discy_update_options",$_POST);
	$setting_options = $_POST[discy_options];
	unset($setting_options['export_setting']);
	if (isset($setting_options['import_setting']) && $setting_options['import_setting'] != "") {
		$data = json_decode(stripslashes($setting_options['import_setting']),true);
		$array_options = array(discy_options,"sidebars");
		foreach ($array_options as $option) {
			if (isset($data[$option])) {
				update_option($option,$data[$option]);
			}else{
				delete_option($option);
			}
		}
		echo 2;
		update_option("FlushRewriteRules",true);
		die();
	}else {
		/* Roles */
		global $wp_roles;
		if (isset($setting_options["roles"])) {$k = 0;
			foreach ($setting_options["roles"] as $value_roles) {$k++;
				$is_group = get_role($value_roles["id"]);
				if (isset($value_roles["new"]) && $value_roles["new"] == "new") {
					if (!isset($is_group)) {
						$is_group = add_role($value_roles["id"],ucfirst($value_roles["group"]),array('read' => false));
						$is_group->add_cap('new');
					}
				}
				if (isset($is_group)) {
					$roles_array = array("ask_question","show_question","add_answer","show_answer","add_post","add_category","send_message","upload_files","approve_question","approve_answer","approve_post","approve_comment","approve_question_media","approve_answer_media","without_ads");
					if (isset($roles_array) && !empty($roles_array)) {
						foreach ($roles_array as $roles_key) {
							if (isset($value_roles[$roles_key]) && $value_roles[$roles_key] == "on") {
								$is_group->add_cap($roles_key);
							}else {
								$is_group->remove_cap($roles_key);
							}
						}
					}
				}
			}
		}
		if (isset($setting_options["schedules_time_hour"])) {
			$schedules_time_hour = get_option("schedules_time_hour");
			if ($setting_options["schedules_time_hour"] != $schedules_time_hour) {
				update_option("schedules_time_hour",$setting_options["schedules_time_hour"]);
				delete_option("discy_schedules_time");
			}
		}
		if (isset($setting_options["schedules_time_day"])) {
			$schedules_time_day = get_option("schedules_time_day");
			if ($setting_options["schedules_time_day"] != $schedules_time_day) {
				update_option("schedules_time_day",$setting_options["schedules_time_day"]);
				delete_option("discy_schedules_time");
			}
		}
		/* Save */
		update_option(discy_options,$setting_options);
	}
	update_option("FlushRewriteRules",true);
	die();
}
add_action( 'wp_ajax_discy_update_options', 'discy_update_options' );
/* Reset options */
function discy_reset_options() {
	$options = discy_admin_options();
	foreach ($options as $option) {
		if (isset($option['id']) && isset($option['std'])) {
			$option_res[$option['id']] = $option['std'];
		}
	}
	update_option(discy_options,$option_res);
	update_option("FlushRewriteRules",true);
	die();
}
add_action('wp_ajax_discy_reset_options','discy_reset_options');
/* Send the custom mail */
add_action('wp_ajax_discy_send_custom_mail','discy_send_custom_mail');
function discy_send_custom_mail() {
	if (class_exists("WPQA")) {
		$mail_groups_users = discy_options("mail_groups_users");
		$custom_mail_groups = discy_options("custom_mail_groups");
		$mail_specific_users = discy_options("mail_specific_users");
		$specific_users = explode(",",$mail_specific_users);
		$groups_users = ($mail_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($custom_mail_groups) && is_array($custom_mail_groups)?$custom_mail_groups:array()));
		$role_include = ($mail_groups_users == "users"?"include":"role__in");
		$email_template = discy_options("email_template");
		$mail_smtp = discy_options("mail_smtp");
		$email_template = ($mail_smtp == "on"?discy_options("mail_username"):$email_template);
		$email_title = discy_options("title_custom_mail");
		$email_title = ($email_title != ""?$email_title:esc_html__("Welcome","discy"));
		$users = get_users(array("meta_query" => array('relation' => 'OR',array("key" => "unsubscribe_mails","compare" => "NOT EXISTS"),array("key" => "unsubscribe_mails","compare" => "!=","value" => "on")),$role_include => $groups_users,"fields" => array("ID","user_email","display_name")));
		if (isset($users) && is_array($users) && !empty($users)) {
			foreach ($users as $key => $value) {
				$user_id = $value->ID;
				$send_text = wpqa_send_email(discy_options("email_custom_mail"),"","","","","","","","","","","","","","","","",$user_id);
				$last_message_email = wpqa_email_code($send_text,"email_custom_mail");
				wpqa_sendEmail($email_template,get_bloginfo('name'),esc_html($value->user_email),esc_html($value->display_name),$email_title,$last_message_email);
			}
		}
	}
	die();
}
/* Send the custom notification */
add_action('wp_ajax_discy_send_custom_notification','discy_send_custom_notification');
function discy_send_custom_notification() {
	if (class_exists("WPQA")) {
		$active_notifications = discy_options("active_notifications");
		$notification_groups_users = discy_options("notification_groups_users");
		$custom_notification_groups = discy_options("custom_notification_groups");
		$notification_specific_users = discy_options("notification_specific_users");
		$specific_users = explode(",",$notification_specific_users);
		$groups_users = ($notification_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($custom_notification_groups) && is_array($custom_notification_groups)?$custom_notification_groups:array()));
		$role_include = ($notification_groups_users == "users"?"include":"role__in");
		$custom_notification = discy_options("custom_notification");
		if ($active_notifications == "on" && $custom_notification != "") {
			$user_id = get_current_user_id();
			$users = get_users(array($role_include => $groups_users,"fields" => array("ID")));
			if (isset($users) && is_array($users) && !empty($users)) {
				foreach ($users as $key => $value) {
					if ($user_id != $value->ID) {
						wpqa_notifications_activities($value->ID,"","","","",$custom_notification,"notifications");
					}
				}
			}
		}
	}
	die();
}
/* Send the custom message */
add_action('wp_ajax_discy_send_custom_message','discy_send_custom_message');
function discy_send_custom_message() {
	if (class_exists("WPQA")) {
		$active_message = discy_options("active_message");
		$message_groups_users = discy_options("message_groups_users");
		$custom_message_groups = discy_options("custom_message_groups");
		$message_specific_users = discy_options("message_specific_users");
		$specific_users = explode(",",$message_specific_users);
		$groups_users = ($message_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($custom_message_groups) && is_array($custom_message_groups)?$custom_message_groups:array()));
		$role_include = ($message_groups_users == "users"?"include":"role__in");
		$title_custom_message = discy_options("title_custom_message");
		$custom_message = discy_options("custom_message");
		if ($active_message == "on" && ($title_custom_message != "" || $custom_message != "")) {
			$user_id = get_current_user_id();
			$users = get_users(array($role_include => $groups_users,"fields" => array("ID","user_email","display_name")));
			if (isset($users) && is_array($users) && !empty($users)) {
				$users_array = array();
				$data = array(
					'post_content' => $custom_message,
					'post_title'   => sanitize_text_field($title_custom_message),
					'post_status'  => 'publish',
					'post_author'  => $user_id,
					'post_type'	   => 'message',
				);
				$post_id = wp_insert_post($data);
				foreach ($users as $key => $value) {
					if ($user_id != $value->ID) {
						$users_array[] = $value->ID;
						update_post_meta($post_id,'message_user_'.$value->ID,$value->ID);
						update_post_meta($post_id,'message_not_new_'.$value->ID,"no");
						$send_text = wpqa_send_email(wpqa_options("email_new_message"),$value->ID,$post_id,"","","","","","","","","","","","","",$user_id,$value->ID);
						$last_message_email = wpqa_email_code($send_text);
						$email_title = wpqa_options("title_new_message");
						$email_title = ($email_title != ""?$email_title:esc_html__("New message","discy"));
						$email_template = wpqa_options("email_template");
						$mail_smtp = wpqa_options("mail_smtp");
						$email_template = ($mail_smtp == "on"?wpqa_options("mail_username"):$email_template);
						$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$value->ID);
						$send_message_mail = get_the_author_meta('send_message_mail',$value->ID);
						if ($unsubscribe_mails != "on" && $send_message_mail == "on") {
							wpqa_sendEmail($email_template,get_bloginfo('name'),$value->user_email,$value->display_name,$email_title,$last_message_email);
						}
					}
				}
				if ($message_groups_users == "groups") {
					update_post_meta($post_id,'message_groups_array',$groups_users);
				}
				update_post_meta($post_id,'message_user_array',$users_array);
			}
		}
	}
	die();
}
/* Send the popup notification */
add_action('wp_ajax_discy_send_popup_notification','discy_send_popup_notification');
function discy_send_popup_notification() {
	if (class_exists("WPQA")) {
		$post_id = (int)$_POST["post_id"];
		$custom_popup_notification = ($post_id > 0?discy_post_meta("custom_popup_notification",$post_id):"");
		if ($custom_popup_notification == "on") {
			$popup_notification_text = discy_post_meta("popup_notification_text",$post_id);
		}else {
			$popup_notification_text = discy_options("popup_notification_text");
		}
		if ($popup_notification_text != "") {
			if ($custom_popup_notification == "on") {
				$popup_notification_time = discy_post_meta("popup_notification_time",$post_id);
				$popup_notification_groups_users = discy_post_meta("popup_notification_groups_users",$post_id);
				$popup_notification_groups = discy_post_meta("popup_notification_groups",$post_id);
				$popup_notification_specific_users = discy_post_meta("popup_notification_specific_users",$post_id);
				$popup_notification_button_text = discy_post_meta("popup_notification_button_text",$post_id);
				$popup_notification_button_url = discy_post_meta("popup_notification_button_url",$post_id);
				$popup_notification_button_target = discy_post_meta("popup_notification_button_target",$post_id);
				$popup_notification_home_pages = "";
				$popup_notification_pages = "";
			}else {
				$popup_notification_time = discy_options("popup_notification_time");
				$popup_notification_home_pages = discy_options("popup_notification_home_pages");
				$popup_notification_pages = discy_options("popup_notification_pages");
				$popup_notification_groups_users = discy_options("popup_notification_groups_users");
				$popup_notification_groups = discy_options("popup_notification_groups");
				$popup_notification_specific_users = discy_options("popup_notification_specific_users");
				$popup_notification_button_text = discy_options("popup_notification_button_text");
				$popup_notification_button_url = discy_options("popup_notification_button_url");
				$popup_notification_button_target = discy_options("popup_notification_button_target");
			}
			$specific_users = explode(",",$popup_notification_specific_users);
			$groups_users = ($popup_notification_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($popup_notification_groups) && is_array($popup_notification_groups)?$popup_notification_groups:array()));
			$role_include = ($popup_notification_groups_users == "users"?"include":"role__in");
			$user_id = get_current_user_id();
			$users = get_users(array($role_include => $groups_users,"fields" => array("ID")));
			if (isset($users) && is_array($users) && !empty($users)) {
				foreach ($users as $key => $value) {
					if ($user_id != $value->ID) {
						wpqa_notifications_activities($value->ID,"","","","",$popup_notification_text,"pop_notification","","","",
							array(
								"custom_notification" => $custom_popup_notification,
								"post_id"             => $post_id,
								"date_years"          => date_i18n('Y/m/d',current_time('timestamp')),
								"date_hours"          => date_i18n('g:i a',current_time('timestamp')),
								"time"                => current_time('timestamp'),
								"user_id"             => $value->ID,
								"text"                => $popup_notification_text,
								"notification_time"   => $popup_notification_time,
								"home_pages"          => $popup_notification_home_pages,
								"pages"               => $popup_notification_pages,
								"button_text"         => $popup_notification_button_text,
								"button_url"          => $popup_notification_button_url,
								"button_target"       => $popup_notification_button_target,
							)
						);
					}
				}
			}
		}
	}
	die();
}
/* Delete role */
function discy_delete_role() {
	$roles_val = $_POST["roles_val"];
	if (get_role($roles_val)) {
		remove_role($roles_val);
	}
}
add_action( 'wp_ajax_discy_delete_role', 'discy_delete_role' );
/* Admin live search */
function discy_admin_live_search() {
	$search_value = esc_attr($_POST['search_value']);
	if ($search_value != "") {
		$search_value_ucfirst = ucfirst(esc_attr($_POST['search_value']));
		$discy_admin_options = discy_admin_options();
		$k = 0;
		if (isset($discy_admin_options) && is_array($discy_admin_options)) {?>
			<ul>
				<?php foreach ($discy_admin_options as $key => $value) {
					if (isset($value["type"]) && $value["type"] != "content" && $value["type"] != "info" && $value["type"] != "heading" && $value["type"] != "heading-2" && $value['type'] != "heading-3" && ((isset($value["name"]) && $value["name"] != "" && (strpos($value["name"],$search_value) !== false || strpos($value["name"],$search_value_ucfirst) !== false)) || (isset($value["desc"]) && $value["desc"] != "" && (strpos($value["desc"],$search_value) !== false || strpos($value["desc"],$search_value_ucfirst) !== false)))) {
						$find_resluts = true;
						$k++;
						if ((isset($value["name"]) && $value["name"] != "" && (strpos($value["name"],$search_value) !== false || strpos($value["name"],$search_value_ucfirst) !== false))) {?>
							<li><a href="section-<?php echo esc_html($value["id"])?>"><?php echo str_ireplace($search_value,"<strong>".$search_value."</strong>",esc_html($value["name"]))?></a></li>
						<?php }else {?>
							<li><a href="section-<?php echo esc_html($value["id"])?>"><?php echo str_ireplace($search_value,"<strong>".$search_value."</strong>",esc_html($value["desc"]))?></a></li>
						<?php }
						if ($k == 10) {
							break;
						}
					}
				}
				if (!isset($find_resluts)) {?>
					<li><?php esc_html_e("Sorry, no results.","discy")?></li>
				<?php }?>
			</ul>
		<?php }
	}
	die();
}
add_action( 'wp_ajax_discy_admin_live_search', 'discy_admin_live_search' );
/* Categories_ajax */
function discy_categories_ajax () {
	$name = (isset($_POST["name"])?esc_attr($_POST["name"]):"");
	$name_2 = (isset($_POST["name_2"])?esc_attr($_POST["name_2"]):"");
	$tabs = (isset($_POST["tabs"])?esc_attr($_POST["tabs"]):"");
	if ($tabs == "yes") {
		echo '<li><label class="selectit"><input value="on" type="checkbox" name="'.$name.'[show_all_categories]">'.esc_html__('Show All Categories',"discy").'</label></li>';
	}
	echo discy_categories_checklist(array("name" => $name.$name_2,"id" => $name.$name_2));
	die();
}
add_action('wp_ajax_discy_categories_ajax','discy_categories_ajax');?>