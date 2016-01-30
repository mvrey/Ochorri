<style>
<? require("../../views/detailBox/battleDetails.css.php"); ?>
</style>

<div id="battleContainer">
    <?
    if ($isPlayerInvolved)
        {
        if (($battle->getAttackingDivisions()) && ($battle->getDefendingDivisions()))
            { ?>
            <div class="armyColumn" id="attackColumn" style="width: 50%; float: left;">
                <span>Atacante (<?=$attacker->getNick()?>)</span>
                <hr />
            <?
            foreach ($battle->getAttackingDivisions() as $division)
                {
                $unit = $allUnits[$division->getUnitId()];
                ?>
                <div class="unitRow">
                    <img src='<?=$img_units.$unit->getImage()?>' />
                    <span id="unitQuantityA<?=$division->getUnitId()?>" class="unitQuantity"><?=$division->getQuantity()?></span>
                </div>
            <?
                }
            ?>
            </div>

            <div class="armyColumn" id="attackColumn" style="width: 50%; float: right;">
                <span>Defensor (<?=$defender->getNick()?>)</span>
                <hr />
            <?
            foreach ($battle->getDefendingDivisions() as $division)
                {
                $unit = $allUnits[$division->getUnitId()];
                ?>
                <div class="unitRow">
                    <img src='<?=$img_units.$unit->getImage()?>' />
                    <span id="unitQuantityD<?=$division->getUnitId()?>" class="unitQuantity"><?=$division->getQuantity()?></span>
                </div>
                <?
                }

            ?>
            </div>

            <div style="clear:both; margin: 20px 0 0 20px">
                Siguiente ronda en <span id="roundTimeLeft"></span>
            </div>
        <?  }
        elseif ($battle->getAttackingDivisions()==0)
            {
            //$connection->updateDivision (false, $sector->getId(), false, 1, "*", 0);
            echo "Ha ganado el defensor.";
            }
        elseif ($battle->getDefendingDivisions()==0)
            {
            //$connection->updateDivision (false, $sector->getId(), false, 1, "*", 0);
            echo "Ha ganado el atacante.";
            }
        }
    else
        echo "Esta Batalla enfrenta a ".$attackingPlayer->getNick()."(atacante) contra ".$defendingPlayer->getNick()."(defensor)";
    ?>
</div>

<div id="logBox">
<?
    if ($isPlayerInvolved)
    {
        for ($i=$maxRound; $i>=1; $i--)
            { ?>
            <span class="roundTitle" id="round<?=$i?>">Ronda <?=$i?></span>
            <span class="expand" id="expand<?=$i?>" onclick="expandLog(<?=$battle->getId()?>,<?=$i?>)">+</span>
            <br />
            <div class="logBox" id="logBox<?=$i?>"></div>
         <? }
    }
?>
</div>