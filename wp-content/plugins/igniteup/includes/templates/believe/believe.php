<?php global $the_cs_template_options; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title> <?php echo (!empty($the_cs_template_options["general_cs_page_title"]) ? $the_cs_template_options["general_cs_page_title"] : 'Almost Ready to Launch | ' . get_bloginfo('name')); ?>  </title>
        <meta charset="UTF-8">        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<?php igniteup_head(); ?>
        <style>
            a{
                color:<?php echo $the_cs_template_options['font_color']; ?> !important;
                transition: all ease 400ms;
            }            
	    <?php if (!empty($the_cs_template_options['bg_image'])): ?>
    	    body::after{
    		content: '';
    		background: url('<?php echo $the_cs_template_options['bg_image']; ?>') !important;
    		opacity: 0.5;
    		top: 0px;
    		left: 0px;
    		bottom: 0px;
    		right: 0px;
    		position: fixed;
    		z-index: -1 !important;                    
    		background-size:cover;
    	    }
    	    body{
    		background: #000 !important;
    	    }
	    <?php endif; ?>
        </style>	
    </head>
    <body style="background: <?php echo $the_cs_template_options['bg_color']; ?>; color:<?php echo $the_cs_template_options['font_color']; ?> !important;">
        <div class="container-fluid main-container">
            <div class="row">
                <div class="col-sm-8">
                    <div class='logo'><?php
			$logo = $the_cs_template_options['logo'];
			?>
                        <img src="<?php echo $logo; ?>" class="img-responsive"></div>
                    <div class="">
                        <p class="title-top">
			    <?php echo $the_cs_template_options['title_top']; ?>
                        </p>
                        <p class="title-bottom">
			    <?php echo $the_cs_template_options['main_title']; ?>
                        </p>
                        <p class="paragraph"><?php echo $the_cs_template_options['paragraph']; ?>
                        </p>
                    </div>
                    <ul class="social-icon">
			<?php
			$twitter = $the_cs_template_options['common_social_twitter'];
			$facebook = $the_cs_template_options['common_social_facebook'];
			$pinterest = $the_cs_template_options['common_social_pinterest'];
			$gplus = $the_cs_template_options['common_social_googleplus'];
			$youtube = $the_cs_template_options['common_social_youtube'];
			$instagram = $the_cs_template_options['common_social_instagram'];
			$behance = $the_cs_template_options['common_social_behance'];
			$linkedin = $the_cs_template_options['common_social_linkedin'];
			?>
                        <li class = "<?php echo empty($twitter) ? 'hidden' : ''; ?>"><a href = "<?php echo $twitter; ?>" target = "_blank"><span class = "fa fa-twitter"></span></a></li>
                        <li class = "<?php echo empty($facebook) ? 'hidden' : ''; ?>"><a href = "<?php echo $facebook; ?>" target = "_blank"><span class = "fa fa-facebook"></span></a></li>
                        <li class = "<?php echo empty($gplus) ? 'hidden' : ''; ?>"><a href = "<?php echo $gplus; ?>" target = "_blank"><span class = "fa fa-google-plus"></span></a></li>
                        <li class = "<?php echo empty($pinterest) ? 'hidden' : ''; ?>"><a href = "<?php echo $pinterest; ?>" target = "_blank"><span class = "fa fa-pinterest"></span></a></li>
                        <li class = "<?php echo empty($youtube) ? 'hidden' : ''; ?>"><a href = "<?php echo $youtube; ?>" target = "_blank"><span class = "fa fa-youtube"></span></a></li>
                        <li class = "<?php echo empty($instagram) ? 'hidden' : ''; ?>"><a href = "<?php echo $instagram; ?>" target = "_blank"><span class = "fa fa-instagram"></span></a></li>
                        <li class = "<?php echo empty($behance) ? 'hidden' : ''; ?>"><a href = "<?php echo $behance; ?>" target = "_blank"><span class = "fa fa-behance"></span></a></li>
                        <li class = "<?php echo empty($linkedin) ? 'hidden' : ''; ?>"><a href = "<?php echo $linkedin; ?>" target = "_blank"><span class = "fa fa-linkedin"></span></a></li>
                    </ul>                 
                </div>   
                <div id="plane" class="hidden-xs <?php echo (($the_cs_template_options['plane']) == 1) ? '' : 'hidden' ?>">
                    <img src="<?php echo plugins_url('images/plane.png', __FILE__); ?>" class="img-responsive">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
		    <?php
		    $powered_by = $the_cs_template_options['general_powered_by'];
		    if ($powered_by == 1) {
			$class = "visible";
		    } else {
			$class = "hidden";
		    }
		    ?> 
                    <div class="<?php echo $class; ?> text-center" id="powered-by">                        
                        Powered by <a href="https://wordpress.org/plugins/igniteup/" target="_blank">IgniteUp</a>
                    </div>
                </div>
            </div>
        </div>
	<?php igniteup_footer(); ?>
    </body>
</html>
