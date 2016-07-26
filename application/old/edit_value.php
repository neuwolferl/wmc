<?php

include_once "aux.php";
include_once "config.inc.php";



if (!isset($_REQUEST['idop']))
    die('missing idop');
if (!isset($_REQUEST['idrecord']))
    die('missing idrecord');
if (!isset($_REQUEST['datum']))
    die('missing datum');
foreach ($_REQUEST as $key => $value) {
    $_REQUEST[$key] = anti_injection($value);
}

$REGISTERED_SQL = array(
  '1' => "UPDATE external_collections SET emissiondate = '%s' WHERE id = '%s'",
  '2' => "UPDATE external_collections SET receiptdate = '%s' WHERE id = '%s'",
  '3' => "UPDATE external_collections SET depositdate = '%s' WHERE id = '%s'",
  '4' => "UPDATE external_collections SET valuedate = '%s' WHERE id = '%s'",
  '5' => "UPDATE external_collections SET ourbankid = '%s' WHERE id = '%s'",
  '6' => "UPDATE external_collections SET state = '%s' WHERE id = '%s'"
);
if (isset($REGISTERED_SQL[$_REQUEST['idop']])) {
    $sql = sprintf($REGISTERED_SQL[$_REQUEST['idop']], $_REQUEST['datum'], $_REQUEST['idrecord']);
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
