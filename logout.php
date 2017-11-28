<?php
/**
 * Created by PhpStorm.
 * User: Sylvain
 * Date: 28/11/2017
 * Time: 01:41
 */

session_start(); //to ensure you are using same session
session_destroy(); //destroy the session
header("location:/bulletproof/index.php"); //to redirect back to "index.php" after logging out
exit();

