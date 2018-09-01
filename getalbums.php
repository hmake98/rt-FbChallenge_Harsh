<head>
    <link href="./style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php
    @$token = $_COOKIE['token'];
    @$res = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
    @$res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
    @$myData = json_decode($res, true);

    //for photos
    @$res_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,id}}&access_token=$token");
    @$myData_photos = json_decode($res_photos, true);
    @$myData_photos_ac = $myData_photos['albums']['data'];

    $myData_id = json_decode($res_id, true);
    $myArr = $myData['albums']['data'];
 
    //for high quality albums 
    $my_fullsize_photos = file_get_contents("https://graph.facebook.com/me?fields=albums{photos{picture,images}}&access_token=$token");
    $my_fullsize_photos_data = json_decode($my_fullsize_photos, true);
    $my_fullsize_photos_albums = $my_fullsize_photos_data['albums']['data'];

    //array of full size photos
    $my_fullsize_photos_photos = array();

    //store photos in the array
    for ($i = 0; $i < sizeof($my_fullsize_photos_albums); $i++) {
        $temp_1 = array();
        for ($j = 0; $j < sizeof(@$my_fullsize_photos_albums[$i]['photos']['data']); $j++) {
            array_push($temp_1, $my_fullsize_photos_albums[$i]['photos']['data'][$j]['images'][2]['source']);
        }
        $my_fullsize_photos_photos[$my_fullsize_photos_albums[$i]['id']] = $temp_1;
    }

    echo "<div class='row'>
            <div class='col'>
                <form method='post' action='getallzip.php'>
                    <input type='submit' class='btn btn-primary btn-main' value='Download All'>
                </form>
            </div>
            <div class='col'>
                <form method='post' action='indext.php'>
                    <input type='submit' class='btn btn-primary btn-main' value='Save to drive'>
                </form>
            </div>
            <div class='col'>
                <form action='getselected.php' method='post'>
                    <input type='submit' name='submit' class='btn btn-primary btn-main' value='Download Selected'>
            </div>
            <div class='container'>
                <div class='row'>";

    $myAlbumsNameArr = array();
    $myAlbumsIdArr = array();

    $path = './temp/'.$myData_id['id'].'/';
    $usernamePath = "";
    if(!file_exists($path)){
        $usernamePath = "./temp/".$myData_id['id']."";
        mkdir($usernamePath);
    }

    for($i = 0; $i < sizeof($myArr); $i++) {
        array_push($myAlbumsIdArr, $myData['albums']['data'][$i]['id']);
        array_push($myAlbumsNameArr, $myData['albums']['data'][$i]['name']);
        @$id_photos = $myData['albums']['data'][$i]['id'];
        @$src = $myData_photos_ac[$i]['photos']['data'][0]['picture'];
        echo "<div class='col-md-4'>
                <div class='block'>
                    <div class='card-deck'>
                        <div class='card'>
                            <input type='checkbox' class='form-check-input mycheckbox' name='check_list[]' value='$myAlbumsIdArr[$i]'>";
                            if(empty($myData_photos_ac[$i]['photos']['data'])){
                                    echo "<img class='card-img-top' src='https://pbs.twimg.com/profile_images/570238398288261120/UUI283GI.png' width='500' height='250' alt='Facebook albums'>
                                    <div class='card-body'>
                                        <h4> " . $myData['albums']['data'][$i]['name'] . " </h4>
                                    </div>
                                    <button type='button' class='btn btn-primary' id='id_$i'> Download </button>
                                    <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#hello" . $myAlbumsIdArr[$i] . "' onclick='showDivs(1," . $myAlbumsIdArr[$i] . ");'>Slideshow</button>
                                </div>
                            </div>
                        </div>
                    </div>";   
                            } else {   
                            echo "<img class='card-img-top' src='$src' width='500' height='250' alt='Facebook albums'>
                                    <div class='card-body'>
                                        <h4> ".$myData['albums']['data'][$i]['name']. " </h4>
                                    </div>
                                    <button type='button' class='btn btn-primary' id='id_$i'> Download </button>
                                    <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#hello" . $myAlbumsIdArr[$i] . "' onclick='showDivs(1," . $myAlbumsIdArr[$i] . ");'>Slideshow</button>
                                </div>
                            </div>
                        </div>
                    </div>";        
                }  
    }
    echo "</div>
        </div>
    </form>";

    for ($m = 0; $m < sizeof($myArr); $m++) {
    echo "<div class='modal fade' id='hello".$myAlbumsIdArr[$m]."' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>";
                $my_album_photos_arr = @$myData_photos_ac[$m]['photos']['data'];
                @$id_photos = $myData['albums']['data'][$m]['id'];
                if (empty($my_album_photos_arr)) {
                   echo "<h1 style='text-align: center; margin: 5em;'> Ops! No Photos Available in this album </h1>";
                } else {  
                    echo "<div class='wrap'>
                            <div class='w3-content w3-display-container'>";
                            for ($k = 0; $k < sizeof($my_fullsize_photos_photos); $k++) {
                                for ($l = 0; $l < sizeof($my_fullsize_photos_photos[$id_photos]); $l++) {
                                    $photos = $my_fullsize_photos_photos[$id_photos];    
                                echo "<img class='mySlides".$myAlbumsIdArr[$m]."' src='".$photos[$l]."' width='100%' height='550px'>";
                                }
                            }
                    echo "<button class='w3-button w3-black w3-display-left' onclick=' plusDivs(-1,".$myAlbumsIdArr[$m]. "); '>&#10094;</button>
                        <button class='w3-button w3-black w3-display-right' onclick=' plusDivs(1,".$myAlbumsIdArr[$m]. "); '>&#10095;</button>
                    </div>
                </div>";
                }
    echo "</div>
        </div>
        </div>";
    }


    for($j = 0; $j < sizeof($myAlbumsNameArr); $j++) {
        echo "<script type='text/javascript'>
            
            /*document.getElementById('$myAlbumsIdArr[$j]').addEventListener('click', function(){
                window.location.href='getphotos.php?id=".$j. "';
            });*/
            
            /*document.getElementById('$myAlbumsIdArr[$j]').addEventListener('click', function(){
                window.location.href='slideshow.php?id=".$j. "';
            });*/

            document.getElementById('id_$j').addEventListener('click', function(){
                window.location.href='getzip.php?id=" . $j . "';
            });
            </script>";
    }

?>
</body>
<script type='text/javascript' src='./js/main.js'> </script>

