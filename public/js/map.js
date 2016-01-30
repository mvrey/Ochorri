var detailBoxControllerPath = '../../controllers/detailBox/'
var lastClickedSector = "0,0"; //This var controls not to refade the menu if not necessary
var lastOverSector = "";
var showing_sectorMenu = false;
var lastDestinySector = "";
var lastDestinyMode = -1;
var resources = new Array();
var resource_intervals = new Array();
var map_interval = false;
var map_refresh = 5000;     //miliseconds to refresh map
var resource_refresh = 5000; //miliseconds to refresh
var ETAs = new Array();
var slowestSpeed = new Array(0);
var availableUnits = new Array();
var originX = 0;
var originY = 0;
var originIncrementX = 2;
var originIncrementY = 1;

var insuficientResourcesSubject = "Recursos insuficientes";
var insuficientResourcesContent = "No teneis suficientes recursos para completar esa operación.";

var zoom_increment = 1;

var min_map_height = <?=$MIN_HEIGHT?>;
var max_map_height = <?=$MAX_HEIGHT?>;
var min_map_width = 2*min_map_height;
var max_map_width = 2*max_map_height;

sector_types = Array("own","foreign","water","empty","battle");

function show_map() {
    $.ajax({
        url: "../../controllers/map/mapController.php",
        type: "POST",
        success: function(data){
            for (techId in tech_intervals)
                stopTechnologyProgress (techId);
            $("#main_container").html(data);
            refresh_map();
            set_map_properties();
        }
    });
}

function redirect(url) {
    window.location(url);
}

function zoom_in () {
    var height = Math.floor(map_height-zoom_increment);

    if (height>=min_map_height)
        {
        hide_allSectorMenus()
        refresh_map(height, height*2);
        }
}

function zoom_out () {
    var height = Math.ceil(map_height+zoom_increment);

    if (height<=max_map_height)
        {
        hide_allSectorMenus()
        refresh_map(height, height*2);
        }
}

function handle_map_click(event) {
    coordinates = getClickedSector(event);
    if (document.getElementById(coordinates)!=null)
        {
        var list = document.getElementsByClassName('hex');
        for(var i in list)
            list[i].onmouseover=null;

        //if ((coordinates!=lastClickedSector) || (showing_sectorMenu == false))
            //{
            lastClickedSector = coordinates;

            for (var j in sector_types)
                {
                    sectorMenuId = "sectorMenu_"+sector_types[j];
                    display = $("#"+sectorMenuId).css("display");
                    if (display=="block")
                        break;
                }

            $("#"+sectorMenuId).fadeOut('fast', function()
                {

                if (document.getElementById('battle'+coordinates)!=undefined)
                    sector_type = "battle";
                else
                    sector_type = document.getElementById(coordinates).alt;

                map_coordinates = coordinates.split(",");
                map_x = map_coordinates[0];
                map_y = map_coordinates[1];

                placeHighlight (map_x,map_y,0);
                show_sectorMenu (map_x,map_y,sector_type);
                showing_sectorMenu = true;
                });
            //}
        }
}

function mouseOverSector(map_x,map_y) {
    
    lastOverSector = map_x+","+map_y;

    if (!showing_sectorMenu)
        placeHighlight (map_x,map_y,0);
}

function hideHighlight (mode) {

    switch (mode)
        {
        case 0: var highlight = document.getElementById('highlight'); break;
        case 1: var highlight = document.getElementById('highlight-green'); break;
        case 2: var highlight = document.getElementById('highlight-red'); break;
        }
    highlight.style.display = 'none';
}

function hide_allSectorMenus() {
    hide_sectorMenu('sectorMenu_own');
    hide_sectorMenu('sectorMenu_foreign');
    hide_sectorMenu('sectorMenu_empty');
    hide_sectorMenu('sectorMenu_water');
    hide_sectorMenu('sectorMenu_battle');
}

function hide_sectorMenu(id) {
    $("#"+id).fadeOut('fast', function()
        {
        showing_sectorMenu = false;
        var list = document.getElementsByClassName('hex');
        for (var i=0; i<list.length; i++)
            {
            hex_id = list[i].id;
            if ((hex_id!=undefined) && (hex_id!='highlight'))
                {
                list[i].onmouseover = function()
                    {
                        coordinates = this.id.split(",");
                        mouseOverSector(coordinates[0],coordinates[1]);
                    }
                }
            }
        });
};

