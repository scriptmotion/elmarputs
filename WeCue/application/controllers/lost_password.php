<?php
    class Lost_password extends CI_Controller
    {
        function __construct() 
        {
            parent::__construct();
        }
        
        function index( $send = false )
        {
            if( $send )
            {
                if( $this -> user -> get_password($_POST) )
                {
                    $this -> log -> add_message('Nieuw wachwoord naar emailadres verzonden!', 'success');
                    redirect(base_url('index.php/login'));
                }
            }
            $this -> load -> view('lost_password');
        }
    }
?>
