<?php use Functions\Functions; ?>
<h1><?php echo $page; ?></h1>
<?php
    $contactForm =
    [
        "name" => ["label" => "Name", "type" => "text", "required" => true],
        "email" => ["label" => "Email", "type" => "email", "required" => true],
        "subject" => ["label" => "Subject", "type" => "text", "required" => true],
        "message" => ["label" => "Message", "type" => "textarea", "required" => true]
    ];
    echo Functions::genForm("contact", "POST", "contact", $contactForm, $_POST); ?>