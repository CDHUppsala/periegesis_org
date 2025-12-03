<?php


/**
 * @param string $error_type : the error type
 * @return mixed : writes error information to Log File
 * DEVELOP: Distniguish between Error Type and Error Message as parameters
 */
function write_To_Log($form_name, $error_type = 'Reformulate function')
{
    $strPrivatePath = PROJECT_PATH . "/private/sx_log/sx_log_" . date('Y-m') . ".log";

    // Ensure the log file directory exists
    $logDir = dirname($strPrivatePath);
    if (!is_dir($logDir) && !mkdir($logDir, 0755, true)) {
        error_log("Failed to create log directory: $logDir");
        return false;
    }

    $timestamp = date('Y-m-d H:i:s');
    $sUserIP = sx_get_remote_ip();
    $sRealUserIP = sx_get_forwarded_ip();
    $sHistAddress = gethostbyaddr($sUserIP);

    // Sanitize error type to avoid malformed logs
    $error_type = htmlspecialchars($error_type, ENT_QUOTES, 'UTF-8');

    $logEntry = sprintf(
        "[%s] [Form: %s]  [Error: %s] [IP: %s] [Real IP: %s] [Host: %s] [User Agent: %s]%s",
        $timestamp,
        $form_name,
        $error_type,
        $sUserIP,
        $sRealUserIP,
        $sHistAddress,
        $_SERVER["HTTP_USER_AGENT"],
        PHP_EOL
    );

    // Write the log entry to the file
    if (file_put_contents($strPrivatePath, $logEntry, FILE_APPEND | LOCK_EX) === false) {
        error_log("Failed to write to log file: $strPrivatePath");
        return false;
    }

    return true;
}



/**
 * @param int $x : the number of characters to be returned 
 * @return string : random alphanumeric characters including (A-Z)(a-z)(0-9)
 */
function return_Random_Alphanumeric($x)
{
    /*
    $_retval = "";
    for ($i = 0; $i < $x; $i++) {
        $z = random_int(0, 2);
        if ($z == 0) {
            $intNumber = random_int(0, 1000);
            $iChar = chr(65 + intval(($intNumber / 1000) * 25));
        } elseif ($z == 1) {
            $intNumber = random_int(0, 1000);
            $iChar = chr(97 + intval(($intNumber / 1000) * 25));
        } else {
            $iChar = random_int(0, 9);
        }
        $_retval .= $iChar;
    }
    return $_retval;
    */
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $x)), 0, $x);
}

/**
 * ======================================
 * 1 Form token
 * ======================================
 */

/**
 * @param int $x : the number of characters to be returned
 * @return string : a random token, saved in sessions to veryfy forms
 */
function return_Random_Token($x)
{
    $token = bin2hex(random_bytes($x));
    return $token;
}

/**
 * @param string $formName : the prefix of the form Token
 * @param int $x : the number of characters to be returned
 * @return string : a random token with a prefix
 */
function sx_generate_form_token($formName, $x = 32)
{
    $token = return_Random_Token($x);
    $_SESSION[$formName . '_sx_token'] = $token;
    return $token;
}

/**
 * @param string $formName : the prefix of the form Token
 * @param string $token : the Form Token to be validated
 * @return bool : true or false Form Token
 */
function sx_valid_form_token($formName, $token)
{
    if (!isset($_SESSION[$formName . '_sx_token'])) {
        return false;
    } elseif ($_SESSION[$formName . '_sx_token'] !== $token) {
        unset($_SESSION[$formName . '_sx_token']);
        return false;
    } else {
        unset($_SESSION[$formName . '_sx_token']);
        return true;
    }
}


/**
 * ======================================
 * 2. If email address has MX Record
 * ======================================
 */


/**
 * @param string $email : Check if the email domain has MX
 * @param string $record : Check method
 * @return bool : true or false
 */
function sx_has_email_domain_mx($email, $record = 'MX')
{
    $radioValidEmail = false;
    $arrrEmail  = explode('@', $email);
    $user   = $arrrEmail[0];
    $domain = $arrrEmail[1];

    if (count($arrrEmail) === 2 && !empty($user) && !empty($domain) && checkdnsrr($domain, $record)) {
        $radioValidEmail = true;
    }
    return $radioValidEmail;
}



/**
 * ======================================
 * 3. IP Addresses
 * ======================================
 */

function check_User_IP($ip)
{
    return sx_check_User_IP($ip);
}

/**
 * @param string $ip the IP Address to be validated
 * @return bool : true or false
 */
function sx_check_User_IP($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return 1;
    } else {
        return 0;
    }
}

function return_User_IP()
{
    return sx_get_remote_ip();
}

/**
 * Get the REMOTE_ADDR IP Address
 * @return mixed : the IP Address or Null
 */
function sx_get_remote_ip()
{
    $remoteIP = $_SERVER['REMOTE_ADDR'] ?? null;
    return filter_var($remoteIP, FILTER_VALIDATE_IP) ? $remoteIP : null;
}

