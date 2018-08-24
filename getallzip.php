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

    //photos counts
    $res_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,id}}&access_token=$token");
    $myData_photos = json_decode($res_photos, true);
    $myData_photos_ac = $myData_photos['albums']['data'];
    $my_album_photos_arr = @$myData_photos_ac[$_GET['id']]['photos']['data'];
    $res_photos_urls = array();
    $all_photos_data = array();
    $res_photos = array();


    for ($i = 0; $i < sizeof(@$myData_photos_ac); $i++) {
        array_push($res_photos, @$myData_photos_ac[$i]['photos']['data']);
    }

    for ($k = 0; $k < sizeof($myData_photos_ac); $k++) {
        for ($j = 0; $j < sizeof($res_photos[$k]); $j++) {
            array_push($all_photos_data, $res_photos[$k][$j]['picture']);
        }
    }


    $path = './temp/'.$myData_id['id'];
    $albumnamePath = "";
    if (!file_exists($path)) {
        $albumnamePath = "./temp/".$myData_id['id'];
        mkdir($albumnamePath);
    }

    if(file_exists($path.'/albums.zip')){
        echo "<div class='alert alert-success' role='alert'>
                Album already downloaded on server — check it out!
            </div>

            <div class='message-box'>
                    <div class='jumbotron jumbotron-fluid'>
                        <div class='container'>
                            <h1 class='display-4'> Link: </h1>
                            <p class='lead'> Click<a href='" . $path . "/albums.zip' download>  </a> </p>
                        </div>
                    </div>
                 </div>";
    } else {
    $files = $all_photos_data;

    $zip = new ZipArchive();
    $tmp_file = $path.'/albums.zip';
    $zip->open($tmp_file, ZipArchive::CREATE);

    foreach ($files as $file) {
        $download_file = file_get_contents($file);
        $zip->addFromString(basename($file), $download_file);
    }

    @$zip->close();
        echo "<div class='alert alert-success' role='alert'>
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

    ini_set('max_execution_time', 300);
    }
?>
</body>
</html>
