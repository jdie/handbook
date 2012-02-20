<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Редактирование книг</title>
</head>

<body>
<?php
$sdb_name="localhost";
$db_name="imobilco";
$user_name="root";
$db_password="12345";

$db=mysql_connect($sdb_name, $user_name, $db_password)
	or die ("Ошибка при подключении к базе данных " . mysql_error());

mysql_select_db($db_name, $db)
	or die ("Невозможно открыть базу данных $db_name " . mysql_error());

mysql_query('SET NAMES utf8');

// получаем из index.php	
$idBook=$_GET["idBook"];

// запрос выводит конкретную книгу с издательством
$result0=mysql_query("select books.id_book, books.id_publisher, books.title as book_title,books.price,publishers.title as pub_title from books left join publishers on books.id_publisher=publishers.id_publisher where id_book=$idBook")
	or die ("Некорректный запрос: result0 " . mysql_error());

$row=mysql_fetch_array($result0,MYSQL_ASSOC);
$bookTitle=$row["book_title"];
$price=$row["price"];
$pubTitle=$row["pub_title"];
$idPub=$row["id_publisher"];
unset($row);

// запрос выводит авторов для конкретной книги
$result1=mysql_query("select authors.id_author, authors.surname, authors.name, authors.middle_name from authors inner join books_authors on authors.id_author=books_authors.id_author where books_authors.id_book=$idBook")
	or die ("Некорректный запрос: result1 " . mysql_error());

$author=array();

while ($row=mysql_fetch_array($result1,MYSQL_ASSOC)) {
	$author[$row["id_author"]]=$row["surname"]." ".$row["name"]." ".$row["middle_name"];
}
unset($row);

// запрос выводит всех издателей
$result2=mysql_query("select publishers.id_publisher, publishers.title as pub_title_all from publishers order by publishers.title")
	or die ("Некорректный запрос: result2 " . mysql_error());

$pubTitleAll=array();

while ($row=mysql_fetch_array($result2,MYSQL_ASSOC)) {
	$pubTitleAll[$row["id_publisher"]]=$row["pub_title_all"];
}	
unset($row);

// запрос выводит всех авторов
$result3=mysql_query("select id_author, name, surname, middle_name from authors order by surname")
	or die ("Некорректный запрос: result3 " . mysql_error());

$authorAll=array();

while ($row=mysql_fetch_array($result3,MYSQL_ASSOC)) {
	$authorAll[$row["id_author"]]=$row["surname"]." ".$row["name"]." ".$row["middle_name"];
}
unset($row);	

mysql_close($db);
?>
<form action="edit_action.php" name="myform" method="POST">
<?php
echo '<input type="hidden" name="id_book" value="'.$idBook.'" />';
echo '<input type="hidden" name="id_pub_default" value="'.$idPub.'" />';
echo '<input type="hidden" name="book_title_default" value="'.$bookTitle.'" />';
echo '<input type="hidden" name="book_price_default" value="'.$price.'" />';
?>
<table border=0 cellpadding=5 cellspacing=0>
<tr><td>Заголовок: </td><td><input type="text" name="book_title" value="<?php echo $bookTitle; ?>" /><BR /></td></tr>
<tr><td>Цена: </td><td><input type="text" name="book_price" value="<?php echo $price; ?>" /><BR /></td></tr>
<tr><td>Издательство: </td><td>
<select name="selected_publishers" size="1">
<?php	
echo '<option selected="selected" value="'.$idPub.'">'.$pubTitle.'</option>';
foreach ($pubTitleAll as $key => $value){
	echo '<option value="'.$key.'">'.$value.'</option>';
}
?>
</select><BR /></td></tr>
<tr><td>Авторы: </td><td>
<?php
foreach ($author as $key => $value){
echo '<input type="hidden" name="check_author_all[]" value="'.$key.'" />';	
echo '<input type="checkbox" name="check_author[]" value="'.$key.'" checked="checked" />'.$value.'<BR />';
}
?>
</td></tr>
<tr><td>Добавить Автора: </td><td>
<select name="selected_authors" size="1">
<option selected="selected" value="default">Выбрать</option>
<?php
foreach ($authorAll as $key => $value){
	echo '<option value="'.$key.'">'.$value.'</option>';
}
?>
</select><BR /><tr><td>
<tr><td colspan=2><input type="submit" value="Редактировать" /></td></tr>
</table>
</form>

</body>
</html>
