<html>
    <head>
        <title>Inloggen</title>
    </head>
    
    <body>
        <div id="login">
            <h1>Inloggen</h1>
            <?php
            echo form_open( base_url( 'index.php/login/index/send' ) );
            ?>
            Gebruikersnaam: <input type="email" name="email" value="<?php echo ($_POST ? $_POST['email']:''); ?>" ><br />
            Wachtwoord: <input type="password" name="password" value="" ><br />
            <input type="submit" value="Inloggen"><br />

            <?php
                echo form_close();
                echo $this -> log -> show_messages();
            ?>
            <p><a href="<?php echo base_url('index.php/login/get_password'); ?>">Wachtwoord vergeten?</a></p>
            <p><a href="<?php echo base_url('index.php/create_user'); ?>">Registreren als trainer</a></p>
        </div>
    </body>
</html>