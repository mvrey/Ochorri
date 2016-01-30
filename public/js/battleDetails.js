var battle_interval = false;
var round_time = 30000;

function startBattleInterval(x, y) {
     if (battle_interval==false)
        battle_interval = window.setInterval("decreaseBattleTimeLeft("+x+","+y+")", 1000);
}

function stopBattleInterval() {
    window.clearInterval(battle_interval);
    battle_interval = false;
}

function decreaseBattleTimeLeft (x, y) {

    time_left = $("#roundTimeLeft").html();

    if (time_left==undefined)
        stopBattleInterval();
    else
        {
        time_left = Number(time_left);

        if (time_left < 1)
            {
            $("#roundTimeLeft").html(Number(round_time)/1000 - 1);
            doFight(x,y);
            }
        else
            {
            $("#roundTimeLeft").html(Number($("#roundTimeLeft").html()-1));
            }
        }
}

function doFight(coordinateX, coordinateY){
    $.ajax({
        url: "../../controllers/detailBox/battleDetails_request.php",
        type: "POST",
        data: "coordinateX="+coordinateX+"&coordinateY="+coordinateY+"&noView=1",
        success: function(request_data) {
            var data = request_data.split("/");
            var winner = Number(data[0]);

            switch (winner)
                {
                case 0: //Still fighting
                    var attackers = data[1];
                    var defenders = data[2];
                    var battleId = data[3];
                    var roundId = data[4];

                    divisions = attackers.split(",");
                    for (j in divisions)
                        {
                        var division = divisions[j].split(":");
                        var unitId = division[0];
                        var quantity = division[1];
                        $("#unitQuantityA"+unitId).html(quantity);
                        }

                    divisions = defenders.split(",");
                    for (j in divisions)
                        {
                        var division = divisions[j].split(":");
                        var unitId = division[0];
                        var quantity = division[1];
                        $("#unitQuantityD"+unitId).html(quantity);
                        }
                    var logBox_html = $("#logBox").html();
                    //alert($("#round"+roundId).html());
                    if ($("#round"+roundId).html()==null)
                        {
                        var newRound = '<span class="roundTitle" id="round'+roundId+'">Ronda '+roundId+'</span>'
                            +'<span class="expand" id="expand'+roundId+'" onclick="expandLog('+battleId+','+roundId+')">+</span>'
                            +'<br />'
                            +'<div class="logBox" id="logBox'+roundId+'"></div>';
                        $("#logBox").html(newRound+logBox_html);
                        }
                    break;
                case 1: //Attacker
                    stopBattleInterval();
                    $("#battleContainer").html("Ha ganado el atacante.");
                    break;
                case 2: //Defender
                    stopBattleInterval();
                    $("#battleContainer").html("Ha ganado el defensor.");
                    break;
                case 3:
                    stopBattleInterval();
                    break;
                }
            
            }
        });
}

function expandLog(battleId, roundId) {

var visible = ($("#logBox"+roundId).css("display") != "none");
var empty = ($("#logBox"+roundId).html() == '')

if (visible && !empty)
    collapseLog(roundId);
else if (!visible && !empty)
    showLog(roundId);
else
    {
    $.ajax({
        url: "../../controllers/detailBox/roundLog_request.php",
        type: "POST",
        data: "battleId="+battleId+"&roundId="+roundId,
        success: function(request_data) {
            $("#logBox"+roundId).html(request_data);
            $("#expand"+roundId).html("-");
            }
        });
    }
}

function collapseLog(roundId) {
    $("#logBox"+roundId).css("display", "none");
    $("#expand"+roundId).html("+");
}

function showLog(roundId) {
    $("#logBox"+roundId).css("display", "block");
    $("#expand"+roundId).html("-");
}