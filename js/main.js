/**
 * @author Thomas Kane
 */
//contact form submission
		$(function(){
			$('#contactUs').submit(function(e){
				//$('input[name="submit_verify"]').attr('disabled','disabled');
				
				var firstName = $(this)
								.find('input[name="first_name"]')
								.val();
				var lastName = $(this)
								.find('input[name="last_name"]')
								.val();
				var email = $(this)
								.find('input[name="email"]')
								.val();
				var phone = $(this)
								.find('input[name="phone"]')
								.val();
				var referral = $(this)
								.find('input[name="referral"]')
								.val();
				var message = $(this)
								.find('textarea[name="message"]')
								.val();
					
		//submit to sign_up_broker to validate and add to DB					
				$('#contact-results').load('./contact.php', {
					
					firstNameValue: firstName,
					lastNameValue: lastName,
					emailValue: email,
					phoneValue: phone,
					referralValue: referral,
					messageValue: message,
		
				});
				
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});*/
				e.preventDefault();
				//return fa;se
			});
		});
//handle an inspector accepting their invitation and adding profile info from accept_invitation.php
	/*$(function(){
			//$('#submit_accept').submit(function(e){
			$(document).on("submit", 'form.accept', function(e){
				var inspFirstName = $(this)
								.find('input[name="insp_first_name"]')
								.val();
				
				var inspLastName = $(this)
								.find('input[name="insp_last_name"]')
								.val();
								
				var inspMobilePhone = $(this)
								.find('input[name="insp_mobile_phone"]')
								.val();	
								
				if($("#insp_text_capable").prop('checked') == true){
					var inspTextCapable = '1'; 
				}
				else {
					var inspTextCapable = '0';
				}
				
				var inspEmail = $(this)
								.find('input[name="insp_email"]')
								.val();
								
				var inspZip = $(this)
								.find('input[name="insp_postal_code"]')
								.val();
				
				//this is a hidden field			
				var inspId = $(this)
								.find('input[name="insp_id"]')
								.val();
				
		//submit to sign_up_broker to validate and add to DB					
				$('#accept-message').load('./accept_invitation.php', {
					
					inspFirstNameValue: inspFirstName,
					inspLastNameValue: inspLastName,
					inspMobilePhoneValue: inspMobilePhone,
					inspTextCapableValue: inspTextCapable,
					inspEmailValue: inspEmail,
					inspZipValue: inspZip,
					inspIdValue: inspId,
					
				});
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});
				e.preventDefault();
				//return fa;se
			});
		});*/
//Accept quote from pending_inspections_list in dashboard view
$(function(){
	$(document).on("click", 'input.accept_quote_button', function(e){
	$(this).attr('disabled','disabled');
	var quoteInsp=$(this)
		.closest('.test')
		.find('input[name="accept_quote"]')
		.val();
		
		$.ajax({
	        type: "POST",
	        url: "./dashboard.php",
	        data: {
				'quoteInspValue': quoteInsp
			},
			success: function(){
				alert('You have accepted this inspection. You can now view this inspection under "Inspection History"');
					window.location.reload(true);
				
				
			},
			error: function(){
				alert("There has been an error adding your inspector. Please try again.");
			}
		});	
		e.preventDefault();	
	});
});
//accept inspectors quote submission on PROPERTY.PHP
	/*$(function(){
			$('#submitQuote').submit(function(e){
				
				var inspFirstName = $(this)
								.find('input[name="insp_first_name"]')
								.val();
				
				var insplastName = $(this)
								.find('input[name="insp_last_name"]')
								.val();
				
				var inspEmail = $(this)
								.find('input[name="insp_email"]')
								.val();
				
		//submit to process_quote_email.php to validate and add to DB					
				$('#submitQuote_message').load('./property.php', {
					
					inspFirstNameValue: inspFirstName,
					insplastNameValue: insplastName,
					inspEmailValue: inspEmail,
					
				});
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});
				e.preventDefault(e);
				//return fa;se
			});
		});*/
