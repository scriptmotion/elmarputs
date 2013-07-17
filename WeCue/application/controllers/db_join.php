<?php
    class Db_join extends CI_Controller
    {
        function __construct() 
        {
            parent::__construct();
        }
        
        function index()
        {
            $result = $this -> user -> get_all_categories();
            if( $result != false )
            {
                print_r($result);
            }
        }
    }
?>
