<?php
use Functions\Functions;
?>
<?php
    if($message)
        echo Functions::displayFormMsg($message);
?>
<h2>Profile: <?php echo $user['username']; ?></h2>
<?php echo Functions::genForm("userForm", "POST", "/admin/users?$user[username]",
    ["firstname" => "text", "lastname" => "text", "email" => "email"], $user); ?>
<h4>Change Password</h4>
<?php echo Functions::genForm("chngPassword", "POST", "/admin/users?$user[username]",
    ["currPass" => "password", "newPass" => "password",
    "confPass" => "password"]);