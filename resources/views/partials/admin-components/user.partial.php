<?php
use Functions\Functions;
?>
<?php
    if($message)
        echo Functions::displayFormMsg($message);
?>
<h2>Profile: <?php echo $user['username']; ?></h2>
<h4>Rank: <?php echo $user['title']; ?></h4>
<?php
    $userForm =
    [
        "firstname" => ["label" => "First Name", "type" => "text", "required" => true],
        "lastname" => ["label" => "Last Name", "type" => "text", "required" => true],
        "email" => ["label" => "Email", "type" => "email", "required" => true]
    ];
    echo Functions::genForm("userForm", "POST", "/admin/users?$user[username]", $userForm, $user);
?>
<h4>Change Password</h4>
<?php
    $chngPass =
    [
        "currPass" => ["label" => "Current Password", "type" => "password", "required" => true],
        "newPass" => ["label" => "New Password", "type" => "password", "required" => true],
        "confPass" => ["label" => "Confirm Password", "type" => "password", "required" => true]
    ];
    echo Functions::genForm("chngPassword", "POST", "/admin/users?$user[username]", $chngPass);
?>