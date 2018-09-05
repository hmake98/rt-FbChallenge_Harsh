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
    //getting token and id
    $token = $_COOKIE['token'];
    $res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
    $myData_id = json_decode($res_id, true);

    //albums 
    $albums_json_response = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
    $albums_data = json_decode($albums_json_response, true);
    $albums_data_a = $albums_data['albums']['data'];
    $albums_name_data = array();
    $albums_id_data = array();

    //photos counts
    $res_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,id}}&access_token=$token");
    $myData_photos = json_decode($res_photos, true);
    $myData_photos_ac = $myData_photos['albums']['data'];
    $my_album_photos_arr = @$myData_photos_ac[$_GET['id']]['photos']['data'];
    $res_photos_urls = array();
    $all_photos_data = array();
    $res_photos = array();

    //high quality images
    $my_fullsize_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos.limit(200){picture,images}}&access_token=$token");
    $my_fullsize_photos_data = json_decode($my_fullsize_photos, true);
    $my_fullsize_photos_albums = $my_fullsize_photos_data['albums']['data'];
    $my_fullsize_photos_photos = array();

    for($d = 0; $d < sizeof($albums_data_a); $d++){
        array_push($albums_name_data, $albums_data_a[$d]['name']);
    }

    for($d = 0; $d < sizeof($albums_data_a); $d++){
        array_push($albums_id_data, $albums_data_a[$d]['id']);
    }

    //loop through images and store in array
    for ($i = 0; $i < sizeof($my_fullsize_photos_albums); $i++) {
        $temp_1 = array();
        for ($j = 0; $j < sizeof(@$my_fullsize_photos_albums[$i]['photos']['data']); $j++) {
            array_push($temp_1, @$my_fullsize_photos_albums[$i]['photos']['data'][$j]['images'][2]['source']);
        }
        $my_fullsize_photos_photos[$i] = $temp_1;
    }

    $path = './temp/'.$myData_id['id'];
    $albumnamePath = "";
    if (!file_exists($path)) {
        $albumnamePath = "./temp/".$myData_id['id'];
        mkdir($albumnamePath);
    }

    if(file_exists($path.'/albums.zip')){
        echo "<nav class='navbar navbar-dark site-header sticky-top py-1'>  
                <i class='fas fa-arrow-left fa-2x' id='back-page'></i> 
        </nav>
        <div class='alert alert-success' role='alert'>
                Album already downloaded on server — check it out!
            </div>

            <div class='message-box'>
                    <div class='jumbotron jumbotron-fluid'>
                        <div class='container'>
                            <h1 class='display-4'> Link: </h1>
                            <p class='lead'> Click <a href='" . $path . "/albums.zip' download> here </a> to Download. </p>
                        </div>
                    </div>
                 </div>";
    } else { 
    $name_arr = array();
   
    for($t = 0; $t < sizeof($albums_id_data); $t++){
        for($a = 0; $a < sizeof($my_fullsize_photos_albums); $a++){
            if($albums_id_data[$t] == $my_fullsize_photos_albums[$a]['id']){
                array_push($name_arr, $albums_name_data[$t]);            
    
                $files = $my_fullsize_photos_photos[$t];

                $zip = new ZipArchive();
                $tmp_file = $path.'/albums.zip';
                ini_set('max_execution_time', 300);
                $zip->open($tmp_file, ZipArchive::CREATE);
                $zip->addEmptyDir($albums_name_data[$t]);
    
                $e = 1;
                foreach ($files as $file) {
                    //echo $albums_name_data[$t].'/albums-'.$e.'.jpg';
                    @$download_file = file_get_contents($file);
                    $zip->addFromString($albums_name_data[$t].'/'.$albums_name_data[$t].'-'.$e.'.jpg', $download_file);
                    $e++;
                }

                $zip->close();
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
                            <p class='lead'> Click <a href='".$path."/albums.zip' download> here </a> to Download. </p>
                        </div>
                    </div>
                 </div>";
    }
?>
</body>
<script>
    document.getElementById('back-page').addEventListener('click', function(){
        window.history.go(-1);
    });
</script>
</body>
</html>
