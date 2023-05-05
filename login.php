<?php 
include 'php/login_action.php';
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>PARQR-Admin</title>
    <style>
        *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        body{
            background: #213A5C;
        }
        .row{
            background: white;
            border-radius: 30px;
        }
        img{
            width: auto;
            height: auto;
            
        }
        .btn{
            border: none;
            outline: none;
            height: 50px;
            width: 100%;
            background-color: #213A5C;
            color: white;
            border-radius: 4px;
            font-weight: bold;
        }
        .btn:hover{
            background-color: white;
            border: 1px solid;
            color: #213A5C;
        }
        form{
            padding-left: 120px;
            padding-right: 110px;
            padding-top: 20px;
            height: auto;
            width: auto;
        }
    </style>
  </head>

	<body>

    <section class="Form my-5 mx-5">
      <div class="container">
        <div class="row no-gutters">
          <div class="col-lg-6">
            <center>
              <img src="/assets/PARQR_Welcome_Image.png" class="img-fluid" alt="">
            </center>
          </div>
          <div class="col-lg-6">
            <form method="post" enctype="multipart/form-data">
              <center>
                <img src="/assets/PARQR.png" class="img-fluid" alt="">
              </center>
              <div class="form-group">
                <h5>Sign into your account</h5>
                <label>Enter Your Email:</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="form-group">
                <label class="fw">Enter Your Password:</label>
                <input type="password" name="password" class="form-control" required>
              </div> 
              <div class="form-group text-center">
                <a href="register.php">Forgot Password?</a>
              </div>
              <div class="form-group text-right">
                <button class="btn btn-primary btn-block" name="submit">Login</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
		
	</body>
</html>