	</div><!-- END div main -->
</div><!-- END div wrap -->
<div id="footer">
	<div class="container">
		<div class="row">
			<!-- SOCIAL MEDIA -->
			<div class="fourcol"></div>
			<div class="fourcol" style="text-align:center;">
			<img src="./img/facebook.png" onmouseover="this.src='./img/facebook_hover.png';" onmouseout="this.src='./img/facebook.png';" onmousedown="this.src='./img/facebook_hover.png';" onmouseup="this.src='./img/facebook.png';" onclick="window.open('http://www.facebook.com/locizzle')"></img>
			<img src="./img/twitter.png" onmouseover="this.src='./img/twitter_hover.png';" onmouseout="this.src='./img/twitter.png';" onmousedown="this.src='./img/twitter_hover.png';" onmouseup="this.src='./img/twitter.png';" onclick="window.open('http://www.twitter.com/locizzle')"></img>
			<img src="./img/linkedin.png" onmouseover="this.src='./img/linkedin_hover.png';" onmouseout="this.src='./img/linkedin.png';" onmousedown="this.src='./img/linkedin_hover.png';" onmouseup="this.src='./img/linkedin.png';" onclick="window.open('http://www.linkedin.com/company/3018282?trk=company_search')"></img>					
			</div>
			<div class="fourcol last"></div>
			<!-- END SOCIAL MEDIA -->
		</div>
		<div class="row">
		<hr />
			<div class="fourcol" id="links">
				<a href="./terms.php">Terms and Conditions</a> | 
				<a href="./privacy.php">Privacy</a> | 
				<a href="./contact.php">Contact</a> |
				<a href="./faq.php">FAQ</a> 
			</div>
			<div class="fourcol" align="center">
				<img src="./img/zillow.png" />
			</div>
			<div class="fourcol last" style="font-size:15px; padding-top:5px;text-align:right;">
				Locizzle | All rights reserved
			</div>
		</div>
	</div>
</div>
<!-- JS FOR ACCORDIAN FUNCTIONALITY -->
<?php if(isset($accordian)): ?>	
	<script>
		(function() {
		
		$(document).ready(function() {
		      $('body').addClass('js');
		  	  var $tab = $('.tab');
		
				$tab.on("click", function(e){
		      e.preventDefault();
		      var $this = $(this);
		      $this.toggleClass('active');
		      $this.next('.panel').toggleClass('active');
		    });
		});
		
		})();
	</script>

	<script>
		if (document.location.search.match(/type=embed/gi)) {
		  window.parent.postMessage('resize', "*");
		}
	</script>
<?php endif; ?>
<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto();
  });
</script>

<!-- GOOGLE ANALYTICS -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-39447852-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</script>
<!-- begin olark code -->
<script data-cfasync="false" type='text/javascript'>/*<![CDATA[*/window.olark||(function(c){var f=window,d=document,l=f.location.protocol=="https:"?"https:":"http:",z=c.name,r="load";var nt=function(){
f[z]=function(){
(a.s=a.s||[]).push(arguments)};var a=f[z]._={
},q=c.methods.length;while(q--){(function(n){f[z][n]=function(){
f[z]("call",n,arguments)}})(c.methods[q])}a.l=c.loader;a.i=nt;a.p={
0:+new Date};a.P=function(u){
a.p[u]=new Date-a.p[0]};function s(){
a.P(r);f[z](r)}f.addEventListener?f.addEventListener(r,s,false):f.attachEvent("on"+r,s);var ld=function(){function p(hd){
hd="head";return["<",hd,"></",hd,"><",i,' onl' + 'oad="var d=',g,";d.getElementsByTagName('head')[0].",j,"(d.",h,"('script')).",k,"='",l,"//",a.l,"'",'"',"></",i,">"].join("")}var i="body",m=d[i];if(!m){
return setTimeout(ld,100)}a.P(1);var j="appendChild",h="createElement",k="src",n=d[h]("div"),v=n[j](d[h](z)),b=d[h]("iframe"),g="document",e="domain",o;n.style.display="none";m.insertBefore(n,m.firstChild).id=z;b.frameBorder="0";b.id=z+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){
b.src="javascript:false"}b.allowTransparency="true";v[j](b);try{
b.contentWindow[g].open()}catch(w){
c[e]=d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{
var t=b.contentWindow[g];t.write(p());t.close()}catch(x){
b[k]=o+'d.write("'+p().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}a.P(2)};ld()};nt()})({
loader: "static.olark.com/jsclient/loader0.js",name:"olark",methods:["configure","extend","declare","identify"]});
/* custom configuration goes here (www.olark.com/documentation) */
olark.identify('1099-658-10-9636');/*]]>*/</script><noscript><a href="https://www.olark.com/site/1099-658-10-9636/contact" title="Contact us" target="_blank">Questions? Feedback?</a></noscript>
<!-- end olark code -->
</body>
</html>
