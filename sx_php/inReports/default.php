<section>
<h1 class="head"><span><?= $strReportFirstPageTitle ?></span></h1>
<?php
if(!empty($memoReportNotes) && $int_ProjectID == 0) { ?>
    <div class="bg_grey"><?=$memoReportNotes?></div>
<?php }


$rsProjects = sx_getProjects($int_ProjectID);
/**
 * The array $rsProjects might includ multiple rows (If $int_ProjectID = 0)
 * or just one row, (If $int_ProjectID > 0)
 * So, break the foreach loop if it contains only one row!
 */

if (!empty($rsProjects)) {
    foreach ($rsProjects as $row) {
        if (is_array($row)) {
            foreach ($row as $col_name => $val) {
                $iProjectID = $row["ProjectID"];
                $strProjectName = $row["ProjectName"];
                $strProjectSubName = $row["ProjectSubName"];
                $strMediaTopURL = $row["MediaTopURL"];
                $strImagesFromFolder = $row["ImagesFromFolder"];
                $memoProjectNotes = $row["ProjectNotes"];
            }
        } else {
            $iProjectID = $rsProjects["ProjectID"];
            $strProjectName = $rsProjects["ProjectName"];
            $strProjectSubName = $rsProjects["ProjectSubName"];
            $strMediaTopURL = $rsProjects["MediaTopURL"];
            $strImagesFromFolder = $rsProjects["ImagesFromFolder"];
            $memoProjectNotes = $rsProjects["ProjectNotes"];
        } ?>

            <h2 class="head"><a href="reports.php?projectid=<?= $iProjectID ?>"><?= $strProjectName ?></a></h2>
            <?php
            if (!empty($strProjectSubName)) { ?>
                <h3><?= $strProjectSubName ?></h3>
            <?php }

            if (intval($int_ProjectID) == 0) {
                if (!empty($strMediaTopURL)) {
                    $str_MediaTopURL = $strMediaTopURL;
                    if (strpos($strMediaTopURL, ";") > 0) {
                        $str_MediaTopURL = explode(";", $strMediaTopURL)[0];
                    }
                    get_Any_Media($str_MediaTopURL, "Center", "");
                }
            } else {
                $strFolderPhotos = "";
                if (!empty($strImagesFromFolder)) {
                    $strFolderPhotos = return_Folder_Images($strImagesFromFolder);
                }
                if (!empty($strFolderPhotos) && strpos($strFolderPhotos, ";") > 0) {
                    get_Manual_Image_Cycler($strMediaTopURL, $strFolderPhotos, "");
                } elseif (!empty($strMediaTopURL)) {
                    if (strpos($strMediaTopURL, ";") > 0) {
                        get_Manual_Image_Cycler($strMediaTopURL, "", "");
                    } else {
                        get_Any_Media($strMediaTopURL, "Center", "");
                    }
                } ?>
                <div class="text"><div class="text_max_width"><?= $memoProjectNotes ?></div></div>
            <?php
            } ?>
<?php
        if (!is_array($row)) {
            // The array contains only one row, so break the loop!
            break;
        }
    }
}
$rsProjects = null;
?>
</section>