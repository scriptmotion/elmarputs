<html>
    <head>
        <title>Bank kiezen</title>
    </head>
    
    <body>
        <form name="bankselect" action="<?php echo base_url(); ?>index.php/pay/index/send">
        Kies uw bank:
        <select name=bank onChange="document.bankselect.submit();">
        <script src="https://www.targetpay.com/ideal/issuers-nl.js"></script>
        </select>
        </form>
        <?php
            echo $this -> log -> show_messages();
        ?>
    </body>
</html>