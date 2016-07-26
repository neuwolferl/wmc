<?php

include_once "aux.php";
include_once "config.inc.php";



if (!isset($_REQUEST['idop']))
    die('missing idop');
if (!isset($_REQUEST['idrecord']))
    die('missing idrecord');
foreach ($_REQUEST as $key => $value) {
    $_REQUEST[$key] = anti_injection($value);
}

$REGISTERED_SQL = array(
  '1' => "UPDATE external_collections SET deleted='1' WHERE id = '%s'",
  '2' => "DELETE FROM external_collections_so WHERE id = '%s'"
);
if (isset($REGISTERED_SQL[$_REQUEST['idop']])) {
    $sql = sprintf($REGISTERED_SQL[$_REQUEST['idop']], $_REQUEST['idrecord']);
}
//echo $sql; 
$link = mysqli_connect($host, $user, $password, $db);

$result = $link->query($sql);
if ($result === false) {
    echo("Error in the consult.." . mysqli_error($link));
    exit;
}
else {
    echo "ok";
    exit;
}
?>
