<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();
$statement = $handler->query("SELECT stranka FROM activ_user WHERE user='$user'");
$statement->execute();

$n = $statement->rowCount();
if ($n != 0) {
    $successUpdate = $handler->exec("UPDATE activ_user SET user = '$user',stranka = 'null' WHERE user = '$user'");
} else {
    $successInsert = $handler->exec("INSERT INTO activ_user (user,stranka) VALUES ('$user','null')");
}

