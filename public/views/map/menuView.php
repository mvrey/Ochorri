
    <link href="../../views/map/popup.css" rel="stylesheet" type="text/css" />
    <!-- Stylesheet to define map boundary area and hex style -->


    <div id="map_container">

        <div id="map_controller" class="dragable">
            <div style="clear: both;">
                <img class="map_arrow" id="map_arrow_up" src="<?=$img_buttons?>arrow-up.png" alt="up" onclick="move_map(0);"/>
            </div>
            <div>
                <div style="float:right;"><img class="map_arrow" id="map_arrow_right" src="<?=$img_buttons?>arrow-right.png" alt="right" onclick="move_map(1);"/></div>
                <div style="float:left;"><img class="map_arrow" id="map_arrow_left" src="<?=$img_buttons?>arrow-left.png" alt="left" onclick="move_map(3);"/></div>
                <div id="zoom_container" style="float: left;">
                    <img class="zoom" src="<?=$img_buttons?>zoom-in.png" alt="zoom in" onclick="zoom_in();"/>
                    <img class="zoom" src="<?=$img_buttons?>zoom-out.png" alt="zoom out" onclick="zoom_out();" />
                </div>
            </div>
            <div style="clear: both;"><img class="map_arrow" id="map_arrow_down" src="<?=$img_buttons?>arrow-down.png" alt="down" onclick="move_map(2);"/></div>
        </div>

        <div id="map_frame" ><img src="<?=$img_design?>frame.png" style="width: 100%; height: 100%;" /></div>
        <!-- Render the hex map inside of a div block -->
        <div id='hexmap' class='hexmap' onclick='handle_map_click(event);'>
        </div>
        <div id="sectorMenu_own" class="popup sectorMenu">
           <div id="sectorMenu_close" class="close" onclick="hide_sectorMenu('sectorMenu_own');">X</div>
           <span id="title_own" class="dragable">MENU</span>
            <ul>
                <li onclick="show_details('details');">Ver movimientos</li>
                <li onclick="show_details('buildings');">Ver edificios</li>
                <li onclick="show_details('units');">Ver tropas</li>
                <li onclick="show_details('production');">Ver producción</li>
            </ul>
        </div>

        <div id="sectorMenu_foreign" class="popup sectorMenu">
           <div id="sectorMenu_close" class="close" onclick="hide_sectorMenu('sectorMenu_foreign');">X</div>
           <span id="title_foreign" class="dragable">MENU</span>
            <ul>
                <li onclick="show_details('details');">Ver movimientos</li>
                <!--
                <li onclick="show_details();">Ver detalles</li>
                <li onclick="show_details();">Ver tropas</li>
                <li onclick="show_details();">Mover ejército</li>
                -->
            </ul>
        </div>
        <div id="sectorMenu_water" class="popup sectorMenu">
           <div id="sectorMenu_close" class="close" onclick="hide_sectorMenu('sectorMenu_water');">X</div>
           <span id="title_water" class="dragable">MENU</span>
            <ul>
                <li>Sin opciones aún</li>
                <!--
                <li onclick="show_details();">Ver flota</li>
                <li onclick="show_details();">Mover flota</li>
                -->
            </ul>
        </div>
        <div id="sectorMenu_empty" class="popup sectorMenu">
           <div id="sectorMenu_close" class="close" onclick="hide_sectorMenu('sectorMenu_empty');">X</div>
           <span id="title_empty" class="dragable">MENU</span>
            <ul>
                <li onclick="show_details('details');">Ver movimientos</li>
                <!--
                <li onclick="show_details();">Ver detalles</li>
                <li onclick="show_details();">Ver tropas</li>
                <li onclick="show_details();">Mover ejército</li>
                -->
            </ul>
        </div>
        <div id="sectorMenu_battle" class="popup sectorMenu">
           <div id="sectorMenu_close" class="close" onclick="hide_sectorMenu('sectorMenu_battle');">X</div>
           <span id="title_battle" class="dragable">MENU</span>
            <ul>
                <li onclick="show_details('battle');">Ver Batalla</li>
                <li onclick="show_details('details');">Ver movimientos</li>
            </ul>
        </div>
        <div id="detailBoxContainer" class="popup">
            <div class="dragable detailBox_head"></div>
            <div id="detailBox">
            </div>
        </div>
    </div>
    </body>
</html>