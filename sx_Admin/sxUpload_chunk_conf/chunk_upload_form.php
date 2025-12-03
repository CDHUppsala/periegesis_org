<?php

if (!isset($_SESSION["ParticipantID"]) || $radio_UploadConferenceFiles == false || $int_UploadConferenceID == 0) {
	header("Location: ". sx_PATH);
}

$strDestinationURL = "conf_" . $int_UploadConferenceID;
if (!file_exists("../images/" . $strDestinationURL)) {
	echo "<h2>The Upload Folder for the Conference $int_UploadConferenceID Does Not Exists</h2>
	<p>Please contact the administration of the site.</p>";
} else {
	$strFilePrefix = 'pid_' . $_SESSION["ParticipantID"] . "_"; ?>

	<script src="js/jq/jquery.min.js"></script>
	<script src="../sxScripts/src/plupload.full.min.js"></script>
	<script>
		var sx_DestinationURL = '<?= $strDestinationURL ?>';
		var sx_PrefixFileName = '<?= $strFilePrefix ?>';
	</script>

    <style>
        .plup_list_files {
            display: table;
            border: 1px solid #ddd;
        }

        .plup_list_files div {
            display: table-row;
            padding: 4px;
        }

        .plup_list_files div:nth-child(even) {
            background: #eee;
        }

        .plup_list_files div>* {
            display: table-cell;
            padding: 3px 8px;
            border: 1px solid #ddd;
        }
    </style>

	<section>
		<h1><?= lngUploadConferenceMedia ?></h1>
		<h4>Active Conference: <i><?= $str_ActualConferenceTitle ?></i></h4>
		<p>Please, contact the administration if the conference is not correct as files are uploaded in a conference specific folder!</p>
		<fieldset class="plup_files">
			<p class="row_flex">
				<button class="button-border-gray button-gradient-border" id="pick_files">Select First a File</button>
				<button class="button-border-gray button-gradient-border" id="upload_files">Upload the File</button>
			</p>
		</fieldset>
		<p>
			Allowed File Extentions: <b>Media:</b> mp3, mp4, ogg, webm,
			<b>Presentation:</b> odp, ppt, pptx, ppsx
		</p>
		<div class="plup_list_files" id="list_files">Your browser doesn't support HTML5 upload.</div>
		<pre id="console"></pre>

		<p class="align_right">
			<a class="button" href="<?=sx_PATH?>?pg=media">Reload the Page</a>
		</p>
		<h3><?= lngHelp ?></h3>
        <div class="text text_small text_padding">
			<p>Use this application to upload <b>Media Files</b> up to <b>1000mb</b> (1gb) and <b>Presentation Files</b> (PowerPoint and OpenDocument) up to <b>100mb</b>.
				Files with the <b>same name</b> replace each other, so, you can <b>reupload</b> a file if you have revised it.</p>
			<ul>
				<li>Presentation Files greater than 100mb <b>cannot be shown</b> in the website, so you have to export them as Video Files, in <b>mp4 Format</b>.
					<ul>
						<li>Please, select the resolution level <b>HD 720p</b> when you export a Presentation File to Video.</li>
						<li>Select the resolution level <b>HD 1080p</b> only for video files with a size up to about 300mb.</li>
						<li>Uploading files with a size over 300mb requires a <b>very fast internet connection</b>.</li>
						<li><b>Obs!</b> When you export a Presentation File to Video, it is a good practice to <b>remove</b> all background images and shapes
					as they increase the size of the video and make contents less readable.</li>
					</ul>
				</li>
				<li>Please, use short, <b>meaningful</b> initial names, with <b>Latin</b> characters and occasionally <b>numbers</b>.
					Use a single <b>hyphen</b> (-) between words, <b>Not spaces</b>!</li>
				<li>The file keeps its <b>initial name</b>
					with the addition of the Participant's ID as prefix: <b><i>pid_xx_Initial-Name.mp4</i></b>.</li>
			</ul>
			<p>Please notice that uploaded files must first <b>be processed</b> by the administration of the site
				before they can be visible in the site.</p>
			<ul>
				<li>If a file with the same name is already visible, the uploaded file will replace it and be visible immediately,
					although visitors might need to reload the page using <code>Ctrl + F5</code> till clear cached files.
				</li>
			</ul>
		</div>

	</section>
<?php
} ?>
<script>
	window.addEventListener("load", function () {
    var uploader = new plupload.Uploader({
        runtimes: 'html5,html4',
        browse_button: 'pick_files',
        url: 'ajax_chunk_upload.php?destin=' + sx_DestinationURL + '&prefix=' + sx_PrefixFileName,
        chunk_size: '1mb',
        filters: {
            max_file_size: '1000mb',
            mime_types: [{
                title: "Video and Presentation files",
                extensions: "mp3,mp4,ogg,webm,ppt,pptx,pps,ppsx,odp"
            }]
        },
        init: {
            PostInit: function () {
                document.getElementById('list_files').innerHTML = '';

                document.getElementById('upload_files').onclick = function () {
                    uploader.start();
                    return false;
                };
            },
            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('list_files').innerHTML += `<div id="${file.id}"><span>${file.name}</span> <span> ${plupload.formatSize(file.size)} </span> <strong></strong></div>`;
                });
            },
            UploadProgress: function (up, file) {
                document.querySelector(`#${file.id} strong`).innerHTML = `<span>${file.percent}%</span>`;
            },

            Error: function (up, err) {
                console.log(err);
            }
        }
    });
    uploader.init();
});
</script>