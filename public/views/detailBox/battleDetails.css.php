.unitRow {
    height: 50px;
}
.armyColumn img {
    float: left;
    clear: both;
    width: 50px;
    height: 50px;
}
#logBox {
    clear:both;
    border: 1px solid gray;
    width: 90%;
    margin: 0 0 10px 5%;
    height: 200px;
    overflow:auto;
    text-align: left;
}
#logBox hr {
    width: 35%;
    border: 1px solid gray;
    margin-top: 3px;
}
#logBox .title {
    font-size: 12px;
    font-weight: bold;
}
.log {
    font-size: 12px;
    text-align: left;
    margin-left: 20px;
}
.log p {
    margin: 3px 0 3px 0;
}
#attackLog > p span:last-child {
    color: <?=$colors[1]?>;
}
#defendLog > p span:last-child {
    color: <?=$colors[0]?>;
}
#defendLog > p span:first-child {
    color: <?=$colors[1]?>;
}
#attackLog > p span:first-child {
    color: <?=$colors[0]?>;
}
.roundTitle {
    text-align: left;
}
.expand {
    cursor: pointer;
    margin: 10px;
    width: 100px;
    border: 1px solid black;
    padding: 0 5px 0 5px;
}
.logBox {
    text-align: center;
}
#roundTimeLeft {
}
#battleContainer {
    clear: both;
    margin: 30px auto 30px auto;
}