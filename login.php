<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

echo 'start <br />';
$ar=array();
foreach($_COOKIE as $key => $value) $ar[]=$value;
foreach($ar as $key => $v) echo $v.'  ';

// foreach($_COOKIE as $key => $value) unset($_COOKIE[$key]);
//  //setcookie($key, '', time() - 3600, '/');

$string = array(
  'exitlog1' => '<div style="color:green"> Выход выполнен.</div>',
  'exitlog2' => '<div style="color:green"> Вы не авторизованы.</div>',
  'enterlog' => '<div style="color:green"> Вы уже авторизованы, сначала выйдете из аккаунта.</div>',
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

  <form action="" method="post">
    <label> login:<br />
      <!-- _____________________________________________4)________Заполняем пароль и логин из COOKIE___________________________ -->
      <input name="login" value="<?php if (isset($_COOKIE['login'])) print $_COOKIE['login']; ?>" />
    </label><br />

    <label> password:<br />
      <input name="pass" value="<?php if (isset($_COOKIE['pass'])) print $_COOKIE['pass']; ?>" />
    </label><br />

    <input name="enterlog" type="submit" value="Войти" />
    <input name="registration" type="submit" value="регистрация" />
    <input name="exitlog" type="submit" value="Выход" />
  </form>

  <?php
  $msgs = array('exitlog1', 'exitlog2', 'registration', 'exitlog',);
  foreach ($msgs as $m) if (isset($messages[$m])) print($msg[$m]);
  ?>

<?php
}

//________________________________POST_______________________________
else 
  if (isset($_POST['registration'])) //Регистрация
{
  if (session_status() !== PHP_SESSION_ACTIVE) {
    //header('Location: index.php');
    exit();
  } else setcookie('registration', '1');
} else
if ($_POST['exitlog']) //Выход
{
  if (!empty($_SESSION['login'])) {
    session_destroy();
    setcookie('exitlog1', 1);
  } else setcookie('exitlog2', 1);
} else
    if (isset($_POST['enterlog'])) //Вход
{
  if (session_status() === PHP_SESSION_ACTIVE) setcookie('enterlog', 1);
  else {
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
    } else if ($value['pass'] != md5($passu)) {
      $msg['wrong'] = '<div style="color:red"> Неверный пароль!</div>';
    } else { //Если все ок, то авторизуем пользователя.
      $_SESSION['login'] = $loginu;
      $_SESSION['uid'] = $value['id'];
      //header('Location: index.php');
      exit();
    }
  }
}
//header('Location: login.php');
