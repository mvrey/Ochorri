function show_ranking () {
    stopMapInterval();
    $.ajax({
        url: "../../controllers/ranking/ranking_request.php",
        type: "POST",
        success: function(data){
            $("#main_container").html(data);
        }
    });
}