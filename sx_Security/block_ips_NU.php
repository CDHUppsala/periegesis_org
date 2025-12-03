
<?php
// List of blacklisted IP addresses
$blacklist = array(
    '47.128.0.0/16',
    '123.456.789.000', // Replace with the IP address you want to block
    '111.111.111.111'  // Add more IP addresses as needed
);

// Get the IP address of the incoming request
$ip = $_SERVER['REMOTE_ADDR'];

// Check if the IP address is in the blacklist
if (in_array($ip, $blacklist)) {
    // Deny access and optionally redirect to a custom page
    header('HTTP/1.1 403 Forbidden');
    header('Location: https://example.com/blocked');
    exit();
}


$wildcardDecimal = pow(2, (32 - $netmask)) - 1; // Calculate wildcard mask
$netmaskDecimal = ~$wildcardDecimal; // Invert bits to get network mask

function ipInRange($ip, $range) {
    list($range, $netmask) = explode('/', $range, 2);
    $rangeDecimal = ip2long($range);
    $ipDecimal = ip2long($ip);
    $wildcardDecimal = pow(2, (32 - $netmask)) - 1;
    $netmaskDecimal = ~$wildcardDecimal;
    return ($ipDecimal & $netmaskDecimal) === ($rangeDecimal & $netmaskDecimal);
}

// Check if the IP address is in the blacklist
foreach ($blacklist as $range) {
    if (ipInRange($ip, $range)) {
        // Deny access and optionally redirect to a custom page
        header('HTTP/1.1 403 Forbidden');
        header('Location: https://example.com/blocked');
        exit();
    }
}

// echo "Wildcard Mask: " . long2ip($wildcardDecimal) . "\n"; // Output: Wildcard Mask: 0.0.255.255
// echo "Network Mask: " . long2ip($netmaskDecimal) . "\n"; // Output: Network Mask: 255.255.0.0
?>