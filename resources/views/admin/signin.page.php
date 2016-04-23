<!DOCTYPE html>
<html>
<head><title>Quasar | Sign In</title></head>
<body>
<?php use Functions\Functions; ?>
<h1>Quasar</h1>
<?php
    if($message)
        echo Functions::displayFormMsg($message);
    echo Functions::genForm("login", "POST", "/admin/home?signin", ["username" => "text",
    "password" => "password"]);
?>
<div class="form-row">
    <a  href="/admin/home?forgot-password" class="button">Forgot Password</a>
</div>
</body>
</html>