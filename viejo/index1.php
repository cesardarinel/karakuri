<?php
/*
 *								 Cesar Darinel Ortiz Twitter karakuri
 * 
 * */
 /*
  * 
  *1- imagenes...---
  * 2-Mencion de otros usuarios amigos ??---
  * 3- etiquetas o hashtags palabras claves... --palabras sociales 
  * 4-(circulo social.....  )
  * 
  * 
  * 
  */
include('Config.php');
require_once('twitteroauth.php');

#creamos un objeto twitter conectado con nuestro twitter 
$Tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);

#conexion a base de datos

$con=mysql_connect($mysql_host,$mysql_user,$mysql_password)or die ('problema conectando porque :' . mysql_error());
mysql_select_db($mysql_db_Name,$con);
#Query......

$status = $Tweet->get('direct_messages');

/*Gives you the complete array */
$Resul1 = mysql_query("SELECT * FROM  Contador ",$con)or die ('problema conectando porque :' . mysql_error());
while($row = mysql_fetch_array($Resul1)) {
$Contador =$row["Numero"];
}

$Resul = mysql_query("SELECT * FROM mensajes WHERE Numero_mensaje =".$Contador." ",$con)or die ('problema conectando porque :' . mysql_error());
#entramos dentro de los datos optenidos...
/*
 * Comentarios constantes sacado de la base de datos...
 * */

while($row = mysql_fetch_array($Resul)) {

#$Tweet->post('statuses/update', array('status' =>''.$row["Mensaje_txt"].''));
}

/*Control del contador*/
if($Contador>=331)
{
	mysql_query("UPDATE Contador SET Numero='0' ",$con)or die ('problema conectando porque :' . mysql_error());
}
else
{
	$Contador+=1;
	mysql_query("UPDATE Contador SET Numero='.$Contador.' ",$con)or die ('problema conectando porque :' . mysql_error());
}
/*
 * Auto seguimiento
 * Auto follow back a tus seguidores. Crea comunidad siguiendo a tus seguidores automáticamente.
 * Auto Unfollow opcional. Deja de seguir a los que no te siguen.
 * */
function auto_Seguimiento($Tweet, $validar)
{
   
	

    $Seguidores = $Tweet->get('followers/ids', array('cursor' => -1));
    $Amigos = $Tweet->get('friends/ids', array('cursor' => -1));
	
	
    foreach ($Seguidores->ids as $i=>$id)
    {
        if (empty($Amigos->ids) or !in_array($id, $Amigos->ids)) 
        {
			MensajeDirectos($Tweet,$id,"Que detalle que nos sigas,  Saludos y estamos en contacto.");
            $ret = $Tweet->post('friendships/create', array('user_id' => $id));
        }
		if ($i == 99) 
			break; 
    }
    
    if($validar)
    {
		foreach ($Amigos->ids as $i=>$id)
		{
			if (!in_array($id, $Seguidores->ids)) 
			{
				$ret = $Tweet->post('friendships/destroy', array('user_id' => $id));
			}
			if ($i == 99) 
				break; 
		}
	}
}
 
#auto_Seguimiento($Tweet,true);


/*
 
 * Follow Search. Busca nuevos contactos a los que seguir a partir de una palabra clave. Ej. Agencia publicidad, estudio de diseño, e-marketing.
 
 *    */
function Auto_Buscar_Seguidores($Tweet,$FraseBuscar)
{
		function Buscar($Tweet, $query)
		{
		  print_r($Tweet->get('search/tweets', $query));
		  return $Tweet->get('search/tweets', $query);
		}
		 
		function Seguir($Tweet,$id)
		{
		  $Tweet->post('friendships/create', array('user_id' => $id));
		}
		 
		$max_id = "";
		foreach (range(1, 2) as $i) 
		{
		  $query = array(
			"q" => $FraseBuscar,
			"count" => 10,
			"result_type" => "popular",
			"lang" => "es",
			"max_id" => $max_id,
		  );
		 // print_r($query);
		  $results = Buscar($Tweet,$query);
		  
		  foreach ($results->statuses as $result) 
		  {
			Seguir($Tweet,$result->user->id);
			$max_id = $result->id_str;
		  }
		}
}
#Auto_Buscar_Seguidores($Tweet,"Amor");
/*
 * 
 * Direct Message. Mensaje directo automático a tus nuevos followers.
 * */
function MensajeDirectos($Tweet,$Id,$Mensaje)
{
	$uno=$Tweet->post('direct_messages/new', array('user_id' => $Id,'text'=>$Mensaje));
	
}
function PublicarImagenes($Tweet)
{

	$url = "http://www.taringa.net/posts/imagenes/15125263/Rosas---Imagenes-HD---Amor.html";

	$html = file_get_contents($url);

	preg_match_all("/<img[\s]+[^>]*?src[\s]?=[\s\"\']+(.*\.([gif|jpg|png|bmp|jpeg|tiff]{3,4}))[\"\']+.*?>/", $html, $images);

	$images = $images[1];
	$list = array();

	foreach($images as $img) 
	{
		print_r($img);
		$URLADF= file_get_contents("http://api.adf.ly/api.php?key=b0a855a2791fbcf28aceb1ee281a07b8&uid=1223322&advert_type=int&domain=adf.ly&url=".$img."");
		#$Tweet->post('statuses/update', array('status' =>''.$URLADF.''));
	}
}
#PublicarImagenes($Tweet);
function Preguntas($Tweet)
{		$Valor=rand(1, 5);
		$Amigos=$Tweet->get('friends/list',array('cursor' => -1));
		
		$cont=1;
		foreach ($Amigos->users as $i=>$id)
		{
			
			$nombre=$id->screen_name;
			//print_r($nombre);
			if($cont==$Valor)
			{
				$uno=$Tweet->post('statuses/update', array('status' =>'¿Crees que el ser #humano es un ser #estúpido o #inteligente? @'.$nombre.''));
				print_r($uno);
				break;
			}//print_r($ret);
			$cont++;
			
		}
}
Preguntas($Tweet);
?>
