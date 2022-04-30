<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
$msg; //для сообщений

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  //   выход  session_destroy() при нажатии на кнопку Выход.
  printf('Пользователь с логином %s уже авторизован.', $_SESSION['login']);
  if (isset($_POST['exit'])) session_destroy();
  // Делаем перенаправление на форму.
  header('Location: form.php');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>

  <form action="" method="post">
    <label> login:<br />
      <!-- _____________________________________________4)________Заполняем пароль и логин из COOKIE___________________________ -->
      <input name="login" value="<?php if (isset($_COOKIE['login'])) print $_COOKIE['login']; ?>" />
    </label><br />

    <label> password:<br />
      <input name="pass" value="<?php if (isset($_COOKIE['pass'])) print $_COOKIE['pass']; ?>" />
    </label><br />

    <input name="enter" type="submit" value="Войти" />
    <input name="registration" type="submit" value="регистрация" />
    <input name="exit" type="submit" value="Выход" />
  </form>

  <?php
  $msgname = array('update', 'notexist');
  foreach ($msgname as $n)
    if (isset($msg[$n])) print($msg[$n]);
  ?>

<?php
}
//Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  //_____________________________________________Регистрация__________________________________________________________________
  if (isset($_POST['registration'])) {
    setcookie('registration', '1', time() + 24 * 60 * 60);
    header('Location: login.php');
  } else //__________________________________________Вход__________________________________________________________________________
    if (isset($_POST['enter'])) {
      //  Проверть есть ли такой логин и пароль в базе данных.
      $loginu = $_POST['login'];
      $passu = $_POST['pass'];
      //вход в БД
      $user = 'u47586';
      $pass = '3927785';
      $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
      //поиск соответствующего логина
      $sth = $db->prepare("SELECT '*' FROM `users` WHERE `login` = ?");
      $sth->execute(array($loginu));
      $value = $sth->fetch(PDO::FETCH_ASSOC);
      if (empty($value)) // нет такого пользователя
      {
        //echo $value['id']; Выдать сообщение об ошибках.
        $msg['notexist'] = '<div style="color:red"> Пользователь с логином ' . $loginu . ' не существует!</div>';
      } else if ($value['pass'] != $passu) {
        $msg['wrong'] = '<div style="color:red"> Неверный пароль!</div>';
      } else { //Если все ок, то авторизуем пользователя.
        $_SESSION['login'] = $loginu;
        $_SESSION['uid'] = $value['id'];
        header('Location: index.php');
      }//
    }
}
