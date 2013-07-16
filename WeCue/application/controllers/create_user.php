<?php

    class Create_user extends CI_Controller
    {
        function __construct() 
        {
            parent::__construct();
        }
        
        function index( $send = false )
        {
            if( $send )
            {
                if( $this -> user -> create_user( $_POST ) )
                {
                    redirect( base_url( 'index.php/login' ) );
                }
            }
            
            $this -> load -> view ( 'create_user' );
        }
    }

?>
