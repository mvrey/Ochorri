<link rel="stylesheet" type="text/css" href="../../views/detailBox/buildingDetails.css" />

<?  foreach ($buildings as $building)
        {
        $productionCost = $building->getproductionCost(); ?>
        <div class="buildingRow">
            <img class="buildingImg" src="<?=$img_buildings.$building->getImage();?>"  title="<?=$building->getDescription()?>" alt="<?=$building->getName()?>"/>
            <div class="buildingTop">
                <span><?=$building->getName()?></span>
                <div class="buildingTrain">
                    <div id="building_progressBar_container<?=$building->getId()?>" class="building_progressBar_container">
                        <div id="building_progressBar<?=$building->getId()?>" class="progressBar" />
                    </div>
                    <div id="buildingTrigger<?=$building->getId()?>">
                        <?  if (($building->getLevel()>0) && (!$building->getUpgradable()))
                                { ?>
                                <img class="trigger" id="buildingTriggerImg<?=$building->getId()?>" src="<?=$img_buttons.'done.gif'?>" />
                        <?      }
                            else
                                {
                                if (isset($capitolSector))
                                    $capitolSectorNameString = $capitolSector->getNameString();
                                else
                                    $capitolSectorNameString = ""; ?>
                                <img class="trigger" id="buildingTriggerImg<?=$building->getId()?>" src="<?=$img_buttons.'build.png'?>" onclick="startBuilding(<?=$building->getId()?>,<?=$sector->getId()?>,'<?=$capitolSectorNameString?>');" />
                        <?      } ?>
                    </div>
                    &nbsp;
                </div>
            </div>
            <hr />


            <div class="buildingColumn">
                <div><img src="<?=$img_other.'health.jpg'?>" /><?=$building->getHealth()?></div>
            </div>

            <div class="buildingColumn">
                <div><img src="<?=$img_other.'time.jpg'?>" /><?=$building->getTime()?></div>
            </div>

            <div id="buildingResources">
                <div class="buildingColumn">
                    <? $j=0;
                    for ($i=0; $i<count($resources); $i++) {
                        if ($productionCost[$i]!=0) { ?>
                            <img src="<?=$img_resources.$resources[$i+1]->getImg()?>" />
                            <?=ceil($productionCost[$i])?>
                            <? if ($j%2==1) { ?>
                                </div>
                                <div class="buildingColumn">
                            <? }
                            else
                                echo "<br/>";?>
                    <?  $j++; }
                            } ?>
                </div>
            </div>

            <div id="buildingLevelDiv<?=$building->getId()?>" style="<? if ($building->getLevel()<1) echo 'visibility: hidden;'; ?> float: right;">
                    <span>Nivel</span>
                    <span id="buildingLevel<?=$building->getId()?>" >
                    <?=$building->getLevel()?>
                    </span>
            </div>

        </div>
<?      } ?>