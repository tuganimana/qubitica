<?php

function orderbook_coinall_com($exchange,$symbol,$symbol_orig,$marketid,$quotecurrency,$basecurrency,$state,$date,$wertinusd,$PWD1)
{
$orderbook=array();

$conn = new mysqli("localhost", "exchus1", "$PWD1", "exchange1");
if ($conn->connect_errno) {echo"error";
return "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

          $basecurrency=strtolower($basecurrency);

          $sql = "select * from basequote where variable='$basecurrency-usd'";
          //echo"$sql ----";
          $result = $conn->query($sql);

          if ($result->num_rows > 0)
          {
             while($row = $result->fetch_assoc())
                {
                $variablenwert=$row["variablenwert"];

                }
          }


$url="https://www.coinall.com/api/spot/v3/instruments/$symbol_orig/book";

$parsestring = file_get_contents($url);
$response=array();
$response=json_decode($parsestring, true);



$response_buy=$response["bids"];
$response_sell=$response["asks"];

$orderbook_buy=array();
$orderbook_sell=array();

if(isset($response_buy))
{
foreach($response_buy as $zeile)
   {

   $price=$zeile[0];
   $price_usd=$price*$variablenwert;
   $volume=$zeile[1];
   $newdata_buy=array('price' => $price, 'price_usd' => $price_usd,'volume' => $volume);
   array_push($orderbook_buy,$newdata_buy);
   }
}

if(isset($response_sell))
{
foreach($response_sell as $zeile)
   {
   $price=$zeile[0];
   $price_usd=$price*$variablenwert;
   $volume=$zeile[1];
   $newdata_sell=array('price' => $price, 'price_usd' => $price_usd, 'volume' => $volume);
   array_push($orderbook_sell,$newdata_sell);
   }
}


if(isset($response_sell))
{
$orderbook_sell = sortArrayByFields(
    $orderbook_sell,
    array(
        'price' => SORT_ASC
         )
    );
}

if(isset($response_buy))
{
$orderbook_buy = sortArrayByFields(
    $orderbook_buy,
    array(
        'price' => SORT_DESC
         )
    );
}

$orderbook=array('buy' => $orderbook_buy, 'sell' => $orderbook_sell);


return $orderbook;
}

?>