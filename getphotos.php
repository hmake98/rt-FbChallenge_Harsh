<link href="./css/main.css" rel="stylesheet">
<link href="https://www.w3schools.com/w3css/4/w3.css" rel="stylesheet">
<link href="https://bootswatch.com/4/cosmo/bootstrap.min.css" type="text/css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

<?php
    $token = $_COOKIE['token'];
    $res_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,id}}&access_token=$token");
    $myData_photos = json_decode($res_photos, true);
    $myData_photos_ac = $myData_photos['albums']['data'];
    $my_album_photos_arr = @$myData_photos_ac[$_GET['id']]['photos']['data'];
    $res_photos_urls = array();
    if (empty($my_album_photos_arr)) {
        echo "<h1 style='text-align: center; margin-top: 5em;'> Ops! No Photos Available in this album </h1>" ;
    } else {
        echo "<div class='wrap'>
                <button class='btn btn-back' onclick='getBack();'> <i class='fas fa-times fa-3x'></i> </button>";
        echo "<div class='w3-content w3-display-container'>";
        for ($i = 0; $i < sizeof($my_album_photos_arr); $i++) {
            echo "<img class='mySlides' src='".$my_album_photos_arr[$i]['picture']."' width='100%' height='100%'>";
            array_push($res_photos_urls, $my_album_photos_arr[$i]['picture']);
        }
        echo "
            <button class='w3-button w3-black w3-display-left' onclick='plusDivs(-1)'>&#10094;</button>
            <button class='w3-button w3-black w3-display-right' onclick='plusDivs(1)'>&#10095;</button>
        </div>
        </div>";
    }
?>

<script type="text/javascript" src="./js/main.js"> </script>


