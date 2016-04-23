<?php use Functions\Functions; ?>
<h1><?php echo $page; ?></h1>
<p><?php echo $text; ?></p>

<ul>
    <!-- List Users -->
    <?php if(count($users)): ?>
        <?php foreach($users as $k => $v): ?>
            <li>
                <a href="/admin/users?<?php echo $v; ?>">
                    <?php echo $v; ?>
                </a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<!-- Requested User Information -->
<?php
    if($user)
        require __DIR__."/../partials/admin-components/user.partial.php";
?>