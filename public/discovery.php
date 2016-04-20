<?php
    include "../lib/autoload.php";
    use Uauth\Uauth;

    $basic = new Uauth("Secured Area", ['jassok' => 'batman123', 'thomasd' => 'g3tbr0l1k']);
    $basic->auth();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Tech - Discovery</title>
    </head>
    <body>
        <?php echo "Welcome ", $basic->getUser(); ?>
    </body>
</html>