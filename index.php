<?php
require '../simple_html_dom.php';
$csv = 'products.csv';
$mainUrl = 'https://products.z-wavealliance.org/regions/2/categories';
$mainHtml = file_get_html($mainUrl, false);
$rootUrl = 'https://products.z-wavealliance.org';
$listContainer = $mainHtml->find('.listItemContainer');
$categories = array();
$data[] = implode('","', array(
    'Main Category',
    'Product Category',
    'Brand',
    'Product',
    'Device Version',
    'Description',
    'Is S2 Security'
));
for($x = 0; $x < count($listContainer); $x++){
  $catLinks = $listContainer[$x]->find('a', 0)->plaintext;
  $categories[] = array('category' => $catLinks, 'url' => $rootUrl.$listContainer[$x]->find('a', 0)->getAttribute('href'));
}
for($x = 0; $x < count($categories); $x++){
  $pageCount = 1;
  $url = $categories[$x]['url'].'?page='.$pageCount;
  $category = $categories[$x]['category'];
  // pagination
  $html = file_get_html($url, false);
  $page = $html->find('.page');
  $pageCount = $page[count($page) - 1]->plaintext;
  for($z = 1; $z <= $pageCount; $z++){
    $html = file_get_html($url, false);
    $productListContainer = $html->find('#productList', 0);
    $listing = $productListContainer->find('.productListing');
    for($i = 0; $i < count($listing); $i++){
      $productTitle = $listing[$i]->find('.productTitle', 0)->plaintext;
      $infos = $listing[$i]->find('.productListingText');
      $brand = $infos[0]->plaintext;
      $product = $infos[1]->plaintext;
      $deviceVersion = $infos[2]->plaintext;
      $description = $infos[3]->plaintext;

      // look if s2
      if($listing[$i]->find('.S2Picture', 0) != null){
        $isS2 = 'true';
      }else{
        $isS2 = 'false';
      }
      echo $productTitle .'<br>';
      echo $brand .'<br>';
      echo $product .'<br>';
      echo $deviceVersion .'<br>';
      echo $description .'<br>';
      echo $isS2 .'<br>';

      $data[] = implode('","', array(
        $category,
        $productTitle,
        $brand,
        $product,
        $deviceVersion,
        $description,
        $isS2
      ));
    }
  }
}

$file = fopen($csv,"a");
foreach ($data as $line){
    fputcsv($file, explode('","',$line));
}
fclose($file);
?>
