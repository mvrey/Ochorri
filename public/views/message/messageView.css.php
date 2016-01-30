<? require_once ("../../config/paths.php"); ?>

root {
    display: block;
}

#sendMessage_container, #messages_container {
    border: 1px solid gray;
    padding: 8px;
    margin: 5px;
}

#messages_container thead {
    font-weight: bold;
    text-align: center;
}

#messages_container {
    width: 800px;
}

#messages_container img {
    text-align: center;
}

#messages_container thead .sorter{
    cursor: pointer;
    background-image: url("<?=$img_buttons?>sort.gif");
    background-position: right;
    background-repeat: no-repeat;
}

#messages_container tbody tr:hover {
    background-color: aqua;
    cursor: pointer;
}

.odd {
    background-color: gray;
}

.message_container {
    font-size: 13px;
    display: none;
    background-color: #CDCDCD;
}

.message_buttons img {
    width: 20px;
    height: 20px;
}

th.headerSortUp {
    background-image: url(<?=$img_buttons?>asc.gif);
    background-color: #3399FF;
}

th.headerSortDown {
    background-image: url(<?=$img_buttons?>desc.gif);
    background-color: #3399FF;
}