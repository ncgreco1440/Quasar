<?php
use Functions\Functions;
?>
<h2>Profile: <?php echo $user['username']; ?></h2>
<?php echo Functions::genForm("userForm", "POST", "/admin/users?$user[username]",
    ["firstname" => "text", "lastname" => "text", "email" => "email"], $user); ?>