var tech_startCosts = new Array();
var tech_startTimes = new Array();

var tech_progresses = new Array();  //Already researched percentages
var tech_planned = new Array();     //progress on endTime
var tech_intervals = new Array();
var tech_refresh = 1000;

function show_technologies () {
    stopMapInterval();
    $.ajax({
        url: "../../controllers/technologies/technologies_request.php",
        type: "POST",
        success: function(data){
            data = data.split("^_^");
            $("#main_container").html(data[1]);

            var techs = data[0].split("-");
            for (tech in techs)
                {
                tech = techs[tech].split("/");
                tech_progresses[tech[0]] = tech[1];
                tech_planned[tech[0]] = tech[2];
                set_progressBar(tech[0], tech[1], tech[2]);
                stopTechnologyProgress (tech[0]);
                if (tech[2]>0)
                    startTechnologyProgress (tech[0], tech_refresh);
                }
        }
    });
}

function set_progressBar (techId, progress, planned) {

    var progressLeft = planned-progress;
    if (progressLeft<0)
        progressLeft = 0;
    $("#technology_progressBar"+techId).css("width", progress+"%");
    $("#technology_plannedBar"+techId).css("width", planned+"%");
}

function update_researchCosts (techId) {

    var percent = Number($("#percentOrder"+techId).attr("value"));
    var progress = Number($("#progress"+techId).html());
    
    if (percent<1)
        {
        percent = 1;
        $("#percentOrder"+techId).val(1);
        }
    else if (percent+progress>100)
        {
        percent = 100-progress;
        $("#percentOrder"+techId).val(100-progress);
        }

    for (var i=1; i<6; i++)
        {
        cost = $("#cost"+techId+"-"+i).html();
        if (cost!='undefined')
            $("#cost"+techId+"-"+i).html(cost*percent);
        }

    var cost=0;

    for (i=1; i<6; i++)
        {
        cost = $("#cost"+techId+"-"+i).html();
        if (cost!='undefined')
            $("#cost"+techId+"-"+i).html(tech_startCosts[techId][i]*percent);
        }

    $("#time"+techId).html(tech_startTimes[techId]*percent);
}

function set_techStartCosts (techId) {

    var time = cost = Number($("#time"+techId).html());
    tech_startTimes[techId] = Number(time);
    tech_startCosts[techId] = new Array();
    
    for (i=1; i<6; i++)
        {
        cost = $("#cost"+techId+"-"+i).html();
        if (cost!='undefined')
            tech_startCosts[techId][i] = cost;
        }
}

function update_technology (techId) {

    percentOrder = $("#percentOrder"+techId).val();

    $.ajax({
        url: "../../controllers/technologies/updateTechnologies.php",
        type: "POST",
        data: "techId="+techId+"&percentOrder="+percentOrder,
        success: function(data){
            response = Number(data);
            if (response==1)
                jAlert(insuficientResourcesContent, insuficientResourcesSubject);
            else
                show_technologies();
        }
    });
}

function startTechnologyProgress (techId, time) {

    var tech_interval = window.setInterval("increaseTechnologyProgress("+techId+")", time);
    tech_intervals[techId] = tech_interval;
}

function stopTechnologyProgress (techId) {

    window.clearInterval(tech_intervals[techId]);
}

function increaseTechnologyProgress(techId) {

    var progress = Number(tech_progresses[techId]);
    new_progress = progress+Number((1/tech_startTimes[techId])*(tech_refresh/1000));
    tech_progresses[techId] = new_progress;

    var planned = Number(tech_planned[techId]);
    new_planned = planned-Number((1/tech_startTimes[techId])*(tech_refresh/1000));
    tech_planned[techId] = new_planned;

    if (tech_progresses[techId]>=100)
        {
        window.setTimeout("$('#technology_progressBar"+techId+"').css('width', '0')", tech_refresh);;
        window.setTimeout("$('#progress"+techId+"').html('0')", tech_refresh);
        $("#techLevel"+techId).html(Number($("#techLevel"+techId).html())+1);
        stopTechnologyProgress (techId);
        show_technologies ();
        }
    if (tech_planned[techId]<=0)
        {
        stopTechnologyProgress (techId);
        }

    $("#progress"+techId).html(new_progress.toFixed(2));
    $("#technology_progressBar"+techId).css("width", new_progress+"%");
    $("#technology_plannedBar"+techId).css("width", new_planned+"%");
}