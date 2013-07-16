<?php
/**
 * Created by Paul du Long
 * ScriptMotion - http://www.scriptmotion.nl - pauldulong@gmail.com
 */

class Log extends CI_Model{

	var $allowed_types = array('error','success','info'),
		$session_name = 'ms_logs';

	function __construct()
	{
		parent::__construct();
	}

	//Available types: error, warning, confirm, info
	function add_message(  $message = false, $type = 'error' )
	{

		if( !strlen($message) )
			return false;

		$session_data = $this -> get_messages( false );
		$session_data[] = array(
			'message' => $message,
			'type' => $type
		);

		$this -> session -> set_userdata( $this -> session_name, $session_data );
		return true;

	}

	function add_red_fields( $data = array() )
	{
		if( !$data )
			return false;

		foreach( $data as $row )
			$return_arr[] = $row;

		$this -> session -> unset_userdata( 'red_fields' );
		$this -> session -> set_userdata( 'red_fields', $return_arr );
		return false;
	}

	function get_red_fields()
	{
		$red_fields = $this -> session -> userdata( 'red_fields' );
		$this -> session -> unset_userdata('red_fields');

		return $red_fields;
	}

	function add_log( $action = false, $value = false, $type = 'success', $userid = false)
	{
		$sql = array(
			'user'	 => ( $userid ? $userid : $this -> user -> user -> id ),
			'time'	 => time(),
			'action' => $action,
			'value'	 => $value,
			'type'	 => $type
		);

		if( !$this -> db -> insert('log',$sql) )
		{
			$this -> log -> add_message($this -> lang -> line('ui_db_error'));
			return false;
		}

		return true;
	}

	function get_messages( $unset = true )
	{

		$message = $this -> session -> userdata( $this -> session_name );
		if( $unset )
			$this -> unset_messages();

		return $message;

	}

	function show_messages()
	{
		$messages = $this -> get_messages();

		if( !$messages )
			return false;

		$html = '<div class="grid740">';
		$num = count($messages);

		$a = 1;
		foreach( $messages as $message )
		{
			if( !isset($html) ) $html = '';
			$html .= '
			<div class="albox '.$message['type'].'box">
				'.$message['message'].'
				<a href="#" class="close tips" original-title="close">close</a>
			</div>
			';

			$a++;
		}

		$this -> unset_messages();

		return $html .'</div>';
	}

	function unset_messages()
	{
		$this -> session -> unset_userdata( $this -> session_name );
		return true;
	}

	function make_message( $message, $type = 'info')
	{
		return '<div class="albox '.$type.'box">
				'.$message.'
				<a href="#" class="close tips" original-title="close">close</a>
			</div>';
	}

}