<?php
include('simple_html_dom.php');


$product = array();

$html = file_get_html('https://dev-test.hudsonstaging.co.uk/');

foreach ($html->find('.product-tile') as $postDiv) {

  $string = trim($postDiv->find('.details')[0]->innertext);
  // Extracting Paragraphs
  preg_match_all('/<p>(.*?)<\/p>/i', $string, $paragraphs);

  $comma_separated_quantity = implode($paragraphs[1]);
  //Extract the numbers using the preg_match_all function.
  preg_match_all('!\d+!', $comma_separated_quantity, $matches);

  // Extract Names and set in variable
  $name = trim($postDiv->find('.product-name')[0]->innertext);

  // Find all images
  foreach ($postDiv->find('img') as $element) {
    $src = $element->src;
  }

  // Sets the Quantity
  $quantity = implode(',', array_map(function ($el) {
    return $el[0];
  }, $matches));
  $intQuantity = intval($quantity);

  // Sets the Price
  $price = implode(',', array_map(function ($el) {
    return $el[1];
  }, $matches));
  $intPrice = intval($price);

  $varArray = array(
    "product" => $name,
    "metadata" => array(
      "image_url" => $src,
      "quantity" => $intQuantity,
      "price" => $intPrice,
    )
  );
  array_push($product, $varArray);
}

$json = stripslashes(json_encode($product, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES));

// Write to File
if (file_put_contents("data.json", $json))
  echo "JSON file created successfully...";
else
  echo "Oops! Error creating json file...";
