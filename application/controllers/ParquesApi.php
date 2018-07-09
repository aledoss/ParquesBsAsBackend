<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ParquesApi extends CI_Controller {

	/*public function __construct() {
		   parent::__construct();
		   $this->load->model('User_model');
	}*/

	public function getTodos () {          
		$this->load->model('User_model');  
		$result=$this->User_model->getTodos();
		echo json_encode($result);
	}

	public function login()	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
				$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
				$body = json_decode($stream_clean,true);

				$email = $body['email'];
				$password = $body['contrasenia'];
		        
		        $this->load->model('User_model');
		        $response = $this->User_model->login($email,$password);

				json_output($response);
		}
	}

	public function getDocTypes(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$this->load->model('User_model');
	        $response = $this->User_model->getDocTypes();

			json_output($response);
		}
	}

	public function createUser(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$body = json_decode($stream_clean,true);

			$user = array(
				'id_tipo_documento' => $body['id_tipo_documento'],
				'nombre' => $body['nombre'],
				'apellido' => $body['apellido'],
				'numero_documento' => $body['numero_documento'],
				'email' => $body['email'],
				'contrasenia' => $body['contrasenia']
			);

			$this->load->model('User_model');
			$response = $this->User_model->createUser($user);

			json_output($response);
		}
	}

	public function updateUserName(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$body = json_decode($stream_clean,true);

			$user = array(
				'id_usuario' => $body['id_usuario'],
				'nombre' => $body['nombre'],
				'apellido' => $body['apellido']
			);

			$this->load->model('User_model');
			$response = $this->User_model->updateUserName($user);

			json_output($response);
		}
	}

	public function updateDocument($idUsuario){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$body = json_decode($stream_clean,true);

			$documento = array(
				'id_tipo_documento' => $body['id_tipo_documento'],
				'numero_documento' => $body['numero_documento']
			);

			$this->load->model('User_model');
			$response = $this->User_model->updateDocument($idUsuario, $documento);

			json_output($response);
		}
	}

	public function updatePassword(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$body = json_decode($stream_clean,true);

			$user = array(
				'id_usuario' => $body['id_usuario'],
				'contrasenia_vieja' => $body['contrasenia_vieja'],
				'contrasenia' => $body['contrasenia']
			);

			$this->load->model('User_model');
			$response = $this->User_model->updatePassword($user);

			json_output($response);
		}
	}

	public function getParques(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getParques());
		}
	}

	public function getParque($id){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getParque($id));
		}
	}

	public function getParqueComponents($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getParqueComponents($id_parque));
		}
	}

	public function getReclamosByParque($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getReclamosByParque($id_parque));
		}
	}

	public function getReclamosByUsuario($id_usuario){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getReclamosByUsuario($id_usuario));
		}
	}

	public function getReclamosDesc(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getReclamosDesc());
		}
	}

	public function createReclamo(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$body = json_decode($stream_clean,true);

			$id_reclamo = array(
				'id_parque' => $body['id_parque'],
				'id_usuario' => $body['id_usuario'],
				'id_reclamo' => $body['id_reclamo'],
				'comentarios' => $body['comentarios'],
				'latitud' => $body['latitud'],
				'longitud' => $body['longitud'],
				'imagen' => $body['imagen']
			);

			$this->load->model('Parques_model');
			$response = $this->Parques_model->createReclamo($id_reclamo);

			json_output($response);
		}
	}

	public function getActividadesByParque($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getActividadesParque($id_parque));
		}
	}

	public function getHorariosByParqueActividad($id_parque, $id_actividad){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getHorariosByParqueActividad($id_parque, $id_actividad));
		}
	}

	public function getFeriasByParque($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getFeriasByParque($id_parque));
		}
	}

	public function getFeriasItinerantesByParque($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getFeriasItinerantesByParque($id_parque));
		}
	}

	public function getEstSaludablesByParque($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getEstSaludByParque($id_parque));
		}
	}

	public function getPuntosVerdesByParque($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getPuntosVerdesByParque($id_parque));
		}
	}	

	public function updateParqueLike(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$body = json_decode($stream_clean,true);

			$id_parque = $body['id_parque'];
			$increaseLike = $body['increaseLike'];
			$decreaseLike = $body['decreaseLike'];
			$increaseHate = $body['increaseHate'];
			$decreaseHate = $body['decreaseHate'];

				$this->load->model('Parques_model');
			$response = $this->Parques_model->updateParqueLike($id_parque, $increaseLike, $decreaseLike, $increaseHate, $decreaseHate);

			json_output($response);
		}
	}

	public function getEncuestasByParque($id_parque){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getEncuestasByParque($id_parque));
		}
	}
	
	public function getEncuestasParaCalificarByParqueAndUsuario($id_parque, $id_usuario){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getEncuestasParaCalificarByParqueAndUsuario($id_parque, $id_usuario));
		}
	}

	public function getEstadisticasEncuestaByParque($id_parque, $id_encuesta){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getEstadisticasEncuestaByParque($id_parque, $id_encuesta));
		}
	}

	public function getCalificaciones(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		}else{
			$this->load->model('Parques_model');

			json_output($this->Parques_model->getCalificaciones());
		}
	}

	public function insertarCalificacionEncuesta(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(array('status' => 400,'message' => 'Error de petición.'));
		} else {
			$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$body = json_decode($stream_clean,true);

			$calificacionEncuesta = array(
				'id_parque' => $body['id_parque'],
				'id_usuario' => $body['id_usuario'],
				'id_encuesta' => $body['id_encuesta'],
				'id_calificacion' => $body['id_calificacion']
			);

			$this->load->model('Parques_model');
			$response = $this->Parques_model->insertarCalificacionEncuesta($calificacionEncuesta);

			json_output($response);
		}
	}

}

?>