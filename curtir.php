<?php

$coduser = $_POST["codigo_usuario"];
$codtweet = $_POST["codigo_tweet"];

if (isset($coduser) and isset($codtweet)){

    include ("db.php");   

    $conexao = mysql_connect($serer, $user, $senha) or die("Erro na conexão!");
    mysql_select_db($base);

    $sql = "INSERT INTO favoritos(codigo_tweet, codigo_usuario) VALUES ('$codtweet','$coduser')";

    
    
    $result = mysql_query($sql);

    header("Location: ./");


}



?>