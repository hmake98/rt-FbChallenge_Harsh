<html>
<head>
<title> Downloads </title>
<link href="./style.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
<?php
//getting token
$token = $_COOKIE['token'];
$res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
$myData_id = json_decode($res_id, true);

//getting albums 
$res_album = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
$myData = json_decode($res_album, true);
$myData_albums = $myData['albums']['data'];
//echo $myData_albums['name'];

//high quality images 
$my_fullsize_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos.limit(200){picture,images}}&access_token=$token");
$my_fullsize_photos_data = json_decode($my_fullsize_photos, true);
$my_fullsize_photos_albums = $my_fullsize_photos_data['albums']['data'];

$my_fullsize_photos_photos = array();
$myarr_id = array();

//loop though images
for ($i = 0; $i < sizeof($my_fullsize_photos_albums); $i++) {
  $temp_1 = array();
  for($j = 0; $j < sizeof(@$my_fullsize_photos_albums[$i]['photos']['data']); $j++){
    array_push($temp_1, @$my_fullsize_photos_albums[$i]['photos']['data'][$j]['images'][2]['source']);
  }
  array_push($myarr_id, $my_fullsize_photos_albums[$i]['id']);
  $my_fullsize_photos_photos[$my_fullsize_photos_albums[$i]['id']] = $temp_1;
}

//getting selected albums
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

        $album_name_data = array();
        for($q = 0; $q < sizeof($myarr_id); $q++){
            for($l = 0; $l < sizeof($selected_arr); $l++){
                if($selected_arr[$l] == $myarr_id[$q]){
                    array_push($album_name_data, $myData_albums[$l]['name']);

                    $files = $selected_photos_data[$l];

                    $zip = new ZipArchive();
                    $tmp_file = $path . '/selected_photos.zip';
                    ini_set('max_execution_time', 300);
                    $zip->open($tmp_file, ZipArchive::CREATE);
                    $zip->addEmptyDir($album_name_data[$l]);

                    $r = 1;
                    foreach ($files as $file) {
                        $download_file = file_get_contents($file);
                        $zip->addFromString($album_name_data[$l].'/'.$album_name_data[$l].'-'.$r.'.jpg', $download_file);
                        $r++;
                    }

                    @$zip->close();
                }
            }
        }
        
        echo "<nav class='navbar navbar-dark site-header sticky-top py-1'>  
            <i class='fas fa-arrow-left fa-2x' id='back-page'></i>
        </nav>
        <div class='alert alert-success' role='alert'>
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
    }else{
        echo "<nav class='navbar navbar-dark site-header sticky-top py-1'>  
                    <i class='fas fa-arrow-left fa-2x' id='back-page'></i>
                </nav>
                <div class='alert alert-danger' role='alert'>
                    Ops! You haven't selected any albums. Please select any! 😃
                </div>";
    }
}
?>
</body>
<script>
    document.getElementById('back-page').addEventListener('click', function(){
        window.history.go(-1);
    });
</script>
</html>
