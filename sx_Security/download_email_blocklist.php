<?php

ignore_user_abort(true);
set_time_limit(0);

function download_disposebleEmailDomainfile($url, $savePath)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification if needed
    $data = curl_exec($ch);

    if (curl_errno($ch)) {
        write_To_Log("Failed download file $url Error: " . curl_error($ch) . " at date: " . date('Y-m-d H:i:s'));
        return false;
    }

    curl_close($ch);

    // Write data to file
    if (file_put_contents($savePath, $data)) {
        return true;
    } else {
        return false;
    }
}

function get_disposebleEmailDomainFiles($sources, $saveDir)
{
    $loop = 0;
    foreach ($sources as $source) {
        // Extract the file name from the URL
        $fileName = basename(parse_url($source, PHP_URL_PATH));
        $savePath = $saveDir . $loop . '_' . $fileName;

        write_To_Log("Start download source: $source at date: " . date('Y-m-d H:i:s'));

        // Download and save the file
        if (download_disposebleEmailDomainfile($source, $savePath)) {
            write_To_Log("File saved to: $savePath at date: " . date('Y-m-d H:i:s'));
        } else {
            write_To_Log("Failed to save file: $savePath at date: " . date('Y-m-d H:i:s'));
        }
        $loop++;
    }
}



function update_data_caching()
{
    global $insert_Caching_Name;
    $conn = dbConn();

    try {
        if ($insert_Caching_Name) {
            $query = "INSERT INTO data_caching
                (CachingName, CachedData)
                VALUES (:cachingName,:cachedData)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':cachingName' => 'LastBlacklistDownload',
                ':cachedData' => time()
            ]);
            write_To_Log("Data caching name LastBlacklistDownload inserted successfully.");
        } else {
            $query = "UPDATE data_caching
            SET CachedData = :cachedData
            WHERE CachingName = :cachingName";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':cachingName' => 'LastBlacklistDownload',
                ':cachedData' => time()
            ]);

            write_To_Log("Data caching name LastBlacklistDownload updated successfully.");
        }
    } catch (PDOException $e) {
        write_To_Log("Error updating data caching: " . $e->getMessage());
    }
}

function should_update_files()
{
    global $insert_Caching_Name;
    $insert_Caching_Name = false;
    $conn = dbConn();
    $query = "SELECT CachedData FROM data_caching
        WHERE CachingName = 'LastBlacklistDownload'
        LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $lastUpdateTime = $stmt->fetchColumn();

    if ($lastUpdateTime === false) {
        $insert_Caching_Name = true;
        return true;
    }

    // Check if more than 24 hours have passed
    return (time() - $lastUpdateTime) >= 86400;
}

// Define the sources array
$sources = [
    'https://raw.githubusercontent.com/kslr/disposable-email-domains/master/list.txt',
    'https://raw.githubusercontent.com/disposable/disposable-email-domains/master/domains_strict.txt',
    'https://raw.githubusercontent.com/disposable/disposable-email-domains/master/domains.txt'
];

// Directory to save the downloaded files
$saveDir = PROJECT_PATH . '/private/cache_data/';

if (!file_exists($saveDir)) {
    mkdir($saveDir, 0777, true); // Create the directory if it doesn't exist
}

if (should_update_files()) {
    get_disposebleEmailDomainFiles($sources, $saveDir);
    update_data_caching();
}

/*
Do you give me the right to use (plagiate) the expression: 
'Glad I could help! Sometimes, a fresh set of virtual eyes is all it takes 
to spot those little details.'?

Absolutely! Feel free to use that expression. 
I’m here to assist and inspire. 
You have my virtual blessing to use it however you like.

You're welcome! Symmetry often brings clarity and elegance to code. I'm glad you found the approach appealing.

*/
