<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta rel="icon" href="http://www.stickpng.com/assets/images/584ac2d03ac3a570f94a666d.png">
    <title> rt-Facebook </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./style.css" rel="stylesheet" type="text/css">
    <link href="https://www.w3schools.com/w3css/4/w3.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://bootswatch.com/4/cosmo/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        var access_token;
        window.fbAsyncInit = function () {
            FB.init({
                appId: '299669190798021',
                cookie: true,
                xfbml: true,
                version: 'v3.1'
            });

            FB.getLoginStatus(function (response) {
                statusChangeCallback(response);
            });
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                console.log('Logged in and authenticated');
                document.getElementById("log-in").style.display = "none";
                document.getElementById("checker").style.display = "none";
                document.getElementById("log-out").style.display = "block";
                document.getElementById("hide-data").style.display = "block";
                document.getElementById("hide-album").style.display = "block";
                FB.api('/me?fields=id,name,picture', function (response) {
                    access_token = FB.getAuthResponse()['accessToken'];
                    document.cookie = "token="+access_token;
                    console.log('Good to see you, ' + response.name + '.');
                    document.getElementById("name_message").innerHTML = "Hello! " + response.name;
                    document.getElementById("profile_picture").src = "http://graph.facebook.com/" + response.id +
                        "/picture?height=200";
                    document.getElementById("profile_picture").classList.add("img-circle");
                });

                FB.api('/me?fields=gender,birthday,email,hometown', function(response){
                    var MyGen = response.gender;
                    var MyEmail = response.email;
                    var MyBday = response.birthday;
                    var Home = response.hometown.name;
                    document.getElementById("mygen").innerHTML = "Gender : "+MyGen;
                    document.getElementById("myemail").innerHTML = "Email : "+MyEmail;
                    document.getElementById("mybday").innerHTML = "Birthdate : "+MyBday;
                    document.getElementById("hometown").innerHTML = "Hometown : "+Home;
                });
            } else {
                console.log('Not authenticated');
                document.getElementById("log-in").style.display = "block";
                document.getElementById("log-out").style.display = "none";
                document.getElementById("hide-data").style.display = "none";
                document.getElementById("hide-album").style.display = "none";
                document.getElementById("foot").style.position = "fixed";           
                document.getElementById("crap").style.background = "none";
                document.body.style.backgroundImage = "url('https://img00.deviantart.net/31cf/i/2015/028/8/6/super_hero_whatsapp_background_by_x_ama-d8fr7iz.jpg')";
            }
        }

        function checkLoginState(){
            FB.getLoginStatus(function (response) {
                statusChangeCallback(response);
            });
        }
         
        function Fb_login() {
            FB.login(function(response) {
                checkLoginState();
            }, {
                scope: 'email, user_hometown, user_photos, user_birthday, user_gender',
                auth_type: 'rerequest'
            });
        }

        function Fb_logout() {
            FB.logout(function(response){
                //window.location.reload();
            });
        }
    </script>
</head>
<body onload="checkLoginState();">
    <nav class="navbar navbar-dark site-header sticky-top py-1">
        <div class="navbar-brand">
            <h2 style="font-size: 40px;"> rt-Facebook </h2>
        </div>  
        <div class="ml-auto">
            <button class="btn btn-default btn-nav" id="log-in" onclick="Fb_login();"> Login </button>
            <button class="btn btn-default btn-nav" id="log-out" onclick="Fb_logout();"> <i class="fas fa-sign-out-alt"></i> Logout </button>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row bgcolor" id="crap">
            <div class="col-md-6">
                <div class="heading-text">
                    <h3 id="checker" class="slide-left"> Please login to Profile. </h3>
                    <img src="" id="profile_picture">
                    <h3 id="name_message" style="text-align: center;"></h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="heading-text" id="hide-data">
                    <h2 class="slide-left"> <i class="far fa-address-card"></i> Bio </h2> <hr>
                    <h5 id="mygen"> </h5><br>
                    <h5 id="myemail"> </h5><br>
                    <h5 id="mybday"> </h5><br>
                    <h5 id="hometown"> </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
            <div class="albums-border" id="hide-album">
                <h3 style="padding-top: 3em;" class="slide-right"> <strong> Albums </strong> </h3><hr>
                <?php
                    include('getalbums.php');
                ?>
            </div>
        </div>
    </div>
    <footer class="page-footer font-small myfooter" id="foot">
        <div class="footer-copyright text-center py-3">©2018 Copyright &middot;
            <a href="http://harshmakwana.000webhostapp.com/"> <strong> Harsh Makwana </strong> &middot;</a>
            Made with ❤️ and ☕ 
        </div>
    </footer>

</body>

</html>