<?php
require_once ('../../config/paths.php');
require_once ('../../lib/array_lib.php');
require_once ('../../lib/time.php');
require_once ('../../models/ranking/rankingDAO.php');

$rankingConn = new rankingDAO();

$rankingsArr = $rankingConn->getRanking();

$scores = array();
foreach ($rankingsArr as $rankingArr)
    {
    if (!isset($scores[$rankingArr['player_nick']]))
        {
        $scores[$rankingArr['player_nick']] = array('Sectors'=>0, 'Divisions'=>0, 'Buildings'=>0, 'lastUpdate'=>$rankingArr['player_lastUpdate']);
        }
    $scores[$rankingArr['player_nick']][$rankingArr['concept']] = $rankingArr['points'];
    }
foreach ($scores as $index=>$score)
    {
    $scores[$index]['total'] = array_sum($score)-$score['lastUpdate'];
    }
$scores = array_sort($scores, 'total', SORT_DESC);

require ("../../views/ranking/rankingView.php");
?>