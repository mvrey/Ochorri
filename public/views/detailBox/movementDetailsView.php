<link rel="stylesheet" type="text/css" href="../../views/detailBox/movementDetails.css" />

<div id="msg">
    <?=$msg?>
</div>

<table class="movements">
    <? if (count($divisionMovements)>0)
        { ?>
        <thead>
            <tr>
                <td></td>
                <td>Jugador</td>
                <td>Origen</td>
                <td>Destino</td>
                <td>Ejército</td>
                <td>E.T.A.</td>
            </tr>
        </thead>
        <tbody>

    <?  $i=0;
        foreach ($divisionMovements as $divisionMovement)
            {
            $startSector = $divisionMovement->getStartSector();
            $endSector = $divisionMovement->getEndSector();
            $timeLeft = $divisionMovement->getTime()-($_SERVER['REQUEST_TIME']-$divisionMovement->getStartDateTime());
            $ownerNick = $player->getNick();
            $numUnits = 0;
            foreach ($divisionMovement->getDivisions() as $division)
                $numUnits += $division->getQuantity();

            if ($startSector->getId()==$sector->getId())    //If this is initial sector
                {
                $isStart = true;
                $isAttack = ($divisionMovement->getOwnerId()!=$endSector->getOccupant()); //If division goes to an allied sector
                if ($isAttack)
                    {
                    $img_movement = "sword.png";
                    if ($endSector->getOwner())
                        {
                        $owner = $allPlayers[$endSector->getOwner()];
                        $ownerNick = $owner->getNick();}
                        }
                    else
                        $ownerNick = 'Nadie';
                }
            else
                {
                $isStart = false;
                $isAttack = ($divisionMovement->getOwnerId()!=$endSector->getOccupant());   //If it comes from allied sector
                if ($isAttack)
                    {
                    $img_movement = "danger.png";
                    $owner = $allPlayers[$endSector->getOwner()];
                    $ownerNick = $owner->getNick();
                    }
                }
                if (!$isAttack)
                    {
                    $img_movement = "soldiers.png";
                    }
        ?>
            <tr id="movementRow<?=$i?>">
                <td><img src="<?=$img_buttons.$img_movement?>" width="40px" height="40px"/></td>
                <td><?=$ownerNick?></td>
                <td><?=$startSector->getName()." (".$startSector->getCoordinateX().",".$startSector->getCoordinateY().")"?></td>
                <td><?=$endSector->getName()." (".$endSector->getCoordinateX().",".$endSector->getCoordinateY().")"?></td>
                <td><?=$numUnits." unidades"?></td>
                <td><span id="timeLeft<?=$i?>"><?=$timeLeft?></span> segundos</td>
            </tr>


        <?  $i++;
            } ?>
    </tbody>
<?      }
    else
        echo "No hay tropas en tránsito desde o hacia este sector."
?>
</table>