<!DOCTYPE html>
<html>
<head><title>Quasar | Forgot Password</title></head>
<body>
<h1>Quasar</h1>
<?php
use Functions\Functions;

echo Functions::genForm("passwordReset", "POST", "/admin/home?signin", ["email" => "email"]);
?>
<div class="form-row">
    <a  href="/admin/home?signin" class="button">Sign In</a>
</div>

</body>
</html>