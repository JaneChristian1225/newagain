<?php
include("connect.php");
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';


$r_id = array();
$arr_hold = array();
$date_sec = time();
$date_end = array();
$show = mysqli_query($mysqli,"SELECT * FROM renter");
$check_row = mysqli_num_rows($show);
if ($check_row>0) {
while ($rows = mysqli_fetch_array($show)) {
array_push($date_end,$rows["Date_End"]);
array_push($r_id,$rows["Renter_ID"]);
}
} 

for ($x=0;$x<sizeof($date_end);$x++) {
    $hold = strtotime($date_end[$x]);
    $total = $hold-$date_sec;
    if ($total<=0) {
   array_push($arr_hold,$r_id[$x]);
   
    }
    
   }

for ($i=0;$i<sizeof($arr_hold);$i++) {
    $show = mysqli_query($mysqli,"SELECT * FROM renter WHERE Renter_ID = $arr_hold[$i]");
$check_row = mysqli_num_rows($show);
if ($check_row>0) {
while ($rows = mysqli_fetch_array($show)) {
    if ($rows['mailer_r']==0) {

        $web = "Mapasakatan";
        $mail = new PHPMailer(true);
    
        $mail->isSMTP();
        $mail->SMTPAuth=true;
    
        $mail->Host='smtp.gmail.com';
        $mail->Username='ynalockhaven@gmail.com';
        $mail->Password='rtmajrupmtmwakvo';
    
        $mail->SMTPSecure ="tls";
        $mail->Port = 587;
        
        $mail->setFrom("ynalockhaven@gmail.com",$web);
        $mail->addAddress($rows['Renter_Email']);
    
        $mail->isHTML(true);
        $mail->Subject ="Email Verification from Mapasakatan";
    
        $email_template = "
            <h2>BAYAD NA SA RENTA PALIHOG</h2>
            <br><br>
           
        ";
    
        $mail->Body = $email_template;
        $mail->send();
        $result = mysqli_query($mysqli,"UPDATE renter SET mailer_r = 1 WHERE Renter_ID = $arr_hold[$i]");


    }else {

    }
   
}
}
} 


