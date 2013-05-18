
<?php
/*
 * Cesar Darinel Ortiz Twitter karakuri
 * 
 * */
include('Config.php');


require_once('twitteroauth.php');
#creamos un objeto twitter conectado con nuestro twitter 
$Tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);
#conexion a base de datos
$con=mysql_connect($mysql_host,$mysql_user,$mysql_password)or die ('problema conectando porque :' . mysql_error());
mysql_select_db("twitter",$con);
#Query......

$status = $Tweet->get('direct_messages');

/*Gives you the complete array */
$Resul1 = mysql_query("SELECT * FROM  Contador ",$con)or die ('problema conectando porque :' . mysql_error());
while($row = mysql_fetch_array($Resul1)) {
$Contador =$row["Numero"];
}
$Resul = mysql_query("SELECT * FROM mensajes WHERE Numero_mensaje =".$Contador." ",$con)or die ('problema conectando porque :' . mysql_error());
#entramos dentro de los datos optenidos...
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
			//echo $arr[$i]."<br>";
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
 
auto_Seguimiento($Tweet,true);


/*
 
 * Follow Search. Busca nuevos contactos a los que seguir a partir de una palabra clave. Ej. Agencia publicidad, estudio de diseño, e-marketing.
 
 *    */
function Auto_Buscar_Seguidores($Tweet,$FraseBuscar)
{
		function search($Tweet, $query)
		{
		  print_r($Tweet->get('search/tweets', $query));
		  return $Tweet->get('search/tweets', $query);
		}
		 
		function follow($Tweet,$id)
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
		  $results = search($Tweet,$query);
		  
		  foreach ($results->statuses as $result) 
		  {
			follow($Tweet,$result->user->id);
			$max_id = $result->id_str;
		  }
		}
}
#Auto_Buscar_Seguidores($Tweet,"Amor")
?>
