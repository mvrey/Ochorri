<? require_once ("../../config/paths.php"); ?>

#subtitle {
    background-color: gray;
    color: white;
    opacity: 0.8
}

.body_index{
    width: 1280px;
    height: 768px;
    background: url('<?=$img_background?>index.jpg') no-repeat scroll 50% 0px transparent;
    margin-left: auto;
    margin-right: auto;
}

.body_index #register_box {
    margin: 50px 0px 0px 100px;
}

.body_index h1 {
    margin:50px 0px 0px 0px;
}

#login_box {
    margin-top: 80px;
    font-family: "Verdana";
    font-size: 15px;
    vertical-align: middle;
    width: 400px;
}

#media {
    width: 100%;
    height: 30px;
    background-color: black;
    opacity: 0.7;
    vertical-align: top;
    position: fixed;
    top: 0;
    left:0;
}

#media a {
    float:left;
    margin: 5px 10px 5px 20px;
    color: white;
    font-weight: bold;
    text-decoration: none;
}

#media a:hover {
    color: blue;
}

#login_form {
    width: 300px;
    height: 130px;
    color: black;
}

#register_form {
    background-color: black;
    color: white;
    height: auto;
    opacity: 0.7;
    padding: 15px;
    width: 570px;
}

#register_form input, #login_form input {
    float: right;
    margin-bottom: 5px;
}

#register_form span, #login_form span {
    font-weight: bold;
    text-align: left;
    line-height: 2;
}

#register_form #continuar, #login_form #continuar {
    float: none;
    margin-left: 50%;
    margin-right: 50%;
}

#recoverAccount {
    font-family: verdana;
    font-size: 14px;
}

.default_image {
    width:50px;
    height:50px;
    display: inline-block;
}

#default_avatars_container {
    float: right;
    height: 75px;
}

.registerField {
    clear:both;
}