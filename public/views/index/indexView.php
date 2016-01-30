<html>

<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>O'chorri</title>
<link rel="styleSheet" href="../../views/index/indexView.css.php" />
<link rel="icon" type="image/png" href="<?=$img_other?>favicon.png" />
</head>

<body class="body_index">
    <center>
    <div id="media">
        <a href="../blog/">Blog de desarrollo</a>
        |
        <a href="../forum/">Foros</a>
    </div>
    <div id="subtitle">
        <h1>ROUND 2!</h1>
    </div>
    <div id="recoverAccount">
        <a href="../view/recoverAccount.php">¿Olvidaste tus datos de acceso?</a>
    </div>
    <div id="login_box">
        Bienvenido/a!<br/>
        Identif&iacute;cate o <a href="../../controllers/register/registerController.php">Reg&iacute;strate</a>
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