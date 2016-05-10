<?php
    use Quasar\Application\Header;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="robots" content="index follow">
        <meta name="revisit-after" content="7 days">
        <meta name="copyright" content="Nico Greco">
        <meta name="language" content="English">
        <meta name="reply-to" content="nico@nicogreco.com">
        <meta name="web_author" content="Nico Greco nicogreco.com">
        <title>Tech|Nico - <?php echo $page; ?></title>
        <link rel="icon" href="//tech.nicogreco.local/favicon.ico" type="image/x-icon" />
    </head>
    <body>
        <header>
            <nav>
                <ul>
                    <?php
                        $nav = Header::getMainNavigation();
                        foreach($nav as $key => $value)
                            echo "<li><a href=\"$value[link]\">$value[name]</a></li>";
                    ?>
                </ul>
            </nav>
        </header>