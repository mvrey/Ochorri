<link rel="stylesheet" type="text/css" href="../../views/detailBox/detailBox.css" />

<div id="detailBox_close" class="close" onclick="hide_detailBox();">X</div>
<div id="request_head">
    <div class="arrow" onclick="requestPreviousSector('<?=$detailType?>',<?=$coordinateX?>,<?=$coordinateY?>)"><</div>
    <span id="sector_name">
        <?=$sector->getName()?><br />
        <?=$sector->getCoordinateX().",".$sector->getCoordinateY()?>
    </span>
    <div class="arrow" onclick="requestNextSector('<?=$detailType?>',<?=$coordinateX?>,<?=$coordinateY?>)"> > </div>
</div>