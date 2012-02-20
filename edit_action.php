<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Результат редактирования</title>
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

$idBook=$_POST["id_book"];
echo '<a href=edit.php?idBook='.$idBook.'>назад</a><BR /><BR />';

// меняем название книги
$inputBookTitle=@ strip_tags(mysql_escape_string($_POST["book_title"]));
if ($inputBookTitle!==$_POST["book_title_default"]){
	mysql_query("update books set books.title='$inputBookTitle' where books.id_book=$idBook")
		or die ("Невозможно изменить название книги " . mysql_error());
	echo 'меняем название книги <font color=red>"'.$_POST["book_title_default"].'"</font> на <font color=red>"'.$inputBookTitle.'"</font><BR />';
}

// меняем цену книги
$inputBookPrice=@ strip_tags(mysql_escape_string($_POST["book_price"]));
if ($inputBookPrice!==$_POST["book_price_default"]){
	settype($inputBookPrice,"double");
	mysql_query("update books set books.price='$inputBookPrice' where books.id_book=$idBook")
		or die ("Невозможно изменить цену книги " . mysql_error());
	echo 'меняем цену книги с '.$_POST["book_price_default"].' на '.$inputBookPrice.'<BR />';
}

// проверяем были ли отжаты checkbox'ы авторов
$checkAuthor=$_POST["check_author"];
$checkAuthorAll=$_POST["check_author_all"];	
if (isset($checkAuthor)){
	$delAuthorId=array_diff($checkAuthorAll,$checkAuthor);
} else {
	$delAuthorId=$checkAuthorAll;
}

// удаляем авторов 	
if (!empty($delAuthorId)){
	$stringDelAuthorId=implode(",", $delAuthorId);
	mysql_query("delete from books_authors where books_authors.id_author in ($stringDelAuthorId) and books_authors.id_book=$idBook")
		or die ("Невозможно удалить авторов " . mysql_error());
	echo "удаляем следующих авторов (id) из книги: $stringDelAuthorId <BR />";	
}		

// изменяем издателя, если был выбран другой
$selectedPublisher=$_POST["selected_publishers"];
$idPubDefault=$_POST["id_pub_default"];
if ($idPubDefault!==$selectedPublisher){
	mysql_query("update books set books.id_publisher=$selectedPublisher where books.id_book=$idBook")
		or die ("Невозможно поменять издателя " . mysql_error());
	echo 'Издатель был изменен <BR />';
}

// добавляем автора книги
$selectedAuthor=$_POST["selected_authors"];
if (strcmp($selectedAuthor,"default")){
	if (!in_array($selectedAuthor,$checkAuthorAll)){
		mysql_query("insert into books_authors (id_book,id_author) values ('$idBook','$selectedAuthor')")
			or die ("Невозможно добавить автора " . mysql_error());
		echo 'Автор добавлен <BR />';
	}
}
	
mysql_close($db);
?>
</body>
</html>