/**
 * Get HTTP_X_FORWARDED_FOR IP
 * If IP is blacklisted or unauthorized, 
 *  check for the clients real IP in the HTTP_X_FORWARDED_FOR header
 * @return string : the client's real IP, if any, else REMOTE_ADDR IP
 */
function sx_get_forwarded_ip()
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Extract the first IP from the list
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $forwardedIP  = trim($ipList[0]);

        // Validate the extracted IP
        if (filter_var($forwardedIP, FILTER_VALIDATE_IP)) {
            return  $forwardedIP;
        }
    }

    // Fallback to REMOTE_ADDR if no valid forwarded IP is found
    return sx_get_remote_ip();
}



/**
 * Returns the IP Address of the email's domain
 * @param mixed $email
 * @return string
 */
function sx_get_email_domain_ip(string $email): ?string
{
    // Extract the domain from the email
    $domain = substr(strrchr($email, "@"), 1);

    // Resolve the domain's IP
    if ($domain) {
        $domainIP = gethostbyname($domain);

        // Validate the domain IP
        if (filter_var($domainIP, FILTER_VALIDATE_IP)) {
            return $domainIP;
        }
    }
    return null;
}


/**
 * =====================================
 * 4. Check blacklisted IPs
 * =====================================
 */

/**
 * Not used yet - A whitelist of domains not to be checked for blacklisting
 * @param mixed $email_domain
 * @return bool
 */
function is_whitelisted_domain($email_domain)
{
    $whitelisted_domains = [
        'gmail.com',
        'outlook.com',
        'hotmail.com',
        'yahoo.com',
        'icloud.com',
        'protonmail.com',
        'zoho.com',
        'aol.com'
    ];

    return in_array($email_domain, $whitelisted_domains);
}

/**
 * List of DSN Blacklist providers
 * @return string[]
 */
function sx_get_dsn_blacklist_providers()
{
    /*
,
        "b.barracudacentral.org",
        "bl.spamcop.net"

        "dnsbl.sorbs.net",
        "dnsbl-1.uceprotect.net", 
        "sbl.spamhaus.org"
    */

    $dsnbl = [
        "zen.spamhaus.org"
    ];
    return $dsnbl;
}

/**
 * Check if any IP is blacklisted)
 * @param string $ip : the IP address 
 * @return bool : true if blacklisted, else false 
 */
function sx_is_ip_blacklisted($ip)
{
    $dnsbl_lookup = sx_get_dsn_blacklist_providers();
    $reverse_ip = implode(".", array_reverse(explode(".", $ip)));

    foreach ($dnsbl_lookup as $host) {
        if (checkdnsrr($reverse_ip . "." . $host . ".", "A")) {
            return true; // IP is blacklisted
        }
    }

    return false; // IP is not blacklisted
}

/**
 * =====================================
 * 5. Database queries
 * =====================================
 */

/**
 * Summary of sx_log_Rate_Limit_Attempt
 * @param string $remoteIP
 * @param bool $isRemoteIP_Blacklisted
 * @param string $forwardedIP
 * @param bool $isForwardedIP_blacklisted
 * @param string $domainIP
 * @param bool $isDomainIP_Blacklisted
 * @param string $formName
 * @return void
 */
function sx_log_Rate_Limit_Attempt(
    ?string $remoteIP = null,
    bool $isRemoteIP_Blacklisted = false,
    ?string $forwardedIP = null,
    bool $isForwardedIP_blacklisted = false,
    ?string $domainIP = null,
    bool $isDomainIP_Blacklisted = false,
    ?string $formName = null
): void {
    $conn = dbconn();
    $stmt = $conn->prepare("
        INSERT INTO ip_rate_limit_log (
            remote_ip, remote_ip_flagged, 
            forwarded_ip, forwarded_ip_flagged, 
            domain_ip, domain_ip_flagged, 
            attempt_time, form_name
        ) VALUES (
            :remote_ip, :remote_ip_flagged, 
            :forwarded_ip, :forwarded_ip_flagged, 
            :domain_ip, :domain_ip_flagged, 
            NOW(), :form_name
        )
    ");

    $stmt->execute([
        ':remote_ip' => $remoteIP,
        ':remote_ip_flagged' => $isRemoteIP_Blacklisted ? 1 : 0,
        ':forwarded_ip' => $forwardedIP,
        ':forwarded_ip_flagged' => $isForwardedIP_blacklisted ? 1 : 0,
        ':domain_ip' => $domainIP,
        ':domain_ip_flagged' => $isDomainIP_Blacklisted ? 1 : 0,
        ':form_name' => $formName
    ]);
}

/**
 * Summary of sx_check_rate_limit
 * @param string $remoteIP
 * @param string $forwardedIP
 * @param string $domainIP
 * @param array $rateLimits
 * @param string $timeFrame
 * @return bool
 */
function sx_check_rate_limit(
    ?string $remoteIP = null,
    ?string $forwardedIP = null,
    ?string $domainIP = null,
    array $rateLimits = ['remote' => 20, 'forwarded' => 15, 'domain' => 10],
    string $timeFrame = '1 HOUR'
): bool {
    $conn = dbconn();
    $stmt = $conn->prepare("SELECT 
            SUM(CASE WHEN remote_ip = :remote_ip AND remote_ip_flagged = 1 THEN 1 ELSE 0 END) as remote_count,
            SUM(CASE WHEN forwarded_ip = :forwarded_ip AND forwarded_ip_flagged = 1 THEN 1 ELSE 0 END) as forwarded_count,
            SUM(CASE WHEN domain_ip = :domain_ip AND domain_ip_flagged = 1 THEN 1 ELSE 0 END) as domain_count
        FROM ip_rate_limit_log 
        WHERE attempt_time > NOW() - INTERVAL $timeFrame
    ");

    $stmt->execute([
        ':remote_ip' => $remoteIP,
        ':forwarded_ip' => $forwardedIP,
        ':domain_ip' => $domainIP
    ]);

    $result = $stmt->fetch();

    // Check rates against provided limits
    return (
        ($result['remote_count'] ?? 0) <= $rateLimits['remote'] &&
        ($result['forwarded_count'] ?? 0) <= $rateLimits['forwarded'] &&
        ($result['domain_count'] ?? 0) <= $rateLimits['domain']
    );
}



/**
 * ===============================
 * 6. CHECK DISPOSABLE EMAIL DOMAINS
 * ===============================
 */

/**
 * Get the name of fiels in a directory and the physical path to them
 * - to be used for checking
 * @return string[]
 */
function get_FilesFromCacheDataDirectory()
{
    // Directory where  the files with dipsable email domains are saved
    $directory = PROJECT_PATH . '/private/cache_data/';
    $files = [];
    if (is_dir($directory)) {
        if ($dh = opendir($directory)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && is_file($directory . $file)) {
                    $files[] = $directory . $file;
                }
            }
            closedir($dh);
        }
    }
    return $files;
}

