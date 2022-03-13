<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//Load Composer's autoloader
require 'vendor/autoload.php';

// Check if User Coming From A Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Instantiation and passing `true` enables exceptions
    $mailer = new PHPMailer(true);

      // Assign Variables
    $user = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $mail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $cell = filter_var($_POST['cellphone'], FILTER_SANITIZE_NUMBER_INT);
    $msg  = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

     // Creating Array of Errors
    $formErrors = array();
    if (strlen($user) <= 3) {
        $formErrors[] = 'Username Must Be Larger Than <strong>3</strong> Characters';
    }
    if (strlen($msg) < 10) {
        $formErrors[] = 'Message Can\'t Be Less Than <strong>10</strong> Characters';
    }

    // If No Errors Send The Email [ mail(To, Subject, Message, Headers, Parameters) ]

    $headers = 'From: ' . $mail . '\r\n';
    $myEmail = 'email to send from';
    $subject = 'Contact Form';


    $to ='email to send to ';


    if(!empty($_POST['g-recaptcha-response']))
    {
        $secret = '6LetwdgeAAAAAG18vTb-rmrBqVixVx-IsCKyTE-C';
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if($responseData->success)
            {

    if (empty($formErrors)) {

    try {
        //Server settings
        // $mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $mailer->isSMTP();                                            // Send using SMTP
        $mailer->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mailer->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mailer->Username   = $myEmail;                     // SMTP username
        $mailer->Password   = 'your password';                               // SMTP password
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mailer->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mailer->setFrom($to, 'Forget Password');
        $mailer->addAddress($to,$headers);     // Add a recipient
        // $mailer->addAddress('ellen@example.com');               // Name is optional
        $mailer->addReplyTo('mail to replay to', 'Information');
        // $mailer->addCC('cc@example.com');
        // $mailer->addBCC('bcc@example.com');

        // Attachments
        // $mailer->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        // $mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        // Content
        $mailer->isHTML(true);                                  // Set email format to HTML
        $mailer->Subject = $subject . ' from ' .$user;
        $mailer->Body    = $msg ;
        $mailer->AltBody = '';

        $mailer->send();

            $user = '';
            $mail = '';
            $cell = '';
            $msg = '';

            $success = '<div class="alert alert-success">We Have Recieved Your Message</div>';
        

    } catch (Exception $e) {
        $success = '<div class="alert alert-danger">خطأ</div>';
        echo  $mailer->ErrorInfo;
    }
}
            }
        else
            $message = "Some error in vrifying g-recaptcha";
        echo $message;
    } else{
        $recaptchaError = 'reCaptcha Error';
    }



}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elzero Contact Form</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/contact.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,700,900,900i">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>

<body>

    <!-- Start Form -->

    <div class="container">
        <h1 class="text-center">Contact Me</h1>
        <form class="contact-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <?php if (!empty($formErrors)) { ?>
                <div class="alert alert-danger alert-dismissible" role="start">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php
                    foreach ($formErrors as $error) {
                        echo $error . '<br/>';
                    }
                    ?>
                </div>
            <?php } ?>
            <?php if (isset($success)) {
                echo $success;
            } ?>
            <div class="form-group">
                <input class="username form-control" type="text" name="username" placeholder="Type Your Username" value="<?php if (isset($user)) {
                                                                                                                                echo $user;
                                                                                                                            } ?>" />
                <i class="fa fa-user fa-fw"></i>
                <span class="asterisx">*</span>
                <div class="alert alert-danger custom-alert">
                    Username Must Be Larger Than <strong>3</strong> Characters
                </div>
            </div>
            <div class="form-group">
                <input class="email form-control" type="email" name="email" placeholder="Please Type a Valid Email" value="<?php if (isset($mail)) {
                                                                                                                                echo $mail;
                                                                                                                            } ?>" />
                <i class="fa fa-envelope fa-fw"></i>
                <span class="asterisx">*</span>
                <div class="alert alert-danger custom-alert">
                    Email Can't Be <strong>Empty</strong>
                </div>
            </div>
            <input class="form-control" type="text" name="cellphone" placeholder="Type Your Cell Phone" value="<?php if (isset($cell)) {
                                                                                                                    echo $cell;
                                                                                                                } ?>" />
            <i class="fa fa-phone fa-fw"></i>
            <div class="form-group">
                <textarea class="message form-control" name="message" placeholder="Your Message!"><?php if (isset($msg)) {
                                                                                                        echo $msg;
                                                                                                    } ?></textarea>
                <span class="asterisx">*</span>
                <div class="alert alert-danger custom-alert">
                    Message Can\'t Be Less Than <strong>10</strong> Characters
                </div>
            </div>
            <div class="form-group">
            <?php echo isset($recaptchaError)? '<span class="alert alert-danger custom-alert"> Error '.$recaptchaError.'</span>':''; ?>
            </div>
            <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6LetwdgeAAAAAPBnEGcVvZQ30rAvcmIXWLbNmVCe"></div>
            </div>

            <!-- 6LetwdgeAAAAAG18vTb-rmrBqVixVx-IsCKyTE-C -->
            <input class="btn btn-success" type="submit" value="Send Message" />
            <i class="fa fa-send fa-fw send-icon"></i>
        </form>
    </div>

    <!-- End Form -->
    <script src='https://www.google.com/recaptcha/api.js' async defer ></script>
    <script src="js/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>