<?php
?>
<html>
    <head>
        <style>
            #news {
                width: 340px;
                height: 438px;
                border: none;
                float:left;
                margin: 0px 0px 0px 50px;
                background: url('<?=$img_design?>pergamino.png') no-repeat center;
            }
            #news li {
                color: black;
                font-size: 12px;
                list-style: none;
                margin: 5px 5px 5px 10px ;
            }
            #news li img {
                width: 30px;
                height: 30px;
            }
            .spacer {
                width: 100%;
                height: 60px;
            }
            #newsList {
                clear: both;
            }
        </style>
    </head>
    <body>
        <div id="news">
            <div class="spacer"></div>
            <div id="newsList">
                <ul>
                    <li>LEYENDA:</li>
                    <li><img src="<?=$img_sectors?>sector_own.png" />Sector propio</li>
                    <li><img src="<?=$img_sectors?>sector_foreign.png" />Sector enemigo</li>
                    <li><img src="<?=$img_sectors?>sector_empty.png" />Sector vacío</li>
                    <li><img src="<?=$img_sectors?>sector_water.png" />Agua (infraqueable de momento)</li>
                    <li><img src="<?=$img_other?>battle_icon.png" />Batalla</li><br />
                    <li>Pincha en tus sectores y navega por las opciones</li>
                </ul>
            </div>
        </div>
    </body>
</html>