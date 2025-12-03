<div>
    <a class="button" href="javascript:void(0)" onclick="openCenteredWindow('sxCleanText/sxPreserveFormedText.php','formWordText','920','');return false;"><?= lngPreserveFormedText ?></a>
    <a class="button" href="javascript:void(0)" onclick="openCenteredWindow('sxCleanText/sxCleanText.php','cleanTextWin','920','');return false;"><?= str_replace(" ","<br>",lngCleanText) ?></a>
    <a class="button" href="javascript:void(0)" onclick="openCenteredWindow('sxUpload/form_resize_upload_images.php?short=yes','loadArchiveWin','1200','');return false;"><?= str_replace(" ","<br>",lngUploadImages) ?></a>
    <a href="javascript:void(0)" class="button jqLoadArchives" data-id="Images">Copy<br>Image Names</a>
    <?php
    if (in_array($request_Table, $arr_ConferenceTables)) { ?>
        <a href="javascript:void(0)" class="button jqLoadArchives" data-id="Conferences">Copy<br>Conference Files</a>
    <?php }
    if ($request_Table == "conf_papers") { ?>
        <a href="javascript:void(0)" class="button jqLoadArchives" data-id="Abstracts">Copy<br>Abstracts</a>
    <?php }
    if ($request_Table == "templates") { ?>
        <a href="javascript:void(0)" class="button jqLoadArchives" data-id="GetColorSchemes">Get Color<br>Schemes</a>
    <?php }
    if ($request_Table == "item_sections" || $request_Table == "first_page_sections" || $request_Table == "templates") { ?>
        <a href="javascript:void(0)" class="button jqLoadArchives" data-id="GetTemplatesList">View<br>Templates</a>
    <?php }
    if (sx_radioUseAccessories) { ?>
        <a href="javascript:void(0)" class="button jqLoadArchives" data-id="accessories"><?= str_replace(" ","<br>",lngCopyAccessories) ?></a>
    <?php }
    //if ($request_Table == "books" && strpos($_SERVER["PHP_SELF"], "/edit.php") == 0) {
    if ($request_Table == "books") { ?>
        <a href="javascript:void(0)" class="button jqLoadArchives" data-id="BookToAuthors">Load<br>Authors</a>
    <?php }
    if ($request_Table == "admin_login" || $request_Table == "forum_members" || $request_Table == "users" || $request_Table == "newsletters" || $request_Table == "customers") { ?>
        <a class="button" href="javascript:void(0)" onclick="openCenteredWindow('sxHashing/sx_getHashedCode.php','hash','920','');return false;">Hash<br>Password</a>
    <?php } ?>
</div>