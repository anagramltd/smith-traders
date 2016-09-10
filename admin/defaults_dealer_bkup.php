<?php



function _hook_header_style6(){

// GET DISPLAY DATA
$core_admin_values = get_option("core_admin_values");  
$st = $core_admin_values['shoptop'];

ob_start();
?> 
 
<div class="col-md-5 col-sm-5 col-xs-12" id="core_logo">

<a href="<?php echo get_home_url(); ?>/" title="<?php echo get_bloginfo('name'); ?>"><?php echo hook_logo(true); ?></a></div>

<div class="col-md-7 col-sm-7 hidden-xs">

<div class="menuitems">

            <div class="row">
               
                <div class="col-md-6 text-center hidden-sm">
                	<div class="text-area icon2"><h3><?php echo stripslashes($st[1]['t']); ?></h3><p><?php echo stripslashes($st[1]['d']); ?></p></div>
                </div>
                
                <div class="col-md-6 text-center hidden-sm">
                  
                  <div class="text-area icon1"><h3><?php echo stripslashes($st[2]['t']); ?></h3><p><?php echo stripslashes($st[2]['d']); ?></p></div>
                  
                </div> 
                
            </div>
        </div>
          
</div>
<?php
return ob_get_clean();
} 
add_action('hook_header_style6','_hook_header_style6');


 add_action('hook_admin_1_topnav_top', '_hook_admin_1_topnav_top');

function _hook_admin_1_topnav_top(){

$core_admin_values = get_option("core_admin_values");  

// ONLY SHOW IF FULL HTML LAYOUT IS SELECTED
if($core_admin_values['layout_header'] != 6){ return; }

$default = array(

"1" => array("t" => "BUY &amp; SELL CARS ONLINE ", "d" => "Signup today and list your vehicle." ),
"2" => array("t" => "CALL NOW: 1234-1214-123", "d" => "Speak to one of our sales team today!" ),
 
);

// GET DATA
$st = $core_admin_values['shoptop']; 

ob_start();
?>


<div class="heading2">Header Text (opposite logo) </div>
  
  
<?php foreach($default as $key => $t){ ?>
<div class="form-row control-group row-fluid">
	<label class="control-label span4">Box <?php echo $key; ?></label>
    <div class="controls span7">
    <p>Title</p>
    <input name="admin_values[shoptop][<?php echo $key; ?>][t]"  type="text" value="<?php if(isset($st[$key]['t']) && strlen($st[$key]['t']) > 0){ echo stripslashes($st[$key]['t']); }else{ echo $t['t']; } ?>" class="row-fluid">
    <p>Description</p>
    <input name="admin_values[shoptop][<?php echo $key; ?>][d]"  type="text" value="<?php if(isset($st[$key]['d']) && strlen($st[$key]['d']) > 0){ echo stripslashes($st[$key]['d']); }else{ echo $t['d']; }  ?>" class="row-fluid">
      
    </div>                      
</div>
<?php } ?>

<?php
echo ob_get_clean(); 
}








class core_dealer extends white_label_themes {

	function core_dealer(){ global $wpdb;	
 		
	// REGISTER TAXONOMIES
	register_taxonomy( 'make', THEME_TAXONOMY.'_type', array( 'hierarchical' => true, 'labels' => array('name' => 'Make') , 'query_var' => true, 'rewrite' => true, 'rewrite' => array('slug' => 'make') ) ); 		
	register_taxonomy( 'model', THEME_TAXONOMY.'_type', array( 'hierarchical' => true, 'labels' => array('name' => 'Model') , 'query_var' => true, 'rewrite' => true, 'rewrite' => array('slug' => 'model') ) );  
		 
	// ADD FIELDS TO THE ADMIN
	add_action('hook_fieldlist_0', array($this, '_hook_adminfields' ) );
	
	// ADD IN NEW CUSTOM FIELDS
	add_action('hook_add_fieldlist',  array($this, '_hook_customfields' ) );
	
	// ADD IN EXTRA FIELDS FOR SHORTCODES
	add_action('hook_item_pre_code', array($this, 'wlt_data_display') );
		 
	// SHORTCODES	
	add_shortcode( 'MAKERS', array($this,'wlt_shortcode_makes') );
	add_shortcode( 'CARTYPES', array($this,'wlt_shortcode_cartypes') );
	add_shortcode( 'CARDETAILS', array($this,'wlt_shortcode_cardetails') );
	add_shortcode( 'CARSTATUS', array($this,'wlt_shortcode_carstatus') );
	
		// ADD IN FILED LIST
	add_action('hook_shortcode_fields_show', array($this, '_showfields' ) ); 
		
	// ADDIN TO LIST
	add_action('hook_shortcodelist',array($this, '_hook_shortcodelist') );	
    }
	
