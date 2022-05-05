<?php
header('Content-Type: text/html; charset=UTF-8');
$ar = array();
foreach ($_COOKIE as $key => $value) $ar[$key] = $value;
foreach ($ar as $key => $v) echo $key . ':' . ' ' . $v . '<br/>';

$string = array(
  'exitlog1' => '<div style="color:green"> Выход выполнен.</div>',
  'exitlog2' => '<div style="color:green"> Вы не авторизованы.</div>',
  'enterlog' => '<div style="color:green"> Ошибка входа.</div>',
  'enterlogerror' => '<div style="color:green"> заполните все поля.</div>',
  'registration' => '<div style="color:green"> Что бы начать регистрацию выйдете из аккаунта.</div>',
);
$msg = array();
foreach ($string as $name => $str) {
  if (isset($_COOKIE[$name]))
    $msg[$name] = $str;
  else $msg[$name] = '';
  setcookie($name, '', time() - 100000);
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="stiles.css">
    <title>5Back_login</title>
    <style>
      .for {
        border: 2px solid rgb(26, 18, 144);
        font-size: x-large;
        text-align: center;
        max-width: 420px;
        margin: 0 auto;
        margin-top: 50px;
      }

      input {
        margin-top: 10px;
        margin-bottom: 10px;
        font-size: x-large;
      }

      option {
        font-size: x-large;
      }
    </style>

  </head>

  <body>
    <div class="for">
      <form action="" method="POST">
        <label> login:<br />
          <input name="login" value="<?php if (isset($_COOKIE['login'])) print($_COOKIE['login']);
                                      else print(''); ?>" />
        </label><br />

        <label> password:<br />
          <input name="pass" value="<?php if (isset($_COOKIE['pass'])) print($_COOKIE['pass']);
                                    else print(''); ?>" />
        </label><br />

        <input name="buttlog" type="submit" value="Enter" />
        <!-- <input name="registration" type="submit" value="регистрация" /> -->
        <input name="buttlog" type="submit" value="Exit" />
      </form>

      <?php
      $msgs = array('exitlog1', 'exitlog2', 'enterlog', 'enterlogerror', 'registration', 'exitlog', 'wrong', 'notexist');
      foreach ($msgs as $m) if (isset($msg[$m])) print($msg[$m]);
      ?>
    </div>
  </body>

  </html>
<?php
} //________________________________POST_______________________________
else {
  // setcookie('login', '', time() - 100000);
  // setcookie('pass', '', time() - 100000);
  $loginu = $_POST['login'];
  $passu = $_POST['pass'];
  $enterlog = 0;
  $exitlog = 0;
  if (isset($_POST['buttlog']))
    switch ($_POST['buttlog']) {
      case 'Enter':
        $enterlog = 1;
        break;
      case 'Exit':
        $exitlog = 1;
        break;
    }
  if ($exitlog == 1) //Выход
  {
    if (isset($_COOKIE['login'])) {
      session_destroy();
      setcookie('exitlog1', 1);
      setcookie('all_OK','',time()-1000);
      setcookie('login', '',time()-1000);
      setcookie('pass', '',time()-1000);
      header('Location: login.php');
      exit();
    } else {
      setcookie('exitlog2', 1);
      header('Location: login.php');
      exit();
    }
  } else
  if ($enterlog == 1) //Вход
  {
    if (!empty($loginu) && !empty($passu))
    {
      //  Проверть есть ли такой логин и пароль в базе данных.
      //вход в БД
      $user = 'u47586';
      $pass = '3927785';
      $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
      //поиск соответствующего логина
      try 
      {
        $sth = $db->prepare("SELECT '*' FROM 'users' WHERE 'login' = ?");
        $sth->execute(array($loginu));
        $value = $sth->fetch(PDO::FETCH_ASSOC);
      } catch (PDOException $e) 
      {
        print('Error:' . $e->GetMessage());
        exit();
      }
      if (empty($value)) // нет такого пользователя
      { setcookie('empty_request',$value['id']);
        echo '$value[id] ' . $value['id'] . 'Пуст<br/>'; // Выдать сообщение об ошибках.
        $msg['notexist'] = '<div style="color:red"> Пользователь с логином ' . $loginu . ' не существует!</div>';
        header('Location: login.php');
        exit();
      } else if (password_verify($passu, $value['pass'])) {
        echo 'Неверный пароль <br/>'; setcookie('pass_error',$passu);
        $msg['wrong'] = '<div style="color:red"> Неверный пароль!</div>';
        header('Location: login.php');
        exit();
      } else {
        setcookie('all_OK',$loginu);
        echo 'Всё ОК <br/>'; //Если все ок, то авторизуем пользователя.
        session_start();
        $_SESSION['login'] = $loginu;
        $_SESSION['uid'] = $value['id'];
        // include('form.php');
        header('Location: index.php'); //открыть форму
        exit();
      }
    } else
    {
      setcookie('enterlog', 1);
      header('Location: login.php'); //Ошибка входа
      exit();
    }
  }
}
