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

//photos counts
$res_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,id}}&access_token=$token");
$myData_photos = json_decode($res_photos, true);
$myData_photos_ac = $myData_photos['albums']['data'];
$my_album_photos_arr = @$myData_photos_ac[$_GET['id']]['photos']['data'];


$selected_arr = array();
$myAlbumsIdArr = array();

for ($i = 0; $i < sizeof($myData_albums); $i++) {
    array_push($myAlbumsIdArr, $myData_photos_ac[$i]['id']);
}

$my_sel_photos_arr = array();
$all_photos_data = array();

if (isset($_POST['submit'])) {
    if (!empty($_POST['check_list'])) {
        foreach ($_POST['check_list'] as $selected) {
            //echo $selected . "</br>";
            array_push($selected_arr, $selected);
            if(in_array($selected, $myAlbumsIdArr)){
                for($j = 0; $j < sizeof($myData_photos_ac); $j++){
                    if($selected == $myData_photos_ac[$j]['id']){
                        $key = array_search($selected, $myAlbumsIdArr);
                        array_push($my_sel_photos_arr, @$myData_photos_ac[$key]['photos']['data']);
                    }
                }
            }
        }

        for ($k = 0; $k < sizeof($my_sel_photos_arr); $k++) {
            for ($l = 0; $l < sizeof($my_sel_photos_arr[$k]); $l++) {
                array_push($all_photos_data, $my_sel_photos_arr[$k][$l]['picture']);
            }
        }

        $path = './temp/' . $myData_id['id'] . '/sel_albums';
        $albumnamePath = "";
        if (!file_exists($path)) {
            $albumnamePath = "./temp/" . $myData_id['id'] . "/sel_albums";
            mkdir($albumnamePath);
        }
            $files = $all_photos_data;

            $zip = new ZipArchive();
            $tmp_file = $path . '/selected_albums.zip';
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
                            <p class='lead'> <a href='" . $path . "/selected_albums.zip' download> '" . $path . "/selected_albums.zip' </a> </p>
                        </div>
                    </div>
                 </div>";
    }
}

ini_set('max_execution_time', 300);

?>
</body>
</html>
