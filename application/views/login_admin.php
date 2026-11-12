<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=base_url()?>/assets/AdminLTE/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?=base_url()?>/assets/AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url()?>/assets/AdminLTE/dist/css/adminlte.min.css">
  <!-- recapthca google v3-->
  <script src="https://www.google.com/recaptcha/api.js?render=6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH"></script>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="../../index2.html"><b>Login</b>ADMIN</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form method="POST" action="<?=base_url('login_admin')?>">
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" style="display: none">
        <input type="hidden" id="token" name="token">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Email" name="email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?=base_url()?>/assets/AdminLTE/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=base_url()?>/assets/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url()?>/assets/AdminLTE/dist/js/adminlte.min.js"></script>
<!--- google recpatcha -->
<script>
   grecaptcha.ready(function() {
     grecaptcha.execute('6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH', {action: 'homepage'}).then(function(token) {
     console.log("onload's token : "+token);
     document.getElementById("token").value = token;
     });
   });
   /*refresh get token before it's expire every 90 Seconds */
   setInterval(function () {
     grecaptcha.ready(function () {
     grecaptcha.execute("6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH", {action: "request_call_back"}).then(function (token) {
     console.log("refresh toekn : "+token);
     document.getElementById("token").value = token;
     });
     });
   }, 90 * 1000);
</script>
</body>
</html>
