<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="/socket.io/socket.io.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="container">

      <div class="row mb-4 mt-3">
        <div class="col p-2 text-center">
            <span class="h1">
              <a href="/">
                <img src="logo.png" alt="">
              </a>
            </span>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 offset-md-4 mt-3">

          <!--<h4>Sign In</h4>
          <hr>-->
          
          <?php if (session()->getFlashdata('failed') !== NULL) : ?>
          <div class="alert alert-danger" role="alert">
              <?php echo session()->getFlashdata('failed'); ?>
          </div>
      <?php endif; ?>

          <form action="/auth/login" method="post" class="form">
            <?=csrf_field();?>
            <div class="form-group mb-3">
              <label for="">Username</label>
              <input type="text" name="username" placeholder="username" class="form-control">
            </div>

            <div class="form-group mb-3">
              <label for="">Password</label>
              <input type="password" name="password" placeholder="password" class="form-control">
            </div>

            <div class="form-group mb-3">
              <input type="submit" value="Login" class="btn btn-info w-100">
            </div>

          </form>
        </div>
      </div>
    </div>

    <?php echo view('footer.php'); ?>
  </body>
</html>