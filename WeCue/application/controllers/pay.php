<?php
    class Pay extends CI_Controller
    {
        function __construct() 
        {
            parent::__construct();
            $this -> user -> access(10);
        }
        
        function index( $send = false )
        {
            if( $send )
            {
                $data = $this -> ideal -> request_tp($_GET['bank']);
                if( $data != false )
                {
                    redirect($data[1]);
                }
            }
            
            $this -> load -> view('choose_bank');
        }
        
        function paid()
        {
            $this -> ideal -> check_trx_id($_GET['trxid']);
            $this -> ideal -> check_paid($_GET['trxid']);
            $this -> load -> view('choose_bank');
        }
    }
?>
