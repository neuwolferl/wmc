<?php

include 'config.inc.php';
include 'aux.php';
if (!isset($_GET['accid'])) {
    die('none');
}
else {
    $accountid = anti_injection($_GET['accid']);
}


$link = mysqli_connect($host, $user, $password, $db) OR die("impossibile connettersi");

$sql = "SELECT 
    CONCAT('<a href=\"http://54.213.213.176/vtigercrm/index.php?module=SalesOrder&action=DetailView&record=',so.salesorderid,'\">',so.salesorder_no,'</a>') as solink,
    so.salesorderid,
	so.subject,
    so.potentialid,
	so.salesorder_no,
	so.quoteid,
	so.duedate,
	so.total,
	so.subtotal,
	so.accountid,
	so.cf_675 as modalita," .
//	so.giaversato,
  "group_concat(invlink separator ',') as invoices
FROM
    (SELECT *
    FROM
        ((vtiger_salesorder a
    JOIN vtiger_salesordercf b USING (salesorderid))
    JOIN vtiger_crmentity c ON (a.salesorderid = c.crmid)" .
//    LEFT JOIN 
//    (
//        SELECT salesorderid, SUM(partialamount) as giaversato FROM external_collections_details
//	GROUP BY salesorderid
//	) accum
//    USING (salesorderid)
  ")
    WHERE
        a.accountid = '" . $accountid . "' AND c.deleted = '0'
    ORDER by duedate) so
LEFT JOIN
    (SELECT 
        salesorderid,CONCAT('<a href=\"http://54.213.213.176/vtigercrm/index.php?module=Invoice&action=DetailView&record=',invoiceid,'\">',invoice_no,'</a>') as invlink
    FROM
        ((vtiger_invoice a
    JOIN vtiger_invoicecf b USING (invoiceid))
    JOIN vtiger_crmentity c ON (a.invoiceid = c.crmid))
    WHERE
        a.accountid = '" . $accountid . "' AND c.deleted = '0'
    ORDER BY invoicedate) inv 
	USING (salesorderid)
	GROUP BY so.salesorderid";

//echo $sql;

$result = $link->query($sql);
$sos = "";
$soscum = array();
$ret = "";
$i = 0;

if ($num = $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $ret .="<tr>";
        if ($sos != $row['salesorder_no']) {
            $soscum[] = $row['salesorderid'];
            echo "<td class=\"tableso\">";
            echo "</td>";
            echo "<tr class=\"tableso\"><td class=\"tableso\">" . $row['solink'] . "</td>\n";
            echo "<td class=\"tableso\">" . $row['subject'] . "</td>\n";
            echo "<td class=\"tableso\">" . $row['total'] . "</td>\n";
            echo "<td class=\"tableso\">" . str_ireplace(",", "<br />", $row['invoices']) . "</td>\n";
            echo "<td class=\"tableso\"><input type=\"number\" step=\"0.1\" name=\"" . $row['salesorderid'] . "\" size=\"5\" id=\"partial" . $i . "\" value=\"0\" onchange=\"check_total()\">"
            . "<br>G/V: " . $row['giaversato']
            . "</td></tr>\n";
            $sos = $row['salesorder_no'];
            $i++;
        }
        echo "</tr><tr heigth=\"30px\"><td></td></tr>";
    }
    echo "<tr><td><input type=\"hidden\" id=\"count\" name=\"countso\" size=\"4\" value=\"" . $num . "\"></td>";
    echo "<td><input type=\"hidden\" name=\"sos\" id=\"sos\" value=\"" . implode($soscum, "|") . "\"></td></tr>";
}
?>
