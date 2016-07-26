<?php

include_once "aux.php";
include_once "config.inc.php";


$link = mysqli_connect($host, $user, $password, $db);

$sql = "SELECT
	*
FROM
	(
	SELECT -- states
        id as stateid, name
    FROM
        external_collections_state
    WHERE
        id < '5'
    ORDER BY id
	) sta
	LEFT JOIN
	(
	SELECT *
	FROM
		(
		SELECT 
			collacc.collectionid, collacc.accountname, collacc.amount,
			collacc.type, collacc.description, collacc.state,
			det.suddivisione, collacc.deposit_slip 
		FROM	
			(
			SELECT 
				acc.accountname,
				a.id as collectionid,a.amount,
				a.type, a.description, a.accountid, a.state, a.deposit_slip
			FROM
			(
				SELECT 
					* 
				FROM
					(
						SELECT 
							* 
						FROM
							external_collections
						WHERE
							deleted != '1'
					) ec
					LEFT JOIN
					(
						SELECT 
							collid, '1' as deposit_slip
						FROM
							external_deposit_slip
					) dep
					ON (ec.id = dep.collid)
				) a
				JOIN 
					vtiger_account acc
				USING (accountid)
			) collacc
			LEFT JOIN
			(
			SELECT 
				collectionid,
				GROUP_CONCAT(CONCAT(b.subject, ' :: ', a.partialamount, ' &euro;')
				SEPARATOR ',') as suddivisione
			FROM
				external_collections_details a
			JOIN 
				vtiger_salesorder b USING (salesorderid) -- so
			WHERE
				partialamount > '0'
			GROUP BY collectionid
			) det
			USING (collectionid)
		) cad
		LEFT JOIN
		(
			SELECT collectionid, bankname, CONCAT(bankaccount, chequenumber) as rif,
			bankabi, bankcab, CONCAT(bankstreet, '<br>\t',bankcode,' ',bankcity, ' (', bankprovince,')') as bankaddr,
			emission_date, receipt_date, deposit_date, value_date,
			IF(ISNULL(our_bankid), '0', our_bankid) as our_bankid,
			our_bank
			FROM
				(
				SELECT 
					b.*, eob.name as our_bank
				FROM
					external_bank_transfers_cheques b
				LEFT JOIN
					external_our_banks eob
				ON (b.our_bankid = eob.id)
				) beob
				JOIN 
					external_banks 
				ON (bankid = external_banks.id)
		) trache
		USING (collectionid)
	ORDER BY receipt_date, emission_date
	) polp
	ON (sta.stateid = polp.state)";
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
    while ($row = $result->fetch_assoc()) {
//        $args = cleanforjs($row);

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
