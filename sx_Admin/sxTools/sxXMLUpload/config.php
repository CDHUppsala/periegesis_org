<?php
// delete
CONST REMOTE_ImportExportFolder = "import_export_files/";
define("REMOTE_ServerPath", realpath(dirname($_SERVER["DOCUMENT_ROOT"]) . "/private/" . REMOTE_ImportExportFolder));
