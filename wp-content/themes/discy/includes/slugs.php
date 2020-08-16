<?php if (isset($category_id) && $category_id > 0?$category_id:0) {
	$feed_slug                 = "";
	$answers_might_like_slug   = "";
	$answers_for_you_slug      = "";
	$questions_for_you_slug    = "";
	$recent_questions_slug     = discy_options("recent_questions_slug");
	$most_answers_slug         = discy_options("most_answers_slug");
	$question_bump_slug        = discy_options("question_bump_slug");
	$answers_slug              = discy_options("category_answers_slug");
	$most_visit_slug           = discy_options("most_visit_slug");
	$most_vote_slug            = discy_options("most_vote_slug");
	$no_answers_slug           = discy_options("no_answers_slug");
	$recent_posts_slug         = discy_options("recent_posts_slug");
	$posts_visited_slug        = discy_options("posts_visited_slug");
	$random_slug               = discy_options("random_slug");
	$question_new_slug         = discy_options("question_new_slug");
	$question_sticky_slug      = discy_options("question_sticky_slug");
	$question_polls_slug       = discy_options("question_polls_slug");
	$question_followed_slug    = discy_options("question_followed_slug");
	$question_favorites_slug   = discy_options("question_favorites_slug");

	$feed_slug_2               = "";
	$answers_might_like_slug_2 = "";
	$answers_for_you_slug_2    = "";
	$questions_for_you_slug_2  = "";
	$recent_questions_slug_2   = discy_options("recent_questions_slug_2");
	$most_answers_slug_2       = discy_options("most_answers_slug_2");
	$question_bump_slug_2      = discy_options("question_bump_slug_2");
	$answers_slug_2            = discy_options("answers_slug_2");
	$most_visit_slug_2         = discy_options("most_visit_slug_2");
	$most_vote_slug_2          = discy_options("most_vote_slug_2");
	$no_answers_slug_2         = discy_options("no_answers_slug_2");
	$recent_posts_slug_2       = discy_options("recent_posts_slug_2");
	$posts_visited_slug_2      = discy_options("posts_visited_slug_2");
	$random_slug_2             = discy_options("random_slug_2");
	$question_new_slug_2       = discy_options("question_new_slug_2");
	$question_sticky_slug_2    = discy_options("question_sticky_slug_2");
	$question_polls_slug_2     = discy_options("question_polls_slug_2");
	$question_followed_slug_2  = discy_options("question_followed_slug_2");
	$question_favorites_slug_2 = discy_options("question_favorites_slug_2");
}else {
	$feed_slug                 = discy_post_meta("feed_slug");
	$recent_questions_slug     = discy_post_meta("recent_questions_slug");
	$questions_for_you_slug    = discy_post_meta("questions_for_you_slug");
	$most_answers_slug         = discy_post_meta("most_answers_slug");
	$question_bump_slug        = discy_post_meta("question_bump_slug");
	$answers_slug              = discy_post_meta("answers_slug");
	$answers_might_like_slug   = discy_post_meta("answers_might_like_slug");
	$answers_for_you_slug      = discy_post_meta("answers_for_you_slug");
	$most_visit_slug           = discy_post_meta("most_visit_slug");
	$most_vote_slug            = discy_post_meta("most_vote_slug");
	$no_answers_slug           = discy_post_meta("no_answers_slug");
	$recent_posts_slug         = discy_post_meta("recent_posts_slug");
	$posts_visited_slug        = discy_post_meta("posts_visited_slug");
	$random_slug               = discy_post_meta("random_slug");
	$question_new_slug         = discy_post_meta("question_new_slug");
	$question_sticky_slug      = discy_post_meta("question_sticky_slug");
	$question_polls_slug       = discy_post_meta("question_polls_slug");
	$question_followed_slug    = discy_post_meta("question_followed_slug");
	$question_favorites_slug   = discy_post_meta("question_favorites_slug");

	$feed_slug_2               = discy_post_meta("feed_slug_2");
	$recent_questions_slug_2   = discy_post_meta("recent_questions_slug_2");
	$questions_for_you_slug_2  = discy_post_meta("questions_for_you_slug_2");
	$most_answers_slug_2       = discy_post_meta("most_answers_slug_2");
	$question_bump_slug_2      = discy_post_meta("question_bump_slug_2");
	$answers_slug_2            = discy_post_meta("answers_slug_2");
	$answers_might_like_slug_2 = discy_post_meta("answers_might_like_slug_2");
	$answers_for_you_slug_2    = discy_post_meta("answers_for_you_slug_2");
	$most_visit_slug_2         = discy_post_meta("most_visit_slug_2");
	$most_vote_slug_2          = discy_post_meta("most_vote_slug_2");
	$no_answers_slug_2         = discy_post_meta("no_answers_slug_2");
	$recent_posts_slug_2       = discy_post_meta("recent_posts_slug_2");
	$posts_visited_slug_2      = discy_post_meta("posts_visited_slug_2");
	$random_slug_2             = discy_post_meta("random_slug_2");
	$question_new_slug_2       = discy_post_meta("question_new_slug_2");
	$question_sticky_slug_2    = discy_post_meta("question_sticky_slug_2");
	$question_polls_slug_2     = discy_post_meta("question_polls_slug_2");
	$question_followed_slug_2  = discy_post_meta("question_followed_slug_2");
	$question_favorites_slug_2 = discy_post_meta("question_favorites_slug_2");
}