/**
 * Check if an email domain exists in downloaded disposable email blacklist files
 * either by fgets() or by creating an array from the file
 * @param mixed $email : the full email address
 * @return bool
 */
function is_email_domain_disposable($email, $type = 'fgets')
{
    if ($type === 'fgets') {
        return is_email_domain_disposable_fgets($email);
    } else {
        return is_email_domain_disposable_array($email);
    }
}

/**
 * Check files of disposable email domains by the PHP function fgets()
 * @param mixed $email
 * @return bool
 */
function is_email_domain_disposable_fgets($email)
{
    $domain = strtolower(trim(explode('@', $email)[1] ?? ''));

    if (!$domain || !filter_var('test@' . $domain, FILTER_VALIDATE_EMAIL)) {
        return false; // Invalid email
    }

    static $cache = [];
    if (isset($cache[$domain])) {
        return $cache[$domain]; // Use cached result
    }

    $sources = get_FilesFromCacheDataDirectory();

    foreach ($sources as $file) {
        if (is_readable($file) && ($handle = fopen($file, 'r'))) {
            while (($line = fgets($handle)) !== false) {
                if (trim(strtolower($line)) == $domain) {
                    fclose($handle);
                    return $cache[$domain] = true;;
                }
            }
            fclose($handle);
        }
    }
    return $cache[$domain] = false; // Domain not found in any file
}

/**
 * Check files of disposable email domains by the PHP function array_map()
 * @param mixed $email
 * @return bool
 */
function is_email_domain_disposable_array($email)
{
    $domain = strtolower(trim(explode('@', $email)[1] ?? ''));

    if (!$domain) {
        return false; // Invalid email
    }

    $sources = get_FilesFromCacheDataDirectory();

    foreach ($sources as $file) {
        $domains = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $domains = array_map('strtolower', $domains);

        if (in_array($domain, $domains)) {
            return true;
        }
    }
    return false; // Domain not found in any file
}


/**
 * =================
 * NOT USED FUNCTION
 * ==================
 */

/**
 * Check if an email domain exists in disposable email blacklists
 * cached in the DATABASE TABLE emali_blacklist
 * OBS! Not used, as none effective way to update the DB was found
 * @param mixed $email : the full email address
 * @return bool
 */
function is_email_domain_disposable_db($email)
{
    // Extract the domain part of the email
    $domain = strtolower(trim(explode('@', $email)[1] ?? ''));

    if (!$domain) {
        return false; // Invalid email
    }

    // Query the database
    $conn = dbconn();
    $query = "SELECT 1 FROM email_blocklist WHERE DomainName = :domain LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([':domain' => $domain]);

    return (bool)$stmt->fetchColumn(); // Return true if the domain exists
}


function sx_handle_error($formName, $message, $logMessage = null)
{
    if ($logMessage) {
        write_To_Log($formName, $logMessage);
    }
    echo '<h2>An Error Occurred</h2>';
    echo '<p>' . htmlspecialchars($message) . '</p>';
    exit;
}

/* Example
if (!$radioValidFooteToken) {
    $reason = !isset($_POST['FooterFormToken']) ? "Empty Token" : "Wrong Token";
    handleError("An error occurred. Please reload the page and try again.", "Newsletter Footer: {$reason} Hack-Attempt");
}
*/