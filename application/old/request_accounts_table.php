<?php

include 'config.inc.php';
include 'aux.php';
//if (!isset($_GET['accid'])) {
//    die('none');
//}
//else {
//    $accountid = anti_injection($_GET['accid']);
//}


$link = mysqli_connect($host, $user, $password, $db) OR die("impossibile connettersi");

$sql = "SELECT * FROM vtiger_accounts";

//echo $sql;

$result = $link->query($sql) or die('');
$num = $result->num_rows;
//echo $num;
if ($num) {
    while ($row = $result->fetch_assoc()) {
        
        echo "<option value=\"{$row['idso']}\">{$row['no']} :: {$row['subject']} : {$row['tot']}&euro;</option>\n";
    }
}
?>
