
<?php
include ("db.php");

 if (isset($_GET["cods"])){
 	
 	$login = $_GET["cods"];
    $conexao = mysql_connect($serer, $user, $senha) or die("Erro na conexão!");
    mysql_select_db($base);




     
    $sqluser = "select * from usuarios where login = '$login'";

    $resp = mysql_query($sqluser) or die ("erro de consulta");

    $consulta = mysql_fetch_array($resp);
    $codigo = $consulta["codigo"];





    if (empty($codigo)){
    	$sql = "select * from tweets";
    } else {
    	
        $sql = "select texto,retweets.data_hora_postagem,nome FROM retweets,tweets,usuarios
                  WHERE retweets.data_hora_postagem = (SELECT max(retweets.data_hora_postagem)FROM retweets ) and
                 tweets.codigo = retweets.codigo_tweet and
                 retweets.codigo_usuario = usuarios.codigo and usuarios.codigo = $codigo";
    }

    sleep(1);

    $result = mysql_query($sql);
    $cont = mysql_affected_rows($conexao);

  if ($cont > 0) {
      
 $restweet = "<div class=\"panel-body\">";
      while ($linha = mysql_fetch_array($result)) {
  
        $restweet .= "<div class=\"col-lg-8 col-md-8 col-sm-8 \">";
	    $restweet .= "<label for=\"titulo\">Retweet</label><br>";

	    $restweet .= "<label for=\"nome\">Nome</label>";
	    $restweet .= "<input type=\"text\" name=\"nome\" id=\"nome\" readonly";
	    $restweet .= "class=\"form-control\" placeholder=\"nome\" value='".utf8_encode($linha["nome"])."'>";
        
	    $restweet .= "</div>";
	    $restweet .= "<div class=\"col-lg-8 col-md-8 col-sm-8\">";
	    $restweet .= "<textarea name=\"texto\" id=\"texto\" readonly";
	    $restweet .= "class=\"form-control\" placeholder=\"texto\" cols='50'>".utf8_encode($linha["texto"])."</textarea>";
        $restweet .= "<br></br>";
        
       
        $restweet .= "<hr></hr>";


        $restweet .= "<br></br></div>";

	    }

	    echo $restweet .= "</div>";
   } 
                                   
 }




?>