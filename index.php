<?php
/*
 * Cesar Darinel Ortiz Twitter karakuri
 * 
 * */
$consumerKey    = 'kFS1SxfBjyC4TEjnTodbA';
$consumerSecret = '1QKNbLAb1JjMMs3Ulw9BKoLVmVbG2OSgySZ6ewnFo';
$oAuthToken     = '458560800-MFGwCNNwniOm8BaRjSd60QTRLGI51oWxKDj3w28m';
$oAuthSecret    = 'HAJtzFf3h2e7oTccsWKUPigh5eJp5aTHDEpdsXuCQ';
# API OAuth
require_once('twitteroauth.php');

$Tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);

# your code to retrieve data goes here, you can fetch your data from a rss feed or database

$Tweet->post('statuses/update', array('status' => 'here the content of kjaskjdkasdkaksjdkajsdasd your tweet, you can add hashtags or links'));
?>
