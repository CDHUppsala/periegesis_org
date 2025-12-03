<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

function get_courses_spam_attacts()
{
    $conn = dbconn();
    $sql = "SELECT 
        SUM(Endeavours) AS Endeavours_ByIP,
        LEFT(UpdateDate,10) AS Update_Date,
        LEFT(IPAddress,6) AS Left_IPAddress
    FROM course_blacklisted_ips
    WHERE (ValidMailAddress = 0)
    GROUP BY Update_Date, Left_IPAddress
    HAVING (Endeavours_ByIP > 1)
    ORDER BY Update_Date DESC, Endeavours_ByIP DESC, Left_IPAddress ";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

function get_events_attacts_by_date()
{
    $conn = dbconn();
    $sql = "SELECT 
        SUM(Endeavours) AS Endeavours_ByIP,
        LEFT(UpdateDate,
            10) AS Update_Date,
        IPAddress AS IPAddress
    FROM
        event_blacklisted_ips
    WHERE
        (ValidMailAddress = 0)
    GROUP BY Update_Date , IPAddress
    HAVING (Endeavours_ByIP > 1)
    ORDER BY Update_Date DESC , Endeavours_ByIP DESC , IPAddress";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

function get_events_attacts_by_event()
{
    $conn = dbconn();
    $sql = "SELECT 
        SUM(Endeavours) AS Endeavours_ByIP,
        LEFT(FormSource,
            13) AS EventID,
        IPAddress AS IPAddress
    FROM
        event_blacklisted_ips
    WHERE
        (ValidMailAddress = 0)
    GROUP BY EventID , IPAddress
    HAVING (Endeavours_ByIP > 1)
    ORDER BY EventID DESC , Endeavours_ByIP DESC , IPAddress";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

function get_conferences_spam_attacts()
{
    $conn = dbconn();
    $sql = "SELECT SUM(Endeavours) AS Endeavours_ByIP,
        LEFT(UpdateDate,10) AS Update_Date,
        LEFT(IPAddress,6) AS Left_IPAddress
    FROM conf_blacklisted_ips
    WHERE (ValidMailAddress = 0)
    GROUP BY Update_Date, Left_IPAddress
    HAVING (Endeavours_ByIP > 0)
    ORDER BY Update_Date DESC, Endeavours_ByIP DESC, Left_IPAddress";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

function get_view_attacks_by_date()
{
    $conn = dbconn();
    $sql = "SELECT 
        LEFT(AttackDate, 10) AS Attack_Date,
        IPAddress AS IP_Address,
        COUNT(0) AS Frequency
    FROM attacking_ips
    GROUP BY Attack_Date , IPAddress
    ORDER BY Attack_Date DESC , Frequency DESC , IPAddress ";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}


function get_view_attacks_by_ip()
{
    $conn = dbconn();
    $sql = "SELECT 
        IPAddress AS IPAddress,
        COUNT(0) AS Frequency
    FROM
        attacking_ips
    GROUP BY IPAddress
    ORDER BY Frequency DESC , IPAddress";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

function get_view_attacks_by_message()
{
    $conn = dbconn();
    $sql = "SELECT 
        ErrorMessage AS ErrorMessage,
        IPAddress AS IPAddress,
        COUNT(0) AS Frequency
    FROM
        attacking_ips
    GROUP BY ErrorMessage , IPAddress
    ORDER BY Frequency DESC , IPAddress";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

$strViewTable = $_GET['view_table'] ?? '';
$strViewMode = $_GET['mode'] ?? '';


$aResults = "";
$strViewTitle = "";
if (!empty($strViewTable)) {
    if ($strViewTable == 'courses') {
        $aResults = get_courses_spam_attacts();
        $strViewTitle = "Students Area";
    } elseif ($strViewTable == 'conferences') {
        $aResults = get_conferences_spam_attacts();
        $strViewTitle = "Conferences";
    } elseif ($strViewTable == 'events') {
        if (!empty($strViewMode)) {
            if ($strViewMode === 'bydate') {
                $aResults = get_events_attacts_by_date();
                $strViewTitle = "Events by Date";
            } else {
                $aResults = get_events_attacts_by_event();
                $strViewTitle = "Events by Event ID";
            }
        } else {
            $aResults = get_events_attacts_by_event();
            $strViewTitle = "Events by Event ID";
        }
    } elseif ($strViewTable == 'attacks') {
        if (!empty($strViewMode)) {
            if ($strViewMode === 'byip') {
                $aResults = get_view_attacks_by_ip();
                $strViewTitle = "Attacks by IP Address";
            } elseif ($strViewMode === 'bydate') {
                $aResults = get_view_attacks_by_date();
                $strViewTitle = "Attacks by Date";
            } else {
                $aResults = get_view_attacks_by_message();
                $strViewTitle = "Attacks by Message";
            }
        } else {
            $aResults = get_view_attacks_by_ip();
            $strViewTitle = "Attacks by IP Address";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>SX Statistics</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js"></script>
</head>

<body id="bodyStats" class="body">
    <header id="header">
        <h2>Check Possible Attacks</h2>
    </header>
    <section class="maxWidth">

        <h1><?php echo $strViewTitle ?></h1>
        <?php
        if ($strViewTable == 'events') { ?>
            <h3>Check Blacklisted IPs for Possible Attacks</h3>
            <p>The table <b>blacklisted_ips</b> contains IP Addresses that have successfully submitted  a form but are blacklisted by external, 
            online sources. If you Suspect Brute Force Attacks:</p>
            <ul>
                <li>Open the table <b>Event Setup</b> and set the value of the fields
                    <b>Stop Mail With Blacklisted IP</b> temporally to YES. This will stop sending mails to fake email addresses.
                </li>
                <li>Open the table <b>Blacklisted IP Addresses</b> and check the validity of Names and Email Addresses of the last entries.
                    If you find them valid, set the value of the field <b>Valid Mail Address</b> to YES.

                    <ul>
                        <li>If the visitor will try to register again, the applicaton will be registered normally this time.</li>
                    </ul>
                </li>
                <li>If the attack slows down or stops, set the value of the field <b>Stop Mail With Blacklisted IP</b> back to NO.</li>
            </ul>

        <?php
        }
        if ($strViewTable == 'courses' || $strViewTable == 'conferences') { ?>
            <h3>Check Blacklisted IPs for Possible Attacks</h3>
            <ul>
                <li>By default, mails are not sent to applicants with blacklisted IP.
                    However, the application <b>is registered</b> in the database and a mail is sent to the administration for approval,
                    with a warning for the blacklisted IP.
                    <ul>
                        <li>If approved, a mail will be sent to the applicant
                            with a link to verify the email address and activate the registration.
                        </li>
                    </ul>
                </li>
                <li>In case of <b>suspected attack</b>, the system <b>automatically stops</b> the registration of applications
                    comming from blacklisted IP.
                    <ul>
                        <li>You can open the table <b>Blacklisted IP Addresses</b> and check the validity of Names and Email Addresses of the last entries.
                            If you find them valid, set the value of the field <b>Valid Mail Address</b> to YES.</li>
                        <li>If the applicant will try to register again, the applicaton will be registered normally this time.</li>
                    </ul>
                </li>
            </ul>

        <?php
        }
        if ($strViewTable == 'attacks') { ?>
            <h3>Check Hack Attacks from IP Addresses</h3>
            <p>The table <b>attacking_ips</b> contains all IP Addreses that have tried to submit forms with unauthorized means and have been <b>rejected</b>
            by the program. The table also contains information about the Form used and the reason of rejection.</p>
            <p>Eventually, frequently encountered IP Addresses might be included in a list of forbitten IPs.</p>
        <?php
        }
        if (is_array(($aResults))) { ?>
            <table>
                <tr>
                    <?php
                    foreach ($aResults as $row) {
                        foreach ($row as $key => $value) {
                            echo "<th>$key</th>";
                        }
                        break;
                    } ?>
                </tr>
                <?php
                foreach ($aResults as $row) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        <?php
        } else {
            echo '<p>No records are found!</p>';
        }
        $aResults = null;
        ?>

    </section>
</body>

</html>