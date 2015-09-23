/**
 * Document ready will run after the page elements have loaded
 */
$(document).ready(function(){
	//Empty the email textbox when the user clicks on the textbox
	$("#email").focus(function () {		
        $(this).val('');
   });
	
	//Click Event on the submit button
	$("#submit").click(function () {
		//Convert the email to lowercase
		var email = $("#email").val().toLowerCase();
		var name = $("#name").val();

		//If email is empty display an error
		if(email == ''){
			emailStatus("error");
		} else {
			//If email isnt empty then validate the email
			if(!validateEmail(email)){
				//If email hasn't validated then display error message
				emailStatus("error", "Email is not valid");
			} else {
				//If email is valid then submit the email passing through the valid email address
				submitEmail(email, name);
			}			
		}
   });
});

/**
 * Validate email function with regualr expression
 * 
 * If email isn't valid then return false
 * 
 * @param email
 * @return
 */
function validateEmail(email){
	var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	var valid = emailReg.test(email);

	if(!valid) {
        return false;
    } else {
    	return true;
    }
}

/**
 * If the email validates then send the email address to the server to be processed
 * 
 * @param email
 * 
 * 
 * @return
 */
function submitEmail(email,name){
	//jQuery ajax request to process page
	$.ajax({  
		  type: "POST",  
		  url: "./process.php",  
		  data: 'email=' + email +'&name=' + name,  
		  success: function(result,status) { 
			//email sent successfully displays a success message
			if(result != 'failed'){
				emailStatus("success");
			} else {
				//email failed display message
				emailStatus("error", "Email was not sent please try again");
			}
		  },
		  error: function(result,status){
			  //Ajax returns error display error message
			  emailStatus("error", "Email was not sent please try again");
		  }  
		});  

	return false;
}

/**
 * This is a function which will display the status message to the user
 * 
 * @param status
 * To display a error or a success status
 * 
 * @param message
 * The message to display to the user
 * 
 * @return
 */
function emailStatus(status, message){
	
	//decide what status to display the user default is an error
	switch(status){
		case "error":
		default:
			//Clear all classes and add a error class
			$('#form_status').removeClass().addClass('error');
		
			//Add error heading
			$('#form_status h4').text('Error');
			
			//Add message to error status
			if(message == undefined){
				$('#form_status p').text('Please enter an email address.');
			} else {
				$('#form_status p').text(message);
			}
			
		break;

		case "success":
			//Clear all classes and add a success class
			$('#form_status').removeClass().addClass('success');
			
			//Add success heading
			$('#form_status h4').text('Success');
			
			//Add message to success status
			if(message == undefined){
				$('#form_status p').text('Email has been sent successfully.');
			} else {
				$('#form_status p').text(message);
			}
			
		break;
	}
}