<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
require_once 'redis.php';
$userModel = new UserModel();


if (!empty($_POST['submit'])) {
    $users = [
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ];
    $user = NULL;
    if ($user = $userModel->auth($users['username'], $users['password'])) {
        //Login successful
        $_SESSION['id'] = $user[0]['id'];
        $_SESSION['message'] = 'Login successful';

        // Remember Me - Save cookie to file
        if (!empty($_POST['remember'])) {
            $cookieDir = __DIR__ . '/cookies';
            if (!is_dir($cookieDir)) {
                mkdir($cookieDir, 0755, true);
            }

            // Tìm key cũ của user (nếu có)
            $userId = $user[0]['id'];
            $key = null;
            foreach (glob($cookieDir . '/*') as $file) {
                if (file_get_contents($file) == $userId) {
                    $key = basename($file);
                    break;
                }
            }

            // Nếu chưa có key thì tạo mới
            if (!$key) {
                do {
                    $key = bin2hex(random_bytes(16));
                    $filePath = $cookieDir . '/' . $key;
                } while (file_exists($filePath));
                file_put_contents($filePath, $userId);
                // Lưu lên Redis
                $redis->set($key, $userId);
            } else {
                // Nếu đã có key, đảm bảo Redis cũng có
                if (!$redis->exists($key)) {
                    $redis->set($key, $userId);
                }
            }

            setcookie('remember_key', $key, time() + (86400 * 30), "/");
        }

        header('location: list_users.php');
        exit;
    }else {
        //Login failed
        $_SESSION['message'] = 'Login failed';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php'?>

    <div class="container">
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title">Login</div>
                    <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
                </div>

                <div style="padding-top:30px" class="panel-body" >
                    <form method="post" class="form-horizontal" role="form">

                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">
                        </div>

                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                        </div>

                        <div class="margin-bottom-25">
                            <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                            <label for="remember"> Remember Me</label>
                        </div>

                        <div class="margin-bottom-25 input-group">
                            <!-- Button -->
                            <div class="col-sm-12 controls">
                                <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
                                <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 control">
                                    Don't have an account!
                                    <a href="form_user.php">
                                        Sign Up Here
                                    </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>