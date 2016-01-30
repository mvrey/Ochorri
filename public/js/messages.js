//http://www.datatables.net/release-datatables/examples/basic_init/zero_config.html

function show_messages () {
    stopMapInterval();
    $.ajax({
        url: "../../controllers/messages/messages_request.php",
        type: "POST",
        success: function(data){
            //data = data.split("^_^");
            $("#main_container").html(data);
            setTimeout('setTableProperties()', 1000);
            }
        });
}

function setTableProperties() {
    
    $("#messages_container").tablesorter({
        sortColumn: 'name',			// Integer or String of the name of the column to sort by.
		sortClassAsc: 'headerSortUp',		// Class name for ascending sorting action to header
		sortClassDesc: 'headerSortDown',	// Class name for descending sorting action to header
		headerClass: 'header',			// Class name for headers (th's)
		stripingRowClass: ['even','odd'],	// Class names for striping supplyed as a array.
		stripRowsOnStartUp: true,
                sortList: [[2,1]],
    });
    setZebraTable();
}


function setZebraTable() {

    $(".message_container").css("display","none");
    $("#messages_container tbody tr:nth-child(odd)").removeClass("even");
    $("#messages_container tbody tr:nth-child(odd)").addClass("odd");
    $("#messages_container tbody tr:nth-child(even)").removeClass("odd");
    $("#messages_container tbody tr:nth-child(even)").addClass("even");
}

function sendMessage () {

    var subject = $("#message_subject").val();
    var content = $("#message_content").val();
    var recipient = document.getElementById('message_recipient');
    recipient = recipient.options[recipient.selectedIndex].value;

    $.ajax({
        url: "../../controllers/messages/sendMessage.php",
        type: "POST",
        data: "subject="+subject+"&content="+content+"&recipient="+recipient,
        success: function(data){
            //data = data.split("^_^");
            jAlert(data);
        }
    });
}

function show_selectedMessage (row, msgId, read) {

if (!read)
    {
    $.ajax({
        url: "../../controllers/messages/alterMessage.php",
        type: "POST",
        data: "msgId="+msgId+"&mode=setRead",
        success: function(){
            $("#message_read_icon"+row).css("display", "block");
    }
    });
    }
var visible = ($("#content"+row).css("display") != "none");

if (visible)
    collapseMessage(row);
else
    showMessage(row);
}

function collapseMessage(row) {
    $("#content"+row).css("display", "none");
}

function showMessage(row) {
    var browser = navigator.appName;
    if(browser == "Netscape")
        displayString = "table-row";
    else
        displayString = "block";
    
    $("#content"+row).css("display", displayString);
}

function deleteMessage (row, msgId) {

    jConfirm("¿Seguro que quieres borrar este mensaje?", 'Borrar mensaje', function(r) {
        if (r)
            {
            $.ajax({
            url: "../../controllers/messages/alterMessage.php",
            type: "POST",
            data: "msgId="+msgId+"&mode=delete",
            success: function() {
                $("#message_row"+row).css("display", "none");
                $("#content"+row).css("display", "none");
                }
    });
            }
    
    });
}