<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Авторизація</title>
    <link rel="stylesheet" href="css/style.css">
    <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <style>
        th {
            color: #fff;
            vertical-align: middle;
            font-weight: bold;
        }
    </style>
</head>
<body>

<section class="container">
    <div class="login">
        <h1>Користувач:</h1>
        <form method="post" action="auth.php">
            <p><input type="text" name="login" value="" placeholder="Логін"></p>
            <p><input type="password" name="password" value="" placeholder="Пароль для логіну"></p>
            <p class="submit"><input type="submit" name="commit" value="Авторизуватися"></p>
        </form>
    </div>
</section>
</body>
</html>