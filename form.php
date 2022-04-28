<!DOCTYPE html>
<html>
<head>
<meta charset= "UTF-8">
<link rel="stylesheet" href="stiles.css">
<title>5Back</title>
<style>
.for
{
border: 2px solid rgb(26, 18, 144);
font-size: x-large;
text-align: center;
max-width:420px;
margin: 0 auto;
margin-top:50px;
}
input
{
 margin-top:10px;
 margin-bottom:10px;
 font-size: x-large;
 }
 option
 {
  font-size: x-large;
 }
</style>

</head>
<body>
    <div class="for">
    
    <?php if (isset($messages['change'])) print($messages['change']); ?>
    <?php if (isset($messages['change'])) print($messages['change']); ?>
    <?php if (isset($messages['thesame'])) print($messages['thesame']); ?>
    <h4><form action="" method="POST">
<?php if (isset($messages['save'])) print($messages['save']); ?>
<?php if (isset($messages['name'])) print($messages['name']); ?>
      <label> Имя:<br />
      <input name="name" <?php if (isset($errors['name']) && $errors['name']==2)  print 'style="color:red"'; else print 'style="color:black"'; ?> value="<?php print $values['name']; ?>" /></label><br />
 
<?php if (isset($messages['email'])) print($messages['email']); ?>
      <label> e-mail:<br />
   <input name="email" <?php if (isset($errors['email']) && $errors['email']==2) print 'style="color:red"'; else print 'style="color:black"';?> value="<?php print $values['email']; ?>" type="email" /> </label><br />
       
<?php if (isset($messages['date'])) print($messages['date']); ?>
    <label>  Дата рождения:<br /><input name="date"  value="<?php print $values['date']; ?>" type="date" /></label><br />
             
<?php if (isset($messages['gender'])) print($messages['gender']); ?>
             Пол:<br /> 
             <label><input type="radio" <?php if(isset($values['gender']) && $values['gender'] == "m") print ' checked="checked"'; ?>  checked="checked" name="gender" value="m" /> М</label>
             <label><input type="radio" <?php if(isset($values['gender']) && $values['gender'] == "w") print ' checked="checked"'; ?> name="gender" value="w" /> Ж</label><br />
       
<?php if (isset($messages['hand'])) print($messages['hand']); ?>
       Количество конечностей:<br />
             <label><input type="radio" <?php if(isset($values['hand']) && $values['hand'] == 1) print ' checked="checked"'; ?> checked="checked" name="hand" value="1" />1</label>
             <label><input type="radio" <?php if(isset($values['hand']) && $values['hand'] == 2) print ' checked="checked"'; ?>  name="hand" value="2" /> 2</label>
             <label><input type="radio" <?php if(isset($values['hand']) && $values['hand'] == 3) print ' checked="checked"'; ?>  name="hand" value="3" /> 3</label>
             <label><input type="radio" <?php if(isset($values['hand']) && $values['hand'] == 4) print ' checked="checked"'; ?>  name="hand" value="4" /> 4</label>
             <label><input type="radio" <?php if(isset($values['hand']) && $values['hand'] == 5) print ' checked="checked"'; ?>  name="hand" value="5" /> 5</label>

<?php if (isset($messages['syperpover'])) print($messages['syperpover']); ?>             
             <label>
               Сверхспособности:<br />
               <select name="syperpover[]" multiple>
                 <option value="immortality" selected="selected">бессмертие</option>
                 <option value="passing through walls" >прохождение сквозь стены</option>
                 <option value="levitation">левитация</option>
               </select>
             </label><br />
       
       <label>
      
<?php if (isset($messages['biography'])) print($messages['biography']); ?>
               Биография:<br />
               <textarea name="biography" 
                         <?php if (isset($errors['biography']) && $errors['biography']==2) print 'style="color:red"'; else print 'style="color:black"'; ?>
                         placeholder="<?php print $values['biography']; ?>" ></textarea>
             </label><br />
             <br />
             <?php if (isset($errors['check']) ) print('<div style="color:red"> Необходимо согласия на обработку данных!</div>'); ?>
             <label><input type="checkbox" value="Y"
              name="check" />
               Согласен(а) на обработку данных. </label><br />
             
<input type="submit" value="Отправить" />
</form></h4>
</div>
</body>
</html>

