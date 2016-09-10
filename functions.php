<?php
// TELL THE CORE THIS IS A CHILD THEME
define("WLT_CHILDTHEME", true);

// CHILD THEME LAYOUT SETTINGS
function childtheme_designchanges(){

				// LOAD IN CORE STYLES AND UNSET THE LAYOUT ONES SO OUR CHILD THEME DEFAULT OPTIONS CAN WORK
				$core_admin_values = get_option("core_admin_values");

					// SET HEADER
					$core_admin_values["layout_header"] = "6";
					// SET MENU
					$core_admin_values["layout_menu"] = "2";
					// SET RESPONISVE DESIGN
					$core_admin_values["responsive"] = "1";
					// SET COLUMN LAYOUTS
					$core_admin_values["layout_columns"] = array('homepage' => '3', 'search' => '2', 'single' => '2', 'page' => '2', 'footer' => '', '2columns' => '', 'style' => '', '3columns' => '');
					// SET WELCOME TEXT
					$core_admin_values["header_welcometext"] = "<ul class=\'list-inline\'><li class=\'list-inline-item\'>Link</li></ul>";
					// SET RATING
					$core_admin_values["rating"] 		= "1";
					$core_admin_values["rating_type"] 	= "1";
					// BREADCRUMBS
					$core_admin_values["breadcrumbs_inner"] 	= "1";
					$core_admin_values["breadcrumbs_home"] 		= "0";
					// TURN OFF CATEGORY DESCRIPTION
					$core_admin_values["category_descrition"] 	= "1";
					// GEO LOCATION
					$core_admin_values["geolocation"] 	= "";
					$core_admin_values["geolocation_flag"] 	= "NZ";
					// FOOTER SOCIAL ICONS
					$core_admin_values["social"] 	= array(
					'twitter' => '', 'twitter_icon' => 'fa-twitter',
					'facebook' => '', 'facebook_icon' => 'fa-facebook',
					'dribbble' => '', 'dribbble_icon' => 'fa-google-plus',
					'linkedin' => '', 'linkedin_icon' => 'fa-linkedin',
					'youtube' => '', 'youtube_icon' => 'fa-youtube',
					'rss' => '', 'rss_icon' => 'fa-rss',
					);
					// FOOTER COPYRIGHT TEXT
					$core_admin_values["copyright"] 	= "© Copyright 2016 - http://smithtraders.co.nz";
					// HOME PAGE OBJECT SETUP
					$core_admin_values["homepage"]["widgetblock1"] = "text_0,text_1,tabs_2,text_3";

$core_admin_values['widgetobject']['text']['0'] = array(
'fullw' => "yes",
'autop' => "no",
'text' => "<div class='row'><div class='col-md-8'><img src='http://smithtraders.co.nz/wp-content/themes/DL/templates/template_dealer_theme/img/demo/slide2.jpg'> 				</div>	 	<div class='col-md-4'>				<div class='bannerboxside'>					<div><a href='http://smithtraders.co.nz/?s='><h1>Looking for a Car?</h1> <p>We've got a great range of cars available to buy now! </p>  </a></div>  			 			<div  class='last alt2'><a href=''><h1>Want to sell a Car?</h1><p>Add your car FREE to our website today!</p></a></div></div></div></div>[CARTYPES]",
);
$core_admin_values['widgetobject']['tabs']['2'] = array(
'title1' => "Featured Products",
'query1' => "&order=asc&posts_per_page=8",
'title2' => "Recently Added",
'query2' => "&order=desc",
'fullw' => "yes",
);
$core_admin_values['widgetobject']['text']['3'] = array(
'fullw' => "yes",
'text' => "<div class='block'><div class='block-title'><h3>Browse by Manufacturer</h3></div>[MAKERS]</div>",
);
					// SET ITEMCODE
					$core_admin_values["itemcode"] 	= "[IMAGE][price]  <h2>[TITLE]</h2><p> [miles] / [transmission]  / <span class='hidden_details'> [engine] [ctype] /</span> [petrol]</p>	<div class='hidden_details'>[ICON id='map-marker'] [LOCATION]	<hr />[EXCERPT]	 </div>   <hr /><div class='right hidden_details'>[FAVS]</div>[DISTANCE]";
					// SET LISTING PAGE CODE
					$core_admin_values["listingcode"] 	= "<h1>[TITLE-NOLINK] [price]</h1><p class='ebits'> [DISTANCE info=0] [miles]  [transmission]  [engine] [ctype]  [petrol]</p>	<hr style='margin-top:0px;'><div class='row'><div class='col-md-6'>[IMAGES]</div>        <div class='col-md-6'> 	[EXCERPT size='400' end='...']	 </div></div><div class='clearfix'></div>[BTNBAR]<ul class='nav nav-tabs navstyle1' id='Tabs'>  <li class='active'><a href='#t1' data-toggle='tab'><i class='fa fa-edit'></i> Description</a></li>  <li><a href='#t4' data-toggle='tab'><i class='fa fa-comments'></i> Comments</a></li></ul> <div class='tab-content'><div class='tab-pane active' id='t1'>[CONTENT] <h3><i class='fa fa-dashboard'></i> Vehicle Details</h3>[CARDETAILS]<h3> [ICON id='map-marker'] Location</h3><p>[LOCATION]</p>[GOOGLEMAP] </div><div class='tab-pane' id='t4'>[COMMENTS]</div>  </div> <h3 class='related'>Related Vehicles</h3>[CAROUSEL related=true]";
					// SET PRINT PAGE CODE
					$core_admin_values["printcode"]  = "<div class='center'><p id='postTitle'>[TITLE-NOLINK]</p>            <p id='postMeta'>Date:<strong>[DATE]</strong>  </p>            <p id='postLink'>[LINK]</p>               <div id='postContent'>[IMAGE] [CONTENT]</div>                 <div id='postFields'>[FIELDS]</div>            <p id='printNow'><a href='#print' onClick='window.print(); return false;' title='Click to print'>Print</a></p>            </div>";
					// RETURN VALUES
					return $core_admin_values;
}

// FUNCTION EXECUTED WHEN THE THEME IS CHANGED
function _after_switch_theme(){
	// SAVE VALUES
	update_option("core_admin_values",childtheme_designchanges());
}
add_action("after_switch_theme","_after_switch_theme");
// DEMO MODE
if(defined("WLT_DEMOMODE")){
	$GLOBALS["CORE_THEME"] = childtheme_designchanges();
}?>
