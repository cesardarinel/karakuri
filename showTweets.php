<meta charset="utf-8">
<?php /*

ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');
//Set access tokens here - see: https://dev.twitter.com/apps/ 
$settings = array(
    'oauth_access_token' => "",
    'oauth_access_token_secret' => "",
    'consumer_key' => "",
    'consumer_secret' => ""
);
/*
//URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ 

$url = 'https://api.twitter.com/1.1/blocks/create.json';
$requestMethod = 'POST';
 //POST fields required by the URL above. See relevant docs as above 
$postfields = array(
    'screen_name' => 'usernameToBlock', 
    'skip_status' => '1'
);
// Perform a POST request and echo the response 
$twitter = new TwitterAPIExchange($settings);
echo $twitter->buildOauth($url, $requestMethod)
             ->setPostfields($postfields)
             ->performRequest();
*/
// Perform a GET request and echo the response 
// Note: Set the GET field BEFORE calling buildOauth(); */

class Twitter{

    function getTweets($user){
        ini_set('display_errors', 1);
        require_once('TwitterAPIExchange.php');

        $settings = array(
            'oauth_access_token' => "188182409-42GMHRGMih8CUbgVsa1xXfHqjMSTFfrFLdE4w6i6",
            'oauth_access_token_secret' => "BxzFOm0Rez2cagjjaMJJGg6V32ULAsja5Kp4xhx4Xg",
            'consumer_key' => "JQ1SGc92mullUIYDoQC0Gg",
            'consumer_secret' => "JQ1SGc92mullUIYDoQC0Gg"
        );

        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?screen_name='.$user.'&count=100';        
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $json =  $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
        return $json;

    }

    function getArrayTweets($jsonraw){
        $rawdata = "";
        $json = json_decode($jsonraw);
        $num_items = count($json);
        for($i=0; $i<$num_items; $i++){
            $user = json_decode($json[$i], true);
            $fecha = $user->created_at;
            $url_imagen = $user->user->profile_image_url;
            $screen_name = $user->user->screen_name;
            $tweet = $user->text;

            $imagen = "<a href='https://twitter.com/".$screen_name."' target=_blank><img src=".$url_imagen."></img></a>";
            $name = "<a href='https://twitter.com/".$screen_name."' target=_blank>@".$screen_name."</a>";

            $rawdata[$i][0]=$fecha;
            $rawdata[$i]["FECHA"]=$fecha;
            $rawdata[$i][1]=$imagen;
            $rawdata[$i]["imagen"]=$imagen;
            $rawdata[$i][2]=$name;
            $rawdata[$i]["screen_name"]=$name;
            $rawdata[$i][3]=$tweet;
            $rawdata[$i]["tweet"]=$tweet;
        }
        return $rawdata;
    }

    function displayTable($rawdata){

        //DIBUJAMOS LA TABLA
        echo '<table border=1>';
        $columnas = count($rawdata[0])/2;
        //echo $columnas;
        $filas = count($rawdata);
        //echo "<br>".$filas."<br>";
        //Añadimos los titulos

        for($i=1;$i<count($rawdata[0]);$i=$i+2){
            next($rawdata[0]);
            echo "<th><b>".key($rawdata[0])."</b></th>";
            next($rawdata[0]);
        }
        for($i=0;$i<$filas;$i++){
            echo "<tr>";
            for($j=0;$j<$columnas;$j++){
                echo "<td>".$rawdata[$i][$j]."</td>";

            }
            echo "</tr>";
        }       
        echo '</table>';
    }
}

$twitterObject = new Twitter();
$jsonraw =  $twitterObject->getTweets("alex_esquiva");
$rawdata =  $twitterObject->getArrayTweets($jsonraw);
$twitterObject->displayTable($rawdata);

?>