<?php $accordion='';
$_GET['id']=''; ?>
<?php include './includes/header_dashboard.php'; ?>
<div class="container" id="learn_more_section">
	<div class="row" style="text-align:left;color: #3C2111; font-size:25px; padding:0px;">
	<br /><br />
	<h1 align="center" class="bold">Frequently Asked Questions</h1>
	<h3 style="font-size:18px; margin:0 auto; text-align:center;">For infrequently asked questions, <a href='./contact.php'>Contact Us</a></h3>
	</div>
	<br />
	<div class="row" style="text-align:left; margin:auto;color: #3C2111;">
	<img src="./img/faq.png" />
	<div class="sixcol">
		<br />
		<h1>About Locizzle</h1>
		<?php if(isset($mobile)): ?>
			<div style="margin:auto;" id="accordion">
		<?php endif; ?>	
		<?php if(!isset($mobile)): ?>
			<div style=" margin:auto;" id="accordion">
		<?php endif; ?>	
			<h3>How does Locizzle work?</h3>
				<div style="font-size:12px;">
					Locizzle automates the process of obtaining home inspection quotes and schedules for a single home inspection. The Locizzle app enables brokers to submit a home inspection request to all their qualified inspectors via text or email in seconds. Inspectors receive a link with the home information respond with a quote including their price, availability, and qualifications.
				</div>	
			<h3>Who should use Locizzle?</h3>
				<div style="font-size:12px;">
					Every broker looking to increase productivity, and reduce workload. Locizzle automates the tedious process of making phone calls, leaving voicemails, and sending emails to multiple Home Inspectors. 
				</div>	
			<h3>How much does it cost?</h3>
				<div style="font-size:12px;">
					Absolutely nothing. Locizzle is a free service for both real estate broker owners, agents, and their clients. Even better... For every Home Inspection Request completed using Locizzle, broker owners receive $5 in additional revenue. 
