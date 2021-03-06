<?php
  echo "->mysql.php"; //for debug only; delete this line after the complete development
  //Security principle: Never use the root database account in the web application
  $mysqli = new mysqli('localhost', 'sp2018an' /*Database username*/,
                                    'p4ss@w@d'  /*Database password*/, 
                                    'myblog' /*Database name*/);

  if ($mysqli->connect_error) {
      die('Connect Error (' . $mysqli->connect_errno . ') '
              . $mysqli->connect_error);
  }
  echo "->mysql.php:Debug>Connected to the database"; //for debug only; delete this line after the complete development
  function mysql_checklogin_insecure($username, $password) {
    global $mysqli;
    echo "->mysql.php:Debug>->mysql_checklogin_insecure"; //for debug only; delete this line after the complete development
    $sql = "SELECT * FROM users where username=\"" . $username . "\"";
    $sql.= " and password=password(\"". $password . "\");";
    echo "->mysql.php:Debug>sql=$sql"; //for debug only; delete this line after the complete development
    $result = $mysqli->query($sql);
    if ($result->num_rows == 1) {
    	echo "->mysql.php:Debug>:username/password found"; //for debug only; delete this line after the complete development
      return TRUE;
    } else {
      echo "->mysql.php:Debug>:username/password NOT found"; //for debug only; delete this line after the complete development
    }
    return FALSE;

  }

  function mysql_checklogin_secure ($username, $password) {
    global $mysqli;
    $prepared_sql = "SELECT * FROM users where username= ?"
    . " and password=password(?);";
    if(!$stmt = $mysqli->prepare($prepared_sql))
	echo "Prepared Statement Error";
    $stmt->bind_param("ss", $username,$password);
    if(!$stmt->execute()) echo "Execute Error";
    if(!$stmt->store_result()) echo "Store_result Error";
    if ($stmt->num_rows == 1) return TRUE;
    return FALSE;
}

  function mysql_change_users_password ($username, $newpassword) {
    global $mysqli;
    $prepared_sql = "UPDATE users SET password=password(?) WHERE username=?";
    if(!$stmt = $mysqli->prepare($prepared_sql))
	echo "Prepared Statement Error";
    $stmt->bind_param("ss",$newpassword, $username);
    if(!$stmt->execute()) {echo "Execute Error"; return FALSE;}
    return TRUE;
} 


function show_posts() {
   global $mysqli;
   $sql = "SELECT * FROM posts";
   $result = $mysqli->query($sql);
   if ($result->num_rows > 0){
  //output data of each row
   while($row = $result->fetch_assoc()) {
	$postid = $row["id"];
	echo "<h3>Post " . $postid . "-" . $row["title"]. "</h3>";
	echo $row["text"] . "<br>";
	echo "<a href ='comment.php?postid=$postid'>";
	$sql = "SELECT * FROM comments WHERE postid='$postid';";
	$comments = $mysqli->query($sql);
	if ($comments->num_rows >0) {
		echo $comments->num_rows . "comments </a>";
	}else{
	     echo "Post your first comment </a>";
	}
	}
	}else{ echo "No post in this blog yet <br>";}
	}


function new_post($title,$text) { 

echo " ->new post: creating new post";
global $mysqli;

$prepared_sql = "INSERT INTO posts(title,text) VALUES (?,?);";
if(!$stmt = $mysqli->prepare($prepared_sql))
echo "Prepared statement error";
$stmt->bind_param('ss', $title,$text);

if($stmt->execute()) {echo "Execute Error"; return FALSE;}
return TRUE;

}

function display_singlepost($postid) {
global $mysqli;
echo " Post for id = $postid";
   $sql = "SELECT * FROM posts WHERE id=?";
}


function display_comments($postid){
   global $mysqli;
   echo "Comments for Postid= $postid <br>";
   $prepared_sql = "select title, content from comments where postid=?;";
   if(!$stmt = $mysqli->prepare($prepared_sql))
	echo "Prepared Statement Error";
   $stmt->bind_param('i', $postid);
   if(!$stmt->execute()) echo "Execute failed ";
   $title = NULL;
   $content = NULL;
   if(!$stmt->bind_result($title,$content)) echo "Binding failed ";
   $num_rows = 0;
   while($stmt->fetch()){ 
	echo "Comment title:" . htmlentities($title) . "<br>";
	echo htmlentities($content) . "<br>";
	$num_rows++;
   } 
   if($num_rows==0) echo "No comment for this post. Please post your comment";
}

function new_comment($postid,$title,$content,$commenter){
	global $mysqli;
	$prepared_sql = "INSERT into comments (title,content,commenter,postid) VALUES (?,?,?,?);";
	if(!$stmt = $mysqli->prepare($prepared_sql))
	echo "Prepared Statement Error";
	$stmt->bind_param("sssi", htmlspecialchars($title),
				  htmlspecialchars($content),
				  htmlspecialchars($commenter),$postid);
	if(!$stmt->execute()) {echo "Execute Error"; return FALSE;}
return TRUE;
}

function edit_post($id,$title,$text) { 

echo " ->Editing the post";
global $mysqli;
$prepared_sql = "UPDATE posts SET title=?, text=? WHERE id=?;";

if(!$stmt = $mysqli->prepare($prepared_sql))
echo "Prepared statement error";
$stmt->bind_param('iss', $id,$title,$text);

if(!$stmt->execute()) {echo "Execute Error"; return FALSE;}
return TRUE;

}

function delete_post($id) { 

echo " ->Deleting the post";
global $mysqli;
echo "test";
$prepared_sql = "DELETE from posts WHERE id=?;";
if(!$stmt = $mysqli->prepare($prepared_sql))
echo "Prepared statement error";
$stmt->bind_param('i', $id);

if(!$stmt->execute()) {echo "Execute Error"; return FALSE;}
return TRUE;

}
?>
