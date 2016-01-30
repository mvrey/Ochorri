var movement_intervals = new Array();
var arrivedMsg = "";
var detailsX = 0;
var detailsY = 0;

function startMovementCounter (movementId) {

    movement_intervals[movementId] = window.setInterval("increaseMovementCounter("+movementId+")", 1000);
}

function stopMovementCounter(movementId) {

    window.clearInterval(movement_intervals[movementId]);
    movement_intervals[movementId] = false;
}

function increaseMovementCounter (movementId) {

    var timeLeft = Number($('#timeLeft'+movementId).html());
    if (timeLeft>0)
        $('#timeLeft'+movementId).html(timeLeft-1)
    else
        {
        if ($('#movementRow'+movementId).html()==undefined)
            stopMovementCounter(movementId);
        else
            {
            stopMovementCounter(movementId);
            $('#movementRow'+movementId).html("");
            arrivedMsg = $("#msg").html();
            show_details('details',detailsX,detailsY);
            window.setTimeout('$("#msg").html(arrivedMsg+($("#msg").html()))', 1000);
            }
        }
}