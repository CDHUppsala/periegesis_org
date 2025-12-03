<?php

/**
 * Path to hidden files - accessible only for login users 
 * is NOT defined in admin_design.php for every site - BUT here
 * must be subfolder to /private 
 */
const sx_IncludePrivateArchivesFolder = false;

/**
 * ==========================================
 * The physical path to the private directory
 */

Define("SX_ProjectPrivate", PROJECT_PRIVATE . "/");
Define("sx_PrivateArchivesPath", SX_ProjectPrivate . sx_PrivateArchivesFolder);

