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

global $post, $CORE, $userdata;

ob_start();
?>

<a name="toplisting"></a>

<div class="listing-header">
  <div class="listing-header-summary v-top">
    <h1>[TITLE-NOLINK]</h1>
    <ul class="list-inline list-unstyled">
      <li class="list-item item-price">From: [price]</li>
      <li class="list-item item-contact"><a class="btn btn-danger" href="#wlt_shortcode_contactmodal_<?php the_ID(); ?>" role="button" data-toggle="modal"><i class="glyphicon glyphicon-envelope"></i> Ask a Question</a></li>
    </ul>
  </div>
  <div class="listing-header-links v-top">
    [HASVALUE key=trademe]
      <a class="btn btn-url" href="[trademe]">View on TradeMe</a>
    [/HASVALUE]
  </div>
</div>

<div class="listing-detail panel panel-default">

  [STICKER]

  <div class="panel-body">

  [IMAGES type="images"]

  <div class="clearfix"></div>

  <hr />

  [BTNBAR]

  <div class="clearfix"></div>

  <div class="pull-right hidden-xs">[DATE]</div>

  <ul class="nav nav-tabs" id="Tabs">
    <li class="active"><a href="#tab_description" data-toggle="tab">{Description}</a></li>
    <!-- <li><a href="#tab_comments" data-toggle="tab" > <?php // echo $CORE->_e(array('single','37')); ?> </a></li> -->
  </ul>

  <div class="tab-content">
    <div class="tab-pane active" id="tab_description">

      [CONTENT]

      <div class="block">
        <div class="block-title">
          <i class="fa fa-dashboard"></i> Vehicle Details
        </div>
      </div>

      <!-- [CARDETAILS hide="interior,drive"] -->
      [FIELDS hide="interior,drive,price,type,year,miles"]

      <hr /> [RELATED]  </div>

      <!-- <div class="tab-pane fade" id="tab_comments">[COMMENTS tab=0]</div> -->

    </div>
  </div>

</div>

<?php $SavedContent = ob_get_clean();

echo hook_item_cleanup($CORE->ITEM_CONTENT($post, hook_content_single_listing($SavedContent)));

?>
