<h1><?php echo $page; ?></h1>
<p><?php echo $text; ?></p>

<ul>
    <!-- List Users -->
    <?php foreach($users as $k => $v): ?>
        <li>
            <a href="/admin/users?<?php echo $v['username']; ?>">
                <?php echo $v['username']; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<h2>Profile: <?php echo $user['username']; ?></h2>