function hide_detailBox() {
    hideHighlight(1);
    hideHighlight(2);
    stopBattleInterval();
    $("#detailBoxContainer").fadeOut('fast');
    //showing_detailBox = false;
    lastDestinyMode = 0;
};

function show_detailBox() {
    $("#detailBoxContainer").fadeIn('fast');
    //showing_detailBox = true;
};

function show_details(type,coordinateX,coordinateY) {

    show_detailBox();

    if ((coordinateX==null) || (coordinateY==null))
        {
        map_coordinates = lastClickedSector.split(",");
        map_x = Number(map_coordinates[0])+Number(originX);
        map_y = Number(map_coordinates[1])+Number(originY);
        }
    else
        {
        map_x = Number(coordinateX)+Number(originX);
        map_y = Number(coordinateY)+Number(originY);
        }

    switch (type)
        {
        case 'details': script = detailBoxControllerPath+'movementDetails_request.php'; break;
        case 'buildings': script = detailBoxControllerPath+'buildingsDetails_request.php'; break;
        case 'units': script = detailBoxControllerPath+'unitsDetails_request.php'; break;
        case 'production': script = detailBoxControllerPath+'productionDetails_request.php'; break;
        case 'battle': script = detailBoxControllerPath+'battleDetails_request.php'; break;
        default: script='';
        }

    $.ajax({
        url: script,
        type: "POST",
        data: "coordinateX="+map_x+"&coordinateY="+map_y,
        success: function(datos){
            switch (type)
            {
            case 'details':
                $("#detailBox").html(datos);
                var i=0;
                var timeLeft = Number($('#timeLeft'+i).html());
                while (timeLeft>0)
                    {
                    stopMovementCounter(i);
                    startMovementCounter(i);
                    i++;
                    timeLeft = Number($('#timeLeft'+i).html());
                    }
                detailsX = map_x;
                detailsY = map_y;
                break;

            case 'buildings':
                datos = datos.split('^_^');
                stopBuildingProgressBar();
                $("#detailBox").html(datos[5])

                activeBuildingId = datos[1];
                activeBuildingTime = datos[2];
                percentList = datos[3].split(",");
                sectorId = datos[4];

                for (var i=0; percentList.length>0; i++)
                    {
                    if (activeBuildingId == i)
                        {
                        stopBuildingProgressBar();
                        startBuildingProgressBar (activeBuildingId, activeBuildingTime*10, percentList.shift());
                        setTriggerVisibility (activeBuildingId, 0, sectorId);
                        }
                    else
                        {
                        percent = percentList.shift();
                        $("#building_progressBar"+i).css("width",percent+"%");
                        }
                    }
                break;
                
            case 'units':
                datos = datos.split('^_^');
                unitList = datos[2].split(",");
                if (unitList[0]=="")
                    unitList.shift();

                timeList = datos[3].split(",");
                if (timeList[0]=="")
                    timeList.shift();

                availableUnits = datos[4].split(",");
                speeds = datos[5].split(",");

                distances = datos[6].split(",");
                ETAs = distances;
                slowestSpeed = [0];

                percent = datos[1];

                stopProgressBar();

                $("#detailBox").html(datos[7]);
                
                if (unitList.length>0)
                    {
                    unitId = unitList.shift();
                    time = timeList.shift();
                    startProgressBar (unitId, time*10, percent);
                    }
                
                for (j in availableUnits)
                    set_unitStartCosts (availableUnits[j]);

                if (update_trainingCosts (quantity)!=1)
                update_trainingCosts (trainingQuantity)

                updateSelectedQuantity();
                break;
            
            case 'battle':
                datos = datos.split("^_^");
                $("#detailBox").html(datos[1]);
                var time_left = (Number(round_time)/1000) - Number(datos[0]);
                $("#roundTimeLeft").html(time_left);
                stopBattleInterval();
                startBattleInterval(map_x, map_y);
                //setTimeout("doFight("+map_x+","+map_y+")", time_left*1000);
                //setTimeout("startBattleInterval("+map_x+","+map_y+")", time_left*1000);
                break;
            default: $("#detailBox").html(datos);
            }
        }
    });
}

