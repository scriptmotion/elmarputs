<?php
    class User extends CI_Model
    {
        function __construct() 
        {
            parent::__construct();
        }

        function login( $data = array() )
        {
                // Check if data is not empty
                if( empty( $data['email']) || empty($data['password'] ) )
                {
                        $this -> log -> add_message('Een of meerdere velden zijn niet ingevuld,
                            probeer opnieuw!', 'error');
                        return false;
                }

                $this -> db -> where('email', $data['email']);
                $result = $this -> db -> get('trainers');

                // Check if query returns something
                if( $result -> num_rows() )
                {
                    $row = $result -> row();
                    // Check if password matches database entry
                    if( $row -> password != md5( $data['password'] ) )
                    {
                            $this -> log -> add_message('Wachtwoord onjuist', 'error');
                            return false;
                    }
                }
                else
                {
                    $this -> log -> add_message('Geen gebruiker gevonden met dit e-mailadres', 'error');
                    return false;
                }

                // Log in user
                $session_data = array
                (
                        'email' 	=> $row -> email,
                        'user_id'	=> $row -> id,
                        'logged_in'     => true
                );

                $this -> session -> set_userdata( $session_data );
                $this -> log -> add_message('Login succesvol!', 'success');
                return true;
        }

        function logout()
        {
                $this -> session -> sess_destroy();
                return true;
        }
        
        function access( $level = 1 )
        {
            $id = $this -> session -> userdata('user_id');
            
            $this -> db -> where('id', $id);
            $result = $this -> db -> get('trainers');
            if( !$result -> num_rows() )
            {
                $this -> log -> add_message('Niet aangemeld', 'error');
                return false;
            }
            
            $user = $result -> row();
            if( $user -> id > $level )
            {
                $this -> log -> add_message('U heeft niet de juiste rechten om deze pagina weer te geven', 'error');
                return false;
            }
                
            return true;
        }
        
        function get_all( $offset = 0, $limit = 30 )
        {
            $this -> db -> select('id, name');
            $result = $this -> db -> get('trainers', $limit, $offset);
            
            if( !$result -> num_rows() )
            {
                $this -> log -> add_message('Geen gebruikers gevonden', 'error');
                return false;
            }
            
            return $result -> result();
        }
        
        function get_user_by_id( $data )
        {
            $this -> db -> where('id', $data);
            $result = $this -> db -> get('trainers');
            
            if( !$result -> num_rows() )
            {
                $this -> log -> add_message('Deze gebruiker is niet gevonden', 'error');
                return false;
            }
            
            return $result -> row();
        }
        
        function get_all_categories()
        {
            $this -> db -> select('categories.category, trainers.name, trainers.email');
            $this -> db -> from('categories');
            //$this -> db -> group_by('categories.category');
            $this -> db -> join('trainers', 'categories.id = trainers.category', 'left');
            $result = $this -> db -> get();
            
            if( !$result -> num_rows() )
            {
                $this -> log -> add_message('Geen categorie&euml;n');
                return false;
            }
            
            $output = $result -> result_array();
            print_r($output);
            echo '<br /><br />';
            for( $i = 0; $i < count($output); $i++ )
            {
                $output[$i] =   array
                                (
                                    'data'      => array
                                        (
                                            'category' => $output[$i]['category']
                                        ),
                                    'results'   => array
                                        (
                                            'name'  => $output[$i]['name'],
                                            'email' => $output[$i]['email']
                                        )
                                );
            }
            
            return $output;
        }
        
        function search( $data = array() )
        {
            if( empty($data['query']) )
            {
                $this -> log -> add_message('Geen zoekterm ingevoerd', 'error');
                return false;
            }
            
            $this -> db -> like('name', $data['query']);
            $this -> db -> or_like('email', $data['query']);
            $result = $this -> db -> get('trainers');
            
            if( !$result -> num_rows() )
            {
                $this -> log -> add_message('Geen resultaten gevonden', 'error');
                return false;
            }
            
            return $result -> result();
        }

        function create_user( $data = array() )
        {
            // Check if user already exists
            $this -> db -> where( 'email', $data['email'] );
            $result = $this -> db -> get( 'trainers' );
            if( $result -> num_rows() )
            {
                    $this -> log -> add_message('Gebruiker bestaat reeds, probeer opnieuw', 'error');
                    return false;
            }

            $input = array
            (
                'email'         => $data['email'],
                'password'      => md5($data['password']),
                'name'          => $data['name'],
                'address'       => $data['address'],
                'city'          => $data['city'],
                'description'   => $data['description'],
                'phone'         => $data['phone'],
                'salary'        => $data['salary'],
                'website'       => $data['website'],
                //'photo'         => $data['photo']
                'level'         => 10
            );
            
            if( !$this -> db -> insert('trainers', $input) )
            {
                $this -> log -> add_message('Aanmaken gebruiker mislukt', 'error');
                return false;
            }
            
            $this -> email -> from('elmar@scriptmotion.nl');
            $this -> email -> to($input['email']);
            $this -> email -> subject('Nieuwe account aangemaakt voor WeCue');
            $message = 'Hallo ' . $input['name'] . ',
                er is een nieuwe WeCue traineraccount aangemaakt met de volgende gegevens:
                ' . $input['name'] . '
                ' . $input['email'] . '
                ' . $input['address'] . '
                ' . $input['city'] . '
                ' . $input['description'] . '
                ' . $input['phone'] . '
                ' . $input['salary'] . '
                ' . $input['website'];
            $this -> email -> message($message);
            if( !$this -> email -> send() )
            {
                $this -> log -> add_message('Er is geen email verzonden door een fout, probeer opnieuw', 'error');
                return false;
            }
            
            $this -> log -> add_message('Gebruiker succesvol aangemaakt!', 'success');
            return true;
        }

        function delete_user( $data = array() )
        {
                // Check if data is not empty
                if( empty( $data['username'] ) || empty( $data['password'] ) )
                {
                        echo 'Een of meerdere velden is/zijn niet ingevuld<br />';
                        return false;
                }

                // Check if user exists
                $this -> db -> where( 'username', $data['username'] );
                $result = $this -> db -> get( 'users' );
                if( !$result -> num_rows() )
                {
                        echo 'Gebruiker bestaat niet; probeer opnieuw<br />';
                        return false;
                }

                // Check if password matches database entry
                $row = $result -> row();
                if( $row -> password != $data['password'] )
                {
                        echo 'Wachtwoord onjuist<br />';
                        return false;
                }

                $this -> db -> where( 'username', $data['username'] );
                $this -> db -> delete( 'users' );

                return true;
        }

        function change_user( $data = array() )
        {
                // Check if data is not empty
                if( empty( $data['old_username'] ) || empty( $data['old_password'] ) || empty( $data['new_username'] ) || empty ( $data['old_username'] ) )
                {
                        echo 'Een of meerdere velden is/zijn niet ingevuld<br />';
                        return false;
                }

                // Check if user exists
                $this -> db -> where( 'username', $data['old_username'] );
                $result = $this -> db -> get( 'users' );
                if( !$result -> num_rows() )
                {
                        echo 'Gebruiker bestaat niet; probeer opnieuw<br />';
                        return false;
                }

                // Check if password matches database entry
                $row = $result -> row();
                if( $row -> password != $data['old_password'] )
                {
                        echo 'Wachtwoord onjuist<br />';
                        return false;
                }

                // Check if new username isn't already taken
                $this -> db -> where( 'username', $data['new_username'] );
                $result = $this -> db -> get( 'users' );
                if ( $result -> num_rows() )
                {
                        echo 'Gebruikersnaam al in gebruik<br />';
                        return false;
                }

                // Update user
                $input = array
                (
                        'username' => $data['new_username'],
                        'password' => $data['new_password']
                );

                $this -> db -> where( 'username', $data['old_username'] );
                $this -> db -> update( 'users', $input );

                return true;
        }
        
        function get_password( $data = array() )
        {
            if( empty($data['email']) )
            {
                $this -> log -> add_message('Vul uw email in om een nieuw wachtwoord op te vragen!', 'error');
                return false;
            }
            
            // Check if a valid email address is submitted
            
            $this -> db -> where('email', $data['email']);
            $result = $this -> db -> get('trainers');
            
            if( !$result -> num_rows )
            {
                $this -> log -> add_message('Gebruiker niet gevonden!', 'error');
                return false;
            }
            
            // Generate a new password
            $new_password = $this -> generate_string(10);
            
            // Update db record
            $this -> db -> where('email', $data['email']);
            $this -> db -> set('password', md5($new_password));
            
            if( !$this -> db -> update('trainers') )
            {
                $this -> log -> add_message('Er is iets misgegaan', 'error');
                return false;
            }
            
            // Email new password to user
            $this -> email -> from('elmar@scriptmotion.nl');
            $this -> email -> to($data['email']);
            $this -> email -> subject('Nieuw wachtwoord aangemaakt voor WeCue');
            
            $row = $result -> row();
            $message = 'Hallo ' . $row -> name . ', er is een nieuw wachtwoord voor uw account aangemaakt.
                Het wachtwoord is: ' . $new_password;
            $this -> email -> message($message);
            if( !$this -> email -> send() )
            {
                $this -> log -> add_message('Er is geen email verzonden door een fout, probeer opnieuw', 'error');
                return false;
            }
            
            $this -> log -> add_message($new_password, 'success');
            return true;
        }
        
        private function generate_string( $length )
        {
            $string = '';
            $chars = '0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()';
            
            for( $i = 0; $i < $length; $i++ )
            {
                $string .= $chars[rand(0, strlen($chars))];
            }
            
            return $string;
        }
    }
?>