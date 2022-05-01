<?php
header('Content-Type: text/html; charset=UTF-8');
$user = 'u47586';
$pass = '3927785';
$parametrs = array('name', 'email', 'date', 'gender', 'hand', 'biography', 'syperpover', 'check');
$strinformassage = array(
  'change' => '<div style="color:green"> Вы можете изменить данные отправленные ранее.</div>',
  'update' => '<div style="color:green"> Данные обновлены.</div>',
  'exit' => '<div style="color:green"> Выход выполнен.</div>',
  'noexit' => '<div style="color:green">Вы не авторизованы.</div>',
);
$messages = array();
foreach ($strinformassage as $name => $str) {
  if (isset($_COOKIE[$name]))
    $messages[$name] = $str;
  else $messages[$name] = '';
  setcookie($name, '', time() - 100000);
}

//____________________________________________________________GET__________________________________________________
//_______________________________________________3)__________Ссылка на login_____________
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (isset($_COOKIE['save'])) {
    if (!empty($_COOKIE['login'])) {
      setcookie('registration', '', time() - 100000);
      $messages['enter'] = '<div style="color:green">Вы можете <a href="login.php">войти</a> с логином <strong>' . $_COOKIE['login'] . '</strong>
      и паролем <strong>' . $_COOKIE['pass'] . '</strong> для изменения данных.' . '</div>';
    }
    setcookie('save', '', time() - 100000);
    setcookie('login', '', time() - 100000);
    setcookie('pass', '', time() - 100000);
    $messages['save'] = '<div style="color:green"> Спасибо, результаты сохранены.</div>';
  } //______________________________________________________________________________________

  $errors = array();
  $values = array();



  foreach ($parametrs as $name) {
    $errorname = $name . '_error';
    if (isset($_COOKIE[$errorname]))
      $errors[$name] = $_COOKIE[$errorname];
  }
  $formassage = array(
    'name' => " Имя", 'email' => " Электронная почта", 'date' => " Дата рождения", 'gender' => " Пол", 'hand' => " Конечности",
    'biography' => " Биография", 'syperpover' => " Суперспособность", 'check' => " "
  );
  foreach ($errors as $name => $val) {
    if (isset($name)) {
      $errorname = $name . "_error";
      if ((int)$errors[$name] == 1) $messages[$name] = '<div style="color:red">Заполните поле' . (string)$formassage[$name] . '.</div>';
      else if ((int)$errors[$name] == 2) $messages[$name] = '<div style="color:red"> Недопустимые символы в поле' . (string)$formassage[$name] . '! </div>';
      setcookie($errorname, '', time() - 3600);
    }
  }

  //________________________________6)_______________________________Авторизованный пользователь____________________________

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
    // загрузить данные пользователя из БД
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
    $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    try {
      $sth1 = $db->prepare("SELECT `*` FROM `users` WHERE `login` = ?");
      $sth1->execute(array($_SESSION['login']));
      $id = $sth1->fetch(PDO::FETCH_ASSOC);

      $sth2 = $db->prepare('SELECT * FROM `MainData` WHERE `id` = ?"'); // запрос данных пользователя
      $sth2->execute(array($id['id']));
      $data = $sth2->fetch(PDO::FETCH_ASSOC);
      // и заполнить переменную $values, предварительно санитизовав.
      foreach ($parametrs as $name) {
        if (isset($data[$name])) //strip_tags?
        {
          $values[$name] = $data[$name];
        } else $values[$name] = '';
      }
    } catch (PDOException $e) {
      print('Error:' . $e->GetMessage());
      exit();
    }
  } //______________________________________________________________________________________________________________________
  else //____________________1)______________Не выполнен вход, заполнение формы из COOKIE____________________________________
  {
    foreach ($parametrs as $name) {
      if (isset($_COOKIE[$name])) //strip_tags
      {
        $values[$name] = strip_tags($_COOKIE[$name]);
      } else $values[$name] = '';
    }
  } //__________________________________________________________________________________________________________________

  include('form.php');
} //____________________________________________POST__________________________________________________________________

