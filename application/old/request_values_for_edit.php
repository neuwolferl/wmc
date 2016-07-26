<?php

include_once "aux.php";
include_once "config.inc.php";



if (!isset($_REQUEST['idop']))
    die('');
foreach ($_REQUEST as $key => $value) {
    $_REQUEST[$key] = anti_injection($value);
}

$REGISTERED_SQL = array(
  '1' => "SELECT state as value, statename as text FROM external_collections_state",
  '2' => "SELECT ourbankid as value, ourbankname as text FROM external_ourbank"
);
$sql = "SELECT 1";
if (isset($REGISTERED_SQL[$_REQUEST['idop']])){
    $sql = $REGISTERED_SQL[$_REQUEST['idop']];
}
//echo $sql;
$link = mysqli_connect($host, $user, $password, $db);

$result = $link->query($sql);
if ($result === false) {
    echo("Error in the consult.." . mysqli_error($link));
    exit;
}
$list = '';
$num = $result->num_rows;
//echo $num;
if ($num) {
    $list .="<select>";
    while ($row = $result->fetch_assoc()) {
        $list .="<option value=\"{$row['value']}\">" . $row['text'] . "</option>\n";
    }
    $list .="</select>";
}
else {
    $list .="<select></select>";
}

$list = str_ireplace("@visualized@", $counter, $list);
if ($num > $limit) {
    $list = str_ireplace("@suggestion@", "Aggiungere altri dettagli alla ricerca.<br />", $list);
}
else {
    $list = str_ireplace("@suggestion@", "", $list);
}
echo $list;
unset($list);
?>
