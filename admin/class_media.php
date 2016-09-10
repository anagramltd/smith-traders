<?php

class core_media extends white_label_themes {

	public $media_set = array(); // STORES ARRAY OF MEDIA FILES ALREADY SET

	function __construct(){

	}


/*
This function handles all images where no featured one is set

*/
function _FALLBACK($postID=""){

	global $post, $CORE; $old_img_system = "";

	if(isset($GLOBALS['CORE_THEME']['fallback_image']) && strlen($GLOBALS['CORE_THEME']['fallback_image']) > 5){


		$old_img_system = get_post_meta($postID,'image',true);
		$old_img_system = str_replace("&", "&amp;", $old_img_system);

		// FINAL CHECK IF THEY HAVE ADDED AN IMAGE USING THE MEDIA SYSTEM INSTEAD
		if($old_img_system == ""){
			$g = get_post_meta($postID, "image_array", true);
			if(is_array($g) && !empty($g) ){
					$old_img_system = $g[0]['thumbnail'];
					if($old_img_system == "" || ( substr($old_img_system,0,4) == "http" && file_exists($old_img_system) ) ){
					$old_img_system = $g[0]['src'];
					}
			}
		}

		// CHECK FOR VIDEO IMAGES
		if($old_img_system == ""){
			$g = get_post_meta($postID,"video_array", true);
			if(isset($g[0]) && isset($g[0]['thumbnail']) && strlen($g[0]['thumbnail']) > 1){
				$old_img_system = $g[0]['thumbnail'];
			}
		}

		// DEMO IMG EXTRAS
		if(defined('WLT_DEMOMODE') && $post->post_author == 1 && isset($GLOBALS['CORE_THEME']['sampledata']) ){

			$did = filter_var($post->post_title, FILTER_SANITIZE_NUMBER_INT);

		  	if(isset($GLOBALS['CORE_THEME']['sampledata']) && isset($GLOBALS['CORE_THEME']['sampledata'][$did]) ){

				if(isset($GLOBALS['flag-single'])){
				$old_img_system = $GLOBALS['CORE_THEME']['sampledata'][$did]['f'];
				}else{
				$old_img_system = $GLOBALS['CORE_THEME']['sampledata'][$did]['t'];
				}

			}
		}


		// SETUP IMAGE STRING
		if (strpos($old_img_system, "http") !== false) {
		$img = '<img src="'.$old_img_system.'" alt="fallback-no-image-'.$postID.'" class="wlt_thumbnail img-responsive" />';
		}else{
		$img = '<img src="'.$GLOBALS['CORE_THEME']['fallback_image'].'" alt="no-image-'.$postID.'" class="noimage wlt_thumbnail img-responsive" />';
		}


	}// end if


	return hook_fallback_image_display($img);
}


/*
This function will get all media item for this listing

*/

function _GET($postID, $type = 'all'){ global $post, $CORE; $meida_array = array();


// GET THE POST DATA
$post = get_post($postID);

// GET THE FILE TYPE STORAGE KEY
if($type == "image" || $type == "images"){
	$get_type = array("image_array");	$includeImages = true;
}elseif($type == "video"){
	$get_type = array("video_array");
}elseif($type == "music"){
	$get_type = array("music_array");
}elseif($type == "doc"){
	$get_type = array("doc_array");
}elseif($type == "allbutmusic"){
	$get_type = array("image_array", "video_array", "doc_array");	 $includeImages = true;
}else{
	$get_type = array("image_array", "video_array", "doc_array", "music_array");	 $includeImages = true;
}

// LOOP SELECTED MEDIA AND GET THE DATA
foreach($get_type as $type){
	$g = get_post_meta($postID,$type, true);
	if(is_array($g)){
	$meida_array = array_merge($meida_array, $g);
	}

}

// CHECK IF THE LISTING CONTENT CONTAINS IMAGE GALLERIES
if (isset($includeImages) && strpos($post->post_content,"gallery ids") != false || strpos($post->post_content,"gallery column") != false || strpos($post->post_content,"gallery link") != false){

		// GET THE ATTACHMENT IDS TO BUILD THE NEW GALLERY
		preg_match('/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids);
		$wordpress_default_gallery_ids = explode(",", $ids[1]);

		// GET THE CURRENT WP UPLOAD DIR
		$uploads = wp_upload_dir(); $user_attachments = array(); $i=0;
		foreach($wordpress_default_gallery_ids as $img_id){
			if(is_numeric($img_id)){
				$f = wp_get_attachment_metadata($img_id);
				if(isset($f['file'])){
					$user_attachments[$i]['src'] 		= $uploads['baseurl']."/".$f['file'];
					$user_attachments[$i]['thumbnail'] 	= $user_attachments[$i]['src']; //$uploads['url']."/".$f['sizes']['thumbnail']['file'];
					$user_attachments[$i]['name'] 		= $f['image_meta']['title'];
					$user_attachments[$i]['id'] 		= $img_id;
					$user_attachments[$i]['class'] 		= "";
				}
				$i++;
			}
		}

		if(!empty($user_attachments)){
		$meida_array = array_merge($meida_array, $user_attachments);

		}
}

// CHECK IF ITS EMPTY
if(empty($meida_array)){
	// CHECK TO SEE IF THE CONTENT CONTAINS A VIDEO LINK AND USE THIS AS THE VIDEO
	preg_match_all('!http://[a-z0-9\-\.\/]+\.(?:jpe?g|flv)!Ui', get_the_content($postID), $matches);
	if(is_array($matches)){
		foreach($matches as $mm){
			if(!isset($mm[0]) || ( isset($mm[0]) && $mm[0] == "") ){ continue; }
			$meida_array = array( array("class" => "", "src" => $mm[0], "thumbnail" => str_replace(" ", "-",$mm[0])));
		}
	}
}


		// DEMO IMG EXTRAS
		if(defined('WLT_DEMOMODE') && $post->post_author == 1 && isset($GLOBALS['CORE_THEME']['sampledata']) ){

			$did = filter_var($post->post_title, FILTER_SANITIZE_NUMBER_INT);

		  	if(isset($GLOBALS['CORE_THEME']['sampledata']) && isset($GLOBALS['CORE_THEME']['sampledata'][$did]) ){

				$meida_array[] = array("class" => "", "src" => $GLOBALS['CORE_THEME']['sampledata'][$did]['t'], "thumbnail" => $GLOBALS['CORE_THEME']['sampledata'][$did]['f'] );

			}
		}


return $meida_array;


}


/* =============================================================================
	[MEDIA] - SHORTCODE
	========================================================================== */
function wlt_shortcode_media($atts, $content = null ){ global $post; $STRING = "";


// GET THE MEDIA IN AN ARRAY
$allmedia = $this->_GET($post->ID, "all");

// LOOP ALL MEDIA ITEMS
if(is_array($allmedia) && !empty($allmedia) ){

	// BUNCH TYPES TOGETHER
	$allmedia = $this->multisort( $allmedia , array('type') );

	// DO SOMETHING WITH IT
	foreach($allmedia as $file){

	// REMOVE ALREADY DISPLAYED FILES
	if(in_array($file['id'], $this->media_set) ){ continue; }

	// GET MEDIA DATA FROM POST
	$media = get_post($file['id']);


		// GET FILE DATA
		if($file['type'] == "youtube"){

			 // DONT DUPLICATE
			 if(in_array("youtube",$this->media_set)){ continue; }

			$l = str_replace("[field]", "Youtube_link", str_replace("[type]", "",  $ajax_query));

			$IMAGEBIT = "<div id='wlt_videobox_ajax_".$GLOBALS['vboxID'].$post->ID."' class='videobox'><a href='javascript:void(0);' ".$l." class='frame'><img src='".get_post_meta($post->ID,'image',true)."' alt='video' style='width: 100%; height: 100%; max-height:450px;'><div class='overlay-video overlay-video-active fa fa-play'></div></a></div>";

		}elseif(in_array($file['type'],$this->allowed_music_types)){

			$IMAGEBIT = '<audio id="audio_id_'.$GLOBALS['media_id'].'" style="margin:0 auto; max-width:200px;" preload="none" width="100%"><source type="'.$file['type'].'" src="'.$file['src'].'" /></audio>';

		}elseif($file['type'] == "application/pdf"){

			$IMAGEBIT = '<a href="'.$file['src'].'" target="_blank"><img src="'.FRAMREWORK_URI.'img/icons/pdf.png" alt="pdf"></a>';

		}elseif($file['type'] == "application/octet-stream"){

			$IMAGEBIT = '<a href="'.$file['src'].'" target="_blank"><img src="'.FRAMREWORK_URI.'img/icons/compress.png" alt="zip"> </a>';

		}elseif($file['type'] == "application/msword"){

			$IMAGEBIT = '<a href="'.$file['src'].'" target="_blank"><img src="'.FRAMREWORK_URI.'img/icons/doc.png" alt="doc"></a>';

		}elseif(in_array($file['type'],$this->allowed_video_types)){

			$IMAGEBIT = '<div class="videobox'.$post->ID.'">
						<video id="video_id_99" width="180" height="130"  controls="controls" preload="none">

						<source type="'.$file['type'].'" src="'.$file['src'].'" />

						<object width="100%" height="300" style="width: 100%; height: 100%;" type="application/x-shockwave-flash" data="'.get_template_directory_uri().'/framework/slider/flashmediaelement.swf">
							<param name="movie" value="'.get_template_directory_uri().'/framework/slider/flashmediaelement.swf" />
							<param name="flashvars" value="controls=true&file='.$file['src'].'" />
							<img src="'.$file['src'].'"  title="No video playback capabilities" />
						</object>
						</video><input value="0" class="videotime'.$post->ID.'" type="hidden">
						</div>';

		}else{

			if(isset($GLOBALS['tpl-add'])){
			$IMAGEBIT = '<img src="'.$file['thumbnail'].'" alt="video">';
			}else{

				$l = str_replace("[field]", str_replace("http://","",$file['src']), str_replace("[type]", $file['type'], $ajax_query ));

				$IMAGEBIT = "<div id='wlt_videobox_ajax_".$GLOBALS['vboxID'].$post->ID."' class='videobox'>
				<a href='".$file['src']."' data-gal='prettyPhoto[ppt_gal_".$post->ID."]' ".$l." class='frame'>
				<img src='".$file['thumbnail']."' alt='video'>
				</a>
				</div> <input value='0' class='videotime".$post->ID."' type='hidden'>";

			}
		}

	ob_start();
	?>

      <div class="thumbnail"><div class="wrap">

        <?php echo $IMAGEBIT; ?>
          <!--<div class="caption">
            <h4><?php echo $media->post_title; ?></h4>
          </div>
         -->

      </div> </div>

    <?php
	$STRING .= ob_get_clean();

	}

	if(strlen($STRING) > 5){
	return "<div id='wlt_shortcode_media_wrapper' class='clearfix'>".$STRING."</div>";
	}

}



}

	/* =============================================================================
		[IMAGE] - SHORTCODE
		========================================================================== */
	function wlt_shortcode_image( $atts, $content = null ) {

		global $userdata, $CORE,   $post;  $image = ""; $linkextra = ""; if(!isset($GLOBALS['imagecount'])){ $GLOBALS['imagecount'] = 1; }else{ $GLOBALS['imagecount']++; }

		extract( shortcode_atts( array('pid' => $post->ID, "pathonly" => false, 'text' => "", 'striptags' => false, 'link' => 1, "gallery" => 0,  "class" => "wlt_thumbnail img-responsive", "right" => 0, "count" => 0), $atts ) );




		// LEFT RIGHT IMAGE
		if($right == 1){
		$linkextra = " pull-right";
		}else{
		$linkextra = "";
		}

 		// GET IMAGE LINK
		$permalink = get_permalink($pid);

		// STRIP TAGS IF CONTENT IN NOT EMPTY
		if(strlen($content) > 1){ $striptags = true; }

		// DISPLAY OVERLAY TEXT
		$text_overlay = esc_attr(strip_tags($text));

		// CONTENT
		$content = do_shortcode($content);

		// FIX FOR PRINT PAGE IMAGES
		if(isset($_GET['pid']) && isset($_GET['print'])){ $pid = $_GET['pid']; }

		// CHECK IF WE HAVE A THUMBNAIL
		if ( has_post_thumbnail($pid) ) {

		 		if($link == 1){ $image = '<a href="'.$permalink.'" class="frame'.$linkextra.'">'; }else{ $image .= '<div class="frame'.$linkextra.'">'; }

				$image .= hook_image_display(get_the_post_thumbnail($pid, array(get_option('thumbnail_size_w'),get_option('thumbnail_size_h')), array('class'=> $class." featuredset")));

				$image .= $this->STICKER($pid);

				if(strlen($text_overlay) > 1){ $image .= "<span class='ftext'>".$text_overlay."</span>"; }
				if(strlen($content) > 1){ $image .= $content; }

				if($link == 1){ $image .= '</a>'; }else{ $image .= '</div>'; }

		}else{

			// CHECK FOR FALLBACK IMAGE
			$fimage = $this->_FALLBACK($pid);

			if($fimage !=""){

					if($link == 1){ $image = '<a  href="'.$permalink.'" class="frame'.$linkextra.'">'; }else{ $image .= '<div class="frame'.$linkextra.'">'; }
					$image .= $fimage;

					$image .= $this->STICKER($pid);

					if($count == 1){
						$old_imgs_system = get_post_meta($pid,'image_array',true);
						if(!is_array($old_imgs_system)){ $fc = 0; }else{ $fc = count($old_imgs_system); }
						$image .= "<span class='img-count'>".$fc."</span>";
					}

					if(strlen($text_overlay) > 1){ $image .= "<span class='ftext'>".$text_overlay."</span>"; }
					if(strlen($content) > 1){ $image .= $content; }

					if($link == 1){ $image .= '</a>'; }else{ $image .= '</div>'; }

			}
		}


		if($pathonly){
			preg_match( '@src="([^"]+)"@' , $image , $match );
			if(isset($match[1])){
			return $match[1];
			}
		}


		// REMOVE FIXED WIDTH/HEIGHT VALUES
		$image = preg_replace( '/(width|height)="\d*"\s/', "", $image );

		

		// UNSET
		unset($GLOBALS['noeditor']);

		// ITEMSCOPE
		$image = str_replace("<img ","<img ".$this->ITEMSCOPE("itemprop","image")." ",$image);
		$image = str_replace("&","&amp;",$image);

		// RETURN VALUE
		if($striptags){
			return hook_image_display($image);
		}else{
			return hook_shortcode_image_output(hook_image_display($image));
		}

	}



	/* =============================================================================
		[FILES] - SHORTCODE
		========================================================================== */
	function wlt_shortcode_files( $atts, $content = null ) {

			global $userdata, $CORE, $post, $shortcode_tags; $STRING = "";  $default_options = 'all';

			extract( shortcode_atts( array('type' => $default_options, 'info' => true ), $atts ) );
			$options = explode("|",esc_attr($type));

			foreach($options as $op){
				$STRING	 = $this->UPLOAD_GET($post->ID, 3, array("type" => esc_attr($type) ));
			}

			return $STRING;
	}


/* =============================================================================
	[IMAGES] - SHORTCODE
========================================================================== */
function wlt_shortcode_images( $atts, $content = null){ global $post, $wpdb, $CORE, $MEDIA; $gallery1 = "";   $gallery2 = "";

		// EXTRACT OPTIONS
		extract( shortcode_atts( array('grid' => 0, 'type' => 'allbutmusic'), $atts ) );

		// FIX FOR PRINT PAGE IMAGES
		if(isset($_GET['pid']) && isset($_GET['print'])){ $pid = $_GET['pid']; }else{ $pid = $post->ID; }

		// 1. GET MEDIA
		$images = $MEDIA->_GET($pid, "images");

		//2. CHECK OUTPUT
		if( count($images) == 0){ // FALLBACK IMAGES


			// CHECK IF WE HAVE A VIDEO INSTEAD JUST ENCASE
			$videos = get_post_meta($pid,"video_array", true);
			if(!empty($videos) && isset($videos[0]) ){

			// ADD THE ARRAY TO MEDIA SET
			$this->media_set = array_merge($this->media_set, array($videos[0]['id']));

			return '
						<div class="videobox'.$post->ID.'">
						<video id="video_id_99" width="100%" height="300" style="width: 100%; height: 100%;" controls="controls" preload="none">
						<source type="'.$videos[0]['type'].'" src="'.$videos[0]['src'].'" />
						<!-- Flash fallback for non-HTML5 browsers without JavaScript -->
						<object width="100%" height="300" style="width: 100%; height: 100%;" type="application/x-shockwave-flash" data="'.get_template_directory_uri().'/framework/slider/flashmediaelement.swf">
							<param name="movie" value="'.get_template_directory_uri().'/framework/slider/flashmediaelement.swf" />
							<param name="flashvars" value="controls=true&file='.$videos[0]['src'].'" />
							<img src="'.$videos[0]['src'].'"  title="No video playback capabilities" />
						</object>
						</video><input value="0" class="videotime'.$post->ID.'" type="hidden">
						</div>';

			}else{

				// ELSE SHOW FALLBACK IMAGE
				return "<div class='singleimg text-center'>".$this->_FALLBACK($pid)."</div>";

			}

		}elseif( count($images) == 1 ){ // ONLY 1 IMAGES

			// ADD THE ARRAY TO MEDIA SET
			$this->media_set = array_merge($this->media_set, array($images[0]['id']));

			// GET IMAGE AGAIN ENCASE THE ADMIN HAS MADE CHANGES
			$image_src = wp_get_attachment_image_src( $images[0]['id'], 'full' );
			if(empty($image_src)){
			$imgsrc = $images[0]['src'];
			}else{
			$imgsrc = $image_src[0];
			}

			return "<div class='singleimg text-center'><a href='".$imgsrc."' data-gal='prettyPhoto'><img src='".$imgsrc."' alt='".$images[0]['name']."' class='".$images[0]['class']." img-responsive' /></a></div>";

		}else{ // IMAGE GALLERY

		if(is_array($images) && !empty($images)){

			foreach($images as $nimg){

 				// ADD THE ARRAY TO MEDIA SET
				$this->media_set = array_merge($this->media_set, array($nimg['id']));

				// GET IMAGE AGAIN ENCASE THE ADMIN HAS MADE CHANGES
				$image_src = wp_get_attachment_image_src( $nimg['id'], 'full' );

				$nimg['thumbnail'] = str_replace("-http","http",$nimg['thumbnail']);

				$gallery1 	.= "<a href='".$image_src[0]."' data-gal='prettyPhoto[ppt_gal_".$pid."]'><img src='".$image_src[0]."' alt='".$nimg['name']."' class=' img-responsive'  /></a>";
				$gallery2 	.= "<img src='".$nimg['thumbnail']."' alt='".$nimg['name']." &nbsp;' class='img-responsive owl-lazy' style='cursor:pointer' />";
			}


		}

		//2. CREATE GALLERY
		ob_start();?>

        <div class="wlt_shortcode_images">

            <div id="slider" class="owl-carousel" style="display:none;">
              <?php echo $gallery1; ?>
            </div>

            <div class="navs">
              <a class="btn prev"><?php echo $CORE->_e(array('button','62')); ?></a>
              <a class="btn next"><?php echo $CORE->_e(array('button','61')); ?></a>
            </div>

            <div class="carousel">

                <div id="slider-carousel" class="owl-carousel">
                 <?php echo $gallery2; ?>
                </div>

            </div>

        </div>

<script>

jQuery(window).load(function() {
    function e() {
        var e = this.currentItem;
        jQuery("#slider-carousel").find(".owl-item").removeClass("synced").eq(e).addClass("synced"), void 0 !== jQuery("#slider-carousel").data("owlCarousel") && o(e)
    }

    function o(e) {
        var o = r.data("owlCarousel").owl.visibleItems,
            i = e,
            t = !1;
        for (var l in o)
            if (i === o[l]) var t = !0;
        t === !1 ? i > o[o.length - 1] ? r.trigger("owl.goTo", i - o.length + 2) : (i - 1 === -1 && (i = 0), r.trigger("owl.goTo", i)) : i === o[o.length - 1] ? r.trigger("owl.goTo", o[1]) : i === o[0] && r.trigger("owl.goTo", i - 1)
    }
    var i = jQuery("#slider"),
        r = jQuery("#slider-carousel");
    i.owlCarousel({
        singleItem: !0,
        slideSpeed: 1e3,
        navigation: !1,
        pagination: !1,
        afterAction: e,
        responsiveRefreshRate: 200
    }), r.owlCarousel({
        items: 5,
        lazyLoad: !0,
        itemsDesktop: [1199, 10],
        itemsDesktopSmall: [979, 10],
        itemsTablet: [768, 8],
        itemsMobile: [479, 4],
        pagination: !1,
        responsiveRefreshRate: 100,
        afterInit: function(e) {
            e.find(".owl-item").eq(0).addClass("synced")
        }
    }), jQuery("#slider-carousel").on("click", ".owl-item", function(e) {
        e.preventDefault();
        var o = jQuery(this).data("owlItem");
        i.trigger("owl.goTo", o)
    });

  jQuery(".next").click(function(){
    i.trigger('owl.next');  r.trigger('owl.next');
  });
  jQuery(".prev").click(function(){
    i.trigger('owl.prev');  r.trigger('owl.prev');
  });

});



</script>

        <?php

	 	return ob_get_clean();

		}

		}







	/* =============================================================================
	[VIDEO] - SHORTCODE
	========================================================================== */
	function wlt_shortcode_video($atts, $content = null){  global $wpdb, $post, $CORE; $STRING = "";

		extract( shortcode_atts( array('link' => '', 'postid' => '', 'limit' => 1, 'playlist' => 0 ), $atts ) );


		if($postid != ""){
			// FIRST CHECK VIDEO ARRAY
			return $CORE->UPLOAD_GET($postid,2,array('video',1));
		}


	 	// CHECK FOR THE BASIC YOUTUBE LINK FIRST
		if($link != ""){
			$YOUTUBELINK = $link;
		}else{
			$YOUTUBELINK = get_post_meta($post->ID,'Youtube_link',true);
			if($YOUTUBELINK == ""){
			$YOUTUBELINK = get_post_meta($post->ID,'youtube',true);
			}
		}

		if($YOUTUBELINK != ""){
		$youid = explode("v=",$YOUTUBELINK);
		$thisid = explode("&",$youid[1]);
		$STRING = '
		<div class="hidden-sm hidden-xs videobox'.$post->ID.'">
			<video width="640" height="360" id="player1" preload="none" style="width: 100%; height: 100%;" autoplay="true">
				<source type="video/youtube" src="'.$YOUTUBELINK.'" />
			</video>
		</div>
		<div class="visible-sm visible-xs videobox'.$post->ID.'">
				<iframe style="width:100%; height:100%;" src="//www.youtube.com/embed/'.$thisid[0].'" frameborder="0" allowfullscreen></iframe>
		</div><input value="0"  class="videotime'.$post->ID.'" type="hidden">';

		// ADD THE ARRAY TO MEDIA SET
		$this->media_set = array_merge($this->media_set, array("youtube"));

		}else{

			// NOW RETURN DEFAULT THEME CONTENTS
			$STRING = $this->UPLOAD_GET($post->ID, 2, array("type" => "video", "limit" => 5 ));
		}



		// VIDEO PLAYLIST OPTION
		if($playlist == 1){

		 	// RUN
			$slider_query = new WP_Query( hook_custom_queries("orderby=rand&post_type=listing_type&posts_per_page=10") );

			// The Loop
			if ( $slider_query->have_posts() ) {

			// DEFAULTS
			$data_carousel = "";
			$first_link = "";
			$tooltip = "";
			$i=1;

			// LOOP
			while ( $slider_query->have_posts() ) {


				// GET DATA
				$slider_query->the_post();

				// GET LINK
				$link = get_permalink($post->ID);

				// SET NEXT VIDEO LINK FOR AUTO REDIRECT
				if($first_link == ""){ $first_link  = $link; $tooltip = 'data-toggle="tooltip" data-placement="right" title="Next Video"'; }

				// IMAGE
				$image = hook_image_display(get_the_post_thumbnail($post->ID, 'thumbnail', array('class'=> "wlt_thumbnail")));
				if($image == ""){$image = hook_fallback_image_display($this->_FALLBACK($post->ID)); }

				// SLIDER DATA
				$data_carousel .= '<div class="item"><a href="' . $link . '" class="nextvid'.$i.'" '.$tooltip.' >' . $image . '</a></div>';

				$i++;
			}

		} else {
			// no posts found
			return;
		}
		 wp_reset_postdata();
		ob_start();
		?>

        <div id="wlt_shortcode_video_playerlist" class="owl-carousel">
		<?php echo $data_carousel; ?>
        </div>
        <script>
        jQuery(document).ready(function(){
         jQuery("#wlt_shortcode_video_playerlist").owlCarousel({ items : 4, autoPlay : false, stagePadding: 50, margin:10,  });
        });
        </script>
		<input class="videonextup<?php echo $post->ID; ?>" value="<?php echo $first_link; ?>?autoplayer=1" type="hidden" />
        <?php
		$STRING .= ob_get_clean();

		}

		return $STRING;

	}



 	/* =============================================================================
	[MUSIC] - SHORTCODE
	========================================================================== */
	function wlt_shortcode_music($atts, $content = null){  global $userdata, $CORE, $post; $STRING = "";

		// GET ARRAY ASSIGNED TO LISTING
		$audio = get_post_meta($post->ID, 'music_array', true);

		// MAKE SURE ITS NOT EMMPTY
		if(is_array($audio) && !empty($audio) ){

			$media_file = $CORE->UPLOAD_GET($post->ID, 2, array('type' => 'music', 'limit' => 1) );

			if(strlen($media_file) > 1){

				// IF IS LISTING PAGE
				if(isset($GLOBALS['flag-single'])){

				$add_string = "<div class='single_audio_file'>".$media_file."</div>";
				$add_string .= "<script>jQuery('.single_audio_file audio').mediaelementplayer({ audioWidth: '100%', audioHeight: 30, enableAutosize: true });</script>";

				}else{

				if(!isset($GLOBALS['media_id'])){ $GLOBALS['media_id']=0; }

				$add_string = '<div class="audiobox" id="playbutton_'.$GLOBALS['media_id'].'_wrapper">

				<div class="player">

					<div class="col-md-1">
						<div class="playbtn">
							<span class="glyphicon glyphicon-play play" id="playbutton_'.$GLOBALS['media_id'].'_play" ></span>
							<span class="glyphicon glyphicon-pause pause hidden" id="playbutton_'.$GLOBALS['media_id'].'_pause"></span>
						</div>
					</div>

					<div class="col-md-9 hidden-xs">
						<div class="progress">
							<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%;"></div>
						</div>
					</div>
				<div class="clearfix"></div>
				</div>

			</div> ';

			$add_string .= "<div style='display:none;'>".$media_file.'</div><script type="application/javascript">
				jQuery(\'#audio_id_'.($GLOBALS['media_id']-1).'\').mediaelementplayer({
				audioWidth: 1, audioHeight: 1,
				 success: function(media, domElement, player) {

				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_wrapper .play\').on(\'click\', function() {  media.play();
				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_play\').addClass(\'hidden\');
				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_pause\').removeClass(\'hidden\');
				 });
				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_wrapper .pause\').on(\'click\', function() {  media.pause();
				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_play\').removeClass(\'hidden\');
				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_pause\').addClass(\'hidden\');
				 })
				 }});

				 var progress'.$GLOBALS['media_id'].';
				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_wrapper .play\').on("click", function(event) {
				  setTimeout(audioprocess'.$GLOBALS['media_id'].'() ,300);
				 });

				 jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_wrapper .pause\').on("click", function(event) {
				 clearInterval(progress'.$GLOBALS['media_id'].');
				  var me = jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_wrapper .progress-bar\');
				  perc = me.attr("data-percentage");
				  me.css(\'width\', perc+\'%\');
				 });

				 function audioprocess'.$GLOBALS['media_id'].'(){
						var me = jQuery(\'#playbutton_'.$GLOBALS['media_id'].'_wrapper .progress-bar\');
						var perc = me.attr("data-percentage");
						var current_perc = me.width()/2;
						progress'.$GLOBALS['media_id'].' = setInterval(function() {
							if (current_perc>=perc || current_perc > 99 || perc > 99) {
								clearInterval(progress'.$GLOBALS['media_id'].');
							} else {
								current_perc +=1;
								me.css(\'width\', (current_perc)+\'%\');
							}
							me.text((current_perc)+\'%\');
						}, 500);
				 }

				 </script>';

				}


				return $add_string;

			}

		}

		return;


	}





} // END MEDIA CLASS

?>
