<?php

class Player{
  private $name;
  private $rating;
  private $rank;
  private $rankedGamesWon;

  public function __construct($pName, $pRating)
  {
    $this->name = $pName;
    $this->rating = $pRating;
    $this->rank = "";
    $this->rankedGamesWon = 0;
  }

  public function getName(){
    return $this->name;
  }

  public function setName($pName){
    $this->name = $pName;
  }

  public function getRating(){
    return $this->rating;
  }

  public function setRating($pRating){
    $this->rating = $pRating;
  }

  public function getRank(){
    return $this->rank;
  }

  public function setRank($pRank){
    $this->rank = $pRank;
  }

  public function getRankedGamesWon(){
    return $this->rankedGamesWon;
  }

  public function setRankedGamesWon($pRankedGamesWon){
    $this->rankedGamesWon = $pRankedGamesWon;
  }
}

function getRanks(){
  $ranks = array();
  $ranksUrl = "http://a.scrollsguide.com/ranks";
  $json = json_decode(file_get_contents($ranksUrl), true);

  // due to rate limit
  sleep(1);

  foreach($json["data"] as $rank){
    $ranks[$rank["id"]] = $rank["name"];
  }

  return $ranks;
}

function getTopPlayers(){
  $players = array();


  for($i = 0; $i < 3000; $i = $i + 500){
    $rankingUrl = "http://a.scrollsguide.com/ranking?start=" . $i . "&limit=500";

    $json = json_decode(file_get_contents($rankingUrl), true);

    // due to rate limit
    sleep(1);

    foreach($json["data"] as $player){
      $players[] = new Player($player["name"], $player["rating"]);
    }
  }

  return $players;
}

echo "start: " . date() . "<br>";

// start
$topPlayers = getTopPlayers();
$ranks = getRanks();
$outputObj = array();

foreach($topPlayers as $player){
  $playerInfoUrl = "http://a.scrollsguide.com/player?name=" . $player->getName() . "&fields=name,rankedwon,rank";
  $json = json_decode(file_get_contents($playerInfoUrl), true);

  // due to rate limit
  sleep(1);

  $playerInfo = $json["data"];

  $player->setRankedGamesWon($playerInfo["rankedwon"]);
  $player->setRank($ranks[$playerInfo["rank"]]);
  $outputObj["data"][] = array("name" => $player->getName(),
                                "rating" => $player->getRating(),
                                "rank" => $player->getRank(),
                                "rankedGamesWon" => $player->getRankedGamesWon());
}

file_put_contents("test.json", json_encode($outputObj));

echo "end: " . date();