//submit NEW_REQUEST_CONFIRM form
	$(function(){
		//$('#new_request_confirm').submit(function(e){
			$(document).on("submit", '#new_request_confirm', function(e){
				
				
				var requestDate2 = $(this)
								.find('input[name="request_date2"]')
								.val();
								
				var requestStreet2 = $(this)
								.find('input[name="request_street2"]')
								.val();
				
				var requestCity2 = $(this)
								.find('input[name="request_city2"]')
								.val();
				
				var requestState2 = $(this)
								.find('input[name="request_state2"]')
								.val();
								
				var requestZip2 = $(this)
								.find('input[name="request_zip2"]')
								.val();	
								
				var extraComment = $(this)
								.find('textarea').val();			
				
				var yearBuilt2 = $('input[name="yearBuilt2"]')
								.val();
								
				var type2 = $(this)
								.find('input[name="type2"]')
								.val();
					
				var bedrooms2 = $(this)
								.find('input[name="bedrooms"]')
								.val();
				
				var bathrooms2 = $(this)
								.find('input[name="bathrooms"]')
								.val();
				
				var totalArea2 = $(this)
								.find('input[name="totalArea"]')
								.val();
				
				var numRooms2 = $(this)
								.find('input[name="numRooms2"]')
								.val();
								
				var numFloors2 = $('#numFloors2 :selected').val();
				
				var foundation = $('#foundation :selected').val();
				
				var garage1 = $('#garage1 :selected').val();
				var garage2 = $('#garage2 :selected').val();
				
				var wasteSystem = $('#waste_system :selected').val();
				
		//check boxes for optional reports
				if($("#pool").prop('checked') == true){
					var pool = '1'; 
				}
				else {
					var pool = '0';
				}
				
				if($("#hottub").prop('checked') == true){
					var hottub = '1'; 
				}
				else {
					var hottub = '0';
				}
				
				if($("#pier_and_beam").prop('checked') == true){
					var pier_and_beam = '1'; 
				}
				else {
					var pier_and_beam = '0';
				}
				
				if($("#radon").prop('checked') == true){
					var radon = '1'; 
				}
				else {
					var radon = '0';
				}
				
				if($("#sprinkler").prop('checked') == true){
					var sprinkler = '1'; 
				}
				else {
					var sprinkler = '0';
				}
				
				if($("#termite").prop('checked') == true){
					var termite = '1'; 
				}
				else {
					var termite = '0';
				}
				
				if($("#copy_to_client").prop('checked') == true){
					var copy_to_client = '1'; 
					
					}
				else {
					var copy_to_client = '0';
				}
				var clientFirst = $(this)
								.find('input[name="client_first_name"]')
								.val();
								
				var clientLast = $(this)
							.find('input[name="client_last_name"]')
							.val();
				
				var clientMobile = $(this)
							.find('input[name="client_mobile_phone"]')
							.val();

				if($("#client_text_capable").prop('checked') == true){
					var clientText = '1'; 
				}	
				else {
					var clientText = '0';
				}
							
				var clientEmail = $(this)
							.find('input[name="client_email"]')
							.val();
				
				
		//submit to sign_up_broker to validate and add to DB					
				$('#request_final').load('./includes/new_request.php', {
					
					requestDateValue2: requestDate2,
					requestStreetValue2: requestStreet2,
					requestCityValue2: requestCity2,
					requestStateValue2: requestState2,
					requestZipValue2: requestZip2,
					extraCommentValue: extraComment,
					yearBuiltValue2: yearBuilt2,
					typeValue2: type2,
					bedroomsValue2: bedrooms2,
					bathroomsValue2: bathrooms2,
					totalAreaValue2: totalArea2,
					numRoomsValue2: numRooms2,
				//select box values
					numFloorsValue2: numFloors2,
					foundationValue: foundation,
					garage1Value: garage1,
					garage2Value: garage2,
					wasteSystemValue: wasteSystem,
				//checkbox values
					poolValue: pool,
					hottubValue: hottub,
					pier_and_beamValue: pier_and_beam,
					radonValue: radon,
					sprinklerValue: sprinkler,
					termiteValue: termite,
					copyToClientValue: copy_to_client,
				//clientInfo
					clientFirstValue: clientFirst,
					clientLastValue: clientLast,
					clientMobileValue: clientMobile,
					clientTextValue: clientText,
					clientEmailValue: clientEmail,
		
				});
				// Accordion hides this div; display it again before populating with data
				//$('#request_f').show();
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});*/
				e.preventDefault();
				//return fa;se
			});
		});
//submit NEW_REQUEST form
	$(function(){
		$(document).on('submit','#new_request',function(e){
			//$('#new_request').submit(function(e){
				//alert('hey'); return false;
				var requestDate = $(this)
								.find('input[name="request_date"]')
								.val();
								
				var requestComment = $(this)
								.find('textarea[name="request_comment"]')
								.val();
				
				var requestCity = $(this)
								.find('input[name="request_city"]')
								.val();
								
				var requestState = $('#request_state :selected').val();
				
				var requestZip = $(this)
								.find('input[name="request_zip"]')
								.val();
								
				var requestStreet = $(this)
								.find('input[name="request_street"]')
								.val();
				
		//submit to sign_up_broker to validate and add to DB					
				$('#request_confirmation').load('./includes/new_request.php', {
					
					requestDateValue: requestDate,
					requestCommentValue: requestComment,
					requestStreetValue: requestStreet,
					requestCityValue: requestCity,
					requestStateValue: requestState,
					requestZipValue: requestZip,
					
				}, function(){
					var url=window.location.href;
					url=url.replace(/#.*$/,'');
					window.location=url+'#new_request_anchor';
				});
				// Accordion hides this div; display it again before populating with data
				$('#request_confirmation').show();
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});*/
				e.preventDefault();
				//return fa;se
			});
		});

