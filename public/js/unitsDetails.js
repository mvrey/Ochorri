var unit_interval = false;
var unitList = new Array();
var timeList = new Array();
var lastSelectedUnitsQuantity = 0;
var distanceMultiplier = 1500;
var trainingQuantity = 1;
var trainingQuantityIndex = 0;

var unit_startCosts = new Array();
var unit_startTimes = new Array();

function increaseUnitQueue(action, unitId, coordinateX, coordinateY) {

    $.ajax({
        url: "../../controllers/detailBox/updateUnitQueue.php",
        type: "POST",
        data: "action="+action+"&unitId="+unitId+"&coordinateX="+coordinateX+"&coordinateY="+coordinateY+"&quantity="+trainingQuantity,
        success: function(request_data){
            request_data = request_data.split(";");
            success = Number(request_data[0]);
            if (success==0)
                jAlert(insuficientResourcesContent, insuficientResourcesSubject);
            else if (success==1)
                jAlert("La cola de entrenamiento está llena.", "You can't do that on the internet");
            else
                {
                for (k=0; k<trainingQuantity; k++)
                    {
                    unitList.push(Number(request_data[1]));
                    timeList.push(Number(request_data[2]));
                    }
                unit_costs = request_data[3].split(",");
                percent = 0;

                increaseTotalQueue(unitId, trainingQuantity);
                for (var i=1; i<unit_costs.length; i++)
                    {
                    increaseResource(i, -unit_costs[i-1]*trainingQuantity);
                    }

                if (unit_interval==false)
                    startProgressBar(unitList.shift(), timeList.shift()*10, percent);
                }
        }
    });
}

function increaseTotalQueue(unitId, quantity) {
    document.getElementById('queueTotal'+unitId).innerHTML = Number(document.getElementById('queueTotal'+unitId).innerHTML)+Number(quantity);
}

function startProgressBar (unitId, time, percent) {

    $("#unit_progressBar"+unitId).css("width", percent+"%");
    unit_interval = window.setInterval("increaseProgressBar("+unitId+","+percent+")", time);
}

function stopProgressBar() {

    window.clearInterval(unit_interval);
    unit_interval = false;
}

function increaseProgressBar(unitId, percent) {

    progressBar_width = $("#unit_progressBar"+unitId).css("width");

    if (progressBar_width==undefined)
        stopProgressBar();
    else
        {
        progressBar_width = progressBar_width.replace("px","");

        if (progressBar_width>=100)
            {
            $("#unit_progressBar"+unitId).css("width", "0");
            $("#queueLast"+unitId).html(Number($("#queueLast"+unitId).html())+1);
            $("#availableUnits"+unitId).html(Number($("#availableUnits"+unitId).html())+1);

            stopProgressBar();
            if (unitList.length>0)
                startProgressBar(unitList.shift(), timeList.shift()*10, 0);
            }
        else
            {
            container_width = $("#unit_progressBar_container"+unitId).css("width");
            container_width = container_width.replace("px","");

            progressBar_width = (Number(progressBar_width)/Number(container_width)) * 100;
            progressBar_width++;
            $("#unit_progressBar"+unitId).css("width",progressBar_width+"%");
            }
        }
}

function showETA(option){

    var ETA = Math.round((distances[option]*distanceMultiplier)/slowestSpeed[0]);
    $("#ETABox").html(ETA);
}

function updateSelected()
{
	var destiny = document.getElementById('Destiny');
	var ETABox = document.getElementById('ETABox');

	var selected = destiny.selectedIndex;

        return(selected);
}

function checkSelectedUnits (unitId, speed) {

    var availableUnits = Number($("#availableUnits"+unitId).html());
    var selectedUnits = Number($("#selectedUnits"+unitId).attr("value"));
    if ((selectedUnits<0) || (isNaN(selectedUnits)))
        $("#selectedUnits"+unitId).attr("value","0");
    else if (selectedUnits>availableUnits)
        $("#selectedUnits"+unitId).attr("value", availableUnits);

    if ((selectedUnits>0) && ((speed<slowestSpeed[0]) || (slowestSpeed[0]==0)))
        {
        slowestSpeed.unshift(speed);
        showETA(updateSelected());
        }
    else if ((selectedUnits==0) && (lastSelectedUnitsQuantity!=0))
        {
        slowestSpeed.shift();
        showETA(updateSelected());
        }
}

function setLastSelectedUnitsQuantity (unitId) {
    
    lastSelectedUnitsQuantity = $("#selectedUnits"+unitId).attr("value");
}

function sendDivision (startX, startY) {

    //availableUnits
    var quantities = new Array();

    for (var unit in availableUnits)
        quantities.push($('#selectedUnits'+availableUnits[unit]).attr('value'));

    var destiny = document.getElementById('Destiny');
    var selected = destiny.options[destiny.selectedIndex].value;
    var end = selected.split(",");
    var endX = end[0];
    var endY = end[1];

    $.ajax({
        url: "../../controllers/detailBox/sendDivisions.php",
        type: "POST",
        data: "unitList="+availableUnits.join(",")+"&quantityList="+quantities.join(",")
            +"&startX="+startX+"&startY="+startY+"&endX="+endX+"&endY="+endY+"&speed="+slowestSpeed[0],
        success: function(request_data){

                request_data = request_data.split("^_^")
                var unitList = request_data[0].split(",");
                var quantityList = request_data[1].split(",");
                jAlert("Tropas Enviadas.");
                for (i in unitList)
                    {
                    $('#availableUnits'+unitList[i]).html(Number($('#availableUnits'+unitList[i]).html())-Number(quantityList[i]));
                    $('#selectedUnits'+unitList[i]).attr("value","0");
                    }
        }
    });

    
}

function selectQuantity (index, quantity) {
    jQuery.each($(".trainingQuantitySelector"), function(){$(this).css("background-color", "transparent");} )
    $("#selectQuantity"+index).css("background-color", "cadetblue");
    trainingQuantityIndex = index;
    trainingQuantity = quantity;
    update_trainingCosts(quantity);
}

function update_trainingCosts (quantity) {

for (i in availableUnits)
    {
    unitId = availableUnits[i];
    costs = unit_startCosts[unitId];

    for (var i=0; i<5; i++)
        {
        cost = $("#unit_cost"+unitId+"-"+i).html();
        if (cost!='undefined')
            $("#unit_cost"+unitId+"-"+i).html(costs[i]*quantity);
        }

    $("#unit_time"+unitId).html(unit_startTimes[unitId]*quantity);
    }
}

function updateSelectedQuantity () {
    $("#selectQuantity"+trainingQuantityIndex).css("background-color", "cadetblue");
}

function set_unitStartCosts (unitId) {

    var time = cost = $("#unit_time"+unitId).html();
    unit_startTimes[unitId] = Number(time);
    unit_startCosts[unitId] = new Array();

    for (i=0; i<5; i++)
        {
        cost = $("#unit_cost"+unitId+"-"+i).html();
        if (cost!='undefined')
            unit_startCosts[unitId][i] = Number(cost);
        }
}

$(document).ready(function (){
    
});