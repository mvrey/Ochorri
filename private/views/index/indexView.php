<html>

<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>O'chorri - Administración</title>
<link rel="icon" type="image/png" href="<?=$img_other?>favicon.png" />
</head>

<body class="body_index" style="margin-top:150px;">
    <center>

    <div id="login_box">
        Introduce tus datos para acceder al panel de administración
        <br>
        <form id="login_form" action="../../controllers/index/indexController.php" method="post">
            <p>Usuario: <input type="text" id="nick" name="nick"></p>
            <p>Contrase&ntilde;a: <input type="password" id="pass" name="pass" /></p>
            <p><input type="submit" value="continuar" id="continuar" name="continuar" /></p>
         </form>
    </div>
    </center>
</body>

</html>
<script type="text/javascript" language="javascript">
    document.getElementById("nick").focus();
</script>