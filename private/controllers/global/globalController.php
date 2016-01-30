<?
session_start();
if (!( (isset($_SESSION['admin']) && $_SESSION['admin']) ))
    header("Location: ../../controllers/index/indexController.php");
?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8">
        <script language="javascript" type="text/javascript" src="../../../public/js/jquery.js"></script>
        <script language="javascript" type="text/javascript">
            function restartGame() {
                var confirmed = confirm('Esto borrará todos los datos no estáticos. ¿Estás segur@ de querer reiniciar la partida?');
                if (confirmed)
                    {
                    $("#logBox").html("Reiniciando partida ...");
                    $.ajax({
                        url: "../../controllers/global/start_new_game.php",
                        type: "POST",
                        success: function(data){
                            $("#logBox").html(data);
                            }
                        });
                    }
            }
        </script>
    </head>
    <body>
        <input type="button" name="restart_game" value="Reiniciar partida" onclick="restartGame();" />
        <br/>
        <a href="../../controllers/global/logout.php">Desconectarse</a>
        <div id="logBox" style="width:600px; height: 150px; border: 1px solid black;" />
    </body>
</html>