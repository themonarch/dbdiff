<?php
namespace toolbox;
$this->renderViews('pre-http-header-fullpage');//hook only for main content pages (not ajax, etc.)
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport"
           content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <title><?php echo title::get()->getTitleString(); ?></title>
        <?php
        $this->renderViews('start_of_head_tag');
		?>

        <link rel="stylesheet" type="text/css" href="/assets/app/fontello/css/fontello.css">

        <link rel='shortcut icon' href="/favicon.ico" type="image/x-icon" />


		<?php
        $js_combined = array(
                '/assets/app/js/jquery.min.js',
                '/assets/app/js/jquery-ui.min.js',
                '/assets/app/js/jquery-simple-pagination-plugin.js',
                '/assets/app/js/tinysort.js',
                '/assets/app/js/app.js',
                '/assets/app/js/jquery.dataTables.min.js',
                '/assets/app/js/jquery.tipsy.js',
                '/assets/app/js/moment.min.js',
                '/assets/app/js/flot/jquery.flot.js',
                '/assets/app/js/flot/jquery.flot.resize.js',
                '/assets/app/js/flot/jquery.flot.crosshair.js',
                '/assets/app/js/flot/jquery.flot.cleanPoints.js',
                '/assets/app/js/flot/jquery.flot.navigate.js',


                '/assets/common/js/jquery.livequery.js',
                '/assets/common/js/jquery.autosize.js',
                '/assets/common/js/cookies.js',
                '/assets/common/js/jquery.stickyElements.js',
                '/assets/common/js/dropdown_v2.jquery.js',
                '/assets/common/js/jquery.overlay.js',
                '/assets/common/js/datepicker.js',
                '/assets/common/js/jquery.inview.js',
                '/assets/common/js/jquery.timeago.js',


                '/assets/app/js/datatable-databases.js',
		        '/assets/app/js/member.js'
		);

        $css_combined = array(
	        '/assets/app/css/jquery-ui.css',
	        '/assets/app/css/responsive.css',
	        '/assets/common/css/form.css',
	        '/assets/common/css/grid.css',
	        '/assets/common/css/reset.css',
	        '/assets/common/css/text.css',
	        '/assets/common/css/layout.css',
	        '/assets/common/css/messages.css',
	        '/assets/common/css/overlay.css',
	        '/assets/common/css/sidebar.css',
	        '/assets/common/css/dropdown.css',
	        '/assets/common/css/responsive.css',
	        '/assets/common/css/datepicker.css',

	        '/assets/app/css/common.css',
	        '/assets/app/css/elements.css',
	        '/assets/app/css/buttons.css',
	        '/assets/app/css/jquery.dataTables.min.css',
	        '/assets/app/css/tipsy.css',
	        //'/css/common/text.css',
	        '/assets/app/css/theme-app.css'
		);

		?>
        <script type="text/javascript" src="<?php echo utils::combined_js_include($js_combined); ?>"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo utils::combined_css_include($css_combined); ?>">
		<?php

        $this->renderViews('end_of_head_tag');
        ?>
        <script type="text/javascript">
           $(document).ready(function() {
               $('body').removeClass('loading');//in case footer fails to load
               jQuery.timeago.settings.allowFuture = true;
               $(".timeago").timeago();
               $('.tooltip').tipsy({gravity: 's'});

               $('.nav-button-dropdown').bind('mouseenter', function(){
                      $(this).parent().find('.nav-button-dropdown-contents').stop().slideToggle();
                      $(this).addClass('nav-button-active');
                    return false;
               });

               $('.nav-button-dropdown').bind('mouseleave', function(){
                      $(this).parent().find('.nav-button-dropdown-contents').stop().slideToggle();
                      $(this).removeClass('nav-button-active');
                      return false;
               });
               var nav_button_dropdown = false;
               $('.nav-button-dropdown-contents').bind('mouseenter', function(){
                      nav_button_dropdown = true;
                      $(this).stop().slideToggle();
                      $(this).parent().find('.nav-button-dropdown').addClass('nav-button-active');
                  return false;
               });
               $('.nav-button-dropdown-contents').bind('mouseleave', function(){
                      if(nav_button_dropdown === false){
                         return false;
                      }
                      nav_button_dropdown = false;
                      $(this).stop().slideToggle();
                      $(this).parent().find('.nav-button-dropdown').removeClass('nav-button-active');
                  return false;
               });
           });
        </script>
        <script type="text/javascript">
        <?php
        $this->renderViews('head_js');
        ?>
        </script>
    </head>

    <body class="loading">
        <div class="loader-full_page">
           <div>
           <div class="contents">
               &nbsp;
               <!--<div>Loading, please wait...</div>-->
           </div>
           </div>
        </div>
		<?php
			//loop pre-content modules
			$this->renderViews('post-body');
			if($render_header){ ?>
	        <div id="header-with-nav">
				<?php $this->renderViews('header-with-nav'); ?>
	           <div class="content">
	               <div class="main restrict-width centered clearfix">
	                  <span class="btn-touch sidebar_toggle menu_toggle left showMobile" style="float: left;">
	                      <i class="icon-right"></i>
	                  </span>
	                  <a class="logo" href="<?php echo utils::getHost(); ?>"><span class="name"><?php
                            echo config::getSetting('app_name'); ?></span>
                            <span class="notifications gold"
                            style="position: absolute; z-index: -1; margin: 50px 0px 0px -4px;">Beta</span></a>
	                  <div class="divider hideMobile hideTablet"></div>
	                  <span data-toggle_id="main_menu_nav" class="menu_toggle showMobile showTablet">
	                      <i class="icon-menu"></i>
	                  </span>
	                  <?php $this->renderViews('nav'); ?>
	               </div>
        	        <div class="header-line">
        	           <div class="gradient-line light"></div>
        	        </div>
                </div>
	        </div>
	        <?php } ?>
        <div class="catchall"></div>
        <?php $this->renderViews('post-body-header'); ?>
        <div id="wrapper">
           <div id="content-container">
               <div id="footer-catcher">
               <?php $this->renderViews('post-wrapper'); ?>
               <div id="content" class="">
                  <?php $this->renderViews('app-header'); ?>

                  <div id="global-container" class="<?php
                  	if(page::get()->countViews('content-narrow') > 0){ ?>restrict-width centered<?php }
                  ?>">
                  <?php $this->renderViews('pre-pre-content'); ?>
                  <div class="clear-sidebar">
                  <?php $this->renderViews('pre-header'); ?>
                  <?php title::get()->renderViews(); ?>
                  <?php $this->renderViews('pre-content'); ?>
                  <?php
                      //loop content views
                      $this->renderViews('content-narrow');
                      $this->renderViews('content');
                  ?>
                  </div>
                  </div>
                  <div class="catchall"></div>

               </div><!-- END #content -->
               <div class="catchall"></div>
               </div><!-- END #footer-catcher -->
           </div><!-- END #content-container -->
           <div class="catchall"></div>

        <?php if($render_footer){ ?>
        <div id="footer">
               <?php page::get()->renderViews('footer-pre-contents'); ?>
           <div class="contents">
               <?php page::get()->render('footer.php'); ?>
           </div>
        </div>
        <?php } ?>
        </div>
        <?php if(accessControl::get()->hasRequirement('webmaster')){ ?>
            <div class="modal-overlay" id="sql_time" style="max-width: 800px;">
                <table class="table style1">
                    <thead><tr>
                        <th style="width: 60%;">Action</th>
                        <th>Avg Time /<br> Max Time</th>
                        <th>Total Time</th>
                    </tr></thead>
                <tbody>
                <?php
                $total = 0;
                foreach (bench::getResults() as $key => $value) {
                $total += $value['total'];
                ?>
                <tr>
                    <td><span style="white-space: pre-wrap;"><?php echo $key; ?></span></td>
                    <td><?php echo round($value['avg'], 4); ?>
                       <?php if($total > 1){ ?>
                       <br>(max: <?php echo  round($value['max'], 4); ?>)
                       <?php } ?></td>
                    <td><?php echo  round($value['total'], 4); ?>
                       <br>(total: <?php echo formatter::number_commas($value['count']); ?>)</td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="2" style="text-align: right;">total</td>
                    <td><?php echo round($total, 4); ?></td>
                </tr>
                </tbody>
                </table>
            </div>
        <?php } ?>
        <?php
        $this->renderViews('before_body_end');
        ?>

        <script type="text/javascript">
           $('body').removeClass('loading');
        </script>

    </body>
</html>