<?php
$ar = array();
foreach ($_COOKIE as $key => $value) $ar[$key] = $value;
foreach ($ar as $key => $v) echo $key . '_index:' . ' ' . $v . '<br/>';

header('Content-Type: text/html; charset=UTF-8');
$user = 'u47586';
$pass = '3927785';
$parametrs = array('name', 'email', 'date', 'gender', 'hand', 'biography', 'syperpover', 'check');
$messages = array();


//___________________________________________________GET__________________
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (isset($_COOKIE['save'])) {
    if (isset($_COOKIE['login'])) {
      $messages['enter'] = '<div class="position-absolute top-0 start-50" style="color:green">Вы можете <a href="login.php">войти</a> с логином <strong>' . $_COOKIE['login'] . '</strong>
      и паролем <strong>' . $_COOKIE['pass'] . '</strong> для изменения данных.' . '</div>';
      setcookie('save', '', time() - 100000);
      //$messages['save'] = '<div style="color:green"> Спасибо, результаты сохранены.</div>';
    }
  }
  if (isset($_COOKIE['all_OK'])) $messages['user'] = '<div style="border: 2px solid rgb(26, 18, 144)" class="position-absolute top-0  end-0"> Пользователь: ' . $_COOKIE['all_OK'] . '</div>';
  else  $messages['user'] = '';

  $errors = array();
  $values = array();

  $strinformassage = array(
    'change' => '<div style="color:green" class="position-absolute top-0 start-50"> Вы можете изменить данные отправленные ранее.</div>',
    'update' => '<div style="color:green" class="position-absolute top-0 start-50"> Данные обновлены.</div>',
    'exit' => '<div style="color:green" class="position-absolute top-0 start-50"> Выход выполнен.</div>',
    'noexit' => '<div style="color:green" class="position-absolute top-0 start-50">Вы не авторизованы.</div>',
  );
  foreach ($strinformassage as $name => $str) {
    if (isset($_COOKIE[$name]))
      $messages[$name] = $str;
    else $messages[$name] = '';
    setcookie($name, '', time() - 100000);
  }

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

  //_________________________________________________Авторизованный пользователь____________________________

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && isset($_COOKIE['all_OK'])) 
  {
    // загрузить данные пользователя из БД
    setcookie('avtoriz_uzer',1);
    //printf('Вход с логином ' . $_SESSION['login'] . ', uid ' . $_SESSION['uid']);
    $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    try {
      $sth1 = $db->prepare("SELECT `*` FROM `users` WHERE `login` = ?");
      $sth1->execute(array($_COOKIE['all_OK']));
      $id = $sth1->fetch(PDO::FETCH_ASSOC);

      $sth2 = $db->prepare('SELECT * FROM `MainData` WHERE `id` = ?'); // запрос данных пользователя
      $sth2->execute(array($id['id']));
      $data = $sth2->fetch(PDO::FETCH_ASSOC);
      // и заполнить переменную $values, предварительно санитизовав.
      
    } catch (PDOException $e) {
      print('Error:' . $e->GetMessage());
      exit();
    }
    $parametrs2 = array('name'=>'name', 'email'=>'email', 'date'=>'age', 'gender'=>'gender', 
    'hand'=>'numberOfLimb', 'biography'=>'biography',);
    // = ?, email = ?, age=?, gender=?, numberOfLimb=?, biography=?"
    foreach ($parametrs2 as $name=>$v) 
    {
      if (isset($data[$v]))
      {
        $values[$name] = filter_var($data[$v], FILTER_SANITIZE_SPECIAL_CHARS);
      } else $values[$name] = '';
    }
  } 
   else //__________________________________Не выполнен вход, заполнение формы из COOKIE____________________________________
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
else {

  $sendind = 0;
  $exitind = 0;
  if (isset($_POST['butt']))
    switch ($_POST['butt']) {
      case 'Отправить':
        $sendind = 1;
        break;
      case 'Выход':
        $exitind = 1;
        break;
    }
  if ($exitind == 1) {
    if (session_status() == PHP_SESSION_ACTIVE) {
      setcookie('exit', 1);
      session_destroy();
      header('Location: index.php');
      exit();
    } else {
      setcookie('noexit', 1);
      header('Location: index.php'); //Выход
      exit();
    }
  } else 
    if ($sendind == 1) //Отправить
  {
    setcookie('butt_send_ind', 1);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $date = $_POST['date'];
    $gender = $_POST['gender'];
    $hand = $_POST['hand'];
    $biography = $_POST['biography'];
    $check = $_POST['check'];
    $syperpover = implode(',', $_POST['syperpover']);

    $formpoints = array('gender' => $gender, 'hand' => $hand, 'syperpover' => $syperpover,);
    foreach ($formpoints as  $key => $v) {
      setcookie($key, $v, time() + 30 * 24 * 60 * 60);
    }

    $formdata = array(
      'name' => $name, 'email' => $email, 'date' => $date, 'biography' => $biography,
      'check' => $check,
    );
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
      header('Location: index.php'); //Errors
      exit();
    } else {
      $delcookie = array('name_error', 'email_error',  'date_error', 'gender_error',  'hand_error',  'biography_error',  'syperpover_error',);
      foreach ($delcookie as $v) setcookie($v, '', time() - 3600);
      //setcookie('No', '', time() - 3600);

      //____________________________Авторизованный пользователь Меняет данные______________________________________  

      // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
      if (isset($_SESSION['login'])) {
        setcookie('session_login', $_SESSION['login']);
        $update;
        $form = array(
          'name' => $name,
          'email' => $email,
          'age' => $date,
          'gender' => $gender,
          'numberOfLimb' => $hand,
          'biography' => $biography,
        ); 

        $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
        try {
          $sth1 = $dbh->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
          $sth1->execute(array($_SESSION['login']));
          $id = $sth1->fetch(PDO::FETCH_ASSOC);

          $sth2 = $dbh->prepare("SELECT * FROM `MainData` WHERE `id` = ?"); // запрос данных пользователя
          $sth2->execute(array($id['id']));
          $data = $sth2->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          print('Error:' . $e->GetMessage());
          exit();
        }
        foreach ($form as $name => $v) {
          if (isset($data[$name]) && $data[$name] != $form[$name]) $update[$name] = 1;
        }
        if (empty($update)) {
          setcookie('thesame', 1);
          $messages['thesame'] = '<div style="color:gray" class="position-absolute top-0 start-50"> Нет изменений.</div>';
        } //Нет изменений в данных
        else {
          //перезаписать данные в БД новыми данными,кроме логина и пароля. подготовить запрос
          $request = array();
          foreach ($form as $name => $v) {
            if (isset($update[$name]))
              $request[$name] = $data[$name];
            else $request[$name] = $v;
          }
          try {
            $stmt = $db->prepare("UPDATE MainData SET name = ?, email = ?, age=?, gender=?, numberOfLimb=?, biography=?");
            $stmt->execute($request);

            $super = $db->prepare("INSERT INTO Superpovers SET superpower=?");
            $super->execute(array($syperpover));

            //$update = $db->exec($request); //обновить данные
          } catch (PDOException $e) {
            print('Error:' . $e->GetMessage());
            exit();
          }
          setcookie('update', 1, time() + 30 * 24);
          header('Location: index.php');
          exit(); //Update
        }
      } else //__________________Неавторизованный пользователь выдаём login_______________________________
      {
        echo 'Генерация пароля  <br/>';
        // Генерируем уникальный логин и пароль.
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $loginuser =  substr(str_shuffle($chars), 0, 3);
        $passuser =  substr(str_shuffle($chars), 0, 3);
        // Сохраняем в Cookies.

        // Сохранение данных формы, логина и хеш md5() пароля в базу данных.
        $db = new PDO('mysql:host=localhost;dbname=u47586', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
        try {
          $stmt = $db->prepare("INSERT INTO MainData SET name = ?, email = ?, age=?, gender=?, numberOfLimb=?, biography=?");
          $stmt->execute(array($name, $email, $date, $gender, $hand, $biography));

          $super = $db->prepare("INSERT INTO Superpovers SET superpower=?");
          $super->execute(array($syperpover));

          $mdpass = password_hash($passuser, PASSWORD_DEFAULT);
          $log = $db->prepare("INSERT INTO users SET login = ?, pass = ?");
          $log->execute(array($loginuser, $mdpass));
        } catch (PDOException $e) {
          print('Error:' . $e->GetMessage());
          exit();
        }
        setcookie('save', 1);
        setcookie('login', $loginuser, time() + 30 * 24 * 60 * 60);
        setcookie('pass', $passuser, time() + 30 * 24 * 60 * 60);
        header('Location: index.php');
        exit(); //или ссылка
      }
    }
  }
}
