<?php
$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'first_name';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

$path_to_db = ""; // path to sqlite database

$sqlite = new Sqlite3($path_to_db);


$usingSQL = true;
function runSQL($rsql) {

	global $sqlite;	
	$result = $sqlite->query($rsql);

	while ($row = $result->fetchArray()) {
    $rows[]=$row;
}

	return $rows;
}

function countRec($fname,$tname) {
	$sql = "SELECT count($fname) FROM $tname ";
	return count(runSQL($sql));
}

$sort = "ORDER BY $sortname $sortorder";
$start = (($page-1) * $rp);
$limit = "LIMIT $start, $rp";
$where = "";

if ($query) $where = " WHERE $qtype LIKE '%".mysql_real_escape_string($query)."%' ";

$sql = "SELECT * FROM user $where $sort $limit";
$result = runSQL($sql);
$total = countRec("id","user $where");



$rows = runSQL($sql);


header("Content-type: application/json");
$jsonData = array('page'=>$page,'total'=>$total,'rows'=>array());



foreach($rows AS $row){
	//If cell's elements have named keys, they must match column names
	//Only cell's with named keys and matching columns are order independent.
	$entry = array('id'=>$row['id'],
		'cell'=>array(
			'id'=>$row['last_name'],
			'first_name'=>$row['first_name']
		),
	);
	$jsonData['rows'][] = $entry;
}

echo json_encode($jsonData);
