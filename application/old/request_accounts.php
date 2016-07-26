<?php
//ini_set('display_startup_errors',1);
//ini_set('display_errors',1);
//ini_set('error_reporting', E_ALL);
include_once "aux.php";
include_once "config.inc.php";


$limit = 50;
if (!isset($_REQUEST['name']))
    $_REQUEST['name'] = "";
else
    $_REQUEST['name'] = anti_injection($_REQUEST['name']);
if (!isset($_REQUEST['fields'])) {
    die('');
}

$fields = str_ireplace("|", ",", $_REQUEST['fields']);
$link = mysqli_connect($host, $user, $password, $db);

$sql = "SELECT " . $fields . ", account.account_no, coll.star FROM
(SELECT 
    a.accountid, a.accountname, a.phone as accountphone, a.account_no,
	b.cf_641 as accountvat, b.cf_643, b.cf_644, c.ship_street as accountstreet
	FROM vtiger_account a, vtiger_accountscf b, vtiger_accountshipads c 
	WHERE a.accountid = b.accountid AND a.accountid = c.accountaddressid ";
$where = "AND ";
$whereflag = false;
if (strlen($_REQUEST['name']) >= 2) {
    $where .= "accountname LIKE '%$_REQUEST[name]%' ";
    $whereflag = true;
}
if ($whereflag)
    $sql .=$where;
$sql .=" ) account
	LEFT JOIN
	(SELECT DISTINCT accountid, '*' as star FROM external_collections) coll
	USING (accountid)";
//echo $sql;

$result = $link->query($sql);
if ($result === false) {
    echo("Error in the consult.." . mysqli_error($link));
    exit;
}
$list = '';
$num = $result->num_rows;
//echo $num;
if ($num) {
    $list .= "<p>Ho trovato " . $num . " aziende con nome contenente <strong>{$_REQUEST['name']}</strong> delle quali @visualized@ sono qui visualizzate.</p> @suggestion@";
    $counter = 0;
    while ($row = $result->fetch_assoc()) {
        if (++$counter == $limit)
            break;

        $args = cleanforjs($row);

        $show = ($row['star'] == "*") ? $row['star'] . "&nbsp;" : "&nbsp;&nbsp;";
        $show.= $row['accountname'];
        $show.= " - " . $row['account_no'];
        $safe = '<span onclick="completeform(' . toJSArray(explode(",", $fields)) . ',' . toJSArray($args) . ');">' . $show . ' </span><a target="_blank" href="' . $URLINIT . $HOST . $VTDIR . $viewAccountURL . $row['accountid'] . '">VT</a><br />';

        $safe = str_ireplace(chr(10), "", $safe);
        $safe = str_ireplace(chr(13), "", $safe);
        $list .= str_ireplace(chr(0), "", $safe);
    }
}
else {
    
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
