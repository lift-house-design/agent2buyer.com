<?php $accordian="";?>
<?php include './includes/functions.php'; ?>
<?php include './includes/header.php'; ?>
<!-- Mobile Check -->
<?php if(!isset($mobile)): ?>
<div class="container" align="center">
	<div id="pop-out-logo" align="center">
		<div class="row">
			<!-- About Us Video -->
			<div class="threecol" style="margin-left:50px;" id="videos" align="right" >
				<a href="./img/aboutus-01.png" rel="prettyPhoto"><img src="./img/aboutus.png" alt="About Us <a style='font-size:14px; text-decoration:none; float:right; color:#fff;' href='./'>Close</a>" /></a>
				
			</div>
			
			<div class="fivecol">
				<img src="./img/logo.png" />
			</div>
			<!-- How It Works Video -->
			<div class="threecol last" id="videos" align="left">
				<a href="http://vimeo.com/62632775" rel="prettyPhoto" alt="blah blah" title="Learn more about Locizzle.com for your business"><img src="./img/howitworks.png" alt="How it works <a style='font-size:14px; text-decoration:none; float:right; color:#fff;' href='./'>Close</a>" /></a>	
			</div>
		</div>
	</div>
</div>
<?php endif; ?>	
<!-- END Mobile Check -->
<div class="container">
	<div id="signup_section"><!-- Begin signup section -->
		<div class="row">
		</br>
		<!-- Mobile Check -->
			<?php if(!isset($mobile)): ?>
				</br>
			<?php endif; ?>	
		<!-- END Mobile Check -->
				<div id="signup"><a id="signup-link">sign up today!</a>
				</br>
				<a href="./faq.php">
					<span id="learn_link" style="font-size:20px;">
						or learn more
					</span>
				</a>
					<?php if(isset($mobile)): ?>
					</br>
					<?php endif; ?>	
				<!-- END Mobile Check -->	
				</div>
				
				<?php include './includes/sign_up_broker.php'; ?>
	
				</br></br>
		</div>
	</div><!-- End sign up section -->
</div>
<script>
	$(function(){
		$('#signup-link').click(function(){
			$("#sign-up-broker-panel").click();
		});
	});
</script>
<div class="container">
	<div id="circle_section">
		<div align="center" class="row" style="color:#3C2111;">
			<div class="fourcol">
				<img src="./img/post.png" />
			</div>
			<div class="fourcol">
				<img src="./img/view.png" />
			</div>
			<div class="fourcol last">
				<img src="./img/select.png" />
			</div>
		</div>
	</div>
	<div id="learn_more_section">
		<div class="row">
		<h2>Locizzle App</h2></br>
		Keep the sale moving: Identify with your tech savvy home buyers</br>
		Keep the home inspection search fair, competitive, and at arms length</br>
		Enhance buyer assurance, and reduce your exposure and liability</br>
		Add a revenue stream with locizzle.com at no cost to you or the home buyer</br></br>
		
		Need an inspection completed? Leave phone calls, voicemails, and managing emails to multiple inspectors in the past.</br>
		<span style="line-height:2;">Let Locizzle automate your work flow today, and earn cash back for each inspection ordered with the Locizzle App.</span>
		</div>
	</div>
</div>
<?php include './includes/footer.php'; ?>
