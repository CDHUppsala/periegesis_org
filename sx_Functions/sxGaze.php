<?php
// Basic checking - can also be removed
function containsDangerousPattern($value)
{
    $v = strtolower($value);

    // Only block patterns that NEVER appear in legitimate requests
    $dangerous = [
        '<script',        // XSS payloads
        'javascript:',    // JS URLs
        'onerror=',       // event handlers
        'onload=',
        'union select',   // SQL injection
        'drop table',     // destructive SQL
        '--',             // SQL comment injection
    ];

    foreach ($dangerous as $bad) {
        if (strpos($v, $bad) !== false) {
            return true;
        }
    }

    return false;
}

// Check GET parameters
foreach ($_GET as $key => $value) {
    if (containsDangerousPattern($key) || containsDangerousPattern($value)) {
        header("Location: index.php?sx=g1");
        exit;
    }
}

// Check POST parameters
foreach ($_POST as $key => $value) {
    if (containsDangerousPattern($key) || containsDangerousPattern($value)) {
        header("Location: index.php?sx=g2");
        exit;
    }
}

/**
 * Check user login from any application
 * Is used in different independent/separate gallery applications (e.g. PDF gallery)
 *  to hide/show entries that require login
 * @return bool
 */
function sx_check__UserSessionIsActive(): bool
{
    if (empty($_SESSION["User_Token"])) {
        return false;
    }

    $token = $_SESSION["User_Token"];
    $sessionKey = "Users_" . $token;

    if (empty($_SESSION[$sessionKey])) {
        return false;
    }

    // Optional: check expiration
    if (!empty($_SESSION['User_Expires']) && time() > $_SESSION['User_Expires']) {
        session_unset();
        session_destroy();
        return false;
    }

    return true;
}

// Add User_Expires with login - not used yet
// $_SESSION['User_Expires'] = time() + 3600; // 1 hour
