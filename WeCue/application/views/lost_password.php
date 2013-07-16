<html>
    <head>
        <title>Wachtwoord vergeten</title>
    </head>
    
    <body>
        <div id="lost">
            <h1>Nieuw wachtwoord opvragen</h1>
            <?php
                echo form_open(base_url('index.php/lost_password/index/send'));
            ?>
            E-mailadres: <input type="email" name="email" value="<?php echo ($_POST ? $_POST['email'] : ''); ?>" ><br />
            <input type="submit" value="Verzenden" ><br />
            <?php echo form_close(); ?>
        </div>
        
        <div>
            <?php
                echo $this -> log -> show_messages();
            ?>
        </div>
    </body>
</html>