	function _hook_shortcodelist($c){
	
	return array_merge($c,array(
	'MAKERS' => array('desc' => 'List of all car makes', 'type' => 'inner'),
	'CARTYPES' => array('desc' => 'Listing of all car types', 'type' => 'inner'),
	'CARDETAILS' => array('desc' => 'Table of car details', 'type' => 'inner', 'singleonly' => true),
	));
	
	}
	
	function _showfields($c){ global $CORE, $post; $STRING = ""; $THISPOSTID = $post->ID;
	
		// DATA
		$fields = array(
		"cstatus" 		=> $CORE->_e(array('dealer','23')),
		"make" 			=> $CORE->_e(array('dealer','1')),
		"model" 		=> $CORE->_e(array('dealer','2')),
		"ctype" 		=> $CORE->_e(array('dealer','3')),
		"price" 		=> $CORE->_e(array('checkout','2')),
		"year" 			=> $CORE->_e(array('dealer','6')),
		"miles" 		=> $CORE->_e(array('dealer','7')),
		"exterior" 		=> $CORE->_e(array('dealer','8')),
		"interior" 		=> $CORE->_e(array('dealer','9')),
		"drive" 		=> $CORE->_e(array('dealer','10')),
		"transmission" 	=> $CORE->_e(array('dealer','13')),
		"petrol" 		=> $CORE->_e(array('dealer','18')),		
		);
		foreach($fields as $k => $t){		
			
			// ODD EVENE
			if($i%2){ $ec = "odd"; }else{ $ec = "even"; }
		
			switch($k){
			
				case "make": {
			 
					// GET LIST
					$LIST = get_the_term_list( $THISPOSTID, 'make', "", ', ', '' );
					if(strlen($LIST) > 1){
					// ADD ON CATEGORY
					 if($i%2){ $ec = "odd"; }else{ $ec = "even"; }	 $i++;
					$STRING .= '<tr class="'.$ec.'">
					<td>'.$CORE->_e(array('dealer','1')).'</td>
					<td>'.$LIST.'</td>				
					</tr>';
					}
				
				} break;
				
				case "model": {
			 
					// GET LIST
					$LIST = get_the_term_list( $THISPOSTID, 'model', "", ', ', '' );
					if(strlen($LIST) > 1){
					// ADD ON CATEGORY
					 
					$STRING .= '<tr class="'.$ec.'">
					<td>'.$CORE->_e(array('dealer','2')).'</td>
					<td>'.$LIST.'</td>				
					</tr>';
					}
				
				} break;
				
				default: {
					
					$val = get_post_meta($THISPOSTID, $k, true);
					
					if($k == "drive"){					
						$val1 = array("1" => $CORE->_e(array('dealer','11')), "2" => $CORE->_e(array('dealer','12')) );
						$val = $val1[$val];					
					
					}elseif($k == "transmission"){					
						$val1 = array("1" => $CORE->_e(array('dealer','14')), "2" => $CORE->_e(array('dealer','15')), "3" => $CORE->_e(array('dealer','16')), "4" => $CORE->_e(array('dealer','17')) );
						$val = $val1[$val];	
										
					}elseif($k == "petrol"){					
						$val1 = array("1" => $CORE->_e(array('dealer','19')), "2" => $CORE->_e(array('dealer','20')), "3" => $CORE->_e(array('dealer','21')) );
						$val = $val1[$val];	
										
					}elseif($k == "ctype"){					
						$val1 = array("2" => $CORE->_e(array('dealer','t2')), "3" => $CORE->_e(array('dealer','t3')), "4" => $CORE->_e(array('dealer','t4')), "5" => $CORE->_e(array('dealer','t5')), "6" => $CORE->_e(array('dealer','t6')), "7" => $CORE->_e(array('dealer','t7')) , "8" => $CORE->_e(array('dealer','t8')) , "9" => $CORE->_e(array('dealer','t9')) , "10" => $CORE->_e(array('dealer','t10')),  "12" => $CORE->_e(array('dealer','t12'))  );
						$val = $val1[$val];	
										
					}elseif($k == "cstatus"){					
						$val1 = array("1" => $CORE->_e(array('dealer','24')), "2" => $CORE->_e(array('dealer','25')), "3" => $CORE->_e(array('dealer','26'))  );
						$val = $val1[$val];					
					}
					
					$STRING .= '<tr class="'.$ec.'">
					<td>'.$t.'</td>
					<td>'.$val.'</td>
					</tr>';
				
				
				} break;
			
			} // end switch
			
			$i++;		
		
		}// end foreach
					
	return $c.$STRING;
 	
	}
	function wlt_data_display($c){ global $post, $CORE;
 
	// MILES
	if(strpos($c, "[miles]") != false){
		
		$miles = get_post_meta($post->ID, 'miles', true);
		if($miles != ""){
		$miles = str_replace(",","",$miles);
		$c = str_replace("[miles]","<span class='wlt_shortcode_miles'>". number_format($miles)." ".$CORE->_e(array('widgets','15'))."</span>", $c);
		
		}
	}

	// MILES
	if(strpos($c, "[miles-data]") != false){
		
		$miles = get_post_meta($post->ID, 'miles', true);
		if($miles != ""){
		$miles = str_replace(",","",$miles);
		$c = str_replace("[miles-data]", number_format($miles), $c);		
		}
	}
	
	
	// DRIVE
	if(strpos($c, "[drive]") != false){
		
		$val = get_post_meta($post->ID, 'drive', true);
		$val1 = array("1" => $CORE->_e(array('dealer','11')), "2" => $CORE->_e(array('dealer','12')) );
		$val = $val1[$val];
	
		$c = str_replace("[drive]", "<span class='wlt_shortcode_drive'>".$val."</span>", $c);
	}	
	
	// transmission
	if(strpos($c, "[transmission]") != false){
		
		$val = get_post_meta($post->ID, 'transmission', true);
		$val1 = array("1" => $CORE->_e(array('dealer','14')), "2" => $CORE->_e(array('dealer','15')), "3" => $CORE->_e(array('dealer','16')), "4" => $CORE->_e(array('dealer','17')) );
		$val = $val1[$val];
	
		$c = str_replace("[transmission]", "<span class='wlt_shortcode_transmission'>".$val."</span>", $c);
	}	
	
	// petrol
	if(strpos($c, "[petrol]") != false){
		
		$val = get_post_meta($post->ID, 'petrol', true);
		$val1 = array("1" => $CORE->_e(array('dealer','19')), "2" => $CORE->_e(array('dealer','20')), "3" => $CORE->_e(array('dealer','21')) );
		$val = $val1[$val];
	
		$c = str_replace("[petrol]", "<span class='wlt_shortcode_petrol'>".$val."</span>", $c);
	}	
	
	// petrol
	if(strpos($c, "[ctype]") != false){
		
		$val = get_post_meta($post->ID, 'ctype', true);
		$val1 = array("2" => $CORE->_e(array('dealer','t2')), "3" => $CORE->_e(array('dealer','t3')), "4" => $CORE->_e(array('dealer','t4')), "5" => $CORE->_e(array('dealer','t5')), "6" => $CORE->_e(array('dealer','t6')), "7" => $CORE->_e(array('dealer','t7')) , "8" => $CORE->_e(array('dealer','t8')) , "9" => $CORE->_e(array('dealer','t9')) , "10" => $CORE->_e(array('dealer','t10')),  "12" => $CORE->_e(array('dealer','t12'))  );
		$val = $val1[$val];
	
		$c = str_replace("[ctype]", "<span class='wlt_shortcode_ctype'>".$val."</span>", $c);
	}		
 
	
	return $c;
	
	
	}
	
