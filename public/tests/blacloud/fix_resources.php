<?
require_once ('../DAO/DAO.class.php');
session_start();

$connection = new DAO();
$connection->connect();

$rs = $connection->getAllSectors();

$sects = array();
while (!$rs->EOF)
    {
    $sects[$rs->fields["sector_id"]] = $rs->fields["sector_productionId"];
    $rs->MoveNext();
    }


    $connection = new DAO();
$connection->connect();

    $connection = $connection->getConnection();
foreach ($sects as $id=>$sect)
    {
    //echo $id. "  ";
    $prods = $sect;
    $prods = explode(",",$prods);
    if (count($prods)==4){
        $prods = implode(",", $prods).",0"; echo "<br>";

    $query = "UPDATE Sector SET sector_productionId='".$prods."' WHERE sector_id=".$id.";";

echo $query;
    $connection->Execute($query);}
    }
?>