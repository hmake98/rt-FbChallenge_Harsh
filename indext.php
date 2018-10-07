<link href="./style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<?php
error_reporting(0);
session_start();
$url_array = explode('?', 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$url = $url_array[0];

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';
$client = new Google_Client();
$client->setClientId('--Enter yout Client id here--');
$client->setClientSecret('--Enter your Client secret here--');
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

$name_data = file_get_contents("https://graph.facebook.com/me?fields=name&access_token=$token");
$name_json = json_decode($name_data, true);
$name = $name_json['name'];

$res = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
$res_json = json_decode($res, true);

$album_name = array();

for($s=0; $s < sizeof($res_json['albums']['data']); $s++){
    array_push($album_name, $res_json['albums']['data'][$s]['name']);
}

$client->setAccessToken($_SESSION['accessToken']);
$service = new Google_DriveService($client);
$folder = new Google_DriveFile();
$folder->setTitle('facebook_'.$name.'_albums');
$mime_type_folder = 'application/vnd.google-apps.folder';
$folder->setMimeType($mime_type_folder);
$newFolder = $service->files->insert($folder);
$parentId = $newFolder['id'];
$parent = new Google_ParentReference();
$parent_folders = new Google_ParentReference();
$parent->setId($parentId);
$album_folder_id = array();
for($i = 0; $i < sizeof($album_name); $i++){
    $service = new Google_DriveService($client);
    $folders = new Google_DriveFile();
    $album_folder_name = $album_name[$i];
    $folders->setTitle($album_folder_name);
    $folders->setMimeType($mime_type_folder);
    $folders->setParents(array($parent));
    array_push($album_folder_id, $service->files->insert($folders)['id']);
}


$fulldata = "https://graph.facebook.com/me?fields=albums{photos.limit(100){picture,images}}&access_token=$token";
$albums = "https://graph.facebook.com/me?fields=albums&access_token=$token";

$fullsize = json_decode( file_get_contents( $fulldata ), true );
$albumname = json_decode( file_get_contents( $albums ), true )['albums']['data'];


$i = 0;
$fullsize_albums_data = $fullsize['albums']['data'];
$all_data = array();

// Loop Over each album
foreach ( $fullsize_albums_data as $album ) {
  
// ALBUM NAME	
$images = $album['photos'];
$images_src_array = array();

// Loop over each image of album, first 100
foreach ( $images['data'] as $image ) {
	array_push($images_src_array, $image['images'][1]['source']);
}
	
// Loop over other images from next, of this album
while(isset($images['paging']['next'])) {
	$images = json_decode( file_get_contents( $images['paging']['next'] ), true );
	foreach ($images['data'] as $image)
	{
		array_push($images_src_array, @$image['images'][2]['source']);
	}	
}
array_push($all_data, $images_src_array);

}

$f = 0;
for($k = 0; $k < sizeof($album_name); $k++){
    for($d = 0; $d < sizeof($all_data[$k]); $d++){
        $parent_folders->setId($album_folder_id[$k]);
        $files = new Google_DriveFile();
        $file_name = $album_name[$k].'-'.$f++.'.jpg';
        $files->setTitle($file_name);
        $files->setMimeType('image/jpeg');
        $files->setParents(array($parent_folders));
        ini_set('max_execution_time', 300);
        $service->files->insert(
            $files,
                array(
                    'data' => file_get_contents($all_data[$k][$d]),
                    'mimeType' => 'image/jpeg'
                )
            );
    }
}
echo "<nav class='navbar navbar-dark site-header sticky-top py-1'>  
<i class='fas fa-arrow-left fa-2x' id='back-page'></i>
</nav>
<div class='alert alert-success' role='alert'>
    All albums are sccessfully uplaoded to your drive. :)
</div>";
?>
<script>
    document.getElementById('back-page').addEventListener('click', function(){
        window.history.go(-1);
    });
</script>
