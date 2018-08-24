<head>
    <link href="./style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
    @$token = $_COOKIE['token'];
    @$res = file_get_contents("https://graph.facebook.com/me?fields=albums&access_token=$token");
    @$res_id = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$token");
    $myData = json_decode($res, true);

    $myData_id = json_decode($res_id, true);
    $myArr = $myData['albums']['data'];


    echo "<form method='post' action='getallzip.php'>
            <input type='submit' class='btn btn-primary btn-main' value='Download All'>
            </form>
            <form method='post' action='indext.php'>
            <input type='submit' class='btn btn-primary btn-main' value='Save to drive'>
            </form>
            <form action='getselected.php' method='post'>
            <input type='submit' name='submit' class='btn btn-primary btn-main' value='Download Selected'>
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
        echo "<div class='col-md-4'>
                <div class='block'>
                    <div class='card-deck'>
                        <div class='card'>
                            <input type='checkbox' class='form-check-input mycheckbox' name='check_list[]' value='$myAlbumsIdArr[$i]'>
                            <img class='card-img-top' id='$myAlbumsIdArr[$i]' src='https://cdn3.iconfinder.com/data/icons/free-social-icons/67/facebook_square-512.png' alt='Facebook albums'>
                            <div class='card-body'>
                                <h4> ".$myData['albums']['data'][$i]['name']. " </h4>
                            </div>
                            <button type='button' class='btn btn-primary' id='id_$i' onclick='return download();'> Download </button>
                            </div>
                        </div>
                    </div>
                </div>";
    }

    echo "</div>
         </div>
         </form>";

    for($j = 0; $j < sizeof($myAlbumsNameArr); $j++) {
        echo "<script type='text/javascript'>
            document.getElementById('$myAlbumsIdArr[$j]').addEventListener('click', function(){
                window.location.href='getphotos.php?id=".$j. "';
            });

            function download(){
                window.location.href='getzip.php?id=".$j."';
            }
            </script>";
    }


?>
</body>
