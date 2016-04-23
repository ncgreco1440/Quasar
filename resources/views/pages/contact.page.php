<?php use Functions\Functions; ?>
<h1><?php echo $page; ?></h1>
<?php echo Functions::genForm("contact", "POST", "contact", ["name" => "text", "email" => "email",
    "subject" => "text", "message" => "textarea"]); ?>