<html>
    <head>
        <title>Nieuw account</title>
    </head>
    
    <body>
        <div id="create">
            <h1>Nieuw account aanmaken</h1>
            
            <?php 
                echo form_open(base_url( 'index.php/create_user/index/send' ) );
            ?>
            Naam:<input type="text" name="name" value="<?php echo ($_POST ? $_POST['name']:''); ?>" ><br />
            E-mail:<input type="email" name="email" value="<?php echo ($_POST ? $_POST['email']:''); ?>" ><br />
            Wachtwoord:<input type="password" name="password" value="" ><br />
            Adres:<input type="text" name="address" value="<?php echo ($_POST ? $_POST['address']:''); ?>" ><br />
            Woonplaats:<input type="text" name="city" value="<?php echo ($_POST ? $_POST['city']:''); ?>" ><br />
            Telefoon:<input type="tel" name="phone" value="<?php echo ($_POST ? $_POST['phone']:''); ?>" ><br />
            Beschrijving:<input type="text" name="description" value="<?php echo ($_POST ? $_POST['description']:''); ?>" ><br />
            Gewenst uurloon:<input type="number" name="salary" value="<?php echo ($_POST ? $_POST['salary']:''); ?>" ><br />
            Website:<input type="url" name="website" value="<?php echo ($_POST ? $_POST['website']:''); ?>" ><br />
            <!-- Foto:<input type="file" name="photo" value="<?php echo ($_POST ? $_POST['photo']:''); ?>" ><br /> -->
            <input type="submit" value="Cre&euml;er account" ><br />
            <?php
                echo form_close();
                echo $this -> log -> show_messages();
            ?>
        </div>
    </body>
</html>