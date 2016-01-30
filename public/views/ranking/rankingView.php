<link rel="stylesheet" type="text/css" href="../../views/ranking/rankingView.css" />

<table id="ranking_table">
    <tr>
        <th>Posición</th>
        <th>Jugador</th>
        <th>Sectores</th>
        <th>Tropas</th>
        <th>Edificios</th>
        <th>Puntuación total</th>
                        <th>Último Vistazo al mapa</th>
    </tr>
<?   $position = 1;
foreach ($scores as $nick=>$score)
    {
    $style = "";
    if ($score['total']==0)
        $style="background: url(".$img_other."linethrough.gif) repeat-x center left;";
    if ($position%2==0)
        $style.="background-color: gray;";
?>
    <tr style='<?=$style?>'>
        <td><?=$position?></td>
        <td><?=$nick?></td>
        <td><?=$score['Sectors']?></td>
        <td><?=$score['Divisions']?></td>
        <td><?=$score['Buildings']?></td>
        <td><?=$score['total']?></td>
        <td><?=translate_time($_SERVER['REQUEST_TIME']-$score['lastUpdate'])?></td>
    </tr>
<?  $position++;
    }
?>
</table>