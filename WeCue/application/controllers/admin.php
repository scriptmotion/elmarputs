<?php
    class Admin extends CI_Controller
    {
        public $users = array();
        public $results_per_page = 3;
        
        function __construct() 
        {
            parent::__construct();
            if( !$this -> user -> access( 1 ) )
            {
                redirect(base_url('index.php/login'));
            }
            //$this -> users = $this -> user -> get_all(($this -> uri -> segment(2) - 1) * $this -> results_per_page, $this -> results_per_page);
            $this -> users = $this -> user -> get_all(0, $this -> results_per_page);
        }

        function index()
        {
            $data['users'] = $this -> users;
            $this -> load -> view('admin', $data);
        }
        
        function logout()
        {
            if( $this -> user -> logout() )
            {
                    redirect(base_url('index.php/login'));
            }
        }
        
        function show( $data )
        {
            $redirect = 'index.php/profile/index/' . $data;
            redirect(base_url($redirect));
        }
        
        function search( $send )
        {
            if( $send )
            {
                $result = $this -> user -> search($_POST);
                if( $result )
                {
                    $output['users'] = $result;
                    //$this -> load -> view('admin', $output);
                }
            }
        }
    }
?>