<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

$strListFrom = "";
if (isset($_POST['ListFrom'])) {
    $strListFrom = $_POST['ListFrom'];
} elseif (isset($_GET['ListFrom'])) {
    $strListFrom = $_GET['ListFrom'];
}

$i_CourseID = 0;
if (isset($_POST['CourseID'])) {
    $i_CourseID = $_POST['CourseID'];
} elseif (isset($_GET['CourseID'])) {
    $i_CourseID = $_GET['CourseID'];
}
$i_CourseID = intval($i_CourseID);

$i_ConferenceID = 0;
if (isset($_POST['ConferenceID'])) {
    $i_ConferenceID = $_POST['ConferenceID'];
} elseif (isset($_GET['ConferenceID'])) {
    $i_ConferenceID = $_GET['ConferenceID'];
}
$i_ConferenceID = intval($i_ConferenceID);

$i_LanguageID = 0;
if (isset($_POST['LanguageID'])) {
    $i_LanguageID = $_POST['LanguageID'];
} elseif (isset($_GET['LanguageID'])) {
    $i_LanguageID = $_GET['LanguageID'];
}
$i_LanguageID = intval($i_LanguageID);

$i_NewsGroupID = 0;
if (isset($_POST['NewsGroupID'])) {
    $i_NewsGroupID = $_POST['NewsGroupID'];
} elseif (isset($_GET['NewsGroupID'])) {
    $i_NewsGroupID = $_GET['NewsGroupID'];
}
$i_NewsGroupID = intval($i_NewsGroupID);

$str_ToCustomers = "All";
if (isset($_POST["ToCustomers"])) {
    $str_ToCustomers = $_POST["ToCustomers"];
}

$i_DistrictID = 0;
if (isset($_POST["DistrictID"])) {
    $i_DistrictID = (int) $_POST["DistrictID"];
}


$i_CountryID = 0;
if (isset($_POST["CountryID"])) {
    $i_CountryID = $_POST["CountryID"];
    $i_CountryID = intval($i_CountryID);
}

