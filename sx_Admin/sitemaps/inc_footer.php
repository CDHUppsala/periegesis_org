<section>
    <h3>Read this Information</h3>
    <div class="text">
        <ul>
            <li>The <b>Root Sitemap</b> is created in the Root Directory (<u>www.yourdomain.gr/sitemap.xml</u>) of your site and contains links
                to all ordinary <b>Content Sitemaps</b> of the site (for Texts, Authors, Themes, etc.).</li>
            <li><b>Content Sitemaps</b> are by default located in the folder <u>www.yourdomain.gr/sitemap/</u>.</li>
            <li>The Root Sitemap just informs <b>Search Engines</b> where to find the Content Sitemaps of the site.</li>
            <li>If the site is <b>Multilinqual</b>, links to Text Sitemaps are automatically created for every active language (e.g. <b>articles_en.xml</b> and <b>articles_el.xml</b>).</li>
            <li>If your site contains a huge number of texts (> 15 000), you can create Text Sitemaps for every <b>Year</b> (and language, if multiliqual,
                e.g. <b>articles_en_2017.xml</b> and <b>articles_en_2018.xml</b>)
                <ul>
                    <li>In that case, links to Text Sitemaps in the Root Sitemap must be created for every Year.</li>
                    <li>Contact the creator of the site to activate these options.</li>
                </ul>
            </li>
            <li>Basically, you need to create a Root Sitemap <b>only once</b>. Than, <b>recreate</b> it only if you add a <b>New</b> Content Sitemap (e.g. a Text Sitemap for a new Year).</li>
        </ul>
    </div>
</section>
<section>
    <h3>Existing Content Site Maps</h3>
    <div class="floatClear maxWidth">
        <?php
        if (!empty($strSitemapFolder)) {
            if (!is_dir($strSitemapFolder)) { ?>
                <h4><?= lngTheRequestedFolderDoesNotExist ?></h4>
                <?php
            } else {
                if ($dh = opendir($strSitemapFolder)) {
                    while (($file = readdir($dh)) !== false) {
                        if (is_file($strSitemapFolder . $file)) { ?>
                            <a target="_blank" class="color" href="<?= $levelsBack . $file ?>"><b><?= $file ?></b></a>
                            (<?= number_format(filesize($strSitemapFolder . $file), 0, ",", " ") ?> kb)<br>
                    <?php }
                    }
                    closedir($dh);
                } else { ?>
                    <h4><?= lngTheFolderIsEmpty ?></h4>
        <?php }
            }
        }
        ?>
    </div>
</section>
<section>
    <h3>Root Sitemap</h3>
    <div class="floatClear maxWidth">
        <?php
        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/sitemap.xml")) { ?>
            <a target="_blank" class="color" href="../../sitemap.xml"><b>sitemap.xml</b></a>
        <?php } ?>
    </div>
</section>
<?php if ($radioShowExplanations) { ?>
    <section>
        <h3>Explanations</h3>
        <div class="floatClear maxWidth text">
            <ol>
                <li><b>Content Sitemaps</b> for Texts, Authors, Themes and other groups of records contain links (Full URL) to all articles, authors, themes, etc. in your database.
                    <ul>
                        <li><b>Search Engines</b> read the Content Sitemaps, open the links and create indices for your site (instead of scanning your site for meaningful links).</li>
                        <li>This program saves the Content Sitemaps (as XML-Files) in the folder <b>/sitemap/</b>.
                            <ul>
                                <li>You can open and see all created Sitemaps by clicking on the link <b>Load Archives</b> or <b>Φόρτωσε αρχεία στο Server</b>,
                                    by selecting the Tab <b>View Folder Archives</b> or <b>Δες αρχεία φακέλων</b> and then by selecting the <b>Folder sitemap</b>.</li>
                                <li>You can also <b>Delete</b> a Content Sitemap from the list that opens by the above procedure.</li>
                            </ul>
                        </li>
                    </ul>
                <li>The <b>Root Sitemap</b> is saved in the Root Directory of your site and just tell Search Engines
                    where to find the above Content Sitemaps for Texts, Authors, Themes, etc.
                    <ul>
                        <li><b>Obs!</b> Please check to create Content Sitemaps for every link in the Root Sitemap (and vice versa).</li>
                        <li>If you Delete a Content Sitemap or Add a new one, recreate the Root Sitemap.</li>
                    </ul>
                </li>
            </ol>
        </div>
    </section>
<?php
} ?>