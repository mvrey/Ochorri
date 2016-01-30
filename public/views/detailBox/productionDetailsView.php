<link rel="stylesheet" type="text/css" href="../../views/detailBox/productionDetailsView.css" />

<table class="resources">
    <thead>
        <tr>
            <td></td>
            <td>Base</td>
            <td>Modificador</td>
            <td>Ingresos</td>
            <td>Gastos</td>
            <td>Balance</td>
        </tr>
    </thead>
    <tbody>
        <? $i=0;
        foreach ($resources as $resource)
            {
            if (isset($availableResources[$resource->getId()]))
                {
                $balance = $productions[$i]-$spends[$i];
                if ($productionBases[$i])
                    $modifier = sprintf ("%01.2f",$productions[$i]/$productionBases[$i]);
                else
                    $modifier = "0.00";
                if ($balance>=0) $color="green";
                else $color="red"; ?>
                <tr>
                    <td><img src="<?=$img_resources.$resource->getImg()?>" width="50px" height="50px" /></td>
                    <td><?=$productionBases[$i]?></td>
                    <td>*<?=$modifier?></td>
                    <td class="productions">+<?=sprintf ("%01.2f",$productions[$i])?></span></td>
                    <td class="spends">-<?=$spends[$i]?></span></td>
                    <td class="balance" style="color: <?=$color?>"><?=sprintf("%+d",$balance) ?></td>
                </tr>
        <?      }
            $i++;
            } ?>
    </tbody>
</table>