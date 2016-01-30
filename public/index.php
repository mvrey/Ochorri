<?php
function using_ie()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $ub = False;
    if(preg_match('/MSIE/i',$u_agent))
    {
        $ub = True;
    }

    return $ub;
}

function ie_box() {
    if (using_ie()) {
        ?>
        <div class="iebox">
            Ni lo intentes.
            <img src="img/avatars/trollxplorer.jpg" alt="Tu navegador te trollea" />
            <a href="http://www.mozilla-europe.org/es/">Descarga un navegador decente</a>
        </div>
        <?php
    return;
    }
}
?>
<? if (using_ie())
        ie_box();
    else
        header ('Location: controllers/index/indexController.php'); ?>
