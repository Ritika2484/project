
<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "collection_email";

$conn = mysqli_connect($host , $username , $password , $dbname);

// checking the connections
if($conn){
    echo "connected";
}
else{
    echo "not connected";
}




?>