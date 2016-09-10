<?php
/*
* Theme: PREMIUMPRESS CORE FRAMEWORK FILE
* Url: www.premiumpress.com
* Author: Mark Fail
*
* THIS FILE WILL BE UPDATED WITH EVERY UPDATE
* IF YOU WANT TO MODIFY THIS FILE, CREATE A CHILD THEME
*
* http://codex.wordpress.org/Child_Themes
*/
if (!defined('THEME_VERSION')) {	header('HTTP/1.0 403 Forbidden'); exit; }



global $CORE; $mapData = $CORE->wlt_googlemap_data(true);  $CanShowMap = false; if(strlen($mapData) > 10){ $CanShowMap = true; } ?>

<?php if ( have_posts() ) : ?>

<div class="clearfix"></div>


<?php if($GLOBALS['CORE_THEME']['search_sortby'] == '1'){ ?>

<ul class="list-inline orderby clearfix">

    <li><strong><?php echo $CORE->_e(array('gallerypage','9')); ?>: </strong></li>

    <?php echo $CORE->OrderBy(); ?>
    
</ul>

<hr />

<?php } ?>



<?php if($CanShowMap && isset($GLOBALS['CORE_THEME']['display_search_map'] ) && $GLOBALS['CORE_THEME']['display_search_map']  == "3" && $GLOBALS['CORE_THEME']['google'] == 1 ){ ?>

<?php echo $CORE->wlt_googlemap_html(false); ?>

<?php } ?>


<div class="_searchresultsdata">

	<?php hook_gallerypage_results_top(); ?>

	<a name="topresults"></a>

	<div class="wlt_search_results <?php if($GLOBALS['CORE_THEME']['display']['default_gallery_style'] == "grid"){ echo "grid_style";  }else{ echo "list_style"; } ?>">

		<?php hook_items_before(); ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'content', hook_content_templatename($post->post_type) ); ?>

        <?php wp_reset_postdata(); ?>

		<?php endwhile; ?>

		<?php hook_items_after(); ?>

<?php if($CanShowMap){ ?>
<script>
<?php  $defaults = $CORE->wlt_google_getdefaults();  ?>

var wlt_map_options = [{

	path: "<?php echo get_template_directory_uri(); ?>",
	id: "wlt_google_map",
	region: "<?php echo $defaults['region']; ?>",
	lang: "<?php echo $defaults['lang'] ?>",
	long: <?php echo $defaults['long']; ?>,
	lat: <?php echo $defaults['lat']; ?>,
	zoom: <?php echo $defaults['zoom'] ?>,
	data: <?php echo $mapData; ?>,
	color: "<?php echo $GLOBALS['CORE_THEME']['display_mapcolor_search']; ?>",
	cluster: "<?php if(isset($GLOBALS['CORE_THEME']['googlemap_cluster']) && $GLOBALS['CORE_THEME']['googlemap_cluster'] == 1){ echo "yes"; }else{ echo "no"; } ?>",

	<?php if(isset($_GET['zipcode']) && strlen($_GET['zipcode']) > 1 ){  $radius = $CORE->wlt_google_getradius(); ?>

	radius: [{ "zip":"<?php echo $radius['zip']; ?>", "long":"<?php echo $radius['long']; ?>", "lat":"<?php echo $radius['lat']; ?>", "dis":<?php echo $radius['dis']; ?> }],

	<?php } ?>

}];
</script>
<?php } ?>

<div class="clearfix"></div>

</div>

<?php if( $CanShowMap && ( isset($GLOBALS['CORE_THEME']['default_gallery_map']) && $GLOBALS['CORE_THEME']['default_gallery_map'] == '1') || isset($_GET['showmap']) ):  ?>

	<script>
    jQuery(document).ready(function() {
        loadGoogleMapsApi();
        jQuery('#wlt_search_tab2').removeClass('active');
        jQuery('#wlt_search_tab1').removeClass('active');
        jQuery('#wlt_search_tab3').addClass('active');
    });
    <?php endif; ?>
    </script>

<?php endif; ?>

<?php if($GLOBALS['CORE_THEME']['display']['default_gallery_style'] == "grid"){ ?>
	<script>
    jQuery(document).ready(function() {
        setTimeout(function(){equalheight('.grid_style .itemdata');  }, 2000);
    });
    </script>
<?php } ?>

<?php echo $CORE->PAGENAV(); ?>

<?php hook_gallerypage_after(); ?>

<?php else: ?>

<?php get_template_part( 'search', 'noresults' ); ?>

<?php endif; ?>
