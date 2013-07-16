<?php
	class Login extends CI_Controller
	{		
		function index( $send = false )
		{
			if( $send )
			{
                            if( $this -> user -> login ( $_POST ) )
                            {
                                redirect( base_url('index.php/profile/index/' . $this -> session -> userdata('user_id')));
                            }
			}
			
			$this -> load -> view('login');	
		}
                
                function get_password()
                {
                    redirect(base_url('index.php/lost_password'));
                }
	}
?>