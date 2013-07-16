<?php
    class Ideal extends CI_Model
    {
        private $req_url    = 'https://www.targetpay.com/ideal/start';
        private $check_url  = 'https://www.targetpay.com/ideal/check';
        
        function __construct() 
        {
            parent::__construct();
        }
        
        function request_tp ( $bank = false )
        {
            if( !isset($bank) )
            {
                $this -> log -> add_message('Geen bank-id opgegeven');
                return false;
            }
            
            $fields = array
            (
                'rtlo'          => 99323,
                'bank'          => $bank,
                'description'   => 'Testbetaling',
                'amount'        => 10000,
                'returnurl'     => base_url('index.php/pay/paid')
            );
            
            $response = $this ->send_request($fields, $this -> req_url);
            $this -> log -> add_message('Verzoek verzonden', 'success');
            $values = explode(' ', $response);
            //print_r($values);
            
            // Check if no error code is returned
            if( $values[0] != '000000'  )
            {
                $this -> log -> add_message('Er is iets foutgegaan; foutcode: ' . $values[0]);
                return false;
            }
            
            $values = explode('|', $values[1]);
            print_r($values);
            
            $user = $this -> session -> userdata('user_id');
            if( !$this ->store_trx_id($user, $values[0]))
            {
                return false;
            }
            
            return $values;
        }
        
        function check_paid( $trx_id = false )
        {
            $fields = array
            (
                'rtlo'          => 99323,
                'trxid'         => $trx_id,
                'once'          => 1,
                'test'          => 0
            );
            
            $response = $this ->send_request($fields, $this -> check_url);
            $status = explode(' ', $response);
            if( $status[0] != '000000')
            {
                $this -> log -> add_message('Niet betaald! ' . $status[0]);
                return false;
            }
            
            $this -> log -> add_message($response);
            return true;
        }
        
        function check_trx_id( $trx_id = false )
        {
            $user = $this -> session -> userdata('user_id');
            $db_id = $this -> fetch_trx_id($user);
            
            if( $db_id != $trx_id )
            {
                $this -> log -> add_message('Transactie-id\'s komen niet overeen');
                return false;
            }
            
            $this -> log -> add_message('Transactie-id\'s komen overeen!', 'success');
            return true;
        }
        
        private function send_request( $fields = array(), $url = false )
        {
            $curl = curl_init();
            
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
        
        private function store_trx_id( $user_id = false, $trx_id = false )
        {
            $this -> db -> where('id', $user_id);
            $this -> db -> set('trx_id', $trx_id);
            if( !$this -> db -> update('trainers') )
            {
                $this -> log -> add_message('Er is iets foutgegaan bij het updaten van de database');
                return false;
            }
            
            return true;
        }
        
        // TODO: add catches
        private function fetch_trx_id( $user_id = false )
        {
            $this -> db -> where('id', $user_id);
            $this -> db -> select('trx_id');
            $result = $this -> db -> get('trainers');
            $row = $result -> row();
            
            $this -> log -> add_message('Transactie-id: ' . $row -> trx_id);
            return $row -> trx_id;
        }
    }
?>
