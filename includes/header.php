<?php 
		include './Mobile_Detect.php';
		$detect = new Mobile_Detect();
		
		if ($detect->isMobile()) {
		// Any mobile device.
		$mobile="";
		}
		if (!empty($_SESSION['person'])) {
			header ('Location: ./dashboard.php');
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
	<link rel="canonical" href="http://www.locizzle.com"/>
	
	<!-- Google will often use this as its description of the page/site. Make it good. -->
	<meta name="description" content="Locizzle provides brokers, agents and assitance with the ability to schedule inspections in a matter of seconds. No more phone tag with multiple people, and no more mistakes!">
	
	<meta name="google-site-verification" content="">
	<!-- Google WEB FONTS. -->
	<link href='https://fonts.googleapis.com/css?family=Oleo+Script+Swash+Caps' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Roboto:500italic' rel='stylesheet' type='text/css'>
	
	<!-- Speaking of Google, don't forget to set the site up: http://google.com/webmasters -->
	<meta name="copyright" content="Copyright Locizzle.com 2013. All Rights Reserved.">
	<link rel="shortcut icon" href="./img/favicon.ico">
	<link rel="apple-touch-icon" href="./img/apple-touch-icon.png">
	
	<!-- CSS -->
	<link rel="stylesheet" href="./css/reset.css">
	<link rel="stylesheet" href="./css/style.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="./css/ui-theme.css" />
	
	<!-- CSS GRID INCLUDES -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<!-- 1140px Grid styles for IE -->
	<!--[if lte IE 9]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" /><![endif]-->

	<!-- The 1140px Grid - http://cssgrid.net/ -->
	<link rel="stylesheet" href="./css/1140.css" type="text/css" media="screen" />

	<!-- jQuery Slider/Video assets NOT NEEDED FOR NOW!-->
	<link rel="stylesheet" type="text/css" href="./slider/css/style.css"  />
	<!-- PrettyPhoto -->
	<link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
	<script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
	
	
	<!--script type="text/javascript" src="./slider/js/jquery-ui-personalized-1.5.2.packed.js"></script-->
	<script type="text/javascript" src="./slider/js/sprinkle.js"></script>
	
	<!-- jQUERY -->
	<script type="text/javascript" src="./js/jquery.cycle.js"></script>
	<script type="text/javascript" src="./js/functions.js"></script>
	<script type="text/javascript" src="./js/jquery.easing.1.3.js"></script>
	
	<!-- phone formatting javascript -->
	<script type="text/javascript" src="./js/phone.js"></script>
	
	<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
	<script type="text/javascript" src="./js/css3-mediaqueries.js"></script>
	
	<!-- This is CSS for accordian -->
	<?php if(isset($accordian)): ?>
		<!-- ACCORDIAN JAVASCRIPT -->
	    <script>
		$(function() {
		    $( "#accordion" ).accordion({
		      
			  //autoHeight: false, 
			  active: false,
			  collapsible: true,
			  heightStyle: "content"
		    });
		  });
		  
		</script>
	<?php endif; ?>	
	<!-- jquery to scroll to bottom of page -->
	<script>
	$(document).ready(function() {
	    $('#bottom').click(function(){
	        $('html, body').animate({scrollTop:$(document).height()}, 'slow');
	        return false;
	    });
	});
	</script>
	<!-- Submit contact info -->
	<script type="text/javascript" src="./js/main.js"></script>
<!-- MODAL SIGN IN BOX -->
	<script>
	$(document).ready(function() {
	$('a.login-window').click(function() {
		
                //Getting the variable's value from a link 
		var loginBox = $(this).attr('href');

		//Fade in the Popup
		$(loginBox).fadeIn(300,function(){
			$('#login_email').focus();
		});
		
		//Set the center alignment padding + border see css style
		var popMargTop = ($(loginBox).height() + 24) / 2; 
		var popMargLeft = ($(loginBox).width() + 24) / 2; 
		
		$(loginBox).css({ 
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});
		
		// Add the mask to body
		$('body').append('<div id="mask"></div>');
		$('#mask').fadeIn(300);
		
		return false;
	});
	
	// When clicking on the button close or the mask layer the popup closed
	$(document).on('click','a.close, #mask',function(){
	  $('#mask , .login-popup').fadeOut(300 , function() {
		$('#mask').remove();  
	}); 
		return false;
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
</head>
<body<?php echo isset($homepage) ? ' class="homepage"' : '' ?>>
<div id="wrap">
	<!-- SIGN IN -->
	<div id="account" style="font-size:21px; font-style: italic;">
	<?php if(empty($_SESSION['person'])): ?>	
		<a href="#login-box" class="login-window">Sign In</a>
	<?php endif; ?>	
	<?php if(!empty($_SESSION['person'])): ?>	
		<a href="./logout.php">Sign Out</a>
	<?php endif; ?>	
	
		
		<div id="login-box" class="login-popup">
	     	<a href="#" class="close">
	        	<img src="./img/close.png"  class="btn_close" title="Close Window" alt="Close" />
			</a>
		<!-- SIGNIN FORM -->
          <form method="post" class="signin" action="./login.php">
          	Sign In
            <fieldset class="textbox">
            	<label class="username">
                	<input id="login_email" name="login_email" value="" type="text" autocomplete="on" placeholder="Email">
                </label>
                <label class="password">
                	<input id="login_password" name="login_password" value="" type="password" placeholder="Password">
                </label>
				<div class="button_holder" align="center">
                	<button class="button" type="submit">Sign In</button>
				</div>
                <p>
            		<a class="forgot" href="./forgot-password.php">Forgot your password?</a>
                </p>        
            </fieldset>
          </form>
	  </div><!-- END DIV LOGIN-BOX -->
	<!-- Mobile Check -->
		<?php if(isset($mobile)): ?>
			</br></br>
		<?php endif; ?>	
	<!-- END Mobile Check -->
		<div class="container">
		<div class="row" align="center">
			<!-- Real Estate Broker Image -->
			<img style="margin-bottom:-20px; margin-top:-10px;" src="./img/realestate_broker.png" />
	<!-- Mobile Check -->		
		<?php if(isset($mobile)): ?>
			</br></br>
		<?php endif; ?>	
	<!-- END Mobile Check -->
			<span style="display:inline-block; width:75%; margin:auto;">
				<span class="green">New</span> Locizzle White Label Application: add value to your website. <a class="green" href="/files/locizzle-white-label-application.pdf">Learn More</a><br />
				Add a competitive advantage and a new source of revenue.</br>
				Automate the way you interact with your own go-to home inspectors.
			</span>
			</br>
	<!-- Mobile Check -->	
		<?php if(isset($mobile)): ?>
		<img src="./img/logo.png" />
		</br>
		<a style="display:inline-block; font-size:20px; padding:20px;" href="http://vimeo.com/62632775" title="How It Works">Watch: How It Works</a></br>
		<a href="./img/aboutus-01.png" title="">About Us</a>
		<br />
		<?php endif; ?>
	<!-- END Mobile Check -->
			</br>
		</div>
		</div>
	</div>
	<div id="main">