	// ADD IN CORE FIELDS TO THE ADMIN
	function _hook_adminfields($c){ global $CORE;
	
		$CORE->Language();
		
		// DATA
		$fields = array(
		
		"tab4" => array("tab" => true, "title" => "Dealer Theme Extras" ),		
		"cstatus" 		=> array("label" => $CORE->_e(array('dealer','23')),  "values" => array("1" => $CORE->_e(array('dealer','24')), "2" => $CORE->_e(array('dealer','25')), "3" => $CORE->_e(array('dealer','26')) ) ),
		"ctype" 		=> array("label" => $CORE->_e(array('dealer','3')), "values" => array("2" => $CORE->_e(array('dealer','t2')), "3" => $CORE->_e(array('dealer','t3')), "4" => $CORE->_e(array('dealer','t4')), "5" => $CORE->_e(array('dealer','t5')), "6" => $CORE->_e(array('dealer','t6')), "7" => $CORE->_e(array('dealer','t7')) , "8" => $CORE->_e(array('dealer','t8')) , "9" => $CORE->_e(array('dealer','t9')) , "10" => $CORE->_e(array('dealer','t10')),  "12" => $CORE->_e(array('dealer','t12'))  ) ),
		"price" 		=> array("label" => $CORE->_e(array('checkout','2')) ),
		"year" 			=> array("label" => $CORE->_e(array('dealer','6')) ),
		"miles" 		=> array("label" => $CORE->_e(array('dealer','7')) ),
		"exterior" 		=> array("label" => $CORE->_e(array('dealer','8')) ),
		"interior" 		=> array("label" => $CORE->_e(array('dealer','9')) ),
		"drive" 		=> array("label" => $CORE->_e(array('dealer','10')), "values" => array("1" => $CORE->_e(array('dealer','11')), "2" => $CORE->_e(array('dealer','12')) ) ),
		"transmission" 	=> array("label" => $CORE->_e(array('dealer','13')), "values" => array("1" => $CORE->_e(array('dealer','14')), "2" => $CORE->_e(array('dealer','15')), "3" => $CORE->_e(array('dealer','16')), "4" => $CORE->_e(array('dealer','17')) ) ),
		"petrol" 		=> array("label" => $CORE->_e(array('dealer','18')), "values" => array("1" => $CORE->_e(array('dealer','19')), "2" => $CORE->_e(array('dealer','20')), "3" => $CORE->_e(array('dealer','21')) ) ),		
		"engine" 		=> array("label" => $CORE->_e(array('dealer','22')) ),
		);
 
	 
	return array_merge($c,$fields);
	}
	
