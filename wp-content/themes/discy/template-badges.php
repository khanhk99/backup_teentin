<?php /* Template Name: Badges */
get_header();
$page_id        = $post_id_main = $post->ID;
$active_points  = discy_options("active_points");
$badges_details = discy_options("badges_details");
$badges_style   = discy_options("badges_style");
$badges_groups  = discy_options("badges_groups");
if ($badges_style == "by_groups_points") {
	$badges = discy_options("badges_groups_points");
}else {
	$badges = discy_options("badges");
}
include locate_template("theme-parts/the-content.php");?>
	<div class="page-sections">
		<?php if ($active_points == "on") {
			$edit_profile_items_2  = discy_options("edit_profile_items_2");
			$show_social_points    = ((isset($edit_profile_items_2["facebook"]) && isset($edit_profile_items_2["facebook"]["value"]) && $edit_profile_items_2["facebook"]["value"] == "facebook") || (isset($edit_profile_items_2["twitter"]) && isset($edit_profile_items_2["twitter"]["value"]) && $edit_profile_items_2["twitter"]["value"] == "twitter") || (isset($edit_profile_items_2["youtube"]) && isset($edit_profile_items_2["youtube"]["value"]) && $edit_profile_items_2["youtube"]["value"] == "youtube") || (isset($edit_profile_items_2["vimeo"]) && isset($edit_profile_items_2["vimeo"]["value"]) && $edit_profile_items_2["vimeo"]["value"] == "vimeo") || (isset($edit_profile_items_2["linkedin"]) && isset($edit_profile_items_2["linkedin"]["value"]) && $edit_profile_items_2["linkedin"]["value"] == "linkedin") || (isset($edit_profile_items_2["instagram"]) && isset($edit_profile_items_2["instagram"]["value"]) && $edit_profile_items_2["instagram"]["value"] == "instagram") || (isset($edit_profile_items_2["pinterest"]) && isset($edit_profile_items_2["pinterest"]["value"]) && $edit_profile_items_2["pinterest"]["value"] == "pinterest")?true:false);
			$active_referral       = discy_options("active_referral");
			$points_details        = discy_options("points_details");
			$point_add_question    = discy_options("point_add_question");
			$point_best_answer     = discy_options("point_best_answer");
			$point_voting_question = discy_options("point_voting_question");
			$point_add_comment     = discy_options("point_add_comment");
			$point_voting_answer   = discy_options("point_voting_answer");
			$point_following_me    = discy_options("point_following_me");
			$point_add_post        = discy_options("point_add_post");
			$point_new_user        = discy_options("point_new_user");
			$points_referral       = discy_options("points_referral");
			$points_social         = discy_options("points_social");
			$points_array          = array(
										"point_add_question"    => array("points" => $point_add_question),
										"point_best_answer"     => array("points" => $point_best_answer),
										"point_voting_question" => array("points" => $point_voting_question),
										"point_add_comment"     => array("points" => $point_add_comment),
										"point_voting_answer"   => array("points" => $point_voting_answer),
										"point_following_me"    => array("points" => $point_following_me),
										"point_add_post"        => array("points" => $point_add_post),
										"point_new_user"        => array("points" => $point_new_user),
										"points_referral"       => array("points" => ($active_referral == "on"?$points_referral:0)),
										"points_social"         => array("points" => ($show_social_points == true?$points_social:0)),
									);
			$points_array = apply_filters("discy_filter_points_array",$points_array);
			$points = array_column($points_array,'points');
			array_multisort($points,SORT_DESC,$points_array);
			if (isset($points_array) && is_array($points_array)) {?>
				<div class="page-section">
					<div class="page-wrap-content">
						<h2 class="post-title-3"><i class="icon-bucket"></i><?php esc_html_e("Points System","discy")?></h2>
						<?php if (isset($points_details) && $points_details != "") {?>
							<div class="post-content-text"><p><?php echo discy_kses_stip($points_details)?></p></div>
						<?php }?>
						<div class="points-section">
							<ul class="row">
								<?php foreach ($points_array as $key => $value) {
									if (isset($value["points"]) && $value["points"] > 0) {
										$value_points = (int)$value["points"];?>
										<li class="col col4">
											<div class="point-section">
												<div class="point-div">
													<i class="icon-bucket"></i>
													<span><?php echo discy_count_number($value_points)?></span><?php echo _n("Point","Points",$value_points,"discy")?>
												</div>
												<p><?php if ($key == "point_add_question") {
													esc_html_e("For adding a new question.","discy");
												}else if ($key == "point_best_answer") {
													esc_html_e("When your answer chosen as the best answer.","discy");
												}else if ($key == "point_voting_question") {
													esc_html_e("Your question gets a vote.","discy");
												}else if ($key == "point_add_comment") {
													esc_html_e("For adding an answer.","discy");
												}else if ($key == "point_voting_answer") {
													esc_html_e("Your answer gets a vote.","discy");
												}else if ($key == "point_following_me") {
													esc_html_e("Each time when a user follows you.","discy");
												}else if ($key == "point_add_post") {
													esc_html_e("For adding a new post.","discy");
												}else if ($key == "point_new_user") {
													esc_html_e("For Signing up.","discy");
												}else if ($key == "points_referral") {
													esc_html_e("For referring a new user.","discy");
												}else if ($key == "points_social") {
													esc_html_e("For adding your social media links to your profile.","discy");
												}else if (isset($value["text"]) && $value["text"] != "") {
													echo esc_html($value["text"]);
												}?></p>
											</div>
										</li>
									<?php }
								}?>
							</ul>
						</div>
					</div><!-- End page-wrap-content -->
				</div><!-- End page-section -->
			<?php }
		}
		
		if (($badges_style != "by_groups" && isset($badges) && is_array($badges)) || ($badges_style == "by_groups" && isset($badges_groups) && is_array($badges_groups) && isset($badges_details) && $badges_details != "")) {?>
			<div class="page-section">
				<div class="page-wrap-content">
					<h2 class="post-title-3"><i class="icon-trophy"></i><?php esc_html_e("Badges System","discy")?></h2>
					<?php if (isset($badges_details) && $badges_details != "") {?>
						<div class="post-content-text"><p><?php echo discy_kses_stip($badges_details)?></p></div>
					<?php }
					if ($badges_style != "by_groups") {?>
						<div class="badges-section">
							<ul>
								<?php foreach ($badges as $badges_k => $badges_v) {
									$badge_points = (int)$badges_v["badge_points"];?>
									<li>
										<div class="badge-section">
											<div class="badge-div">
												<span class="badge-span" style="background-color: <?php echo esc_html($badges_v["badge_color"])?>;"><?php echo strip_tags(stripslashes($badges_v["badge_name"]),"<i>")?></span>
												<div class="point-div">
													<i class="icon-bucket"></i>
													<span><?php echo discy_count_number($badge_points)?></span><?php echo _n("Point","Points",$badge_points,"discy")?>
												</div>
											</div>
											<p><?php echo discy_kses_stip(stripslashes($badges_v["badge_details"]))?></p>
										</div>
									</li>
								<?php }?>
							</ul>
						</div>
					<?php }?>
				</div><!-- End page-wrap-content -->
			</div><!-- End page-section -->
		<?php }?>
	</div><!-- End page-sections -->
<?php get_footer();?>