else 
if ($_POST['exit'])//Выход
{
  if (session_status() === PHP_SESSION_ACTIVE)
  { 
    setcookie('exit', 1);
    session_destroy();
  } else setcookie('noexit', 1);
} else 
if ($_POST['login']) //Регистрация
{               
  header('Location: login.php');
  exit();
} else 
if ($_POST['send'])//Отправить
{                                    
  $name = $_POST['name'];
  $email = $_POST['email'];
  $date = $_POST['date'];
  $gender = $_POST['gender'];
  $hand = $_POST['hand'];
  $biography = $_POST['biography'];
  $check = $_POST['check'];
  $syperpover = implode(',', $_POST['syperpover']);

  $formpoints = array('gender' => $_POST['gender'], 'hand' => $_POST['hand'], 'syperpover' => $_POST['syperpover'],);
  foreach ($formpoints as  $key => $v) {
    setcookie($key, (string)$v, time() + 30 * 24 * 60 * 60);
  }

  $formdata = array('name' => $_POST['name'], 'email' => $_POST['email'], 'date' => $_POST['date'], 'biography' => $_POST['biography'], 'check' => $_POST['check'],);
  //______________________________________________ Проверяем ошибки._____________________________________________
  $errors = FALSE;
  foreach ($formdata as  $key => $v) {
    $errorname = $key . "_error";
    if (empty($v)) {
      setcookie($errorname, '1', time() + 24 * 60 * 60);
      $errors = TRUE;
    } else 
    if ($key == 'email' && $key != 'check' && filter_var($v, FILTER_VALIDATE_EMAIL) == false) {
      setcookie($errorname, '2', time() + 24 * 60 * 60);
      setcookie($key, $v, time() + 30 * 24 * 60 * 60);
      $errors = TRUE;
    } else if ($key != 'email' && $key != 'check' && preg_match("/[^а-яА-ЯёЁa-zA-Z0-9\ \-_]+/", $v)) {
      setcookie($errorname, '2', time() + 24 * 60 * 60);
      setcookie($key, $v, time() + 30 * 24 * 60 * 60);
      $errors = TRUE;
    } else {
      setcookie($key, $v, time() + 30 * 24 * 60 * 60);
    }
  } //____________________________________________________________________________________________________________

  if ($errors) {
    header('Location: index.php');
    exit();
  } else 
  {
    setcookie('name_error', '', time() - 3600);
    setcookie('email_error', '', time() - 3600);
    setcookie('date_error', '', time() - 3600);
    setcookie('gender_error', '', time() - 3600);
    setcookie('hand_error', '', time() - 3600);
    setcookie('biography_error', '', time() - 3600);
    setcookie('syperpover_error', '', time() - 3600);
    setcookie('No', '', time() - 3600);

    //________________________________________________Авторизованный пользователь Меняет данные______________________________________  

    // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
    if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
      $update;
      $form = array('name' => $_POST['name'], 'email' => $_POST['email'], 'date' => $_POST['date'], 'biography' => $_POST['biography'], 'gender' => $_POST['gender'], 'hand' => $_POST['hand'], 'syperpover' => $_POST['syperpover'],);

      $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
      try {
        $sth1 = $dbh->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
        $sth1->execute(array($_SESSION['login']));
        $id = $sth1->fetch(PDO::FETCH_ASSOC);

        $sth2 = $dbh->prepare('SELECT * FROM `MainData` WHERE `id` = ?"'); // запрос данных пользователя
        $sth2->execute(array($id['id']));
        $data = $sth2->fetch(PDO::FETCH_ASSOC);
        foreach ($parametrs as $name) {
          if (isset($data[$name]) && $data[$name] != $form[$name]) $update[$name] = 1;
        }
        if (empty($update)) {
          $messages['thesame'] = '<div style="color:gray"> Нет изменений.</div>';
        } //Нет изменений в данных
        else {
          //перезаписать данные в БД новыми данными,кроме логина и пароля. подготовить запрос
          $request = 'UPDATE users SET';
          foreach ($parametrs as $name) {
            if (isset($update[$name]))
              $request .= ' ' . $name . ' = ' . $form[$name] . ',';
          }
          //удалить лишнюю запятую и добавить WHERE
          $request = substr($request, 0, -1) . '  WHERE id = ' . $id['id'];
          $update = $db->exec($request); //обновить данные
        }
      } catch (PDOException $e) {
        print('Error:' . $e->GetMessage());
        exit();
      }
      setcookie('update', 1, time() + 30 * 24);
      //header('Location: login.php');exit();
    } else //__________________Неавторизованный пользователь выдаём login_______________________________
    {
      // Генерируем уникальный логин и пароль.
      $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
      $loginuser =  substr(str_shuffle($chars), 0, 3);
      $passuser =  substr(str_shuffle($chars), 0, 3);
      // Сохраняем в Cookies.
      setcookie('login', $loginuser, time() + 30 * 24 * 60 * 60);
      setcookie('pass', $passuser, time() + 30 * 24 * 60 * 60);

      // Сохранение данных формы, логина и хеш md5() пароля в базу данных.
      $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
      try {
        $stmt = $db->prepare("INSERT INTO MainData SET name = ?, email = ?, age=?, gender=?, numberOfLimb=?, biography=?");
        $stmt->execute(array($name, $email, $date, $gender, $hand, $biography));

        $super = $db->prepare("INSERT INTO Superpovers SET superpower=?");
        $super->execute(array($syperpover));
        
        $mdpass=md5($passuser);
        $log = $db->prepare("INSERT INTO users SET login = ?, pass = ?");
        $log->execute(array($loginuser, $mdpass ));
      } catch (PDOException $e) {
        print('Error:' . $e->GetMessage());
        exit();
      }
      setcookie('save', 1, time() + 30 * 24 * 60 * 60);
      // header('Location: login.php');
    } //________________________________________________________________________________________________________________________

  }

  //setcookie('save', '1');
  //header('Location: index.php');
}
