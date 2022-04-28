<?php
header('Content-Type: text/html; charset=UTF-8');
$user='u47586'; $pass='3927785';
$parametrs=array('name', 'email','date','gender','hand','biography','syperpover','check');
$messages = array();
  
if ($_SERVER['REQUEST_METHOD'] == 'GET') 
{
  if (isset($_COOKIE['save'])) 
  {
    //setcookie('save', '', time()- 100000);
    //$messages['save'] = '<div style="color:green"> Спасибо, результаты сохранены.</div>';
    if (!empty($_COOKIE['pass'])) 
    {
       $messages['change'] = '<div style="color:green">'.sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
       и паролем <strong>%s</strong> для изменения данных.',strip_tags($_COOKIE['login']),strip_tags($_COOKIE['pass'])).'</div>';
    }
     setcookie('save', '', time()- 100000);
     setcookie('login', '', time()- 100000);
     setcookie('pass', '', time()- 100000);
     // Выводим сообщение пользователю.
     $messages['save'] = '<div style="color:green"> Спасибо, результаты сохранены.</div>';
     // Если в куках есть пароль, то выводим сообщение.
  }
  
  $errors = array();
  $values = array();
 
  foreach ($parametrs as $name)
  {
    if(isset($_COOKIE[$name]))//strip_tags
   { $values[$name]=strip_tags($_COOKIE[$name]);}
   else $values[$name]='';
  }
  foreach ($parametrs as $name)
  {
    $errorname=$name .'_error';
    if(isset($_COOKIE[$errorname]))
  $errors[$name]=$_COOKIE[$errorname];
  }
  $formassage=array('name'=>" Имя", 'email'=>" Электронная почта",'date'=>" Дата рождения",'gender'=>" Пол",'hand'=>" Конечности",'biography'=>" Биография",'syperpover'=>" Суперспособность",'check'=>" ");
  foreach ($errors as $name =>$val)
 { 
   if (isset($name))
   {  
     $errorname=$name ."_error";
     if((int)$errors[$name]==1) $messages[$name] = '<div style="color:red">Заполните поле'.(string)$formassage[$name].'.</div>';
    else if((int)$errors[$name]==2) $messages[$name] = '<div style="color:red"> Недопустимые символы в поле'.(string)$formassage[$name].'! </div>';
    setcookie($errorname, '', time() - 3600);
  }
 }
  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  // $values = array();
  // $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
  // TODO: аналогично все поля.

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) 
      {
       // TODO: загрузить данные пользователя из БД
       $db=new PDO('mysql:host=localhost;dbname=u47586',$user,$pass, array(PDO::ATTR_PERSISTENT=>true));
       try{
       $sth1 = $dbh->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
       $sth1->execute(array($_SESSION['login']));
       $id = $sth1->fetch(PDO::FETCH_ASSOC);
       
       $sth2 = $dbh->prepare('SELECT * FROM `MainData` WHERE `id` = ?"'); // запрос данных пользователя
       $sth2->execute(array($id['id']));
       $data=$sth2->fetch(PDO::FETCH_ASSOC);
       // и заполнить переменную $values, предварительно санитизовав.
       foreach ($parametrs as $name)
        {
          if(isset($data[$name]))//strip_tags
          { $values[$name]=strip_tags($data[$name]);}
          else $values[$name]='';
        }
       printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
       }
       catch(PDOException $e){print('Error:'.$e->GetMessage());exit();}
      } 
 
 include('form.php');
}
else
{
  $name=$_POST['name'];
  $email=$_POST['email'];
  $date=$_POST['date'];
  $gender=$_POST['gender'];
  $hand=$_POST['hand'];
  $biography=$_POST['biography'];
  $check=$_POST['check'];
  $syperpover=implode(',',$_POST['syperpover']);
  
  $formpoints=array(
    'gender'=>$_POST['gender'],
    'hand'=>$_POST['hand'],
    'syperpover'=>$_POST['syperpover'],
  );  
  foreach ($formpoints as  $key =>$v)
  {
    setcookie($key, $v, time() + 30 * 24 * 60 * 60);
  }
   $formdata=array(
    'name'=>$_POST['name'],
    'email'=>$_POST['email'],
    'date'=>$_POST['date'],
    'biography'=>$_POST['biography'],
    'check'=>$_POST['check'],
     );
  // Проверяем ошибки.
  $errors = FALSE;
  foreach ($formdata as  $key =>$v)
  {
  $errorname=$key ."_error";
  if (empty($v))
  {
    setcookie($errorname, '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else if ($key=='email' && $krey!='check' && filter_var($v, FILTER_VALIDATE_EMAIL) == false)
  {
    setcookie( $errorname, '2', time() + 24 * 60 * 60);
    setcookie($key, $v, time() + 30 * 24 * 60 * 60);
    $errors = TRUE;
  }
  else if ($key !='email' && $krey!='check' && preg_match("/[^а-яА-ЯёЁa-zA-Z0-9\ \-_]+/",$v) ) 
  {
    setcookie( $errorname, '2', time() + 24 * 60 * 60);
    setcookie($key, $v, time() + 30 * 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
   setcookie($key, $v, time() + 30 * 24 * 60 * 60);
  }
 }

  if ($errors) {
    header('Location: index.php');
    exit();
  }
  else {
    setcookie('name_error', '',time() - 3600);
    setcookie('email_error', '',time() - 3600);
    setcookie('date_error', '',time() - 3600);
    setcookie('gender_error', '',time() - 3600);
    setcookie('hand_error', '',time() - 3600);
    setcookie('biography_error', '',time() - 3600);
    setcookie('syperpover_error', '',time() - 3600);
    setcookie('No','',time() - 3600);
    
  //____________________________________________________________________________________________
  if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login']))
  {
   // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
   $change;
   $form=array(
    'name'=>$_POST['name'],
    'email'=>$_POST['email'],
    'date'=>$_POST['date'],
    'biography'=>$_POST['biography'],
    'gender'=>$_POST['gender'],
    'hand'=>$_POST['hand'],
    'syperpover'=>$_POST['syperpover'],
      ); 
   $db=new PDO('mysql:host=localhost;dbname=u47586',$user,$pass, array(PDO::ATTR_PERSISTENT=>true));
   try{
    $sth1 = $dbh->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
    $sth1->execute(array($_SESSION['login']));
    $id = $sth1->fetch(PDO::FETCH_ASSOC);
    
    $sth2 = $dbh->prepare('SELECT * FROM `MainData` WHERE `id` = ?"'); // запрос данных пользователя
    $sth2->execute(array($id['id']));
    $data=$sth2->fetch(PDO::FETCH_ASSOC);
    foreach ($parametrs as $name)
     {
       if(isset($data[$name]) && $data[$name]!=$form[$name])
       $change[$name]=1;
     }
    if(empty(change)){ $messages['thesame']='<div style="color:gray"> Нет изменений.</div>';}//Нет изменений в данных
     else
     {
      // TODO: перезаписать данные в БД новыми данными,
      // кроме логина и пароля.
      //подготовить запрос
      $request = "UPDATE users SET ";
      foreach ($parametrs as $name)
      {
        if(isset($change[$name]))
        $request.' '.$name.' = '.$form[$name].',';
      }
      substr_replace($request,0,–1);//удалить лишнюю запятую
      $request.'  WHERE id = '.$id['id'];
      $update = $db->exec($request);//обновить данные
     }
    }catch(PDOException $e)
    {print('Error:'.$e->GetMessage());exit();}
  } 
 else 
 {
  // Генерируем уникальный логин и пароль.
  // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
  $login = random_bytes(4);
  $pass = random_bytes(3);
  // Сохраняем в Cookies.
  setcookie('login', $login);
  setcookie('pass', $pass);

  // Сохранение данных формы, логина и хеш md5() пароля в базу данных.
  $db=new PDO('mysql:host=localhost;dbname=u47586',$user,$pass, array(PDO::ATTR_PERSISTENT=>true));
  try{
  $stmt=$db->prepare("INSERT INTO MainData SET name = ?, email = ?, age=?, gender=?, numberOfLimb=?, biography=?");
  $stmt->execute(array($name, $email, $date, $gender, $hand, $biography));
  
  $super=$db->prepare("INSERT INTO Superpovers SET superpower=?");
  $super->execute(array($syperpover));

  $log=$db->prepare("INSERT INTO users SET login = ?, pass = ?");
  $log->execute(array($login, md5($pass)));
  }
  catch(PDOException $e)
  {
   print('Error:'.$e->GetMessage());
   exit();
  }
 }

    setcookie('save', '1');
    header('Location: index.php');
  
}
