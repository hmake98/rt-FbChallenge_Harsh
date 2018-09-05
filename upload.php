<link href="./style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<?php
session_start();
$url_array = explode('?', 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$url = $url_array[0];

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';
$client = new Google_Client();
$client->setClientId('1070022479725-b9krct06n15l2412mbpvckkq9vnd8u2j.apps.googleusercontent.com');
$client->setClientSecret('B2i-MqFbG4Nco-KPDgWxntzb');
$client->setRedirectUri($url);
$client->setScopes(array('https://www.googleapis.com/auth/drive'));
if (isset($_GET['code'])) {
    $_SESSION['accessToken'] = $client->authenticate($_GET['code']);
    header('location:'.$url);exit;
} elseif (!isset($_SESSION['accessToken'])) {
    $client->authenticate();
}

$token = $_COOKIE['token'];
$res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
$myData_id = json_decode($res_id, true);
$id = $myData_id['id'];

$albums_json_response = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
$albums_data = json_decode($albums_json_response, true);
$albums_data_a = $albums_data['albums']['data'];
$albums_name_data = array();

for($d = 0; $d < sizeof($albums_data_a); $d++){
    array_push($albums_name_data, $albums_data_a[$d]['name']);
}

    $my_fullsize_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos.limit(200){picture,images}}&access_token=$token");
    $my_fullsize_photos_data = json_decode($my_fullsize_photos, true);
    $my_fullsize_photos_albums = $my_fullsize_photos_data['albums']['data'];
    $my_fullsize_photos_photos = array();

    //loop through particular photo
    for ($i = 0; $i < sizeof($my_fullsize_photos_albums); $i++) {
        $temp_1 = array();
        for ($j = 0; $j < sizeof(@$my_fullsize_photos_albums[$_GET['id']]['photos']['data']); $j++) {
            array_push($temp_1, $my_fullsize_photos_albums[$_GET['id']]['photos']['data'][$j]['images'][2]['source']);
        }
    }

@$album_name =  $albums_name_data[$_GET['id']];

$dir = dir('./temp/'.$id);

while ($file = $dir->read()) {
    if ($file != '.' && $file != '..' && $file != 'albums.zip') {
        $files[] = $file;
    }
}

$dir->close();

//var_dump($url); die();
if (empty($temp_1)) {
    
    }else{
        $path = './temp/'.$myData_id['id'];
        $albumnamePath = "";
        if (!file_exists($path)) {
            $albumnamePath = "./temp/".$myData_id['id'];
            mkdir($albumnamePath);
        }

        $filesx = $temp_1;

        $zip = new ZipArchive();
        $tmp_file = $path.'/'.$album_name.'.zip';
        ini_set('max_execution_time', 300);
        $zip->open($tmp_file, ZipArchive::CREATE);

        $s = 1;
        foreach ($filesx as $filem) {
            $download_file = file_get_contents($filem);
            $zip->addFromString($album_name.'-'.$s.'.jpg', $download_file);
            $s++;
        }

        $zip->close();
    
        $client->setAccessToken($_SESSION['accessToken']);
        $service = new Google_DriveService($client);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file = new Google_DriveFile();

        $file_path = 'temp/' . $id . '/'.$album_name.'.zip';
        $mime_type = finfo_file($finfo, $file_path);
        $file->setTitle($album_name.'.zip');
        $file->setDescription('This is a ' . $mime_type . ' document');
        $file->setMimeType($mime_type);
        $service->files->insert(
            $file,
                array(
                    'data' => file_get_contents($file_path),
                    'mimeType' => $mime_type,
                )
            );      
        finfo_close($finfo);
        header('location:'.$url);
        exit;
    }
    echo "<nav class='navbar navbar-dark site-header sticky-top py-1'>  
        <i class='fas fa-arrow-left fa-2x' id='back-page'></i>
    </nav>
    <div class='alert alert-success' role='alert'>
        Album is successfully uploaded to the drive 
    </div>";
?>
<script>
    document.getElementById('back-page').addEventListener('click', function(){
        window.history.go(-1);
    });
</script>