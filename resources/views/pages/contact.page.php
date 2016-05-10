<?php use Functions\Functions; ?>
<h1><?php echo $page; ?></h1>
<?php echo Functions::genForm(  "contact",
                                "POST",
                                "contact",
                                ["name" => ["label" => "Name", "type" => "text"],
                                    "email" => ["label" => "Email", "type" => "email"],
                                    "subject" => ["label" => "Subject", "type" => "text"],
                                    "message" => ["label" => "Message", "type" => "textarea"]]); ?>