	// ADD IN FRONT END FIELDS
	function _hook_customfields($c){ global $CORE;
	
		$o = 50;
		
 
		$c[$o]['title'] 			= $CORE->_e(array('dealer','23'));
		$c[$o]['type'] 				= "select";
		$c[$o]['name']				= "cstatus";
		$c[$o]['listvalues']		= array("1" => $CORE->_e(array('dealer','24')), "2" => $CORE->_e(array('dealer','25')), "3" => $CORE->_e(array('dealer','26'))  );
		$c[$o]['class'] 			= "form-control";		 
		$o++;

		$c[$o]['title'] 			= $CORE->_e(array('dealer','1'));
		$c[$o]['type'] 				= "taxonomy";
		$c[$o]['taxonomy']			= "make";
		$c[$o]['taxonomy_link']		= "model";
		$c[$o]['class'] 			= "form-control";		 
		$o++;
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','2'));
		$c[$o]['type'] 				= "taxonomy";
		$c[$o]['taxonomy']			= "model";
		$c[$o]['class'] 			= "form-control";		 
		$o++;
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','3'));
		$c[$o]['type'] 				= "select";
		$c[$o]['name']				= "ctype";
		$c[$o]['listvalues'] 		= array("2" => $CORE->_e(array('dealer','t2')), "3" => $CORE->_e(array('dealer','t3')), "4" => $CORE->_e(array('dealer','t4')), "5" => $CORE->_e(array('dealer','t5')), "6" => $CORE->_e(array('dealer','t6')), "7" => $CORE->_e(array('dealer','t7')) , "8" => $CORE->_e(array('dealer','t8')) , "9" => $CORE->_e(array('dealer','t9')) , "10" => $CORE->_e(array('dealer','t10')),  "12" => $CORE->_e(array('dealer','t12'))  );
		$c[$o]['class'] 			= "form-control";		 
		$o++;	
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','4'));
		$c[$o]['type'] 				= "title"; 
		$o++;
				
		$c[$o]['title'] 			= $CORE->_e(array('checkout','2'));
		$c[$o]['type'] 				= "price";
		$c[$o]['name']				= "price";
		$c[$o]['class'] 			= "form-control";		 
		$o++;
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','5'));
		$c[$o]['type'] 				= "title"; 
		$o++;
		
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','6'));
		$c[$o]['type'] 				= "text";
		$c[$o]['name']				= "year";
		$c[$o]['class'] 			= "form-control";		 
		$o++;
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','7'));
		$c[$o]['type'] 				= "text";
		$c[$o]['name']				= "miles";
		$c[$o]['class'] 			= "form-control";		 
		$o++;	
	 
		$c[$o]['title'] 			= $CORE->_e(array('dealer','8'));
		$c[$o]['type'] 				= "text";
		$c[$o]['name']				= "exterior";
		$c[$o]['class'] 			= "form-control";		 
		$o++;	
		
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','9'));
		$c[$o]['type'] 				= "text";
		$c[$o]['name']				= "interior";
		$c[$o]['class'] 			= "form-control";		 
		$o++;	
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','22'));
		$c[$o]['type'] 				= "text";
		$c[$o]['name']				= "engine";
		$c[$o]['class'] 			= "form-control";		 
		$o++;	
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','10'));
		$c[$o]['type'] 				= "select";
		$c[$o]['name']				= "drive";
		$c[$o]['listvalues'] 		= array("1" => $CORE->_e(array('dealer','11')), "2" => $CORE->_e(array('dealer','12')) );
		$c[$o]['class'] 			= "form-control";		 
		$o++;
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','13'));
		$c[$o]['type'] 				= "select";
		$c[$o]['name']				= "transmission";
		$c[$o]['listvalues'] 		= array("1" => $CORE->_e(array('dealer','14')), "2" => $CORE->_e(array('dealer','15')), "3" => $CORE->_e(array('dealer','16')), "4" => $CORE->_e(array('dealer','17')) );
		$c[$o]['class'] 			= "form-control";		 
		$o++;		
		
		$c[$o]['title'] 			= $CORE->_e(array('dealer','18'));
		$c[$o]['type'] 				= "select";
		$c[$o]['name']				= "petrol";
		$c[$o]['listvalues'] 		= array("1" => $CORE->_e(array('dealer','19')), "2" => $CORE->_e(array('dealer','20')), "3" => $CORE->_e(array('dealer','21')) );
		$c[$o]['class'] 			= "form-control";		 
		$o++;		
		
		return $c;
		
		}


function wlt_shortcode_carstatus($atts, $content = null){  global $post, $CORE;
	
		extract( shortcode_atts( array('id' => ''  ), $atts ) );
		
		// GET STATUS
		$cstatus = get_post_meta($post->ID,'cstatus',true);
		
		$val = array("" => $CORE->_e(array('dealer','24')), "1" => $CORE->_e(array('dealer','24')), "2" => $CORE->_e(array('dealer','25')), "3" => $CORE->_e(array('dealer','26'))  );
		
		return "<span class='wlt_shortcode_carstatus'>".$val[$cstatus]."</span>"; 
}