$aResusts = null;
if ($strListFrom == "Newsletters") {
    $sql = "SELECT LetterID , Email, FullName, UnsubscribeCode 
        FROM newsletters 
        WHERE Active = True ";
    if (intval($i_LanguageID) > 0) {
        $sql .= " AND (LanguageID = " . $i_LanguageID . " OR LanguageID = 0) ";
    }
    if (intval($i_NewsGroupID) > 0) {
        $sql .= " AND GroupID = " . $i_NewsGroupID;
    }
    $sql .= " ORDER BY LetterID ASC ";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResusts = $rs;
    }
    $rs = null;
} elseif ($strListFrom == "Participants") {
    if (intval($i_ConferenceID) > 0) {
        $sql = "SELECT p.ParticipantID, p.Email, CONCAT(p.FirstName, ' ', p.LastName) AS FullName, p.DeleteToken 
        FROM conf_to_participants c
            INNER JOIN conf_participants p
            ON c.ParticipantID = p.ParticipantID
        WHERE p.AllowAccess = True 
            AND c.ConferenceID = " . $i_ConferenceID . " 
            ORDER BY ParticipantID ASC ";
    } else {
        $sql = "SELECT ParticipantID, Email, CONCAT(FirstName, ' ', LastName) AS FullName, DeleteToken 
        FROM conf_participants
        WHERE AllowAccess = True 
        ORDER BY ParticipantID ASC ";
    }

    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResusts = $rs;
    }
    $rs = null;
} elseif ($strListFrom == "StudentsList") {
    if (intval($i_CourseID) > 0) {
        $sql = "SELECT s.StudentID, s.Email, CONCAT(s.FirstName, ' ', s.LastName) AS FullName, s.RemoveCode 
        FROM course_students as s
        INNER JOIN course_to_students as t
            ON s.StudentID = t.StudentID
        WHERE t.CourseID = ?
            AND s.AllowAccess = True 
            AND s.EmailList = True
        ORDER BY s.StudentID ASC ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$i_CourseID]);
        $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    } else {
        $sql = "SELECT StudentID, Email, CONCAT(FirstName, ' ', LastName) AS FullName, RemoveCode 
        FROM course_students 
        WHERE AllowAccess = True 
        AND EmailList = True
        ORDER BY StudentID ASC ";
        $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    }
    if ($rs) {
        $aResusts = $rs;
    }
    $rs = null;
} elseif ($strListFrom == "UsersList") {
    $sql = "SELECT UserID, UserEmail, CONCAT(FirstName, ' ', LastName) AS FullName, EmailListRemoveCode 
        FROM users 
        WHERE AllowAccess = True 
        AND EmailList = True ";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResusts = $rs;
    }
    $rs = null;
} elseif ($strListFrom == "MembersList") {
    $sql = "SELECT MemberID, Email,  CONCAT(FirstName, ' ', LastName) AS FullName, DeregistrationCode 
        FROM members_list 
        WHERE Hidden = False ";
    if (intval($i_LanguageID) > 0) {
        $sql .= " AND (LanguageID = " . $i_LanguageID . " OR LanguageID = 0) ";
    }
    $sql .= " ORDER BY MemberID ASC ";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResusts = $rs;
    }
    $rs = null;
} elseif ($strListFrom == "ForumMembersList") {
    $sql = "SELECT UserID, UserEmail,  CONCAT(FirstName, ' ', LastName) AS FullName, EmailListRemoveCode 
        FROM forum_members 
        WHERE AllowAccess = 1 AND EmailList = 1
        ORDER BY UserID ASC ";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResusts = $rs;
    }
    $rs = null;
} elseif ($strListFrom = "CustomersList") {
    $sql = "SELECT CustomerID, Email,  CONCAT(FirstName, ' ', LastName) AS FullName, MailUnsubscribeCode 
        FROM customers 
        WHERE MailList = True ";
    if ($str_ToCustomers != "All") {
        if ($str_ToCustomers == "Registered") {
            $sql .= " AND RegiseredCustomer = True ";
        } elseif ($str_ToCustomers == "Wholesalers") {
            $sql .= " AND UseGrossPrices = True ";
        } elseif ($str_ToCustomers == "District") {
            if (intval($i_DistrictID) > 0) {
                $sql .= " AND District = " . $i_DistrictID;
            }
        } elseif ($str_ToCustomers == "Country") {
            if (intval($i_CountryID) > 0) {
                $sql .= " AND Country = " . $i_CountryID;
            }
        }
    }
    $sql .= " ORDER BY CustomerID ASC ";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResusts = $rs;
    }
    $rs = null;
}

$strMailList = "";
if (is_array($aResusts)) {

    $iRows = count($aResusts);
    $x = 1;
    $z = 0;
    for ($r = 0; $r < $iRows; $r++) {
        $iLetterID = $aResusts[$r][0];
        $sEmail = $aResusts[$r][1];
        $sName = $aResusts[$r][2];
        $sUnsubscribeCode = $aResusts[$r][3];
        if ($strMailList != "") {
            $strMailList = $strMailList . "; ";
        }
        $strMailList = $strMailList . $iLetterID . ", " . $sEmail . ", " . $sName . ", " . $sUnsubscribeCode;
        $z = (($x * 50) - 1);
        if ($r == $z || $r == ($iRows - 1)) {
            echo '<p><button data-id="' . $x . '" data-source="' . $strListFrom . '">Add the List to the Mail Form</button> ' . ($z - 48) . ' - ' . ($r + 1) . '<br>';
            echo '<textarea style="width: 90%; height: 140px;" id="textarea_' . $x . '">' . $strMailList . "</textarea></p>";
            $strMailList = "";
            $x++;
        }
    }
} else {
    echo "<pre>No Mail Addresses where found!</pre>";
}
?>
<script>
    sxAddEmailLists();
</script>