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

    if (empty($my_album_photos_arr)) {
        echo "<div class='alert alert-danger' role='alert'>
                Ops! No images found in this album.
                </div>";
    } else {
        $path = './temp/'.$myData_id['id'].'/'.$myalbum['name'];
        $albumnamePath = "";
        if (!file_exists($path)) {
            $albumnamePath = "./temp/".$myData_id['id']."/".$myalbum['name'];
            mkdir($albumnamePath);
        }

        for ($i = 0; $i < sizeof($my_album_photos_arr); $i++) {
            array_push($res_photos_urls, $my_album_photos_arr[$i]['picture']);
        }

        if(file_exists($path.'/'.$myalbum['id'].'.zip')){
            echo "<div class='alert alert-success' role='alert'>
                Album already downloaded on server — check it out!
                </div>

                <div class='message-box'>
                    <div class='jumbotron jumbotron-fluid'>
                        <div class='container'>
                            <h1 class='display-4'> Link: </h1>
                            <p class='lead'> <a href='" . $path . "/" . $myalbum['id'] . ".zip' download> '" . $path . "/" . $myalbum['id'] . ".zip' </a> </p>
                        </div>
                    </div>
                 </div>";
        } else {
        $files = $res_photos_urls;

        $zip = new ZipArchive();
        $tmp_file = $path.'/'. $myalbum['id'].'.zip';
        $zip->open($tmp_file, ZipArchive::CREATE);

        foreach ($files as $file) {
            $download_file = file_get_contents($file);
            $zip->addFromString(basename($file), $download_file);
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
                            <p class='lead'> <a href='".$path."/".$myalbum['id'].".zip' download> '".$path."/".$myalbum['id'].".zip' </a> </p>
                        </div>
                    </div>
                 </div>";
        }
    }
?>
</body>
</html>