<?php
$_SESSION["Forum_GreetName"] = $_SESSION["Forum_FirstName"];
 
unset($_SESSION["Forum_UserID"]);
unset($_SESSION["Forum_FirstName"]);
unset($_SESSION["Forum_LastName"]);
unset($_SESSION["Forum_UserEmail"]);
unset($_SESSION["Forum_". sx_HOST]);

header("Location: ". sx_PATH."?pg=message&request=logout");
exit();
