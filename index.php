<?php


echo "<h1>Message board - project</h1>";


require './db/dbManager.php';


session_start();
var_dump($_SESSION);

//session_destroy();

if(isset($_SESSION['current_user'])){
    var_dump($_SESSION);
    ?>
        <a href="/bulletproof/logout.php">Logout</a>
    <?php
}else{
    var_dump("pas connecté");
    ?>
    <form method="POST" action="index.php">
        <input type="text" name="register_name" placeholder="name">
        <input type="password" name="register_password" placeholder="password">
        <input type="submit" value="Register">
    </form>
    <br>
    <br>
    <?php
    //todo: register
    if(isset($_POST['register_name']) && !empty($_POST['register_name']) && isset($_POST['register_password']) && !empty($_POST['register_password'])  ){
            $register_name = trim(htmlspecialchars(htmlentities($_POST['register_name'])));
            $register_password = trim(htmlspecialchars(htmlentities($_POST['register_password'])));

        $hash = password_hash($register_password,PASSWORD_DEFAULT);

        //register
        $stmt = $db->prepare('
    INSERT INTO `user` (`name`, `password`)
    VALUES (:name, :password)');

        $stmt->bindParam(':name',$register_name, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
        $result = $stmt->execute();
        if($result){
            $_SESSION['current_user'] = $register_name;
            var_dump("register session open " . $_SESSION['current_user']);
            //session_destroy();
        }


    }
    ?>
    <form method="post" action="index.php">
        <input type="text" name="login_name" placeholder="name">
        <input type="password" name="login_password" placeholder="password">
        <input type="submit" value="Login">
    </form>
    <?php
    //todo: login
    if(isset($_POST['login_name']) && !empty($_POST['login_name']) && isset($_POST['login_password']) && !empty($_POST['login_password'])  ) {
        $login_name = trim(htmlspecialchars(htmlentities($_POST['login_name'])));
        $login_password = trim(htmlspecialchars(htmlentities($_POST['login_password'])));

        $hash = password_hash($login_password,PASSWORD_DEFAULT);

        $stmt = $db->prepare('
        SELECT * FROM `user` WHERE `name`= :name
    ');
        $stmt->bindParam(':name', $login_name, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (sizeof($result) !== 0) {
            var_dump($result);
            echo "<pre>";
            var_dump("my hash from user password " . $hash);
            $hash_from_db = $result[0]["password"];
            var_dump($hash_from_db);
            var_dump(password_verify($login_password, $hash_from_db));
            echo "</pre>";

            if (password_verify($login_password, $hash_from_db)) {
                $_SESSION['current_user'] = $login_name;
                var_dump($_SESSION);
            }
        }
    }

    //todo: display all post
    $stmt = $db->prepare('
        SELECT * FROM `post`
    ');
    $stmt->execute();
    $result = $stmt->fetchAll();
    if(sizeof($result) !== 0) {
        echo "<pre>";
        echo "List des postes";
        echo "\n";
        var_dump($result);
        echo  "</pre>";




    }else{
        echo "no posts yet !";

    }


}