	function wlt_shortcode_cardetails($atts, $content = null){  global $userdata, $CORE, $post; $STRING = "";
	
		extract( shortcode_atts( array('id' => ''  ), $atts ) );	
		
		// POST ID
		if($id == ""){ $THISPOSTID = $post->ID; }else{  $THISPOSTID = $id; }
		
		// WRAPPER
		$STRING .= '<table class="table table-bordered" id="TableCustomFields">';		
		
		// DATA
		$fields = array(
		"cstatus" 		=> $CORE->_e(array('dealer','23')),
		"make" 			=> $CORE->_e(array('dealer','1')),
		"model" 		=> $CORE->_e(array('dealer','2')),
		"ctype" 		=> $CORE->_e(array('dealer','3')),
		"price" 		=> $CORE->_e(array('checkout','2')),
		"year" 			=> $CORE->_e(array('dealer','6')),
		"miles" 		=> $CORE->_e(array('dealer','7')),
		"exterior" 		=> $CORE->_e(array('dealer','8')),
		"interior" 		=> $CORE->_e(array('dealer','9')),
		"drive" 		=> $CORE->_e(array('dealer','10')),
		"transmission" 	=> $CORE->_e(array('dealer','13')),
		"petrol" 		=> $CORE->_e(array('dealer','18')),		
		);
		foreach($fields as $k => $t){		
			
			// ODD EVENE
			if($i%2){ $ec = "odd"; }else{ $ec = "even"; }
		
			switch($k){
			
				case "make": {
			 
					// GET LIST
					$LIST = get_the_term_list( $THISPOSTID, 'make', "", ', ', '' );
					if(strlen($LIST) > 1){
					// ADD ON CATEGORY
					 if($i%2){ $ec = "odd"; }else{ $ec = "even"; }	 $i++;
					$STRING .= '<tr class="'.$ec.'">
					<td>'.$CORE->_e(array('dealer','1')).'</td>
					<td>'.$LIST.'</td>				
					</tr>';
					}
				
				} break;
				
				case "model": {
			 
					// GET LIST
					$LIST = get_the_term_list( $THISPOSTID, 'model', "", ', ', '' );
					if(strlen($LIST) > 1){
					// ADD ON CATEGORY
					 
					$STRING .= '<tr class="'.$ec.'">
					<td>'.$CORE->_e(array('dealer','2')).'</td>
					<td>'.$LIST.'</td>				
					</tr>';
					}
				
				} break;
				
				default: {
					
					$val = get_post_meta($THISPOSTID, $k, true);
					
					if($k == "drive"){					
						$val1 = array("1" => $CORE->_e(array('dealer','11')), "2" => $CORE->_e(array('dealer','12')) );
						$val = $val1[$val];					
					
					}elseif($k == "transmission"){					
						$val1 = array("1" => $CORE->_e(array('dealer','14')), "2" => $CORE->_e(array('dealer','15')), "3" => $CORE->_e(array('dealer','16')), "4" => $CORE->_e(array('dealer','17')) );
						$val = $val1[$val];	
										
					}elseif($k == "petrol"){					
						$val1 = array("1" => $CORE->_e(array('dealer','19')), "2" => $CORE->_e(array('dealer','20')), "3" => $CORE->_e(array('dealer','21')) );
						$val = $val1[$val];	
										
					}elseif($k == "ctype"){					
						$val1 = array("2" => $CORE->_e(array('dealer','t2')), "3" => $CORE->_e(array('dealer','t3')), "4" => $CORE->_e(array('dealer','t4')), "5" => $CORE->_e(array('dealer','t5')), "6" => $CORE->_e(array('dealer','t6')), "7" => $CORE->_e(array('dealer','t7')) , "8" => $CORE->_e(array('dealer','t8')) , "9" => $CORE->_e(array('dealer','t9')) , "10" => $CORE->_e(array('dealer','t10')),  "12" => $CORE->_e(array('dealer','t12'))  );
						$val = $val1[$val];	
										
					}elseif($k == "cstatus"){					
						$val1 = array("" => $CORE->_e(array('dealer','24')), "1" => $CORE->_e(array('dealer','24')), "2" => $CORE->_e(array('dealer','25')), "3" => $CORE->_e(array('dealer','26'))  );
						$val = $val1[$val];					
					}
					
					$STRING .= '<tr class="'.$ec.'">
					<td>'.$t.'</td>
					<td>'.$val.'</td>
					</tr>';
				
				
				} break;
			
			} // end switch
			
			$i++;		
		
		}// end foreach
		
		$STRING .= '</table>';
		
		// RETURN
		return $STRING;
	}

	
	function wlt_shortcode_cartypes($atts, $content = null){  global $userdata, $CORE, $post; $STRING = "";
	
	extract( shortcode_atts( array('id' => '', 'show' => 100 ), $atts ) );	
	
	
	$STRING .= "<ul class='makerslist'>";
	
	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=2&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/2.png' alt='".$CORE->_e(array('dealer','t2'))."'> </div> <span>".$CORE->_e(array('dealer','t2'))."</span> </a></li>";	
	
	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=3&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/3.png' alt='".$CORE->_e(array('dealer','t3'))."'> </div> <span>".$CORE->_e(array('dealer','t3'))."</span> </a></li>";	
	
	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=4&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/4.png' alt='".$CORE->_e(array('dealer','t4'))."'> </div> <span>".$CORE->_e(array('dealer','t4'))."</span> </a></li>";	

	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=6&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/6.png' alt='".$CORE->_e(array('dealer','t6'))."'> </div> <span>".$CORE->_e(array('dealer','t6'))."</span> </a></li>";	

	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=7&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/7.png' alt='".$CORE->_e(array('dealer','t7'))."'> </div> <span>".$CORE->_e(array('dealer','t7'))."</span> </a></li>";	

	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=8&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/8.png' alt='".$CORE->_e(array('dealer','t8'))."'> </div> <span>".$CORE->_e(array('dealer','t8'))."</span> </a></li>";	

	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=12&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/12.png' alt='".$CORE->_e(array('dealer','t12'))."'> </div> <span>".$CORE->_e(array('dealer','t12'))."</span> </a></li>";	

	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=9&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/9.png' alt='".$CORE->_e(array('dealer','t9'))."'> </div> <span>".$CORE->_e(array('dealer','t9'))."</span> </a></li>";	

	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=10&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/10.png' alt='".$CORE->_e(array('dealer','t10'))."'> </div> <span>".$CORE->_e(array('dealer','t10'))."</span> </a></li>";		
	
	$STRING .= "<li><a href='".home_url()."/?s=&s=&ctype=5&advanced_search=yes'><div class='logo'> <img src='".ACTIVE_THEME_URI."/cars/5.png' alt='".$CORE->_e(array('dealer','t5'))."'> </div> <span>".$CORE->_e(array('dealer','t5'))."</span> </a></li>";
		
	$STRING .= "</ul><div class='clearfix'></div>";		
	
	return $STRING;
	
	
	}
	
	
		
