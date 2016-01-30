<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8">
        <title>O'chorri - Map</title>
        <link rel="icon" type="image/png" href="<?=$img_other?>favicon.png" />

    <script language="javascript" type="text/javascript" src="../../js/jquery.js"></script>

    <script language="javascript" type="text/javascript" src="../../js/movementDetails.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/buildingsDetails.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/unitsDetails.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/battleDetails.js"></script>
    <script language="javascript" type="text/javascript"><? require_once ("../../js/map.js"); ?></script>
    <script language="javascript" type="text/javascript"><? require_once ("../../js/map_updated.js"); ?></script>
    
    <script language="javascript" type="text/javascript" src="../../js/iutil.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/idrag.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/jquery.alerts.js"></script>
    <link rel="StyleSheet" href="../../js/jquery.alerts.css" type="text/css">
    <script language="javascript" type="text/javascript" src="../../js/jquery.tablesorter.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/technologies.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/messages.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/ranking.js"></script>

    
    </head>
    <body onload="originX=<?=$lastMapOrigin[0]?>; originY=<?=$lastMapOrigin[1]?>; refresh_map(<?=$lastMapHeight?>,<?=$lastMapHeight*2?>);">