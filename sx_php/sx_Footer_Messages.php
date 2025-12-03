<?php
if (empty($strMsg)) {
    $strMsg = $_GET['msg'] ?? '';
}

if (isset($str_FooterMessage) && !empty($str_FooterMessage)) {
    $strMsg = $str_FooterMessage;
}

if (!empty($strMsg)) {
    if ($strMsg == "error") {
        $strMsg = "Please check for errors!";
    } elseif ($strMsg == "repeatreset") {
        $strMsg = 'Please, repeat the processes for resetting you password. You can use a request for resetting your password only once.';
    }

    if (!empty($strMsg)) {
        $strMsg = "<div><h1>{$str_SiteTitle}</h1><p>{$strMsg}</p></div>"; ?>
        <script>
            sx_LoadDialogMessages('<?php echo $strMsg; ?>');
        </script>
<?php
    }
}
?>