
/* Função para criar o objeto XMLHTTPRequest  */
function CriaRequest(){
	try {   
       request = new XMLHttpRequest();
	} catch (IEAtual){
	   try {
	      request = new ActiveXObject("Msxml2.XMLHTTP");
	   } catch (IEAntigo) {
	       try {
	           request = new ActiveXObject("Microsoft.XMLHTTP");
	       } catch (falha){
	          request = false;
	       }
	   }
	}

    if (!request)
    	alert("Seu navegador não suporta Ajax!");
    else
    	return request;

}


/* Função para enviar os dados */

function getDados() {

	var cod_seguidores = document.getElementById("cod_seguidores").value;

	var timeline = document.getElementById("timeline");
	var xmlreq = CriaRequest();

    //exibi a imagem de progresso
	timeline.innerHTML = '<img src="progresso.gif"/>';

    //inicia uma requisição
    xmlreq.open("GET","timeline.php?cods=" + cod_seguidores, true);

    xmlreq.onreadystatechange = function(){
    	if (xmlreq.readyState == 4){
    		if (xmlreq.status == 200) {

    			timeline.innerHTML = xmlreq.responseText;
    		} else {
    			timeline.innerHTML = "Erro: " + xmlreq.statusText + "  " + xmlreq.status;

    		}
    	}
    };

    xmlreq.send(null);
}



/* Função para enviar os dados */

function getDadosRetweet() {

    var cod_seguidores = document.getElementById("cod_seguidores").value;

    var retweet = document.getElementById("timelineretweet");
    var xmlreq = CriaRequest();

    
    //inicia uma requisição
    xmlreq.open("GET","timelineretweet.php?cods=" + cod_seguidores, true);

    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4){
            if (xmlreq.status == 200) {

                retweet.innerHTML = xmlreq.responseText;
            } else {
                retweet.innerHTML = "Erro: " + xmlreq.statusText + "  " + xmlreq.status;

            }
        }
    };

    xmlreq.send(null);
}