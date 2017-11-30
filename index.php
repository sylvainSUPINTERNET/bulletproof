<?php


echo "<h1>Message board - project</h1>";


require './db/dbManager.php';


session_start();
//var_dump($_SESSION);

//session_destroy();


//AUTHENTIFICATION
if(isset($_SESSION['current_user']) && !empty(trim($_SESSION['current_user']))){
    //var_dump($_SESSION);
    ?>
    <br>
        <a href="/bulletproof/logout.php">Logout</a>
    <br>
    <br>
    <?php
}else{
    var_dump("pas connecté");
    ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="text" name="register_name" placeholder="name" required>
        <input type="password" name="register_password" placeholder="password" required>
        <input type="submit" value="Register">
    </form>
    <br>
    <br>
    <?php
    //register
    if(isset($_POST['register_name']) && !empty($_POST['register_name']) && isset($_POST['register_password']) && !empty($_POST['register_password'] && $_SERVER["REQUEST_METHOD"] == "POST")  ){
            $register_name = trim(htmlspecialchars(htmlentities($_POST['register_name'])));
            $register_password = trim(htmlspecialchars(htmlentities($_POST['register_password'])));

        $hash = password_hash($register_password,PASSWORD_DEFAULT);


        $stmt = $db->prepare('
    INSERT INTO `user` (`name`, `password`)
    VALUES (:name, :password)');

        $stmt->bindParam(':name',$register_name, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
        $result = $stmt->execute();
        if($result){
            $_SESSION['current_user'] = $register_name;
            var_dump("register session is open yet ");
            //session_destroy();
        }


    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="text" name="login_name" placeholder="name" required>
        <input type="password" name="login_password" placeholder="password" required>
        <input type="submit" value="Login">
    </form>
    <?php
    //login
    if(isset($_POST['login_name']) && !empty($_POST['login_name']) && isset($_POST['login_password']) && !empty($_POST['login_password']) && $_SERVER["REQUEST_METHOD"] == "POST"  ) {
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
            //var_dump($result);
            echo "<pre>";
            //var_dump("my hash from user password " . $hash);
            $hash_from_db = $result[0]["password"];
            //var_dump($hash_from_db);
            //var_dump(password_verify($login_password, $hash_from_db));
            echo "</pre>";

            if (password_verify($login_password, $hash_from_db)) {
                $_SESSION['current_user'] = $login_name;
                //var_dump($_SESSION);
            }
        }
    }

}



//POST

if(isset($_SESSION['current_user']) && !empty(trim($_SESSION['current_user']))){


    //List user
    $stmt = $db->prepare('
        SELECT name,id FROM `user`
    ');
    $stmt->execute();
    $users = $stmt->fetchAll();
    if(sizeof($users) !== 0) {
        echo "List des users";
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <select id="select" name="user_to_delete" required>
                <?php
                foreach($users as $user){
                    ?>
                    <option value=<?php echo $user["name"] ?>><?php echo $user["name"] ?></option>
                    <?php
                }
                ?>
            </select>
            <input type="submit" value="delete User">

        </form>
        <?php
    }else{
        echo "No users yet !";

    }

    //delete user
    if(isset($_POST['user_to_delete']) && !empty($_POST['user_to_delete']) && $_SERVER["REQUEST_METHOD"] == "POST"){
        $userToDeleleteName = trim(htmlspecialchars(htmlentities($_POST['user_to_delete'])));
        $stmt = $db->prepare('
        DELETE FROM `user` WHERE name =:name
    ');
        $stmt->bindParam(':name', $userToDeleleteName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if($result !== 0){
            //case user deleted hhiself
            if($_SESSION['current_user'] === $userToDeleleteName){
                session_destroy(); //destroy the session
            }
        }
    }


    //list posts (connected)
    ?>
    <br>
    <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"><textarea name="message" required></textarea><input type="submit" value="send message"></form>
    <?php

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



    //add new post
    if(isset($_POST['message']) && !empty($_POST['message'])){
        $message = htmlspecialchars(htmlentities($_POST['message']));
        $message = trim($message);

        $author = $_SESSION['current_user'];

        if($message){
            //get author_id (user id)
            $stmt = $db->prepare('
        SELECT * FROM `user` WHERE `name`= :name
        ');
            $stmt->bindParam(':name', $author, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll();

            if($result[0]["id"]){
                $id_author = $result[0]["id"];

                //insert new message
                $stmt = $db->prepare('
                INSERT INTO `post` (`id_author`, `message`)
                VALUES (:id_author, :message)');

                $stmt->bindParam(':id_author', $id_author);
                $stmt->bindParam(':message', $message);
                $result = $stmt->execute();

            }

        }else{
            echo "error message or name (session) not set !";
        }

    }
}else{
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