//add pre-existing inspector to brokers list
$(function(){
	$(document).on("click", 'input.add_insp_button', function(e){
	$(this).attr('disabled','disabled');
	var inspId=$(this)
		.parents('h2.add_insp')
		.find('input[name="insp_id"]')
		.val();
		
		$.ajax({
	        type: "POST",
	        url: "./add_insp.php",
	        data: {
				'inspIdValue': inspId
			},
			success: function(){
				alert("You have added a new inspector.");
				
			},
			error: function(){
				alert("There has been an error adding your inspector. Please try again.");
			}
		});	
		e.preventDefault();	
	});
});

								
//get inspectors in certain radius
$(function() {
    $(":radio").click(function(e) {
		
        if (this.id == 50) {
			var radius = '50';

        } else if (this.id == 100) {
			var radius = '100';

        } else if (this.id == 150) {
			var radius = '150';

        }
		
		var broker = $('input[name="brokerzip"]')
			.val();
								
		$('#inspector_list').load('./add_insp.php', {
					radiusValue: radius,
					brokerZipCodeValue: broker,
		});			
		
		//e.preventDefault();	
    });
	
});
			
//submit ADD-INSP form
	$(function(){
			$('#signUpInsp').submit(function(e){
				
				
				var firstName = $(this)
								.find('input[name="insp_first_name"]')
								.val();
								
				var lastName = $(this)
								.find('input[name="insp_last_name"]')
								.val();
				
				var mobilePhone = $(this)
								.find('input[name="insp_mobile_phone"]')
								.val();
				
				var email = $(this)
								.find('input[name="insp_email"]')
								.val();
								
				var cemail = $(this)
								.find('input[name="cinsp_email"]')
								.val();
				
				var postalCode = $(this)
								.find('input[name="insp_postal_code"]')
								.val();
								
		//did they say they were text capable?
				if($("#insp_text_capable").prop('checked') == true){
					var inspTextCapable = '1'; 
				}
				else {
					var inspTextCapable = 'false';
				}
				
		//submit to sign_up_broker to validate and add to DB					
				$('#insp-message').load('./add_insp.php', {
					
					inspFirstNameValue: firstName,
					inspLastNameValue: lastName,
					inspMobilePhoneValue: mobilePhone,
					inspTextCapableValue: inspTextCapable,
					inspEmailValue: email,
					cInspEmailValue: cemail,
					inspPostalCodeValue: postalCode,
					
				});
				
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});*/
				e.preventDefault();
				//return fa;se
			});
		});

//submit contact form
	$(function(){
			$('#signUpBroker').submit(function(e){
				
				
				var firstName = $(this)
								.find('input[name="first_name"]')
								.val();
								
				var lastName = $(this)
								.find('input[name="last_name"]')
								.val();
				
				var mobilePhone = $(this)
								.find('input[name="mobile_phone"]')
								.val();
				
				var officePhone = $(this)
								.find('input[name="office_phone"]')
								.val();
				
				var ext = $(this)
								.find('input[name="ext"]')
								.val();
				
				var email = $(this)
								.find('input[name="email"]')
								.val();
				
				var cemail = $(this)
								.find('input[name="c_email"]')
								.val();
				
				var password = $(this)
								.find('input[name="password"]')
								.val();
				
				var cpassword = $(this)
								.find('input[name="cpassword"]')
								.val();
				
				var companyName = $(this)
								.find('input[name="company_name"]')
								.val();
				
				var postalCode = $(this)
								.find('input[name="postal_code"]')
								.val();
								
		//did they say they were text capable?
				if ($('#text_capable').attr('checked')) {
					var textCapable = '1'; 
				}
				else {
					var textCapable = 'false';
				}
				
		//submit to sign_up_broker to validate and add to DB					
				$('#contact-message').load('./includes/sign_up_broker.php', {
					
					firstNameValue: firstName,
					lastNameValue: lastName,
					mobilePhoneValue: mobilePhone,
					textCapableValue: textCapable,
					officePhoneValue: officePhone,
					extValue: ext,
					emailValue: email,
					cemailValue: cemail,
					passwordValue: password,
					cpasswordValue: cpassword,
					companyNameValue: companyName,
					postalCodeValue: postalCode
				},function(){
					$('#verify-panel').click();
				});
				
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});*/
				e.preventDefault();
				//return fa;se
			});
		});

//check for validation code		
		$(function(){
			$('#verifyBroker').submit(function(e){
				//$('input[name="submit_verify"]').attr('disabled','disabled');
				
				var confirmationCode = $(this)
								.find('input[name="confirmation_code"]')
								.val();
					
		//submit to sign_up_broker to validate and add to DB					
				$('#verification-message').load('./includes/sign_up_broker.php', {
					
					confirmationCodeValue: confirmationCode,
		
				});
				
				//$(this).remove();
				//$(this).css('visibility','hidden');
				/*$(this).css({
					visibility: 'hidden',
					display: 'none'
				});*/
				e.preventDefault();
				//return fa;se
			});
		});