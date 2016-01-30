<?php
$dir = opendir("../../img/avatars/default/");

$nick="";
$pass1="";
$pass2="";
$errorMsg = "";

if (isset($_POST['continuar']))
    {
    require_once ('../../lib/inclusion.php');
    require_once ('../../config/paths.php');
    require_once_model ('Player');
    require_once_model ('Sector');
    $playerConn = new PlayerDAO();
    $sectorConn = new SectorDAO();

    $continuar=$_POST['continuar'];
    @$nick=$_POST['nick'];
    @$pass1=$_POST['pass1'];
    @$pass2=$_POST['pass2'];
    @$email=$_POST['email'];
    @$civName=$_POST['civName'];
    @$avatar = $_FILES["avatar"];
    @$flag = $_FILES["flag"];
    $images_ok = false;

    if ((isset($_FILES["avatar_file"])) && (isset($_FILES["flag"])))
        {
        $type_ok = (((strpos($_FILES["avatar_file"]["type"], "gif")) || (strpos($_FILES["avatar_file"]["type"], "png")) || (strpos($_FILES["avatar_file"]["type"], "jpeg")))
                    && ((strpos($_FILES["flag"]["type"], "gif")) || (strpos($_FILES["flag"]["type"], "png")) || (strpos($_FILES["flag"]["type"], "jpeg"))));
        $size_ok = (($_FILES["avatar_file"]["size"] < 100000) && ($_FILES["flag"]["size"] < 100000));
        
        if (!(($type_ok) && ($size_ok)))
            {
            $errorMsg .= "* La extensión o el tamaño de los archivos no es correcta.
                    <ul>
                        <li>Se permiten archivos .png, .gif o .jpg</li>
                        <li>se permiten archivos de 100 Kb máximo.</li>
                    </ul>";
            }
        else
            {
            move_uploaded_file($_FILES['avatar_file']['tmp_name'], $img_avatars.$nick."_avatar.png");
            move_uploaded_file($_FILES['flag']['tmp_name'], $img_flags.$nick."_flag.png");
            $images_ok = true;
            }
        }
    //var_dump($_FILES);
    //Comprobamos si el nombre existe y enviamos mensaje de error si procede

    $existente= $playerConn->getNickExists($nick);
    
    $disponible = !(($existente) or ($nick==""));

    if (!$disponible)
        $errorMsg .= "<span>* El nombre no est&aacute disponible</span><br />";

    if (strcmp($pass1, $pass2)!=0)
        $errorMsg .= "<span>* Las contraseñas son diferentes</span>";

    if (($images_ok) && ($disponible) and (strcmp($pass1, $pass2)==0) and ($pass1!=''))
        {
        $playerConn->InsertNewPlayer($nick, $pass1, $email, $nick."_flag.png", $nick."_avatar.png", $civName);
        $playerArr = $playerConn->getPlayerByNick($nick);
        $playerId = $playerArr[0];

        require ("../../controllers/register/set_initial_sector.php");
        while (!$inserted)
            {
            require ("../../controllers/register/seed_map_generator.php");
            require ("../../controllers/register/set_initial_sector.php");
            }
        $playerConn->setLastMapView ($playerId, $startCoordinates, 5);
        header ("Location: ../../views/register/registered.php?player=".$nick);
        }
    }

require ("../../views/register/registerView.php");
?>