<br /><br />Locizzle is also free for home inspectors to receive requests and quote home inspections. For every home inspection that an inspector is selected to complete, they will be charged $12.50.
				</div>	
			<h3>Will Locizzle add revenue to my business?</h3>
				<div style="font-size:12px;">
					Yes! For every Home Inspection you schedule using Locizzle, broker owners will receive $5. Over the course of one year, that adds up and lowers overhead.
				</div>
			<h3>Do my customers benefit when I use Locizzle?</h3>
				<div style="font-size:12px;">
					Looking for a way to impress that tech-savvy 25-55 year old customer base? Look no further, Locizzle will help you do just that. When navigating through the complex home buying process, your customers will greatly appreciate the time you are able to save them. Locizzle automates the process of finding the right  home inspector and schedules the inspection.. In addition, Locizzle provides a platform for competitive Inspector pricing and qualifications, helping your customers make an informed decision. Locizzle assures home buyers that the home inspection selection process is fair, competitive and arms length.
				</div>	
			<h3>Will Locizzle decrease my workload? </h3>
				<div style="font-size:12px;">
					It sure will! Locizzle automates the process of scheduling home inspections. So leave multiple phone calls, voicemails, and emails in the past! Complete a single home inspection request, and have it sent to multiple home inspectors at one time. Within seconds, home inspectors will receive your request and are able to respond with their quote, availability, and qualifications. <br /><a href="./">Use Locizzle Today!</a>
				</div>	
		</div>
		</div>
		
		<!-- RIGHT COLOMN OF FAQ -->
		<div class="sixcol last">
			<br />
			<h1>Using Locizzle</h1>
			<?php if(isset($mobile)): ?>
			<div style="margin:auto;" id="accordion2">
		<?php endif; ?>	
		<?php if(!isset($mobile)): ?>
			<div style="margin:auto;" id="accordion2">
		<?php endif; ?>	
			<h3>How do I sign up?</h3>
				<div style="font-size:12px;">
					Sign-up is easy. Simply provide your basic contact information including: name, email, mobile phone, and postal code <a style="color: #60BDB8;" href="./">here</a>. That&#39;s it!
				</div>	
			<h3>Why do I need to verify my account?</h3>
				<div style="font-size:12px;">
					Verifying your account provides our users with added security assurance. Upon signing up, you will receive a text/email with your verification code. Enter this code in the verification section, and select &#34;Submit&#34;. Your account set up is now complete!
				</div>	
			<h3>How do I add Home Inspectors to my Network?</h3>
				<div style="font-size:12px;">
					Adding home inspectors to your network is easy. Under the &#34;Inspectors&#34; tab on your dashboard Locizzle allows you to add your own &#34;go-to&#34; inspectors to your network. To add a home inspector to your network, select the &#34;Invite A New Inspector&#34; link and add their basic contact information. When complete, select &#34;Invite&#34; and your inspector will be added to your network of inspectors. The application will invite <b class="bold">your</b> inspectors via a link to update their profile details with a photo, logo, and qualifications. All of this information will be included in the quotes.				
				</div>	
			<h3>What is the Master Inspector list?</h3>
				<div style="font-size:12px;">
					Your Master Inspector List contains all of the inspectors that you have added to your network. Use this list to add an inspector to your go-to inspector list.
				</div>
			<h3>What is the Go-To Inspector list?</h3>
				<div style="font-size:12px;">
					Your Go-To Inspector list will receive your home inspection requests. They will respond to each home inspection request with their availability and pricing details. To edit this list for your next inspection request, inspectors can be both added and subtracted from the list.<br /><br /> To add inspectors to your Go-To list select the inspector you wish to add from your master list, and choose &#34;add inspector to go-to list&#34;. Selecting this icon does not subtract this inspector from your master list, but populates the Go-To Inspector list with their information as well. This allows the newly added inspector to receive your next inspection request. 
	<br /><br />To subtract an inspector from your Go-To list select the inspector and choose  &#34;remove from Go-To list&#34;. Subtracting an inspector from this list will only subtract them from the go to list. Their information, however, will be remain on your Master Inspector List. 
				</div>	
			<h3>How do I complete a New Inspection Request?</h3>
				<div style="font-size:12px;">
					Located on your dashboard, the &#34;New Inspection Request&#34; is the first tab listed under the &#34;My Home Inspections&#34; section. By default, this tab is open when first signing in for easy access. 
	Complete the first three fields including the date, address, and postal code of the needed inspection. After selecting &#34;Submit&#34;, Zillow returns all the information listed about the home. For the most accurate inspection quote, complete as much information as possible. If you would like your home buyer to receive inspector quote notifications as well, add their basic contact information to the report. By select &#34;Get Quote&#34; your approved, go to home inspectors will receive your home inspection request within seconds, and reply with their price, availability, and qualifications.
				</div>
			<h3>Should I add my clients contact information to the Home Inspection Request?</h3>
				<div style="font-size:12px;">
					Adding your clients contact information to an inspection request is completely optional. Adding their information allows your client to be notified just as you are when an inspector responds with a quote. Your client will not receive mass texts, or junk email; only home inspection quotes, helping you and your home buyer make an informed decision.
				</div>	
			<h3>How am I notified of inspectors&#39; quotes?</h3>
				<div style="font-size:12px;">
					You will receive a text or email notice that you received a quote and you click on the link to review all inspector quotes for the home. Quotes can also be viewed when you are signed in. All pending inspection quotes are listed under the &#39;Pending Inspections&#39; tab. 
				</div>	
			<h3>How do I accept inspectors&#39; quotes?</h3>
				<div style="font-size:12px;">
					Under the &#34;Pending Inspections&#34; tab, you are able to view all pending inspection quotes, and select the quote that is right for you and your home buyer. By selecting, and confirming an inspection quote, your home buyers home inspection is scheduled for completion.
					The inspector is notified they have been awarded the inspection job and the contact information is provided after a transaction fee of $12.50 is paid.
				</div>
			<h3>
	Can I delete an inspector from my master list?</h3>
				<div style="font-size:12px;">
					Yes. By selecting the inspectors name, and clicking &#34;delete&#34;, that inspector will be removed from your master list.
				</div>		
			<h3>I have selected and confirmed a home inspection for my home buyer. Where did this home inspection info go?</h3>
				<div style="font-size:12px;">
					After selecting a home inspector to complete your customers home inspection, all information about this inspection is moved from the &#34;Pending Inspections&#34; tab to the &#34;Inspection History&#34; tab.
				</div>
			<h3>Can I view a list of all completed Home Inspections?</h3>
				<div style="font-size:12px;">
					Sure can! Under the &#34;Inspection History&#34; tab, is a complete list of all past inspections that you have scheduled with Locizzle. This tab will display inspections that are &#34;awaiting confirmation&#34; (you have selected the home inspector for the job), and &#34;confirmed&#34; (the chosen home inspector has completed payment, allowing them to receive further appointment details).
				</div>	
			<h3>I have a question that hasn&#39;t been addressed here. Who should I contact?</h3>
				<div style="font-size:12px;">
					You may chat with us from any page at Locizzle.com. Click on the &#39;Chat with us!&#39; icon at the right bottom of each page. Our chat widget allows you to chat in real-time. Or, you may send us an email: support@locizzle.com.
				</div>	
		</div>	
		</div>
		
	</div>
</div>
<?php include './includes/footer.php' ?>