	function wlt_shortcode_makes($atts, $content = null){  global $userdata, $CORE, $post; $STRING = "";
	
	extract( shortcode_atts( array('id' => '', 'show' => 100 ), $atts ) );	
	
$args = array(
				  'taxonomy'     => 'make',
				  'orderby'      => 'count',
				  'order'		=> 'desc',
				  'show_count'   => 0,
				  'pad_counts'   => 1,
				  'hierarchical' => 0,
				  'title_li'     => '',
				  'hide_empty'   => 0,				 
				);	
				
				 
				$categories = get_categories($args);  $counter = 1;
				
				if(!empty($categories)){
				
				$STRING .= "<ul class='makerslist'>";
				foreach ($categories as $category) { 
					// HIDE PARENT
					if($category->parent != 0){ continue; }
					if($counter > $show){ continue; }
					
					// GET DETAILS
					$LINK 	= get_term_link($category->slug, 'make');
					$NAME 	= $category->name;
					$COUNT 	= $category->count;
					$LOGO 	= "";
					
					// 
					if(isset($GLOBALS['CORE_THEME']['category_icon_'.$category->term_id]) && strlen($GLOBALS['CORE_THEME']['category_icon_'.$category->term_id]) > 1){
										
					$LOGO = "<img src='".$GLOBALS['CORE_THEME']['category_icon_'.$category->term_id]."' alt='logo' >";					
					
					}			
					
					$STRING .= "<li> 
					<a href='".$LINK."'><div class='logo'> ".$LOGO." </div></a>
					</li>";	
					
					/*
						<div class='col-md-10'><a href='".$LINK."'>".$NAME."</a></div>
					<div class='col-md-1'>".$COUNT."</div>
					*/
					
					$counter++;	
					
				}// end foreach				
				$STRING .= "</ul><div class='clearfix'></div>";				
				}// end if 
				
		return $STRING;
	}

}

?>