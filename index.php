<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Главная страница</title>
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

$result0=mysql_query("select books.id_book, books.title as book_title, publishers.title as pub_title from books left join publishers on books.id_publisher = publishers.id_publisher order by books.id_book")
	or die ("Некорректный запрос: result0 " . mysql_error());

$idBook0=array();
$bookTitle=array();
$pubTitle=array();

while ($row=mysql_fetch_array($result0,MYSQL_ASSOC)) {
	$idBook0[]=$row["id_book"];
	$bookTitle[$row["id_book"]]=$row["book_title"];
	$pubTitle[$row["id_book"]]=$row["pub_title"];
}

$stringIdBook=implode(",", $idBook0);

$result1=mysql_query("select authors.surname, authors.name, authors.middle_name,books_authors.id_book from authors inner join books_authors on authors.id_author=books_authors.id_author where books_authors.id_book in ($stringIdBook) order by books_authors.id_book")
	or die ("Некорректный запрос: result1 " . mysql_error());

unset($row);
$idBook1=array();
$author=array();

while ($row=mysql_fetch_array($result1,MYSQL_ASSOC)) {
	$idBook1[]=$row["id_book"];
	$author[]=$row["surname"]." ".$row["name"]." ".$row["middle_name"];
}

$tmp=0;
$tmpAuthor="";
// внешний цикл - проходимся по id_book из таблицы books 
for ($i=0; $i<count($idBook0);$i++){
	/* если в таблице books_authors нет книги из таблицы
	 * books, то выводим книгу из books без авторов
	 */
	if ($idBook0[$i]!==$idBook1[$tmp]){
		echo '<font size="3" color="red">'.$bookTitle[$idBook0[$i]].'</font>
			<font size="1"> (_удалить_ <a href=edit.php?idBook='.$idBook0[$i].'>_редактировать_</a>)</font><BR />';
		echo '<font size="2" color="blue">'.$pubTitle[$idBook0[$i]].'</font><BR /><BR />';
		continue;
	}
	/* внутренний цикл - проходимся по id_book из таблицы books_authors
	 * т.к. я решил отсортировать выборки по id_book, то нам не обязательно
	 * каждый раз проходить весь цикл (цикл завершается как только следующий
	 * id_book из books_authors не равен id_book из books). К тому же цикл
	 * начинается с того элемента на котором был закончен в прошлый раз
	 */ 	
	for ($j=$tmp; $j<count($idBook1);$j++){
		if ($idBook0[$i]==$idBook1[$j]){
			if($idBook1[$j]==$idBook1[$j+1]){
				$tmpAuthor.=$author[$j].", ";
			} else {			
				echo '<font size="3" color="red">'.$bookTitle[$idBook0[$i]].'</font>
					<font size="1"> (_удалить_ <a href=edit.php?idBook='.$idBook0[$i].'>_редактировать_</a>)</font><BR />';
				echo '<font size="2" color="blue">'.$pubTitle[$idBook0[$i]].'</font><BR />';
				echo '<font size="1">'.$tmpAuthor."".$author[$j].'</font><BR /><BR />';	
				$tmpAuthor="";
			}
		} else {
			$tmp=$j++;
			break;	
		}	
	}	
}	

mysql_close($db);

?>
</body>
</html>
