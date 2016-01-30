<?
require_once ('../../lib/inclusion.php');
require_once_model ('BattleRound');

$battleRoundConn = new BattleRoundDAO();
$battleId = $_POST["battleId"];
$roundId = $_POST["roundId"];

$battleRoundArr = $battleRoundConn->getBattleRound($battleId, $roundId);
$attackLog = explode("^_^", $battleRoundArr[3]);
$defendLog = explode("^_^", $battleRoundArr[4]);
?>

<span class="title">Defensor</span>
    <hr />
    <div id="defendLog" class="log">
<?  foreach($defendLog as $attackMsg)
        {
        echo "<p>".$attackMsg."</p>";
        }
?>
    </div>
    <span class="title">Atacante</span>
    <hr />
    <div id="attackLog" class="log" >
<?  foreach($attackLog as $attackMsg)
        {
        echo "<p>".$attackMsg."</p>";
        }
?>
    </div>