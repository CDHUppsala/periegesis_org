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

function get_events_spam_attacts()
{
    $conn = dbconn();
    $sql = "SELECT 
        SUM(Endeavours) AS Endeavours_ByIP,
        LEFT(UpdateDate,10) AS Update_Date,
        LEFT(IPAddress,6) AS Left_IPAddress
    FROM event_blacklisted_ips
    WHERE (ValidMailAddress = 0)
    GROUP BY Update_Date , Left_IPAddress
    HAVING (Endeavours_ByIP > 1)
    ORDER BY Update_Date DESC , Endeavours_ByIP DESC , Left_IPAddress";
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


$strViewTable = "";
if (isset($_GET['view_table'])) {
    $strViewTable = $_GET['view_table'];
}

$aResults = "";
$strViewTitle = "";
if (!empty($strViewTable)) {
    if ($strViewTable == 'courses') {
        $aResults = get_courses_spam_attacts();
        $strViewTitle = "Students Area";
    }

    if ($strViewTable == 'events') {
        $aResults = get_events_spam_attacts();
        $strViewTitle = "Events";
    }

    if ($strViewTable == 'conferences') {
        $aResults = get_conferences_spam_attacts();
        $strViewTitle = "Conferences";
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
        <h2>Check Spam Attackts from Blacklisted IPs</h2>
    </header>
    <section class="maxWidth">

        <h1><?php echo $strViewTitle ?></h1>
        <h3>If you Suspect Spam Attack:</h3>
        <?php
        if ($strViewTable == 'events') { ?>
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
                <li>If the spam attack slows down or stops, set the value of the field <b>Stop Mail With Blacklisted IP</b> back to NO.</li>
            </ul>

        <?php
        }
        if ($strViewTable == 'courses' || $strViewTable == 'conferences') { ?>
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
                <li>In case of <b>suspected spam attack</b>, the system <b>automatically stops</b> the registration of applications
                    comming from blacklisted IP.
                    <ul>
                        <li>You can open the table <b>Blacklisted IP Addresses</b> and check the validity of Names and Email Addresses of the last entries.
                            If you find them valid, set the value of the field <b>Valid Mail Address</b> to YES.</li>
                        <li>If the applicant will try to register again, the applicaton will be registered normally this time.</li>
                    </ul>
                </li>
            </ul>

        <?php
        } ?>
        <h3>Check Possible Spam Attack:</h3>
        <p>The list bellow is created from the table <b>Blacklisted IP Addresses</b> and shows the <b>number</b>
            of registration endeavours <b>per day</b> by blacklisted IPs which are <b>equal</b> as to their first 6 characters.</p>
        <ul>
            <li>Please notice that the list anly inludes IP addresses where the value of <b>Valid Mail Address</b> is set to false.</li>
            <li>If Registration Endeavours are more then 3-5 per day, you might suspect a spam attack</li>
            <li>If they are more than 10, it is certainly a spam attack.</li>
        </ul>
        <table>
            <tr>
                <th>Registration Endeavours</th>
                <th>Endavour Date</th>
                <th>IP - First 6 Characters</th>
            </tr>
            <?php
            $radioEmpty = false;
            if (is_array(($aResults))) {
                foreach ($aResults as $row) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
            } else {
                $radioEmpty = true;
            }
            $aResults = null;
            ?>
        </table>
        <?php
        if ($radioEmpty) {
            echo '<p>No records are found!</p>';
        }
        ?>

    </section>
    <hr>
    <section class="maxWidth">
        <h3>Example of Spam Attack</h3>

        <table>
            <tr>
                <th>Registration Endeavours</th>
                <th>Endavour Date</th>
                <th>IP - First 6 Characters</th>
            </tr>
            <tr>
                <td>27</td>
                <td>2022-12-01</td>
                <td>213.87</td>
            </tr>
            <tr>
                <td>69</td>
                <td>2022-12-01</td>
                <td>31.173</td>
            </tr>
            <tr>
                <td>24</td>
                <td>2022-11-30</td>
                <td>176.59</td>
            </tr>
            <tr>
                <td>118</td>
                <td>2022-11-30</td>
                <td>178.17</td>
            </tr>
            <tr>
                <td>40</td>
                <td>2022-11-30</td>
                <td>213.87</td>
            </tr>
            <tr>
                <td>80</td>
                <td>2022-11-30</td>
                <td>31.173</td>
            </tr>
            <tr>
                <td>26</td>
                <td>2022-11-29</td>
                <td>176.59</td>
            </tr>
            <tr>
                <td>140</td>
                <td>2022-11-29</td>
                <td>178.17</td>
            </tr>
            <tr>
                <td>57</td>
                <td>2022-11-29</td>
                <td>213.87</td>
            </tr>
            <tr>
                <td>93</td>
                <td>2022-11-29</td>
                <td>31.173</td>
            </tr>
            <tr>
                <td>28</td>
                <td>2022-11-28</td>
                <td>176.59</td>
            </tr>
            <tr>
                <td>128</td>
                <td>2022-11-28</td>
                <td>178.17</td>
            </tr>
            <tr>
                <td>50</td>
                <td>2022-11-28</td>
                <td>213.87</td>
            </tr>
            <tr>
                <td>66</td>
                <td>2022-11-28</td>
                <td>31.173</td>
            </tr>
            <tr>
                <td>26</td>
                <td>2022-11-27</td>
                <td>176.59</td>
            </tr>
            <tr>
                <td>123</td>
                <td>2022-11-27</td>
                <td>178.17</td>
            </tr>
        </table>
    </section>
</body>

</html>