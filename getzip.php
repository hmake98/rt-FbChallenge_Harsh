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
    $res = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
    $res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
    $myData_id = json_decode($res_id, true);
    $myData = json_decode($res, true);
    $myalbum = $myData['albums']['data'][$_GET['id']];

    //photos counts
    $res_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,id}}&access_token=$token");
    $myData_photos = json_decode($res_photos, true);
    $myData_photos_ac = $myData_photos['albums']['data'];
    $my_album_photos_arr = @$myData_photos_ac[$_GET['id']]['photos']['data'];
    $res_photos_urls = array();
    $all_photos_data = array();
    $res_photos = array();

    //high quality images 
    $my_fullsize_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,images}}&access_token=$token");
    $my_fullsize_photos_data = json_decode($my_fullsize_photos, true);
    $my_fullsize_photos_albums = $my_fullsize_photos_data['albums']['data'];
    $my_fullsize_photos_photos = array();

    for ($i = 0; $i < sizeof($my_fullsize_photos_albums); $i++) {
        $temp_1 = array();
        for ($j = 0; $j < sizeof(@$my_fullsize_photos_albums[$_GET['id']]['photos']['data']); $j++) {
            array_push($temp_1, $my_fullsize_photos_albums[$_GET['id']]['photos']['data'][$j]['images'][2]['source']);
        }
    }

    if (empty($temp_1)) {
        echo "<div class='alert alert-danger' role='alert'>
                Ops! No images found in this album.
                </div>";
    } else {
        $path = './temp/'.$myData_id['id'];
        $albumnamePath = "";
        if (!file_exists($path)) {
            $albumnamePath = "./temp/".$myData_id['id'];
            mkdir($albumnamePath);
        }

        if(file_exists($path.'/'.$myalbum['name'].'.zip')){
            echo "<div class='alert alert-success' role='alert'>
                Album already downloaded on server — check it out!
                </div>

                <div class='message-box'>
                    <div class='jumbotron jumbotron-fluid'>
                        <div class='container'>
                            <h1 class='display-4'> Link: </h1>
                            <p class='lead'> Click <a href='" . $path . "/" . $myalbum['name'] . ".zip' download> here </a> to Download. </p>
                        </div>
                    </div>
                 </div>";
        } else {
        $files = $temp_1;

        $zip = new ZipArchive();
        $tmp_file = $path.'/'. $myalbum['name'].'.zip';
        $zip->open($tmp_file, ZipArchive::CREATE);

        foreach ($files as $file) {
            $download_file = file_get_contents($file);
            $zip->addFromString(basename($file.'.jpg'), $download_file);
        }

        $zip->close();

        ini_set('max_execution_time', 300);

        echo "<div class='alert alert-success' role='alert'>
                Album archived successfully — check it out!
                </div>

                <div class='message-box'>
                    <div class='jumbotron jumbotron-fluid'>
                        <div class='container'>
                            <h1 class='display-4'> Link: </h1>
                            <p class='lead'> Click <a href='".$path."/".$myalbum['name'].".zip' download> here </a> to Download. </p>
                        </div>
                    </div>
                 </div>";
        }
    }
?>
</body>
</html>