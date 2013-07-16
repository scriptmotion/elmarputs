<html>
    <head>
        <title>Profiel</title>
    </head>
    
    <body>
        <div>
            <?php
                foreach( $user as $key )
                {
                    echo $key . '<br />';
                }
                echo '<a href="'.'">Verwijder ' . $user -> name . '</a>';
            ?>
        </div>
    </body>
</html>
