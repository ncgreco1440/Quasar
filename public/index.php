 <?php
    // Just a single include statement!
    require "../lib/autoload.php";
    use Database;

    echo "Hello, World!<br/><br/>";

    $web = new Website("Leverage");
    $webpage = new Webpage("A New Web Page");
    Database\Connection::connect();
    Database\Extra::invoked();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Start Up</title>
    </head>
    <body>

    </body>
</html>