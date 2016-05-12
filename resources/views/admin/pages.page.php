<h1><?php echo $page; ?></h1>
<?php if(count($webpages)): ?>
    <ul>
        <?php foreach($webpages as $key => $value): ?>
            <li><a href="pages?<?php echo $value['name']; ?>"><?php echo $value['name']; ?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Requested Page Information -->
<?php
    if($selectedPage)
        require __DIR__."/../partials/admin-components/selected-page.partial.php";
?>