var building_interval = false;

function stopBuilding(buildingId, sectorId) {

    $.ajax({
        url: "../../controllers/detailBox/updateBuildings.php",
        type: "POST",
        data: "action="+1+"&buildingId="+buildingId+"&sectorId="+sectorId+"&pausing=1",
        success: function(request_data){
            data = request_data.split(";");
            var success = Number(data[0]);
            var coordinateX = Number(data[1]);
            var coordinateY = Number(data[2]);
            //if (success)
            show_details('buildings',coordinateX,coordinateY);
            }
        });
}


function startBuilding(buildingId, sectorId, capitolSectorNameString) {

    var confirmed = true;
    if (capitolSectorNameString!="")
        {
        if (buildingId==0)
            confirmed =
            jConfirm("Este capitolio sustituirá al actual en "+capitolSectorNameString, "Reemplazar edificio",
                function (confirmed){
                    if (confirmed) reallyStartBuilding (buildingId, sectorId)
                    });
        else
            reallyStartBuilding (buildingId, sectorId);
        }
    else
        reallyStartBuilding (buildingId, sectorId)
}

function reallyStartBuilding (buildingId, sectorId) {

    $.ajax({
            url: "../../controllers/detailBox/updateBuildings.php",
            type: "POST",
            data: "action="+1+"&buildingId="+buildingId+"&sectorId="+sectorId+"&pausing=0",
            success: function(request_data){
                request_data = request_data.split(";");
                var response=Number(request_data[1]);
                if (response==0)
                    jAlert(insuficientResourcesContent, insuficientResourcesSubject);
                else
                    {
                    buildingTime = request_data[1];
                    building_costs = request_data[2].split(",");
                    percent = request_data[3];

                    for (var i=1; i<building_costs.length; i++)
                        {
                        increaseResource(i, -building_costs[i-1]);
                        }

                    if (building_interval==false)
                        {
                        setTriggerVisibility (buildingId, 0, sectorId);
                        startBuildingProgressBar(buildingId, buildingTime*10, percent);
                        }
                    }
            }
        });
}

function setTriggerVisibility (buildingId, mode, sectorId) {
//mode 0=start; 1=end;

    var i=0;
    buildingTrigger = document.getElementById("buildingTrigger"+i);

    while (!(buildingTrigger==null))
        {
            buildingTrigger = document.getElementById("buildingTrigger"+i);
            if (buildingTrigger==null)
                break;
            else
                {
                if (i==buildingId)
                    {
                    if (mode==0)
                        {
                        buildingTriggerImgSrc = ($("#buildingTriggerImg"+i).attr("src")).replace("build","pause");
                        $("#buildingTriggerImg"+i).attr('onclick', '');
                        $("#buildingTriggerImg"+i).click (function () {stopBuilding(buildingId, sectorId);});
                        }
                    else if (mode==1)
                        {
                        buildingTriggerImgSrc = ($("#buildingTriggerImg"+i).attr("src")).replace("pause","build");
                        $("#buildingTriggerImg"+i).attr('onclick', '');
                        $("#buildingTriggerImg"+i).click (function () {startBuilding(buildingId, sectorId);});
                        }

                    $("#buildingTriggerImg"+i).attr("src",buildingTriggerImgSrc);
                    }
                else
                    {
                    if (mode==0)
                        buildingTrigger.style.visibility = "hidden";
                    else if (mode==1)
                        buildingTrigger.style.visibility = "visible";
                    }
                }
            i++;
        }
}



function startBuildingProgressBar (buildingId, time, percent) {

    $("#building_progressBar"+buildingId).css("width", percent+"%");
    if (building_interval==false)
        building_interval = window.setInterval("increaseBuildingProgressBar("+buildingId+","+percent+")", time);
}

function stopBuildingProgressBar() {

    window.clearInterval(building_interval);
    building_interval = false;
}

function increaseBuildingProgressBar(buildingId, percent) {

    progressBar_width = $("#building_progressBar"+buildingId).css("width");

    if (progressBar_width==undefined)
        stopBuildingProgressBar();
    else
        {
        progressBar_width = progressBar_width.replace("px","");

        if (progressBar_width>=100)
            {
            $("#building_progressBar"+buildingId).css("width","0");
            stopBuildingProgressBar();
            //$("#buildingLevel"+buildingId).html(Number($("#buildingLevel"+buildingId).html())+1);
            //$("#buildingLevelDiv"+buildingId).css("visibility", "visible");
            //setTriggerVisibility (buildingId, 1);
            var coordinates = lastClickedSector.split(",");
            show_details('buildings',coordinates[0],coordinates[1]);
            }
        else
            {
            container_width = $("#building_progressBar_container"+buildingId).css("width");
            container_width = container_width.replace("px","");

            progressBar_width = (Number(progressBar_width)/Number(container_width)) * 100;
            progressBar_width++;
            $("#building_progressBar"+buildingId).css("width",progressBar_width+"%");
            }
        }
}

$(document).ready(function (){

});