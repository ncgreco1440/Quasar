<?php use Functions\Functions; ?>
<h2><?php echo $selectedPage['Pagename'];?></h2>
<?php
    $form =
    [
        "name" => ["label" => "Page Name", "type" => "text", "required" => true],
        "banner" => ["label" => "Banner", "type" => "file", "required" => false],
        "main_text" => ["label" => "Main Paragraph", "type" => "textarea", "required" => true],
        "sub_text_1" => ["label" => "Subsidary Text Field #1", "type" => "textarea", "required" => false],
        "sub_text_2" => ["label" => "Subsidary Text Field #2", "type" => "textarea", "required" => false],
        "sub_text_3" => ["label" => "Subsidary Text Field #3", "type" => "textarea", "required" => false],
        "image_1" => ["label" => "Subsidary Image #1", "type" => "file", "required" => false],
        "image_2" => ["label" => "Subsidary Image #2", "type" => "file", "required" => false],
        "image_3" => ["label" => "Subsidary Image #3", "type" => "file", "required" => false],
        "image_4" => ["label" => "Subsidary Image #4", "type" => "file", "required" => false],
    ];
    echo Functions::genForm("savePage", "POST", "/admin/pages?$selectedPage[Pagename]", $form,
        $selectedPage['Contents'], "multipart/form-data");
?>
