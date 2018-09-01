<html>
<head>
<title> Downloads </title>
<link href="./css/main.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
<?php
$token = $_COOKIE['token'];
$res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
$myData_id = json_decode($res_id, true);

$res_album = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
$myData = json_decode($res_album, true);
$myData_albums = $myData['albums']['data'];

//high quality images 
$my_fullsize_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,images}}&access_token=$token");
$my_fullsize_photos_data = json_decode($my_fullsize_photos, true);
$my_fullsize_photos_albums = $my_fullsize_photos_data['albums']['data'];

$my_fullsize_photos_photos = array();
$myarr_id = array();
for ($i = 0; $i < sizeof($my_fullsize_photos_albums); $i++) {
  $temp_1 = array();
  for($j = 0; $j < sizeof(@$my_fullsize_photos_albums[$i]['photos']['data']); $j++){
    array_push($temp_1, $my_fullsize_photos_albums[$i]['photos']['data'][$j]['images'][2]['source']);
  }
  array_push($myarr_id, $my_fullsize_photos_albums[$i]['id']);
  $my_fullsize_photos_photos[$my_fullsize_photos_albums[$i]['id']] = $temp_1;
}

$selected_arr = array();
$selected_photos_data = array();
if (isset($_POST['submit'])) {
    if (!empty($_POST['check_list'])) {
        foreach ($_POST['check_list'] as $selected) {
            array_push($selected_arr, $selected);
        }

        for($g = 0; $g < sizeof($selected_arr); $g++){
            if(in_array($selected_arr[$g], $myarr_id)){
                array_push($selected_photos_data, $my_fullsize_photos_photos[$selected_arr[$g]]);
            }
        }

        $all_albums_data = array();
        for($r = 0; $r < sizeof($selected_photos_data); $r++){
            for($s = 0; $s < sizeof($selected_photos_data[$r]); $s++){
                $der = $selected_photos_data[$r];
                array_push($all_albums_data, $der[$s]);
            }
        }

        $path = './temp/' . $myData_id['id'];
        $albumnamePath = "";
        if (!file_exists($path)) {
            $albumnamePath = "./temp/" . $myData_id['id'];
            mkdir($albumnamePath);
        }


        $files = $all_albums_data;

        $zip = new ZipArchive();
        $tmp_file = $path . '/selected_photos.zip';
        $zip->open($tmp_file, ZipArchive::CREATE);

        foreach ($files as $file) {
            $download_file = file_get_contents($file);
            $zip->addFromString(basename($file.'.jpg'), $download_file);
        }

        @$zip->close();
        
        echo "<div class='alert alert-success' role='alert'>
                Album archived successfully — check it out!
                </div>

                <div class='message-box'>
                    <div class='jumbotron jumbotron-fluid'>
                        <div class='container'>
                            <h1 class='display-4'> Link: </h1>
                            <p class='lead'> Click <a href='" . $path . "/selected_photos.zip' download> here </a> for Download. </p>
                        </div>
                    </div>
                 </div>";
    
    ini_set('max_execution_time', 300);
    }
}

?>
</body>
</html>
