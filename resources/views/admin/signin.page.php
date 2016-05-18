<!DOCTYPE html>
<html>
<head><title>Quasar | Sign In</title></head>
<body>
<?php use Functions\Functions; ?>
<h1>Quasar</h1>
<?php
    if($message)
        echo Functions::displayFormMsg($message);
    $signinForm =
    [
        "username" => ["label" => "Username", "type" => "text", "required" => true],
        "password" => ["label" => "Password", "type" => "password", "required" => true]
    ];
    echo Functions::genForm("login", "POST", "/admin/home?signin", $signinForm);
?>
<div class="form-row">
    <a  href="/admin/home?forgot-password" class="button">Forgot Password</a>
</div>
</body>
</html>