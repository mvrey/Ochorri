<? require_once ("../../config/map.cfg.php"); ?>

    body {
        /*
        margin: 0;
        padding: 0;
        */
    }

    .hexmap {
        width: <?php echo $MAP_WIDTH * $HEX_SIDE * 1.5 + $HEX_SIDE/2; ?>px;
        height: <?php echo $MAP_HEIGHT * $HEX_SCALED_HEIGHT + $HEX_SIDE; ?>px;
        position: relative;
        background: gray;
        /*background-image: url('../img/sectors/background.png');*/
        float:left;
        margin: 29px 0px 0px 29px;
    }



    .hex {
        position: absolute;
        width: <?php echo $HEX_SCALED_HEIGHT ?>;
        height: <?php echo $HEX_SCALED_HEIGHT ?>;
    }

    .battle_icon {
        position: absolute;
        width: <?php echo $HEX_SCALED_HEIGHT ?>;
        height: <?php echo $HEX_SCALED_HEIGHT ?>;
        z-index: 2;
    }

    .capitol_icon {
        position: absolute;
        margin-top: <?=$HEX_SCALED_HEIGHT/3?>;
        margin-left: <?=$HEX_SCALED_HEIGHT/3?>;
        width: <?php echo $HEX_SCALED_HEIGHT/3 ?>;
        height: <?php echo $HEX_SCALED_HEIGHT/3 ?>;
        z-index: 2;
    }

    .flag_icon {
        position: absolute;
        opacity: 0.6;
        margin-top: <?=$HEX_SCALED_HEIGHT/40?>;
        margin-left: <?=$HEX_SCALED_HEIGHT/4?>;
        width: <?php echo $HEX_SCALED_HEIGHT/3 ?>;
        height: <?php echo $HEX_SCALED_HEIGHT/3 ?>;
        z-index: 2;
    }

    #map_frame {
        position: absolute;
        width: 678px;
        height: 493px;
        z-index: 0;
        border: none;
    }

    #map_container {
        margin-top:10px;
    }
    .dragable {
        cursor: move;
    }
    #detailBoxContainer {
        left: 350px;
        top: 139px;
    }
    .detailBox_head {
        width: 100%;
        height: 15px;
        background-color: #808080;
    }
    .zoom {
        width: 32px;
        height: 32px;
        cursor: pointer;
    }
    #zoom_container {
        width: auto;
        
    }
    #map_controller {
        position: absolute;
        left: 870px;
        top: 220px;
        background: transparent;
        width: 133px;
        z-index: 2;
    }
    .map_arrow {
        cursor: pointer;
        width: 32px;
        height: 32px;
    }

    #map_arrow_up, #map_arrow_down {
        margin-right: 35%;
        margin-left: 35%;
    }