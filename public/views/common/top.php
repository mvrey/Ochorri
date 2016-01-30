<link rel="styleSheet" href="../../views/common/top.css" />

<div id="topBar">
    <div id="civ">
        <img id="mainFlag" src='<? echo $img_flags.$player->getFlag(); ?>' alt="flag" />
        <?=$player->getCivName()?>
    </div>
    <div id="resourceContainer">
        <?
        $i=0;
        $player_resources = $player->getResources();
        $availableResources = $player->getAvailableResources();

        if (count($player_resources)>1)
            {
            foreach ($allResources as $resource)
                {
                if (isset($availableResources[$resource->getId()]))
                    {
                    ?>
            <div style="float:left">
                <img src="<?=$img_resources.$resource->getImg()?>" title="<?=$resource->getName()?>" alt="<?=$resource->getName()?>" />
            </div>
            <div id="resourceQuantities">
                <span id="resourceQuantity<?=$resource->getId()?>"><?=floor($player_resources[$i])?></span><br>
                <span id="totalBalance<?=$resource->getId()?>" class="totalBalance"><!--Filled from JS on map_refresh--></span>
            </div>
            <?  $i++;
                    }
                }
            }
        else
            { ?>
        <div id="gameOver">
            Vuestras cenizas han sido esparcidas en el viento, la sangre de vuestros hombres yace sobre el campo
            y sus cuerpos alimentan a los animales salvajes. Vuestro recuerdo y vuestro nombre han sido borrados de la historia
            y existirán en las generaciones venideras tan solo como el eco de una leyenda.<br/><br/>
            <center>a.k.a. GAME OVER</center
        </div>
        <?  } ?>
    </div>

    <div id="playerBoxContainer"  >
        <div id="playerData" align="center" onmouseover="show_playerMenu();" onmouseout="hide_playerMenu()">
            <?=$player->getNick()?>
            <img id="mainAvatar" src='<? echo $img_avatars.$player->getAvatar(); ?>' alt="avatar" />
        </div>
        <div id="playerMenu" onmouseover="show_playerMenu();" onmouseout="hide_playerMenu()">
            <ul>
                <li onclick="window.location='../../controllers/common/logout.php';">Cerrar Sesion</li>
                <li>Preferencias(No va)</li>
            </ul>
        </div>
    </div>

    <div id="newMessages_container">
        <img id="newMessage_icon" src="<?=$img_other?>new_message.png" alt="New messages" onclick="show_messages();" />
    </div>

    
    <div class="clear" />
</div>
    <script type="text/Javascript">

        function show_playerMenu() {
            $('#playerMenu').show();
        }

        function hide_playerMenu() {
            $('#playerMenu').hide();
        }

        function redirect(url) {
            window.location=url;
        }

        $('#playerMenu').hide();
    </script>