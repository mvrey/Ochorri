<html>
<head>
<meta http-equiv='Content-Language' content='es'>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<title>O'Chorri - Registro</title>
<link rel="styleSheet" href="../../views/register/registerView.css.php" />
<link rel="icon" type="image/png" href="<?=$img_other?>favicon.png" />
</head>

<body class="body_index">

<div id="media">
    <a href="../blog/">Blog de desarrollo</a>
    |
    <a href="../forum/">Foros</a>
</div>

<h1 align="center">Registro</h1>
<hr /><br />
<!--
<div style="background-color: gray; color: white;">
    <h2>AVISO IMPORTANTE</h2>
    Llevo toda la maldita tarde con el bug de la actualización automática y no he conseguido hacero funcionar aun.<br/>
    Pretendía tenerlo arreglado para ahora pero me ha dado muchos más problemas de los que esperaba, aun así se ha marcado como prioridad absoluta.<br/>
    Para los que sea vuestra primera vez, esto significa que el movimiento de tropas, construcción de edificios, entrenamiento de unidades, investigaciones y batallas
    no se actualizan hasta que alguien las mira (pincha en el botón de 'ver' correspondiente).
</div>
-->
<div id="errorLog"><?=$errorMsg?></div>

<div id="register_box">
    <form action="../../controllers/register/registerController.php" id="register_form" method="post" enctype="multipart/form-data">

        <span>Nombre del usuario: </span>
        <input type='text' id='nick' name='nick' value="<?=$nick?>"><br />

    <?
    if (isset($disponible))
        {
        if ($disponible)
            { ?>
            <div class="registerField">
                <span>Contraseña: </span>
                <input type='password' id='pass1' name='pass1' />
            </div>
            <div class="registerField">
                <span>Repita la contraseña: </span>
                <input type='password' id='pass2' name='pass2' />
            </div>

            <div class="registerField">
                <span>Dirección de correo electrónico:</span>
                <input type="text" id="email" name="email" />
            </div>

            <div class="registerField">
                <span>Nombre de tu pueblo:</span>
                <input type="text" id="civName" name="civName" />
            </div>

            <div class="registerField">
                <span>Avatar:</span>

                <div id="default_avatars_container">
                    <? // loop through the array of avatars and print them all
                    $i=0;
                    while($fileName = readdir($dir)) {
                        if (substr($fileName, 0, 1) != "."){ ?>
                            <div class="default_image">
                                <img class="default_image" src="<?='../../img/avatars/default/'.$fileName?>"/>
                                <input type="radio" name="avatar" value="default_a_<?=$i?>">
                            </div>
                    <? $i++; }
                    } ?>
                </div>

                <input type="file" id="avatar" name="avatar_file" />
                <input type="radio" name="avatar" value="custom_a" style="">
            </div>

            <div class="registerField">
                <span>Bandera:</span>
                <input type="file" id="flag" name="flag" />
            </div>
            <?
            }
        }
        echo "<br/><br/><input type='submit' value='continuar' id='continuar' name='continuar'>";

    ?>
</div>
</body>

</html>
<script type="text/javascript" language="javascript">
    document.getElementById("nick").focus();
</script>