<?php
	//turn this on in production mode to force sign in
	if ((!isset($_SESSION['person'])) && (isset($dashboard))) {
		header ('Location: ./index.php');
		exit;
	}
?>
<!doctype html>
<!--[if lt IE 7 ]> <html class="ie ie6 no-js" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" lang="en"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
	<?php 
		include './Mobile_Detect.php';
		$detect = new Mobile_Detect();
		
		if ($detect->isMobile()) {
		// Any mobile device.
		$mobile="";
		}
	?>
	<meta charset="utf-8">
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<!-- Mobile Check to display different title-->
		<?php if(isset($mobile)): ?>
		<title>Mobile Locizzle | Inspection Connection</title>
		<?php endif; ?>	
		<?php if(!isset($mobile)): ?>
		<title>Locizzle | Inspection Connection</title>
		<?php endif; ?>	
	<!-- END Mobile Check -->
	<meta name="title" content="Locizzle.com">
	<meta name="robots" content="index, follow">
	<link rel="canonical" href="https://www.locizzle.com"/>
	
	<!-- Google will often use this as its description of the page/site. Make it good. -->
	<meta name="description" content="Locizzle provides brokers, agents and assitance with the ability to schedule inspections in a matter of seconds. No more phone tag with multiple people, and no more mistakes!">
	
	<meta name="google-site-verification" content="">
	<!-- Google WEB FONTS. -->
	<link href='//fonts.googleapis.com/css?family=Oleo+Script+Swash+Caps' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Roboto:500italic' rel='stylesheet' type='text/css'>
	
	<!-- Speaking of Google, don't forget to set the site up: http://google.com/webmasters -->
	<meta name="copyright" content="Copyright Locizzle.com 2013. All Rights Reserved.">
	<link rel="shortcut icon" href="./img/favicon.ico">
	<link rel="apple-touch-icon" href="./img/apple-touch-icon.png">
	
	<!-- CSS -->
	<link rel="stylesheet" href="./css/reset.css">
	<link rel="stylesheet" href="./css/style.css">
	<link rel="stylesheet" href="./css/dashboard.css">
	
	<!-- jQuery -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	
	<script type="text/javascript">
		 
		//script to expand and collapse h2-parent and h3-child
		$(document).ready(function() {
			var parentDivs = $('#nestedAccordion > div'),
				childDivs = $('#nestedAccordion h3').siblings('div').slideUp();
			$(".sub-accordion").hide();
			
			$('#nestedAccordion h2').click(function(){
				parentDivs.slideUp().parent().find('span.sign').html('+');
				if($(this).next().is(':hidden')){
					$(this).next().slideDown();
					$(this).find('span.sign').html('-');
				}else{
					$(this).next().slideUp();
					$(this).find('span.sign').html('+');	
				}
			});
			$('#nestedAccordion h3').click(function(){
				childDivs.slideUp();
				if($(this).next().is(':hidden')){
					$(this).next().slideDown();
					$(this).find('span.sign').html('-');
					
				}else{
					$(this).next().slideUp();
					$(this).find('span.sign').html('+');	
				}
			});
			
		});
	</script>
	<!--FIX PLACEHOLDER ISSUE IN UNSUPPORTED BROWSERS-->
	<script>
	(function($) {
	  $.fn.placeholder = $(document).ready(function() {
	    if(typeof document.createElement("input").placeholder == 'undefined') {
	      $('[placeholder]').focus(function() {
	        var input = $(this);
	        if (input.val() == input.attr('placeholder')) {
	          input.val('');
			  input.removeClass('placeholder');
        }
	      }).blur(function() {
	        var input = $(this);
	        if (input.val() == '' || input.val() == input.attr('placeholder')) {
	          input.addClass('placeholder');
	          input.val(input.attr('placeholder'));
	        }
	      }).blur().parents('form').submit(function() {
	        $(this).find('[placeholder]').each(function() {
	          var input = $(this);
	          if (input.val() == input.attr('placeholder')) {
	            input.val('');
	          }
	      })
	    });
	  }
	});
	})(jQuery);
	</script>
	
	<!-- PrettyPhoto -->
	<link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
	<script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
	
	<!-- CSS GRID INCLUDES -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<!-- 1140px Grid styles for IE -->
	<!--[if lte IE 9]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" /><![endif]-->

	<!-- The 1140px Grid - http://cssgrid.net/ -->
	<link rel="stylesheet" href="./css/1140.css" type="text/css" media="screen" />
	
	<!-- phone formatting javascript -->
	<script type="text/javascript" src="./js/phone.js"></script>
	
	<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
	<script type="text/javascript" src="./js/css3-mediaqueries.js"></script>
	
	<!-- This is ASSETS for accordion -->
	<?php if(isset($accordion)): ?>
		<!-- ACCORDION CSS -->
		<link rel="stylesheet" href="./css/ui-theme.css" />
		<link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
		<script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
		<!-- DATE PICKER UI JS -->
		<script>
		  $(function() {
		    $( ".calendar" ).datepicker();
		    $( "#anim" ).change(function() {
		      $( ".calendar" ).datepicker( "option", "showAnim", "slideDown" );
		    });
		  });
		</script>
		<!-- ACCORDION JAVASCRIPT -->
		<?php if(isset($_GET['id'])): ?>
	    <script>
		$(function() {
		    $( "#accordion" ).accordion({
		      
			  //autoHeight: false, 
			  collapsible: true,
			  heightStyle: "content"
		    });
			//capture the click on the a tag, must do 3 to capture whole element
		   $("#accordion a h3").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });
		   $("#accordion a span").click(function() {
		      window.location = './dashboard.php';
		      return false;
		   });
		   $("#accordion a").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });
		  });
		  $(function() {
		    $( "#accordion2" ).accordion({
		      
			  //autoHeight: false, 
			  collapsible: true,
			  heightStyle: "content"
		    });
			//capture the click on the a tag, must do 3 to capture whole element
		   $("#accordion a h3").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });
		   $("#accordion a span").click(function() {
		      window.location = './dashboard.php';
		      return false;
		   });
		   $("#accordion a").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });
		  });
		</script>

		<?php endif; ?>	
		<?php if(!isset($_GET['id'])): ?>
		<script>
			$(function() {
		    $( "#accordion" ).accordion({
		      
			  //autoHeight: false, 
			  active: false,
			  collapsible: true,
			  heightStyle: "content"
		    });
			//capture the click on the a tag, must do 3 to capture whole element
		   $("#accordion a h3").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });
		   $("#accordion a span").click(function() {
		      window.location = './dashboard.php';
		      return false;
		   });
		   $("#accordion a").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });
		  });
		$(function() {
		    $( "#accordion2" ).accordion({
		      
			  //autoHeight: false, 
			  active: false,
			  collapsible: true,
			  heightStyle: "content"
		    });
			//capture the click on the a tag, must do 3 to capture whole element
		   $("#accordion a h3").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });
		   $("#accordion a span").click(function() {
		      window.location = './dashboard.php';
		      return false;
		   });
		   $("#accordion a").click(function() {
		      window.location = $(this).attr('href');
		      return false;
		   });

		  });
		  
		</script>
	<?php endif; ?>	
	<?php endif; ?>	
	<!-- Submit contact info -->
	<script type="text/javascript" src="./js/main.js"></script>
