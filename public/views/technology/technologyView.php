<link rel="stylesheet" type="text/css" href="../../views/technology/technologyView.css" />

<?
foreach ($technologies as $technology)
    {
    $techOver = ((!$technology->getUpgradable()) && ($technology->getLevel()>0));
    if ((!$techOver) && ($technology->getIsAge()))
        {
        $techCosts = $technology->getCosts();
        $advanceCosts = $playerConn->getAgeAdvanceCosts ($player->getId());
        $totalCosts = array();
        foreach ($advanceCosts as $index=>$advanceCost)
            {
            $totalCosts[$index] = $techCosts[$index]+$advanceCost;
            }
        $technology->setCosts($totalCosts);
        }
    $productionCost = $technology->getCosts();
    ?>
<div class="technologyRow">
        <img class="technologyImg" src="<?=$img_technologies.$technology->getPicture();?>" alt="<?=$technology->getName()?>"/>
        <div class="technologyTop">
            <span class="techName"><?=$technology->getName()?></span>

            <div class="technologyTrain">
                <? if (!$techOver)
                    { ?>
                    <div id="technology_progressBar_container<?=$technology->getId()?>" class="progressBar_container">
                        <div id="technology_progressBar<?=$technology->getId()?>" class="progressBar" />
                        <div id="technology_plannedBar<?=$technology->getId()?>" class="plannedBar" />
                    </div>
                    <span class="progressVal" id="progress<?=$technology->getId()?>"><?=floatval($technology->getProgress())?></span> %
                    <div style="float:right; margin-top: 5px;">
                            <span>Nivel</span>
                            <span class="techLevel" id="techLevel<?=$technology->getId()?>" style="float:none;">
                                <?=$technology->getLevel()+0?>
                            </span>
                    </div>
                <?  }
                else
                    { ?>
                    <div style="float:right; margin-top: 5px;">
                        <img src="<?=$img_buttons?>done.gif" alt="done" />
                    </div>
                <?  } ?>
                &nbsp;
            </div>
        </div>
<hr />


<? if (!$techOver)
    { ?>
    <div class="technologyColumn">
        <div>
            <img src="<?=$img_other.'time.jpg'?>" />
            <?  if ($technology->getUpgradable() && ($technology->getLevel()>0))
                    $realTime = $technology->getTime()*$technology->getIncrementTime()*$technology->getLevel();
                else
                    $realTime = $technology->getTime(); ?>
            <span id="time<?=$technology->getId()?>">
                <?=$realTime?>
            </span>
        </div>
    </div>

    <div id="technologyResources">
        <div class="technologyColumn">
            <? $j=0;
                $increments = $technology->getIncrements();
            for ($i=1; $i<count($resources); $i++) {
                if ($productionCost[$i]!=0) {
                    if ($technology->getUpgradable() && ($technology->getLevel()>0))
                        $realCost = $productionCost[$i]*$increments[$i]*$technology->getLevel();
                    else
                        $realCost = $productionCost[$i]; ?>
                    <img src="<?=$img_resources.$resources[$i+1]->getImg()?>" />
                    <span id="cost<?=$technology->getId()?>-<?=$i?>"><?=$realCost?></span>
                    <? if ($j%2==1) { ?>
                        </div>
                        <div class="technologyColumn">
                    <? }
                    else
                        echo "<br/>";?>
            <?  $j++; }
                    } ?>
        </div>
    </div>
<?  } ?>

        <div id="descriptionContainer">
                <span><?=$technology->getDescription()?></span>
        </div>

<? if (!$techOver)
    { ?>
    <div style="float: right;">
        +
        <input id="percentOrder<?=$technology->getId()?>" type="text" size="3"  value="1" onchange="update_researchCosts(<?=$technology->getId()?>)" />
        %
        <input type="button" value="Investigar" onclick="update_technology(<?=$technology->getId()?>)">
    </div>
<?  } ?>

</div>
    <script type="text/javascript" language="javascript">
        set_techStartCosts (<?=$technology->getId()?>);
    </script>
<? } ?>