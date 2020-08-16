<?php
/* Video */
add_action( 'widgets_init', 'widget_video_widget' );
function widget_video_widget() {
	register_widget( 'Widget_Video' );
}
class Widget_Video extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'video-widget' );
		$control_ops = array( 'id_base' => 'video-widget' );
		parent::__construct( 'video-widget',discy_theme_name.' - Video', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title		= apply_filters('widget_title', $instance['title'] );
		$video_type = esc_attr($instance['video_type']);
		$video_id   = esc_attr($instance['video_id']);
		$embed_code = $instance['embed_code'];
		$width		= 'width="100"';
		$height		= esc_attr($instance['height']);
		$embed_code = preg_replace('/width="([3-9][0-9]{2,}|[1-9][0-9]{3,})"/',$width,$embed_code);
		$embed_code = preg_replace( '/height="([0-9]*)"/' , $height , $embed_code );
		$width1		= 'width: 100';
		$height1	= 'height: 170';
		$embed_code = preg_replace('/width:"([3-9][0-9]{2,}|[1-9][0-9]{3,})"/',$width1,$embed_code);
		$embed_code = preg_replace( '/height: ([0-9]*)/' , $height1 , $embed_code );  
			
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_attr($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Video","discy")."</h3>";
			}?>
			<div class="widget-wrap">
				<?php if ($video_id != "") {
					if ($video_type == 'youtube') {
						preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",$video_id,$matches);
						if (isset($matches[1])) {
							$video_id = $matches[1];
						}
						$type = "https://www.youtube.com/embed/".$video_id;
					}else if ($video_type == 'vimeo') {
						preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/",$video_id,$matches);
						if (isset($matches[3])) {
							$video_id = $matches[3];
						}
						$type = "https://player.vimeo.com/video/".$video_id;
					}else if ($video_type == 'daily') {
						preg_match("!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!",$video_id,$matches);
						if (isset($matches[2])) {
							$video_id = $matches[2];
						}
						$type = "https://www.dailymotion.com/embed/video/".$video_id;
					}else if ($video_type == 'facebook') {
						$type = "https://www.facebook.com/video/embed?video_id=".$video_id;
					}
				}
				if ($video_type == 'embed' && $embed_code != "") {
					echo ($embed_code);
				}else if (isset($type) && $type != "") {
					echo '<div class="question-video"><iframe allowfullscreen '.$width.' height="'.$height.'" src="'.$type.'"></iframe></div>';
				}?>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>