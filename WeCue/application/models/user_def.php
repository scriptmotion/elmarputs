<?php
/**
 * Created by Chris Bongers & Paul du Long
 */

class User extends CI_Model{

	var $session_name 	= 'crm_login';
	var $user 			= false;
	var $online_marge	= 600; //Seconds till user is registered as offline

	function __construct()
	{
		parent::__construct();
	}

	/**
	**  Login function for the admin part mainly
	**/

	function do_login( $data = array() )
	{
		if( !$data )
		{
			$this -> log -> add_message('Please try to login again, something went wrong');
			return false;
		}

		$this -> db -> where('email', $data['email']);
		$result = $this -> db -> get( 'users' );

		if( $result -> num_rows() )
		{
			/* We know this user he already exists */
			$db_result = $result -> row();
			if( md5( $data['password'] ) != $db_result -> password )
			{
				$this -> log -> add_message('Uw wachtwoord komt niet overeen!');
				return false;
			}
		} else {
			$this -> log -> add_message('Er is geen account gevonden met deze gegevens.');
			return false;
		}

		$session_data = array(
			'email' 	=> $db_result -> email,
			'userid'	=> $db_result -> id,
		);

		unset($_SESSION[$this -> session_name]);
		$_SESSION[$this -> session_name] = $session_data;

		return true;
	}

	function access( $level = 1, $die = true, $redirect = false)
	{
		$redirect = 'login/index';

		$data = $this -> get_login_data();

		if( $data )
		{
			foreach( $data as $key => $value)
				define( $key, $data[$key] );

			$this -> db -> where('id', userid);
			$response = $this -> db -> get('users');

			if( !$response -> num_rows() )
			{
				$this -> log -> add_message('Niet gevonden');
				redirect( $redirect );
				return false;
			}

			$this -> user = $response -> row();
			if( $this -> user -> level < $level )
			{
				$this -> log -> add_message('U heeft niet de juist bevoegdheden om hier te komen!', 'info');
				redirect( $redirect );
			}

			$this -> db -> where('id', $this -> user -> id);
			$this -> db -> update('users', array('online' => time()));

			return true;
		}

		if( $die )
		{
			if( $redirect )
				$_SESSION['redirect_to'] = $redirect;

			redirect('login');
		}
	}

	function get_users()
	{
		$result = $this -> db -> get('users');
		if( !$result -> num_rows() )
		{
			$this -> log -> add_message('Geen gebruikers gevonden');
			return false;
		}

		foreach( $result -> result() as $k => $v )
		{
			$output[$v -> id] = $v;
		}

		return $output;
	}

	function get_user_by_id( $id = false )
	{
		if( !$id )
		{
			$this -> log -> add_message('Deze gebruiker is niet gevonden');
			return false;
		}

		$this -> db -> where('id', $id);
		$result = $this -> db -> get('users');

		return( $result -> num_rows() ? $result -> row() : false );
	}

	function change_password( $data = array() )
	{
		$this -> load -> model('pl_notifications', 'notifications');
		$user = $this -> get_login_data();
		if( !$user )
		{
			$this -> log -> add_message('We hebben u account niet kunnen vinden');
			return false;
		}

		$this -> db -> where('id', $user['userid'] );
		$db = $this -> db -> get('users');
		if( !$db -> num_rows() )
		{
			$this -> log -> add_message('We hebben u account niet gevonden');
			return false;
		}

		$db_row = $db -> row();

		$required_input = array('password', 'new_password_1', 'new_password_2');
		foreach( $required_input as $row )
		{
			if( array_key_exists($row, $data) )
			{
				if( !strlen($data[$row]) )
				{
					$invalid_input = true;
					$invalid_fields[] = $row;
				}
			} else {
				$invalid_input = true;
				$invalid_fields[] = $row;
			}
		}

		if( isset($invalid_input) )
		{
			$this -> log -> add_message( 'Er zijn verplichte velden die nog niet ingevuld zijn', 'error' );
			return false;
		}

		if( md5( $data['password'] ) != $db_row -> password )
		{
			$this -> log -> add_message('Het huidige wachtwoord komt niet overeen');
			return false;
		}

		if( $data['new_password_1'] != $data['new_password_2'] )
		{
			$this -> log -> add_message('Voer aub 2x een zelfde wachtwoord toe');
			return false;
		}

		$sql = array(
			'password'	=> md5( $data['new_password_1'] )
		);

		$this -> db -> where('id', $db_row -> id);
		if( !$this -> db -> update('users', $sql) )
		{
			$this -> log -> add_message('Database fout');
			return false;
		}

		$note = array(
			'to'		=> $user['userid'],
			'action'	=> 'password',
			'message'	=> 'U hebt u wachtwoord gewijzigd'
		);

		$this -> notifications -> add_notification( $note );

		return true;
	}

	function get_login_data()
	{
		$data = (isset($_SESSION[$this -> session_name])?$_SESSION[ $this -> session_name]:false);
		return $data;
	}

	function kill_session()
	{
		$_SESSION[ $this -> session_name ] = '';
		return false;
	}

}