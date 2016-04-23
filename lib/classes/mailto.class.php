<?php
namespace Mail;
class MailTo
{
    public static function htmlEmail($to, $subject, $message, $from, $fromname)
    {
        $headers = "From: $fromname <$from>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $boundary = uniqid("HTMLEMAIL");
        $headers .= "Content-Type: multipart/alternative;".
                    "boundary = $boundary\r\n";
        $headers .= "This is a MIME encoded message.\r\n";
        $headers .= "--$boundary\r\n".
                    "Content-Type: text/plain; charset=ISO-8859-1\r\n".
                    "Content-Transfer-Encoding: base64\r\n";
        $headers .= chunk_split(base64_encode(strip_tags($message)));
        $headers .= "--$boundary\r\n".
                    "Content-Type: text/html; charset=ISO-8859-1\r\n".
                    "Content-Transfer-Encoding: base64\r\n";
        $headers .= chunk_split(base64_encode($message));

        $sendmail = mail($to,$subject,"",$headers);

        return $sendmail;
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