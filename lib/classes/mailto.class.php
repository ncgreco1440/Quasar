<?php
namespace Mail;

use Vendor\PHPMailer\PHPMailer;

class MailTo
{
    public static function sendEmail($to, $subject, $message, $from, $fromname)
    {
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Set who the message is to be sent from
        $mail->setFrom("support@quasar.cms", "Quasar Support");
        //Set an alternative reply-to address
        $mail->addReplyTo("noreply@quasar.cms", "No Reply");
        //Set who the message is to be sent to
        $mail->addAddress($to);
        //Set the subject line
        $mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($message);
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

    public static function passwordResetMsg($addresse, $password)
    {
        return "<p>Greetings ".$addresse.",</p>".
            "<p>Your password has been reset.</p>".
            "<p>Your new password is <strong>".$password."</strong></p>".
            "<p>Sign in at <a href='http://tech.nicogreco.local/admin/home?signin'>".
            "Quasar Admin</a>.</p>".
            "<p>This message was sent from Quasar, if you did not request a password reset<br/>".
            "then please contact support at nico@nicogreco.com</p>".
            "<p>Regards,</p>".
            "<p>- The Quasar Team</p>";
    }

    public static function passwordChangeMsg($addresse)
    {
        return "<p>Greetings ".$addresse.",</p>".
            "<p>We have noticed that a change in password has been requested from your ".
            "account.</p>".
            "<p>If you issued this request then kindly ignore this message,<p>otherwise please ".
            "contact support at nico@nicogreco.com</p>".
            "<p>Regards,</p>".
            "<p>- The Quasar Team</p>";
    }
}