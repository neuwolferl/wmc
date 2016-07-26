<?php

include_once "aux.php";
include_once "config.inc.php";

$limit = 200;
if (!isset($_REQUEST['bank']))
    $_REQUEST['bank'] = "";
else
    $_REQUEST['bank'] = anti_injection($_REQUEST['bank']);
if (!isset($_REQUEST['abi']))
    $_REQUEST['abi'] = "";
else
    $_REQUEST['abi'] = anti_injection($_REQUEST['abi']);
if (!isset($_REQUEST['cab']))
    $_REQUEST['cab'] = "";
else
    $_REQUEST['cab'] = anti_injection($_REQUEST['cab']);


if (!isset($_REQUEST['fields'])) {
    die('');
}

$fields = str_ireplace("|", ",", $_REQUEST['fields']);
$link = mysqli_connect($host, $user, $password, $db) OR die("impossibile connettersi");
$sql = "SELECT {$fields} FROM external_banks ";
$where = "WHERE ";
$whereflag = false;
if (strlen($_REQUEST['bank']) >= 2) {
    $where .= "bankname LIKE '%$_REQUEST[bank]%' ";
    $whereflag = true;
}
if (strlen($_REQUEST['abi']) >= 2) {
    if ($whereflag)
        $where .= "AND ";
    $where .= "bankabi LIKE '%$_REQUEST[abi]%' ";
    $whereflag = true;
}
if (strlen($_REQUEST['cab']) >= 2) {
    if ($whereflag)
        $where .= "AND ";
    $where .= "bankcab LIKE '%$_REQUEST[cab]%' ";
    $whereflag = true;
}
if ($whereflag)
    $sql .=$where;
//echo $sql;
$result = $link->query($sql);
$list = '<ul>';
$counter = 0;
$num = $result->num_rows;
if ($num) {
    $list .= "<li>Ho trovato " . $num . " banche con nome contenente <strong>{$_REQUEST['bank']}</strong>, "
    . "abi contenente <strong>{$_REQUEST['abi']}</strong> e cab contenente <strong>{$_REQUEST['cab']}</strong>, "
    . "delle quali @visualized@ sono qui visualizzate.</li> @suggestion@";

    $search = array("'", '"');
    $replace = array("\'", "\'");
    while ($row = $result->fetch_assoc()) {
        if (++$counter == $limit)
            break;

        $args = cleanforjs($row);
        
        
        $show = "";
        $show.= $row['bankname'];
        $show.= " " . $row['bankstreet'];
        $show.= " " . $row['bankcity'];
        $show.= " ABI <strong>" . $row['bankabi'] . "</strong>";
        $show.= " CAB <strong>" . $row['bankcab'] . "</strong>";
        
        $safe = '<span onclick="completeform(' . toJSArray(explode(",", $fields)) . ',' . toJSArray($args) . ');">' . $show . ' </span><br />';
        $safe = str_ireplace(chr(10), "", $safe);
        $safe = str_ireplace(chr(13), "", $safe);
        $list .= str_ireplace(chr(0), "", $safe);
    }
}
else {
    
}
$list .= '</ul>';
$list = str_ireplace("@visualized@", $counter, $list);
if ($num > $limit) {
    $list = str_ireplace("@suggestion@", "Aggiungere altri dettagli alla ricerca.", $list);
}
else {
    $list = str_ireplace("@suggestion@", "", $list);
}

echo $list;
unset($list);
?>
