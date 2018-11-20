<?php
session_start();

unset($_SESSION['loginEmail']);
unset($_SESSION['sinceDate']);
unset($_SESSION['userID']);
unset($_SESSION['mailboxPassword']);

session_destroy();

header("Location: index.php");

?>