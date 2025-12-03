<?php


// Check if emale has MX Record Check, using PHP function checkdnsrr()
if (!sx_has_email_domain_mx($s_EmailToCheck)) {
    write_To_Log($s_SentFormName, "Email address with no MX Record: {$s_EmailToCheck} Hack-Attempt ");
    $radioContinue = false;
    $arrError[] = lngWriteCorrectEmail;
}


/**
 * IP Adresses
 */

$remoteIP = sx_get_remote_ip();
$domainIP = sx_get_email_domain_ip($s_EmailToCheck);
$forwardedIP = sx_get_forwarded_ip();

if (empty($remoteIP)) {
    write_To_Log($s_SentFormName, "Empty Remote IP Hack-Attempt!");
    echo '<h2>No Way Home 3!</h2>';
    exit;
} else {
    $s_ClientIP = $remoteIP;
}

if (empty($domainIP)) {
    write_To_Log($s_SentFormName, "Empty Email Domain IP for: {$s_EmailToCheck} Hack-Attempt");
    echo '<h2>An error occurred 1</h2>';
    echo '<p>The email domain could not be verified. Please check your email address, reload the page and try again.</p>';
    exit;
} else {
    $domain = substr(strrchr($s_EmailToCheck, "@"), 1);
    if (!is_whitelisted_domain($domain)) {
        $s_ClientIP = $domainIP;
    }
}

// The 2 following IPs cannot be empty here
$isDomainIP_Blacklisted = sx_is_ip_blacklisted($domainIP);
$isRemoteIP_Blacklisted = sx_is_ip_blacklisted($remoteIP);

$isForwardedIP_blacklisted = false;
if (!empty($forwardedIP)) {
    $isForwardedIP_blacklisted = sx_is_ip_blacklisted($forwardedIP);
}

if ($isDomainIP_Blacklisted) {
    write_To_Log($s_SentFormName, "Email Domain IP Blacklisted for: {$s_EmailToCheck} {$domainIP} Hack-Attempt");
    echo '<h2>An error occurred 2</h2>';
    echo '<p>The email domain could not be verified. Please check your email address, reload the page and try again.</p>';
    exit;
}
if ($isForwardedIP_blacklisted) {
    write_To_Log($s_SentFormName, "Remote IP Blacklisted for: {$s_EmailToCheck} {$forwardedIP} Warning Possible Hack-Attempt");
}
if ($isRemoteIP_Blacklisted) {
    write_To_Log($s_SentFormName, "Forwarded IP Blacklisted for: {$s_EmailToCheck} {$remoteIP} Information Possible Hack-Attempt");
}

if ($isRemoteIP_Blacklisted || $isDomainIP_Blacklisted || $isForwardedIP_blacklisted) {
    $is_ClientIPBlackListed = 1;
    sx_log_Rate_Limit_Attempt(
        $remoteIP,
        $isRemoteIP_Blacklisted,
        $forwardedIP,
        $isForwardedIP_blacklisted,
        $domainIP,
        $isDomainIP_Blacklisted,
        $s_SentFormName
    );

    $isAllowed = sx_check_rate_limit(
        $remoteIP,
        $forwardedIP,
        $domainIP,
        [
            'remote' => 20,   // Least strict
            'forwarded' => 15, // Moderate
            'domain' => 10    // Strictest
        ],
        '1 HOUR'
    );

    if (!$isAllowed) {
        write_To_Log($s_SentFormName, "Rate limit exceeded: {$s_EmailToCheck} Information Possible Hack-Attempt");
        echo '<h2>Rate limit exceeded</h2>';
        echo '<p>Please try again later.</p>';
        exit;
    }
}

// Check time interval after checking email address and IPs
if (!isset($_SESSION[$s_TimeSessionName]) || !return_Is_Date($_SESSION[$s_TimeSessionName])) {
    write_To_Log($s_SentFormName, "Empty Form Time - Hack-Attempt");
    echo '<h2>No Way Home 4!</h2>';
    exit;
} else {
    $seconds_passing = return_Date_Time_Total_Difference($_SESSION[$s_TimeSessionName], date('Y-m-d H:i:s'), 'seconds');
    // Email has no MX Record or Remot IP or Forwarded IP is Blacklisted
    if ($radioContinue === false || $is_ClientIPBlackListed === 1) {
        sleep(10);
        if ((int) $seconds_passing < 10) {
            write_To_Log($s_SentFormName, "Martked Form sent less than 10 seconds - Hack-Attempt");
            echo '<h2>No Way Home 5!</h2>';
            exit;
        }
    } else {
        // Email address and IPs are valid
        if ((int) $seconds_passing < 5) {
            write_To_Log($s_SentFormName, "Form sent less than 10 seconds - Potensial Hack-Attempt");
            echo '<h2>Session Timeout!</h2>';
            echo '<p>Please reload the page, check your information and try again.</p>';
            exit;
        }
    }
}