</head>
<body<?php echo isset($homepage) ? ' class="homepage"' : '' ?>>
<div id="wrap">
	<!-- SIGN IN -->
	<div id="account" style="font-size:21px; font-style: italic;">
	<?php if(isset($dashboard)): ?>
		<a href="./logout.php" class="login-window">Sign Out</a>
	<?php endif; ?>	
	<style>
		#home-button {
			left: 10px;
			right: auto !important;
		}
	</style>
	<?php if(!isset($no_home_button)): ?>
		<a href="./" id="home-button">&#171; Home</a>
	<?php endif; ?>	
	
	<!-- Mobile Check -->
		<?php if(isset($mobile)): ?>
			</br></br>
		<?php endif; ?>	
	<!-- END Mobile Check -->
		<div class="container">
	<?php if(isset($dashboard)): ?>
		<div class="row" align="center">
			<h1 style="padding:35px;">Welcome, <?php echo $_SESSION['person']['first_name']; ?>&nbsp;<?php echo $_SESSION['person']['last_name']; ?></h1>
		</div>
	<?php endif; ?>	
		</div>
	</div><!-- END div account -->
	<!-- START div LOGO -->

	<div style="width: 100%;" class="container drop_shadow" align="center">
		<div class="row">
			<!-- add tutorial link to dashboard page, otherwise just place logo-->
			<?php if(isset($dashboard)): ?>
			<div class="threecol">
				<!--<a style="display:inline-block; font-size:20px; padding:20px;" href="#" rel="prettyPhoto" title="Tutorial">Watch Tutorial</a>-->
			</div>
			<div class="sixcol">
			<?php endif; ?>	
			
			<img src="./img/logo.png" />
			
			<?php if(isset($dashboard)): ?>
			</div>
			<div class="threecol last">	
			</div>
			<?php endif; ?>			
		</div>
	</div>

	<div id="main">
