<?php
if (!empty($strNewDatabaseName) && !str_contains($strNewDatabaseName, ' ')) {
    $sql = "CREATE DATABASE IF NOT EXISTS $strNewDatabaseName";
    echo "# $sql \n\n";
    $return = $conn->exec($sql);
    if ($return == false) {
        echo "Error in creating Database in MySQL. Propably, you have no rights to create schemas." . "\n";
        echo "Error Description: " . $err . $description;
    } else {
        echo "<p>The New Table Schema <b>$strNewDatabaseName</b> has been created in the target MySQL Database.</p>";
    }
}
