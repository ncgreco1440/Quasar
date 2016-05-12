<h2><?php echo $selectedPage['Pagename']; ?></h2>
<?php foreach($selectedPage['Text'] as $key => $value): ?>
    <textarea><?php echo $value; ?></textarea>
<?php endforeach; ?>
print_r($selectedPage);?>