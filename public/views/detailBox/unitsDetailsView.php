<link rel="stylesheet" type="text/css" href="../../views/detailBox/unitsDetailsView.css" />

<div id="detailBox_close" class="close" onclick="hide_detailBox();">X</div>
<div id="request_head">
    <div class="arrow" onclick="requestPreviousSector('units',<?=$coordinateX?>,<?=$coordinateY?>)"><</div>
    <span id="sector_name">
        <?=$sector->getName()?><br />
        <?=$sector->getCoordinateX().",".$sector->getCoordinateY()?>
    </span>
    <div class="arrow" onclick="requestNextSector('units',<?=$coordinateX?>,<?=$coordinateY?>)"> > </div>
</div>

<? $quantities = array(1,5,10,20,50,100); ?>
<table id="TrainingQuantity">
    <tr>
<?      foreach ($quantities as $i=>$quantity)
            { ?>
            <td id="selectQuantity<?=$i?>" class="trainingQuantitySelector" onclick="selectQuantity(<?=$i?>,<?=$quantity?>);"><?=$quantity?></td>
<?          } ?>
    </tr>
</table>

<?  foreach ($player->getAvailableUnits() as $unit) {
    //if (!(($unit->getId()==6) && ($player->getId()!=4))) {
            $productionCost = $unit->getproductionCost(); ?>
            <div class="unitRow">
                <img class="unitImg" src="<?=$img_units.$unit->getImage();?>"  title="<?=$unit->getDescription()?>" alt="<?=$unit->getName()?>"/>
                <div class="unitTop">
                    <span><?=$unit->getName()?></span>

<?                  if ($haveBarracks)
                        { ?>
                        <div class="unitTrain">
                            <span id="queueLast<?=$unit->getId()?>">0</span>
                            <span>/</span>
                            <span id="queueTotal<?=$unit->getId()?>"><?=$queuedNumbers[$unit->getId()]?></span>
                            <div id="unit_progressBar_container<?=$unit->getId()?>" class="unit_progressBar_container">
                                <div id="unit_progressBar<?=$unit->getId()?>" class="progressBar" />
                            </div>
                                <img class="trigger" src="<?=$img_buttons.'plus.png'?>" onclick="increaseUnitQueue(0,<?=$unit->getId()?>,<?=$sector->getCoordinateX()?>,<?=$sector->getCoordinateY()?>);" />
                                <img class="trigger" src="<?=$img_buttons.'minus.png'?>" />
                            &nbsp;
                        </div>
<?                      }
                    else
                        { ?>
                        <div style='font-size:12px; padding-top:20px;'>Necesitas construír unos cuarteles antes de poder entrenar tropas en este sector</div>
<?                      } ?>
                </div>
        <hr />


                <div class="unitColumn">
                    <div><img src="<?=$img_other.'attack.jpg'?>" /><?=$unit->getAttack()?></div>
                    <div><img src="<?=$img_other.'health.jpg'?>" /><?=$unit->getHealth()?></div>
                </div>

                <div class="unitColumn">
                    <div><img src="<?=$img_other.'speed.png'?>" /><?=$unit->getSpeed()?></div>
                    <div>
                        <img src="<?=$img_other.'time.jpg'?>" />
                        <span id="unit_time<?=$unit->getId()?>">
                            <?=$unit->getTime()?>
                        </span>
                    </div>
                </div>

                <div id="unitResources">
                    <div class="unitColumn">
                        <? $j=0;
                        for ($i=0; $i<count($resources); $i++) {
                            if ($productionCost[$i]!=0) { ?>
                                <img src="<?=$img_resources.$resources[$i+1]->getImg()?>" />
                                <span id="unit_cost<?=$unit->getId()?>-<?=$i?>">
                                    <?=$productionCost[$i]?>
                                </span>
                                <? if ($j%2==1) { ?>
                                    </div>
                                    <div class="unitColumn">
                                <? }
                                else
                                    echo "<br/>";?>
                        <? $j++; }
                                } ?>
                    </div>
                </div>

                <div style="float: right; text-align:center;">
                    <input type="text" id="selectedUnits<?=$unit->getId()?>" size="5" maxlength="7" onfocus="setLastSelectedUnitsQuantity(<?=$unit->getId()?>)" onblur="checkSelectedUnits(<?=$unit->getId()?>, <?=$unit->getSpeed()?>)" value="0" />
                    /
                    <span class="availableUnits" id="availableUnits<?=$unit->getId()?>" >
                    <?  if (isset($divisions[$unit->getId()])){$asdf=$divisions[$unit->getId()];
                            echo $asdf->getQuantity();}
                        else echo 0;
                    ?>
                    </span>
                </div>

            </div>
<?    }  //} ?>

<div id="MovementBox" style="margin-top: 10px;">
<?  if ($haveHeadquarters)
        { ?>
        <select id="Destiny" onchange="showETA(updateSelected());">
        <? $i=0;
        foreach ($reachableSectors as $reachableSector)
            { ?>
            <option value="<?=$reachableSector->getCoordinateX().','.$reachableSector->getCoordinateY()?>"
                    onmouseover="showETA(<?=$i?>), placeHighlight (<?=$reachableSector->getCoordinateX().",".$reachableSector->getCoordinateY()?>,<? if ($reachableSector->getOwner()==$player->getId()) echo 1; else echo 2; ?>, true)" id="destiny<?=$i?>">
                <?=$reachableSector->getName()." (".$reachableSector->getCoordinateX().",".$reachableSector->getCoordinateY().")"?>
            </option>
        <?  $i++;
            } ?>
        </select>
        ETA: <span id="ETABox">0</span> segundos
        <div id="sendButtonDiv" style="margin:10px;">
            <input type="button" value="Enviar" onclick="sendDivision(<?=$sector->getCoordinateX()?>,<?=$sector->getCoordinateY()?>)" />
        </div>
<?      }
    else
        echo "Necesitas construír un centro de mando antes de poder enviar tropas desde este sector";
?>
</div>