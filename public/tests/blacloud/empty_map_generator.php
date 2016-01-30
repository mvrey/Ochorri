<?
require_once ('../DAO/DAO.class.php');
session_start();

$connection = new DAO();
$connection->connect();

    $connection = $connection->getConnection();
    for ($i=0;$i<10;$i++)
        {
        for ($j=0;$j<20;$j++)
            {
            $query = "insert into Sector (sector_coordinateX,sector_coordinateY) values (".$j.",".$i.");";
            $result = $connection->Execute ($query);
            }
        }
?>