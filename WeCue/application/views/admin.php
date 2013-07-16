<html>
    <head>
        <title>Adminpagina</title>
    </head>
    
    <body>
        <div>
            <!-- Trainers weergeven -->
            <h2>Zoeken op naam of email</h2>
            <?php
                echo form_open(base_url('index.php/admin/search/send'));
            ?>
            <input type="text" name="query">
            <input type="submit" value="Zoeken">
            <?php
                echo form_close();
                foreach( $users as $user )
                {
                    echo '<a href="' . base_url('index.php/admin/show') . '/' . $user -> id . '">' . $user -> name . '</a><br />';
                }
                echo $this -> log -> show_messages();
            ?>
        </div>
        
        <div>
            <a href="admin/logout">Log uit</a>
        </div>
    </body>
</html>