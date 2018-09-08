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
error_reporting(0);
    //getting token and albums 
    $token = $_COOKIE['token'];
    $res = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
    $res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
    $myData_id = json_decode($res_id, true);
    $myData = json_decode($res, true);
    $myalbum = $myData['albums']['data'][$_GET['id']];
    $name = $myalbum['name'];

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
  	    //echo '<h1>'.$albumname[$i++]['name'].'</h1>';
	
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

        //echo "<br><br>";
        array_push($all_data, $images_src_array);
    }

    //loop through particular photo
    for ($i = 0; $i < sizeof($my_fullsize_photos_albums); $i++) {
        $temp_1 = array();
        for ($j = 0; $j < sizeof(@$my_fullsize_photos_albums[$_GET['id']]['photos']['data']); $j++) {
            array_push($temp_1, $my_fullsize_photos_albums[$_GET['id']]['photos']['data'][$j]['images'][2]['source']);
        }
    }

    //if no images are available in this album 
    if (empty($temp_1)) {
        echo "<nav class='navbar navbar-dark site-header sticky-top py-1'>  
            <i class='fas fa-arrow-left fa-2x' id='back-page'></i>
        </nav>
        <div class='alert alert-danger' role='alert'>
                Ops! No images found in this album.
                </div>";
    } else {
        //making path and store that album zip in path
        $path = './temp/'.$myData_id['id'];
        $albumnamePath = "";
        if (!file_exists($path)) {
            $albumnamePath = "./temp/".$myData_id['id'];
            mkdir($albumnamePath);
        }

        //if album is already on server 
        if(file_exists($path.'/'.$myalbum['name'].'.zip')){
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
                            <p class='lead'> Click <a href='" . $path . "/" . $myalbum['name'] . ".zip' download> here </a> to Download. </p>
                        </div>
                    </div>
                 </div>";
        } else {
        $files = $all_data[$_GET['id']];

        $zip = new ZipArchive();
        $tmp_file = $path.'/'. $myalbum['name'].'.zip';
        ini_set('max_execution_time', 300);
        $zip->open($tmp_file, ZipArchive::CREATE);

        $s = 1;
        foreach ($files as $file) {
            @$download_file = file_get_contents(@$file);
            $zip->addFromString($name.'-'.$s.'.jpg', $download_file);
            $s++;
        }

        $zip->close();

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
                            <p class='lead'> Click <a href='".$path."/".$myalbum['name'].".zip' download> here </a> to Download. </p>
                        </div>
                    </div>
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