if(isset($_POST['log'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $mysqli->query("SELECT * from landlord where Landlord_Email='$email' and Landlord_Password='$password'"); 
    if($result->num_rows > 0){ 
        while($row = $result->fetch_assoc()){ 
          if ($row['Landlord_Email'] == $email && $row['Landlord_Password'] == $password) {
            $_SESSION['id'] = $row['Landlord_ID'];
              header("location:landlord.php");
        }
      
  }
}else {
    echo '<script>alert("LOGIN ERROR")</script>';
    
    }}
    if (isset($_POST['reg'])) {
        
       
        
        $pass = $_POST['password'];
        $email = $_POST['email'];
        $name = $_POST['name'];
        $number = $_POST['number'];
        $image = $_FILES['image'];
        $img_name = $_FILES['image']['name'];
        $img_size = $_FILES['image']['size'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $img_error = $_FILES['image']['error'];
       $vkey = md5(time().$name);
        $img_ex = pathinfo($img_name,PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);

        $allowed_exs = array("jpg","jpeg","png");
        if(in_array($img_ex_lc, $allowed_exs)){
        $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
        $img_upload_path = 'uploads/'.$new_img_name;
        move_uploaded_file($tmp_name,$img_upload_path);
        }
        else{
            echo "jpg/jpeg/png only.";
        }

        $check = mysqli_query($mysqli,"SELECT * from landlord where Landlord_Email='$email'");
        $check_row = mysqli_num_rows($check);
        if ($check_row>0) {
      
            echo '<script>alert("EMAIL ALREADY EXIST")</script>';
          
        }else {
        
            $result = mysqli_query($mysqli,"INSERT INTO landlord values(0,'$email','$pass','$name','$number', '$new_img_name','$vkey',0)");
            
            echo '<script>alert("SUCCESSFULLY REGISTERED. PLEASE VERIFY YOUR EMAIL.")</script>';

            $qrysqls = "SELECT * from landlord order by Landlord_ID DESC LIMIT 1";
            $result = $mysqli->query($qrysqls);
            $rows = $result->fetch_assoc();
            $_SESSION['id'] = $rows["Landlord_ID"];
       
          
        }
      
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale = 1.0">
    <title>Mapasakatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css?" type="text/css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <!----======== CSS ======== -->

</head>
<body>
    <nav>
        <div>
            <ul>
                <li><img class ="logo" width="5%"  src="./Mapasakatan.png" alt="logo"></li>
                <li class="li"><a href="#aboutus">About Us</a></li>
                <li class="li"><a href="#contactus">Contact Us</a></li>
                <li class="li"> <!-- Button trigger modal -->
                    <button type="submit" data-bs-toggle="modal" data-bs-target="#exampleModal" class="signin" type="button">Log In As Landlord</button></a>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content modal1">
                                <div class="modal-body">
                                    <button type='button' class='btn-close'style="background-color: #fcb316;" data-bs-dismiss='modal' aria-label='Close'></button>
                                    <div class="hero">
                                        <div class="div form-box">
                                            <div class="div button-box">
                                                <div id="btn"></div>
                                                <button type="button" class="toogle-btn" id="logbtn" onclick="login()">Log In</button>
                                                <button type="button" class="toogle-btn" id="regbtn" onclick="register()">Register</button>
                                            </div>
                                            <form id="login" method="POST" class="input-group" enctype="multipart/form-data">
                                                <div class="input-box">
                                                    <i class="fas fa-envelope"></i>
                                                    <input type="text" name="email" placeholder="Enter your email" required>
                                                </div>
                                                <div class="input-box">
                                                    <i class="fas fa-lock"></i>
                                                    <input type="password" name="password" placeholder="Enter your password" required>
                                                </div><br><br><br><br>
                                                <button type="submit" name="log" class="submit-btn">Log In</button>
                                            </form>
                                            <form id="register" method="POST" class="input-group1" enctype="multipart/form-data">
                                                <div class="input-box">
                                                    <i class="fas fa-user"></i>
                                                    <input type="text" name="name" placeholder="Enter your name" required>
                                                </div>
                                                <div class="input-box">
                                                    <i class="fas fa-envelope"></i>
                                                    <input type="text" name="email" placeholder="Enter your email" required>
                                                </div>
                                                <div class="input-box">
                                                    <i class="fas fa-lock"></i>
                                                    <input type="password" name="password" placeholder="Enter your password" required>
                                                </div>
                                                <div class="input-box">
                                                    <i class="fas fa-lock"></i>
                                                    <input type="text"  name="number" placeholder="Enter your contact number" required>
                                                </div>
                                                <div class="titlee">Upload an Image of You</div>
                                                <input type="file" class="addimg" name="image" required>
                                                <button type="submit" name="reg"  class="submit-btn1">Register</button>
                                            </form>
                                            <script>
                                            $('.signin').click(function(){
                                                    $('#exampleModal').appendTo("body").modal('show');
                                                });
                                                
                                                var x = document.getElementById("login");
                                                var y = document.getElementById("register");
                                                var z = document.getElementById("btn");
                                                var a = document.getElementById("regbtn");
                                                var b = document.getElementById("logbtn");

                                                function register(){
                                                    x.style.left = "-400px";
                                                    y.style.left = "10px";
                                                    z.style.left = "110px";
                                                    a.style.color = "#1a1851";
                                                    b.style.color = "white";
                                                }
                                                function login(){
                                                    x.style.left = "10px";
                                                    y.style.left = "450px";
                                                    z.style.left = "0";
                                                    b.style.color = "#1a1851";
                                                    a.style.color = "white";
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>   
    <div class="cover">
        <div class= "title"> LOVE YOUR LIFE. <br> LIVE WHERE YOU LOVE.</div>
        <p class="des"> We know that finding a room to stay is a crucial moment in your life since it embodies your new journey in life.<p class="des">We also know that it can be very busy and hectic, which is why we focus on keeping things simple and easy. </p></p>
        <div>
            <a href="router_t.php"><input type='button' value='Browse Now' id='browsenowbttn'></a>
        </div>
    </div>
    <br>
    <div id="aboutus">
        <br>
        <p class="Blog" >About Us</p>
        <div class ="blogPost">
            <img src="./team1.jpg" alt="post-1" style="width:30%">
        </div>
        <div class ="blogPost">
            <p>We Aim to provide assistance to the students     who are having trouble finding aparment that is near the vicinity of USTP and assistance also for landlords to get more tenants.</p>
        </div>
    </div>
        <br>
        <br>
        <br>
        <!-- <div class="bottom" id="contactus">
            <div class="Footer">Let Us Be Part of Your Journey</div><br>
            <div class="botFooter">We at Mapasakatan are committed to giving you the best experience. For any questions, suggestions, comments, or issues, kindly contact us.
            </div>
                <p class="book"><span>Contact Number:</span> +639970682067</p>
                <p class="book"><span>Email: </span>mapasakatan@gmail.com</p>
                <p class="book"><span>Facebook messenger:</span> https://www.messenger.com/t/Mapasakatan</p>
        </div> -->
        <div class="bottom" id="contactus">
        <div class="Footer">Services</div><br>
            <div class="botFooter">
                <div>
                    <h4>For renters</h4>
                </div> <p> Mapasakatan provides an easy and convenient website where you can browse different properties than you can rent. </p> 
            </div>
            <div class="botFooter1">
                <div>
                    <h4>For landlords</h4>
                </div> <p>Mapasakatan provides an opportunity for the school administration to help students look for a temporary place to settle.</p> 
            </div>
            <div class="botFooter2">
                <div>
                    <h4>For schools</h4>
                </div> <p>We at Mapasakatan are committed to giving you the best experience. For any questions, suggestions, comments, or issues, kindly contact us.</p> 
            </div><br>
        </div>
        <br>
        <br>
        <div class="moreinfo">
        <p class="book"><span>Contact Number:</span> +639970682067</p>
                <p class="book"><span>Email: </span>mapasakatan@gmail.com</p>
                <p class="book"><span>Facebook messenger:</span> https://www.messenger.com/t/Mapasakatan</p>
        </div>
        <p class= "copyright">&copy; All Rights Reserved.</p>
        <br><br>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
 <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</script>
