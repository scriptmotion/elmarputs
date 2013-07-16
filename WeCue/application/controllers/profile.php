<?php
    class Profile extends CI_Controller
    {
        function __construct() 
        {
            parent::__construct();
            // Check if allowed to visit this page
            if( !$this -> user -> access(10) )
            {
                redirect(base_url('index.php/login'));
            }
        }
        
        function index( $id )
        {
            $data['user'] =  $this -> user -> get_user_by_id($id);
            $this -> load -> view('profile', $data);
        }
    }
?>
