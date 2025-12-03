<?php
if ($radioTextTable) { ?>
    <a class="button" title="Κάθε φορά που προσθέτεις Νέα Κείμενα" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Texts','texts','960','');return false;">Texts Sitemap</a>
<?php }
if ($radioTextTable || $request_Table == "text_authors") { ?>
    <a class="button" title="Κάθε φορά που προσθέτεις Νέους Αρθρογράφους" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Authors','authors','960','');return false;">Authors Sitemap</a>
<?php }
if ($radioTextTable || $request_Table == "themes") { ?>
    <a class="button" title="Κάθε φορά που προσθέτεις Νέα Θέματα" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Themes','themes','960','');return false;">Themes Sitemap</a>
<?php }
if ($request_Table == "events") { ?>
    <a class="button" title="Κάθε φορά που προσθέτεις Νέα Εκδήλωση" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Events','events','960','');return false;">Events Sitemap</a>
<?php }
if ($request_Table == "conferences") { ?>
    <a class="button" title="Every time you add a new Conference" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Conferences','Conferences','960','');return false;">Conferences Sitemap</a>
<?php }
if ($request_Table == "conf_sessions") { ?>
    <a class="button" title="Every time you add a new Conference Session" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Sessions','Sessions','960','');return false;">Sessions Sitemap</a>
<?php }
if ($request_Table == "conf_papers") { ?>
    <a class="button" title="Every time you add a new Conference Paper" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Papers','Papers','960','');return false;">Papers Sitemap</a>
<?php }
if ($request_Table == "about") { ?>
    <a class="button" title="Κάθε φορά που προσθέτεις Νέα Κείμενα About" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=About','about','960','');return false;">About Sitemap</a>
<?php }
if ($request_Table == "articles") { ?>
    <a class="button" title="Every time you add a new Article" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Article','about','960','');return false;">Articles Sitemap</a>
<?php }
if ($request_Table == "products") { ?>
    <a class="button" title="Κάθε φορά που προσθέτεις Νέο Προϊόν" href="javascript:void(0)" onclick="openCenteredWindow('sitemaps/create_xml.php?type=Products','products','960','');return false;">Products Sitemap</a>
<?php }


?>