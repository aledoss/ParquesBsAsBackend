<?php
class User_model extends CI_Model {

	const USERS_TABLE = 'usuarios';

	public function getTodos() {
		$result= $this->db->query('SELECT * FROM parques');
		return $result->result();
	}

	public function login($email,$password)	{
		//$q  = $this->db->get_where(User_model::USERS_TABLE, array('email' => $email))->row();
		$q = $this->db->query("SELECT u.*, d.descripcion as tipo_doc FROM usuarios u inner join tipos_documento d on u.id_tipo_documento = d.id_tipo_documento WHERE u.email = '" . $email . "'")->result_array();

		if(is_null($q) || empty($q) || $q[0]['activo'] == 0){
			return array('status' => 204,'message' => 'Usuario no encontrado');
		} else {
			$q = $q[0];	//como me va a devolver 1 solo usuario, obtengo el primero.
			if ($password == $q['contrasenia']) {
				return array('status' => 200,'message' => 'Inicio de sesión correcto','response' => $q);
			} else {
				return array('status' => 204,'message' => 'Contraseña incorrecta');
			}
		}
	}

	public function getDocTypes(){
		$docTypes = $this->db->get('tipos_documento')->result_array();

		if(is_null($docTypes) || empty($docTypes)){
			return array('status' => 404,'message' => 'No se pudieron obtener los tipos de documento');
		}else{
			return array('status' => 200,'message' => 'Tipos de documento obtenidos correctamente', 'response' => $docTypes);
		}
	}

	public function createUser($user){
		date_default_timezone_set('America/Argentina/Buenos_Aires');
		$fechaCreacion = date('Y-m-d H:i:s');
		$user['id_tipo_usuario'] = 2;
		$user['fecha_creacion'] = $fechaCreacion;
		$user['activo'] = 1;

		$cantUser = $this->db->get_where('usuarios', array('email' => $user['email']))->num_rows();
		if($cantUser){
			return array('status' => 409, 'message' => 'Email existente');
		}else{
			$this->db->trans_start();
			$this->db->insert('usuarios', $user);
			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return array('status' => 500,'message' => 'No se pudo crear la cuenta');
			} else {
				$this->db->trans_commit();
				return array('status' => 200,'message' => 'Cuenta creada correctamente');
			}
		}

	}

	public function updateUserName($user){
		$validUser = $this->db->get_where('usuarios', array('id_usuario' => $user['id_usuario']))->num_rows();
		if($validUser <> 1){
			return array('status' => 409, 'message' => 'Usuario inválido');
		}else{
			$this->db->trans_start();
			$this->db->where('id_usuario', $user['id_usuario']);
			$data = array(
				'nombre'=> $user['nombre'],
				'apellido' => $user['apellido']
			);
			$this->db->update('usuarios', $data);
			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return array('status' => 500,'message' => 'No se pudieron actualizar los datos');
			} else {
				$this->db->trans_commit();
				return array('status' => 200,'message' => 'Datos actualizados correctamente');
			}
		}
	}

	public function updateDocument($idUsuario, $user){
		$validUser = $this->db->get_where('usuarios', array('id_usuario' => $idUsuario))->num_rows();
		if($validUser <> 1){
			return array('status' => 409, 'message' => 'Usuario inválido');
		}else{
			$this->db->trans_start();
			$this->db->where('id_usuario', $idUsuario);
			$data = array(
				'id_tipo_documento' => $user['id_tipo_documento'],
				'numero_documento' => $user['numero_documento']
			);
			$this->db->update('usuarios', $data);
			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return array('status' => 500,'message' => 'No se pudieron actualizar los datos');
			} else {
				$this->db->trans_commit();
				return array('status' => 200,'message' => 'Datos actualizados correctamente');
			}
		}
	}

	public function updatePassword($user){
		$idUsuario = $user['id_usuario'];
		$dbUser = $this->db->query("SELECT contrasenia from usuarios u where $idUsuario = u.id_usuario")->row();
		$contraseniaDB = $dbUser->contrasenia;

		if($dbUser == null){
			return array('status' => 409, 'message' => 'Usuario inválido');
		}else{
			if ($user['contrasenia_vieja'] === $contraseniaDB){
				$this->db->trans_start();
				$this->db->where('id_usuario', $user['id_usuario']);
				$data = array(
					'contrasenia' => $user['contrasenia']
				);
				$this->db->update('usuarios', $data);
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					return array('status' => 500,'message' => 'No se pudo actualizar la contraseña');
				} else {
					$this->db->trans_commit();
					return array('status' => 200,'message' => 'Contraseña actualizada correctamente');
				}
			} else {
				return array('status' => 409,'message' => 'Contraseña antigua incorrecta');
			}
		}
	}

	public function deleteCuenta($idUsuario){
		$validUser = $this->db->get_where('usuarios', array('id_usuario' => $idUsuario))->num_rows();
		if($validUser <> 1){
			return array('status' => 409, 'message' => 'Usuario inválido');
		}else{
			$this->db->trans_start();
			$this->db->where('id_usuario', $idUsuario);
			$data = array(
				'activo' => 0
			);
			$this->db->update('usuarios', $data);
			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return array('status' => 500,'message' => 'No se pudieron actualizar los datos');
			} else {
				$this->db->trans_commit();
				return array('status' => 200,'message' => 'Cuenta borrada correctamente', 'response' => 'Su cuenta ha sido borrada correctamente');
			}
		}
	}

}
//return array('status' => $q);

/*$q  = $this->db->select('id_usuario,contrasenia')
        		->from('usuarios')
        		->where('email', $email)
        		->get()->row();*/
        		?>