function move_map (direction) {

    var aux;
    hide_allSectorMenus();
    switch (direction)
        {
        case 0:
            aux = originY-originIncrementY;
            if (aux>=0)
                {
                originY = aux;
                refresh_map(map_height, map_width);
                }
            break;
        case 1:
            aux = Number(originX) + Number(originIncrementX);
            if (aux+map_width<=max_map_width)
                {
                originX = aux;
                refresh_map(map_height, map_width);
                }
            break;
        case 2:
            aux = Number(originY) + Number(originIncrementY);
            if (aux+map_height<=max_map_height)
                {
                originY = aux;
                refresh_map(map_height, map_width);
                }
            break;
        case 3:
            aux = originX - originIncrementX;
            if (aux>=0)
                {
                originX = aux;
                refresh_map(map_height, map_width);
                }
            break;
        default:
            alert("Error al mover el mapa");
            break;
        }
}

function refresh_map_controller(map_height, map_width, startX, startY) {

    if (startX!='undefined')
        originX = startX;
    if (startY!='undefined')
        originY = startY;
    
    var up = Number(originY)-Number(originIncrementY);
    var right = Number(originX)+Number(map_width)+Number(originIncrementX);
    var down = Number(originY)+Number(map_height)+Number(originIncrementY);
    var left = Number(originX)-Number(originIncrementX);

    if (up<0)
        $("#map_arrow_up").attr("src", "<?=$img_buttons?>no-arrow-up.png");
    else
        $("#map_arrow_up").attr("src", "<?=$img_buttons?>arrow-up.png");
    if (right>max_map_width)
        $("#map_arrow_right").attr("src", "<?=$img_buttons?>no-arrow-right.png");
    else
        $("#map_arrow_right").attr("src", "<?=$img_buttons?>arrow-right.png");
    if (down>max_map_height)
        $("#map_arrow_down").attr("src", "<?=$img_buttons?>no-arrow-down.png");
    else
        $("#map_arrow_down").attr("src", "<?=$img_buttons?>arrow-down.png");
    if (left<0)
        $("#map_arrow_left").attr("src", "<?=$img_buttons?>no-arrow-left.png");
    else
        $("#map_arrow_left").attr("src", "<?=$img_buttons?>arrow-left.png");
}

function refresh_map(height, width) {
    
    $.ajax({
        url: "../../controllers/map/map_request.php",
        type: "POST",
        data: "height="+height+"&width="+width+"&originX="+originX+"&originY="+originY,
        success: function(data){
            data = data.split("^_^");
            startX = data[1];
            startY = data[2];
            balances = data[3].split(",");
            resources = data[4].split(",");
            newMessages = (data[5]>0);

            if (newMessages)
                $("#newMessages_container").css("display", "block");
            else
                $("#newMessages_container").css("display", "none");

            // Disabled js resource interval as it is already done on the refresh map interval
            //stopIncreaseResources();

            for (var i=0; i<balances.length; i++)
                {
                if (document.getElementById("resourceQuantity"+(i+1))!=undefined)
                    {
                    document.getElementById('resourceQuantity'+(i+1)).innerHTML = Math.floor(resources[i]);
                    quantity = (balances[i]/3600000)*resource_refresh;

                    balance = Math.round(balances[i]*100)/100;
                    if (balance>=0)
                        {
                        $("#totalBalance"+(i+1)).css("color", "green");
                        $("#totalBalance"+(i+1)).html("+"+balance);
                        }
                    else
                        {
                        $("#totalBalance"+(i+1)).css("color", "red");
                        $("#totalBalance"+(i+1)).html(balance);
                        }
                    
                    //startIncreaseResource(i+1, quantity);
                    }
                }
            $("#hexmap").html(data[6]);
            if (showing_sectorMenu)
                var coordinates = lastClickedSector.split(",");
            else
                var coordinates = lastOverSector.split(",");
            placeHighlight (coordinates[0],coordinates[1],0);

            if (lastDestinyMode>0)
                {
                var destiny = lastDestinySector.split(",");
                placeHighlight (destiny[0],destiny[1],lastDestinyMode, true);
                }
            stopMapInterval();
            startMapInterval(height, width);
            refresh_map_controller(map_height, map_width, startX, startY);
        }
    });
}

function startMapInterval(height, width) {
    if (map_interval==false)
        map_interval = window.setInterval("refresh_map("+height+", "+width+")", map_refresh)
}

function stopMapInterval() {
    window.clearInterval(map_interval);
    map_interval = false;
}

function startIncreaseResource (resourceId, quantity) {

    resource_intervals.push(window.setInterval("increaseResource("+resourceId+","+quantity+")", resource_refresh));
}

