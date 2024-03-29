<?php
error_reporting(0); 
require 'config.php';
require 'smtp.php';
require 'Date.php';

require 'PHPMailer/PHPMailer-master/src/Exception.php';
require 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['firstname']) AND isset($_POST['lastname']) AND isset($_POST['email']) AND isset($_POST['password']) AND isset($_POST['gender'])){

    
    //sanitize inputs
    $firstname = ucfirst(mysqli_real_escape_string($conn, $_POST["firstname"]));
    $lastname  = ucfirst(mysqli_real_escape_string($conn, $_POST["lastname"]));
    $email     = mysqli_real_escape_string($conn, $_POST["email"]);
    $gender    = mysqli_real_escape_string($conn, $_POST["gender"]);
    $password  = mysqli_real_escape_string($conn, $_POST["password"]);

    $hashed_password = md5($password);

    //validate email 
    $check_email = mysqli_query($conn,"SELECT client_email FROM client WHERE client_email = '$email' ");
    if(mysqli_num_rows($check_email) > 0){
        echo "0";
    }elseif(!preg_match("/^[a-zA-Z-' ]*$/",$firstname)){
        echo "1";
    }elseif (!preg_match("/^[a-zA-Z-' ]*$/",$lastname)) {
        echo "2";
    }elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        echo "3";
    }elseif($password == "Password"){
        echo "4";
    }elseif($password == "123"){
        echo "4";
    }elseif($password == "test123"){
        echo "4";
    }elseif($password == "password123"){
        echo "4";
    }elseif($password == "password"){
        echo "4";
    }else {

        $stmt = $conn->prepare("INSERT INTO `client`(`client_firstname`, `client_lastname`, `client_email`, `client_password`,`client_gender`,`client_image`,`account_created`,`role`,`verified`) VALUES (?,?,?,?,?,'images/default.png',CURRENT_DATE(),'0','0')");
        $stmt->bind_param("sssss",$firstname,$lastname,$email,$hashed_password,$gender);
            if($stmt->execute() == true){

                $code = rand(123456,78901);
                $charset = md5($code);
                $activation = sha1('abcdefghijklmnopqrstuvwxyxABCDEFGHIJKLMNOPQRSTUVWXYZ12345678900987654321!@#$%^&*()');
                //Instantiation and passing `true` enables exceptions
                    $mail = new PHPMailer(true);
                    $object = new Year();

                    try {
                        //Server settings
                        $mail->SMTPDebug = 0;                    
                        $mail->isSMTP();                                           
                        $mail->Host       = 'smtp.gmail.com';                     
                        $mail->SMTPAuth   = true;                                   
                        $mail->Username   = $smtp_email;                     
                        $mail->Password   = $smtp_password;                               
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
                        $mail->Port       = 587;                                   

                        //SENDER
                        $mail->setFrom($smtp_email, 'System Administrator');
                        //RECEIVER
                        $mail->addAddress($email, $firstname.' '.$lastname);     
                       

                        //Content
                        $mail->isHTML(true);                                 
                        $mail->Subject = 'One Time Email Verification';
                        $mail->Body    = '
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <tbody><tr style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <td class="content-block" style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                    Please confirm your email address by clicking the link below.
                                </td>
                            </tr>
                            <tr style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <td class="content-block" style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                    Dear ,' .$firstname.' '.$lastname.'
                                    Please click the verification link.
                                </td>
                            </tr>
                            <tr style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <td class="content-block" itemprop="handler" itemscope="" itemtype="" style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                    <a href="https://new-appointment-sys.herokuapp.com/login?code='.htmlspecialchars($code).'&&charset='.$charset.'&&activation='.$activation.'&&firstname='.htmlspecialchars($firstname).'&&lastname='.htmlspecialchars($lastname).'" class="btn-primary" itemprop="url" style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #f06292; margin: 0; border-color: #f06292; border-style: solid; border-width: 8px 16px;">Verify Email Address</a>
                                </td>
                            </tr>

                            <tr style="font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <td class="content-block" style="text-align: center;font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0;" valign="top">
                                &copy; 2021 NEW ISRAEL RESERVATION SYSTEM
                                </td>
                            </tr>
                        </tbody></table>';
                        $mail->AltBody = 'Dear ,' .$firstname.' '.$lastname.'
                        Please click the verification link <a href="https://new-appointment-sys.herokuapp.com/login?code='.htmlspecialchars($code).'&&charset='.$charset.'&&activation='.$activation.'&&firstname='.htmlspecialchars($firstname).'&&lastname='.htmlspecialchars($lastname).'">Verify</a>
                        ';

                        $mail->send();
                        $month = $object->get_month(date("M"));
                    
                            $update_0 = mysqli_query($conn,"UPDATE monthly_client SET no_client = no_client+1 WHERE month = '$month' ");
                            if($update_0){
                                echo "5";
                            }
                        
                        
                    } catch (Exception $e) {
                            $month = $object->get_month(date("M"));
                            $update_1 = mysqli_query($conn,"UPDATE monthly_client SET no_client = no_client+1 WHERE month = '$month' ");
                            if($update_1){
                                echo "6";
                            }
                    }
                  
            }else{
                echo "error in inserting a data";
            }
        $stmt->close();
        $conn->close();
    }

}else{
    echo "Mo data";   
}


