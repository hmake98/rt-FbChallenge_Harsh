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
@$res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
$myData_id = json_decode($res_id, true);
$id = $myData_id['id'];

$files= array();

$dir = dir('./temp/'.$id);

while ($file = $dir->read()) {
    if ($file != '.' && $file != '..') {
        $files[] = $file;
    }
}
$dir->close();
if (!empty($_POST)) {
    $client->setAccessToken($_SESSION['accessToken']);
    $service = new Google_DriveService($client);
    $file->setParents([{'id': }])
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file = new Google_DriveFile();  
    foreach ($files as $file_name) {
        $file_path = './temp/'.$id.'/';
        $mime_type = finfo_file($finfo, $file_path);
        $file->setTitle($file_name);
        $file->setDescription('This is a '.$mime_type.' document');
        $file->setMimeType($mime_type);
        $service->files->insert(
            $file,
            array(
                'data' => file_get_contents($file_path),
                'mimeType' => $mime_type
            )
        );
    }
    finfo_close($finfo);
    header('location:'.$url);exit;
}
include 'index.phtml';
?>