function stopIncreaseResources() {

    for (id in resource_intervals)
        window.clearInterval(resource_intervals[id]);

    resource_intervals = [];
}

function increaseResource(resourceId, quantity) {

    resources[resourceId-1] = Number(resources[resourceId-1])+Number(quantity);
    document.getElementById('resourceQuantity'+resourceId).innerHTML = Math.round(resources[resourceId-1]);
}

function requestPreviousSector(type, coordinateX, coordinateY) {

    coordinateX-=originX;
    coordinateY-=originY;

    if (type=='battle')
        sectors = document.getElementsByClassName('battle_icon');
    else
        sectors = document.getElementsByClassName('hex');
    
    top = -1; topX=-1; topY=-1;
    for (var i in sectors)
        {
        if ( (sectors[i].id!=null) && ((sectors[i].alt=="own") || (type=='battle')))
            {
            if (type=='battle')
                coord = (sectors[i].id).substr(6,3);
            else
                coord = sectors[i].id;

            c = coord.split(",");
            sum = (c[0]*10)+Number(c[1]);
            if ((sum < Number(coordinateX)*10+Number(coordinateY)) && (sum>top))
                {
                topX = c[0];
                topY = c[1];
                top = (c[0]*10)+Number(c[1]);
                }
            }
        }
    if (top == -1)
        {
        topX = coordinateX;
        topY = coordinateY;
        }
    var isBattle = document.getElementById('battle'+topX+","+topY);
    if ((isBattle==undefined) || ((isBattle!=undefined) && (type=='battle')))
        {
        placeHighlight(topX,topY,0);
        //show_sectorMenu (topX,topY,'own');
        lastClickedSector = topX+","+topY;
        show_details(type,topX,topY);
        }
    else
        requestPreviousSector(type, topX, topY);
}

function requestNextSector(type, coordinateX, coordinateY) {

    coordinateX-=originX;
    coordinateY-=originY;

    if (type=='battle')
        sectors = document.getElementsByClassName('battle_icon');
    else
        sectors = document.getElementsByClassName('hex');

    top = 999; topX=999; topY=999;

    for (var i in sectors)
        {
       if ( (sectors[i].id!=null) && ((sectors[i].alt=="own") || (type=='battle')))
            {
            if (type=='battle')
                coord = (sectors[i].id).substr(6,3);
            else
                coord = sectors[i].id;

            c = coord.split(",");
            sum = (c[0]*10)+Number(c[1]);
            if ((sum > Number(coordinateX)*10+Number(coordinateY)) && (sum<top))
                {
                topX = c[0];
                topY = c[1];
                top = (c[0]*10)+Number(c[1]);
                }
            }
        }
    if (top == 999)
        {
        topX = coordinateX;
        topY = coordinateY;
        }
    var isBattle = document.getElementById('battle'+topX+","+topY);
    if ((isBattle==undefined) || ((isBattle!=undefined) && (type=='battle')))
        {
        placeHighlight(topX,topY,0);
        //show_sectorMenu (topX,topY,'own');
        lastClickedSector = topX+","+topY;
        show_details(type,topX,topY);
        }
    else
        requestNextSector(type, topX, topY);
}

function set_map_properties() {
    
   //var img_w = $("#sectorMenu img").width() + 10;
   //var img_h = $("#sectorMenu img").height() + 28;

   //Darle el alto y ancho
   //$("#sectorMenu").css('width', img_w + 'px');
   //$("#sectorMenu").css('height', img_h + 'px');

   //Esconder el sectorMenuup
   $("#sectorMenu_own").hide();
   $("#sectorMenu_foreign").hide();
   $("#sectorMenu_water").hide();
   $("#sectorMenu_empty").hide();
   $("#sectorMenu_battle").hide();

   $("#detailBoxContainer").hide();
   //temporizador, para que no aparezca de golpe
   //setTimeout("show_sectorMenu()",1500);
   $('#sectorMenu_own').Draggable({handle: 'span'});
   $('#sectorMenu_foreign').Draggable({handle: 'span'});
   $('#sectorMenu_water').Draggable({handle: 'span'});
   $('#sectorMenu_empty').Draggable({handle: 'span'});
   $('#sectorMenu_battle').Draggable({handle: 'span'});
   $('#detailBoxContainer').Draggable({handle: 'div.dragable'});
   $('#map_controller').Draggable({});

   //refresh_map_controller(5,10,0,0);
}

$(document).ready(function (){

   set_map_properties();
});