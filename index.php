<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>横浜市の石造物</title>
  <link rel="stylesheet" href="stone.css" type="text/css">
</head>
<body>
<h2>Wikidata と Wikimedia Commons による横浜市の石造物</h2>
<table border="1">
<?php
  $query = 
    'SELECT
       ?item
       (GROUP_CONCAT(DISTINCT ?bunruiLabel; SEPARATOR="<br/>") AS ?type)
       (GROUP_CONCAT(DISTINCT ?keijoLabel; SEPARATOR="<br/>") AS ?shape)
       (GROUP_CONCAT(DISTINCT ?pic; SEPARATOR=", ") AS ?image)
       (GROUP_CONCAT(DISTINCT ?addr; SEPARATOR="<br/>") AS ?address)
       (GROUP_CONCAT(DISTINCT ?zoLabel; SEPARATOR="<br/>") AS ?kokuzo)
       (GROUP_CONCAT(DISTINCT ?mei; SEPARATOR="<br/>") AS ?meibun)
       (GROUP_CONCAT(DISTINCT YEAR(?time); SEPARATOR=", ") AS ?year)
     WHERE {
       ?item wdt:P31 ?bunrui;
             wdt:P1419 ?keijo;
             wdt:P131 ?ward;
             wdt:P6375 ?addr.
       ?keijo (wdt:P279+) wd:Q97613936.
       ?ward wdt:P131 wd:Q38283.
       OPTIONAL { ?item wdt:P1684 ?mei. }
       OPTIONAL { ?item wdt:P180 ?zo. }
       OPTIONAL { ?item wdt:P4896 ?pic. }
       OPTIONAL { ?item wdt:P18 ?pic. }
       OPTIONAL { ?item wdt:P571 ?time. }
       SERVICE wikibase:label {
         bd:serviceParam wikibase:language "ja".
         ?bunrui rdfs:label ?bunruiLabel.
         ?keijo rdfs:label ?keijoLabel.
         ?zo rdfs:label ?zoLabel.
       }
     }
     GROUP BY ?item
     ORDER BY ?year';

  $wikidata = 'https://query.wikidata.org/sparql';
  $param = array(
    'query' => $query,
    'format' => 'json'	
  );
  $curl=curl_init($wikidata);
  curl_setopt($curl, CURLOPT_POST, TRUE);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));
  curl_setopt($curl, CURLOPT_USERAGENT, 'stone.php/0.1');
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
  $json = curl_exec($curl);
  $array = json_decode($json, true);
  $items = $array['results']['bindings'];
  echo '<tr><th>画像</th><th>種別</th><th>形状</th><th>刻像</th><th>銘文</th><th class="nowrap">造立年</th><th>所在地</th></tr>';
  foreach($items as $item) {
    echo '<tr>';
    $img = str_replace('http:', 'https:', $item['image']['value']);
    $link = str_replace('Special:FilePath/', 'File:', $img);
    if(substr($img, -4) == '.stl') {
      echo '<td><a href="'.$link.'" target="_blank"><img src="'.$img.'?width=320" class="thumbnail"></a></td>';
    } else {
      echo '<td><a href="'.$link.'" target="_blank"><img src="'.$img.'?width=320" class="thumbnail2"></a></td>';
    }
    echo '<td class="nowrap">'.$item['type']['value'].'</td>';
    echo '<td class="nowrap">'.$item['shape']['value'].'</td>';
    echo '<td class="nowrap">'.$item['kokuzo']['value'].'</td>';
    echo '<td>'.$item['meibun']['value'].'</td>';
    echo '<td class="nowrap">'.$item['year']['value'].'</td>';
    echo '<td>'.$item['address']['value'].'</td>';
    echo '</tr>';
  }
?>
</table>
</body>
</html>

      