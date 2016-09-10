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

 global $CORE, $userdata;

if(!isset($GLOBALS['wlt_remove_body'])){

?>

        <?php if(!isset($GLOBALS['flag-custom-homepage'])): ?>

        <?php echo $CORE->BANNER('middle_bottom'); ?>

       </div></article>

        <?php if(!isset($GLOBALS['nosidebar-left'])): ?>

          <?php get_template_part( 'sidebar', 'left' ); ?>

        <?php endif; ?>


        <?php if(!isset($GLOBALS['nosidebar-right'])): ?>

          <?php get_template_part( 'sidebar', 'right' ); ?>

        <?php endif; ?>

        <?php hook_core_columns_after(); ?>

    <?php endif; ?>

    </div>

    </div>

	</div>

	</div>

</div>

<?php hook_container_after(); ?>

<div class="container">
  <div class="row visible-xs">
    <div class="col-xs-12">
      <ul class="contact-option list-unstyled">
        <li class="list-item contact-item contact-item-primary"><a class="btn btn-primary btn-lg btn-block" href="/contact"><span class="contact-option-title">Sell with Us</span></a></li>
        <li class="list-item contact-item"><a class="btn btn-primary btn-lg btn-block" href="mailto:mitchigansmith@gmail.com"><i class="glyphicon glyphicon-envelope"></i> <span class="contact-option-title">Email Us</span></a></li>
        <li class="list-item contact-item"><a class="btn btn-primary btn-lg btn-block" href="tel:0272530888"><i class="glyphicon glyphicon-earphone"></i> <span class="contact-option-title">Call Us</span></a></li>
        <li class="list-item contact-item"><a class="btn btn-primary btn-lg btn-block" href="sms:0272530888"><i class="glyphicon glyphicon-phone"></i> <span class="contact-option-title">Text Us</span></a></li>
      </ul>
    </div>
  </div>
</div>

<?php hook_footer(_design_footer());?>

</div>

<?php hook_wrapper_after(); ?>

<?php } // remove body ?>

<div id="core_footer_ajax"></div>

<?php wp_footer(); ?>

</body>

</html>