$feed_slug                 = ($feed_slug != ""?$feed_slug:"feed");
$recent_questions_slug     = ($recent_questions_slug != ""?$recent_questions_slug:"recent-questions");
$questions_for_you_slug    = ($questions_for_you_slug != ""?$questions_for_you_slug:"questions-for-you");
$most_answers_slug         = ($most_answers_slug != ""?$most_answers_slug:"most-answers");
$question_bump_slug        = ($question_bump_slug != ""?$question_bump_slug:"question-bump");
$answers_slug              = ($answers_slug != ""?$answers_slug:"answers");
$answers_might_like_slug   = ($answers_might_like_slug != ""?$answers_might_like_slug:"answers-might-like");
$answers_for_you_slug      = ($answers_for_you_slug != ""?$answers_for_you_slug:"answers-for-you");
$most_visit_slug           = ($most_visit_slug != ""?$most_visit_slug:"most-visit");
$most_vote_slug            = ($most_vote_slug != ""?$most_vote_slug:"most-vote");
$random_slug               = ($random_slug != ""?$random_slug:"random");
$question_new_slug         = ($question_new_slug != ""?$question_new_slug:"new");
$question_sticky_slug      = ($question_sticky_slug != ""?$question_sticky_slug:"sticky");
$question_polls_slug       = ($question_polls_slug != ""?$question_polls_slug:"polls");
$question_followed_slug    = ($question_followed_slug != ""?$question_followed_slug:"followed");
$question_favorites_slug   = ($question_favorites_slug != ""?$question_favorites_slug:"favorites");
$no_answers_slug           = ($no_answers_slug != ""?$no_answers_slug:"no-answers");
$recent_posts_slug         = ($recent_posts_slug != ""?$recent_posts_slug:"recent-posts");
$posts_visited_slug        = ($posts_visited_slug != ""?$posts_visited_slug:"posts-visited");

$feed_slug_2               = ($feed_slug_2 != ""?$feed_slug_2:"feed-time");
$recent_questions_slug_2   = ($recent_questions_slug_2 != ""?$recent_questions_slug_2:"recent-questions-time");
$questions_for_you_slug_2  = ($questions_for_you_slug_2 != ""?$questions_for_you_slug_2:"questions-for-you-time");
$most_answers_slug_2       = ($most_answers_slug_2 != ""?$most_answers_slug_2:"most-answers-time");
$question_bump_slug_2      = ($question_bump_slug_2 != ""?$question_bump_slug_2:"question-bump-time");
$answers_slug_2            = ($answers_slug_2 != ""?$answers_slug_2:"answers-time");
$answers_might_like_slug_2 = ($answers_might_like_slug_2 != ""?$answers_might_like_slug_2:"answers-might-like-time");
$answers_for_you_slug_2    = ($answers_for_you_slug_2 != ""?$answers_for_you_slug_2:"answers-for-you-time");
$most_visit_slug_2         = ($most_visit_slug_2 != ""?$most_visit_slug_2:"most-visit-time");
$most_vote_slug_2          = ($most_vote_slug_2 != ""?$most_vote_slug_2:"most-vote-time");
$random_slug_2             = ($random_slug_2 != ""?$random_slug_2:"random-time");
$question_new_slug_2       = ($question_new_slug_2 != ""?$question_new_slug_2:"new-time");
$question_sticky_slug_2    = ($question_sticky_slug_2 != ""?$question_sticky_slug_2:"sticky-time");
$question_polls_slug_2     = ($question_polls_slug_2 != ""?$question_polls_slug_2:"polls-time");
$question_followed_slug_2  = ($question_followed_slug_2 != ""?$question_followed_slug_2:"followed-time");
$question_favorites_slug_2 = ($question_favorites_slug_2 != ""?$question_favorites_slug_2:"favorites-time");
$no_answers_slug_2         = ($no_answers_slug_2 != ""?$no_answers_slug_2:"no-answers-time");
$recent_posts_slug_2       = ($recent_posts_slug_2 != ""?$recent_posts_slug_2:"recent-posts-time");
$posts_visited_slug_2      = ($posts_visited_slug_2 != ""?$posts_visited_slug_2:"posts-visited-time");
?>