<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
$msg="";//для сообщений

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) 
{
  // +++Если есть логин в сессии, то пользователь уже авторизован.
  // ++++TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  printf('Пользователь с логином %s уже авторизован.', $_SESSION['login']);
  if (isset($_POST['exit'])) session_destroy();
  // Делаем перенаправление на форму.
  header('Location: form.php');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') 
{
?>

<form action="" method="post">
<label> login:<br />
  <input name="login" value=""/>
  </label><br />

  <label> password:<br />
  <input name="pass" value=""/>
  </label><br />

  <input type="submit" value="Войти" />
  <input name="exit" type="submit" value="Выход" />
</form>

<?php
}
// +++++Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

  // ++++++TODO: Проверть есть ли такой логин и пароль в базе данных.
  $login=$_POST['login'];
  //вход в БД
  $user='u47586'; $pass='3927785';
  $db=new PDO('mysql:host=localhost;dbname=u47586',$user,$pass, array(PDO::ATTR_PERSISTENT=>true));
  //поиск соответствующего логина
  $sth = $dbh->prepare("SELECT '*' FROM `users` WHERE `login` = ?");
  $sth->execute(array($login));
  $value = $sth->fetch(PDO::FETCH_ASSOC);
  if(empty($value))// нет такого пользователя
  {
    echo $value['id']; 
    //+++++++ Выдать сообщение об ошибках.
    printf('Пользователь с логином %s не существует!', $login);
  }
  else
  {// ++++Если все ок, то авторизуем пользователя.
  $_SESSION['login'] = $login;
  //___________________________??????????Зачем он ???__ Записываем ID пользователя.
  $_SESSION['uid'] = 123;
  }
  // ______________??????___Куда__???___Делаем перенаправление.
  header('Location: form.php');

//_________________________________SELECT *  
// $result = $mysqli->query('SELECT * FROM `table_name`'); // запрос на выборку
// while($row = $result->fetch_assoc())// получаем все строки в цикле по одной
// {
//     echo '<p>Запись id='.$row['id'].'. Текст: '.$row['text'].'</p>';// выводим данные
// }
}
