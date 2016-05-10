<?php
use Functions\Functions;
?>
<?php
    if($message)
        echo Functions::displayFormMsg($message);
?>
<h2>Profile: <?php echo $user['username']; ?></h2>
<?php echo Functions::genForm(  "userForm",
                                "POST",
                                "/admin/users?$user[username]",
                                ["firstname" => ["label" => "First Name", "type" => "text"],
                                    "lastname" => ["label" => "Last Name", "type" => "text"],
                                    "email" => ["label" => "Email", "type" => "email"]],
                                $user); ?>
<h4>Change Password</h4>
<?php echo Functions::genForm(  "chngPassword",
                                "POST",
                                "/admin/users?$user[username]",
                                ["currPass" => ["label" => "Current Password", "type" => "password"],
                                    "newPass" => ["label" => "New Password", "type" => "password"],
                                    "confPass" => ["label" => "Confirm Password", "type" => "password"]]);