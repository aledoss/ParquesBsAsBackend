<?php
class Parques_model extends CI_Model {

	public function getFechaActual(){
		date_default_timezone_set('America/Argentina/Buenos_Aires');
		return date('Y-m-d H:i:s');
	}

	public function getParques(){
		$parques = $this->db->query("SELECT p.id_parque, c.comuna, b.descripcion barrio, p.id_wifi wifi, p.nombre, p.descripcion, p.direccion, p.imagen, p.patio_juegos, p.latitud, p.longitud, p.latitud, p.imagen_android FROM parques p LEFT JOIN comunas c on p.id_comuna = c.id_comuna LEFT JOIN barrios b on p.id_barrio = b.id_barrio WHERE p.activo = '1'")->result_array();

		if(is_null($parques) || empty($parques)){
			return array('status' => 404, 'message' => 'No se pudieron obtener los parques');
		}else{
			return array('status' => 200, 'message' => 'Parques obtenidos correctamente', 'response' => $parques);
		}
	}

	public function getParque($id){
		$parque = $this->db
		->select('id_parque, id_wifi wifi, nombre, descripcion, direccion, patio_juegos, latitud, longitud, latitud, likes, hates, imagen_android')
		->where(array('id_parque'=> $id, 'activo' => 1))
		->get('parques')
		->row();
		if(is_null($parque) || empty($parque)){
			return array('status' => 404, 'message' => 'No se pudo obtener el parque');
		}else{
			return array('status' => 200, 'message' => 'Parque obtenido correctamente', 'response' => $parque);
		}
	}

	public function getParqueComponents($id_parque){
		$cantActividades = (int) $this->db->query("SELECT count(id_parque) Actividades from parques_actividades where id_parque = $id_parque AND activo = 1")->row()->Actividades;
		$cantFeriasComunes = (int) $this->db->query("SELECT count(id_parque) FeriasComunes from ferias_comunes where id_parque = $id_parque")->row()->FeriasComunes;
		$cantFeriasItiner = (int) $this->db->query("SELECT count(id_parque) FeriasItinerantes from ferias_itinerantes where id_parque = $id_parque")->row()->FeriasItinerantes;
		$cantEstSaludables = (int) $this->db->query("SELECT count(id_parque) EstacionesSaludables from estaciones_salud where id_parque = $id_parque")->row()->EstacionesSaludables;
		$cantPuntosVerdes = (int) $this->db->query("SELECT count(id_parque) PuntosVerdes from puntos_verdes where id_parque = $id_parque")->row()->PuntosVerdes;

		$i = 0;
		$components = array();

		if($cantActividades > 0){
			$components[$i] = new StdClass;
			$components[$i]->componente = 'Actividades';
			$i++;
		}

		if($cantFeriasComunes > 0){
			$components[$i] = new StdClass;
			$components[$i]->componente = 'Feria';
			$i++;
		}

		if($cantFeriasItiner > 0){
			$components[$i] = new StdClass;
			$components[$i]->componente = 'Feria Itinerante';
			$i++;
		}

		if($cantEstSaludables > 0){
			$components[$i] = new StdClass;
			$components[$i]->componente = 'Estaciones Saludables';
			$i++;
		}

		if($cantPuntosVerdes > 0){
			$components[$i] = new StdClass;
			$components[$i]->componente = 'Puntos Verdes';
		}
		
		if(count($components) > 0){
			return array('status' => 200, 'message' => 'Componentes de parques obtenidos correctamente', 'response' => $components);
		} else {
			return array('status' => 204, 'message' => 'No se encontraron componentes para este parque');
		}


	}

	public function getReclamosByParque($id_parque){
		$reclamosParque = $this->db->query("SELECT COUNT(r.descripcion) as cantidad, r.descripcion FROM usuarios_reclamos_parques urp LEFT JOIN reclamos r on urp.id_reclamo = r.id_reclamo where urp.id_parque = $id_parque group by r.descripcion")->result_array();

		if(is_null($reclamosParque) || empty($reclamosParque)){
			return array('status' => 204, 'message' => 'No se encontraron reclamos para este parque');
		}else{
			return array('status' => 200, 'message' => 'Reclamos obtenidos correctamente', 'response' => $reclamosParque);
		}
	}

	public function getReclamosByUsuario($id_usuario){
		$reclamosParque = $this->db->query("SELECT p.nombre as nombre_parque, r.descripcion, re.descripcion as estado, urp.fecha_creacion, urp.comentarios, urp.imagen, urp.latitud, urp.longitud, re.color FROM usuarios_reclamos_parques urp LEFT JOIN reclamos r on urp.id_reclamo = r.id_reclamo INNER JOIN reclamos_estado re on urp.id_estado = re.id_estado LEFT JOIN parques p on urp.id_parque = p.id_parque where urp.id_usuario = $id_usuario order by urp.fecha_creacion desc")->result_array();

		if(is_null($reclamosParque) || empty($reclamosParque)){
			return array('status' => 204, 'message' => 'No se encontraron reclamos para este usuario');
		}else{
			return array('status' => 200, 'message' => 'Reclamos obtenidos correctamente', 'response' => $reclamosParque);
		}
	}

	public function getReclamosDesc(){
		$reclamosDesc = $this->db
		->select('*')
		->get('reclamos')
		->result_array();

		if(is_null($reclamosDesc) || empty($reclamosDesc)){
			return array('status' => 404, 'message' => 'No se puieron obtener los reclamos');
		}else{
			return array('status' => 200, 'message' => 'Reclamos obtenidos correctamente', 'response' => $reclamosDesc);
		}
	}

	public function createReclamo($reclamo){
		$reclamo['fecha_creacion'] = $this->getFechaActual();
		$reclamo['id_estado'] = 1;

		$this->db->trans_start();
		$this->db->insert('usuarios_reclamos_parques', $reclamo);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return array('status' => 500,'message' => 'No se pudo generar el reclamo');
		} else {
			$this->db->trans_commit();
			return array('status' => 200,'message' => 'Reclamo enviado correctamente');
		}

	}

	public function getActividadesParque($id_parque){
		$result = $this->db->query("SELECT a.id_actividad, a.nombre from parques_actividades pa left join actividades a on pa.id_actividad = a.id_actividad where $id_parque = pa.id_parque group by pa.id_actividad")->result_array();

		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se encontraron actividades para este parque');
		}else{
			return array('status' => 200, 'message' => 'Actividades obtenidas correctamente', 'response' => $result);
		}
	}

	public function getHorariosByParqueActividad($id_parque, $id_actividad){
		$result = $this->db->query("SELECT pa.desde, pa.hasta, pa.dia from parques_actividades pa left join actividades a on pa.id_actividad = a.id_actividad and $id_parque = pa.id_parque where $id_parque = id_parque and $id_actividad = pa.id_actividad and activo = 1")->result_array();

		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se encontraron horarios para este actividad');
		}else{
			return array('status' => 200, 'message' => 'Horarios obtenidos correctamente', 'response' => $result);
		}
	}

	public function getFeriasByParque($id_parque){
		$result = $this->db->query("SELECT f.tipo, f.fecha, f.latitud, f.longitud from ferias_comunes f where $id_parque = id_parque and activo = 1")->result_array();

		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se encontraron ferias para este parque');
		}else{
			return array('status' => 200, 'message' => 'Ferias obtenidos correctamente', 'response' => $result);
		}
	}

	public function getFeriasItinerantesByParque($id_parque){
		$result = $this->db->query("SELECT f.dias, f.direccion, f.latitud, f.longitud from ferias_itinerantes f where $id_parque = id_parque and activo = 1")->result_array();

		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se encontraron ferias itinerantes para este parque');
		}else{
			return array('status' => 200, 'message' => 'Ferias Itinerantes obtenidos correctamente', 'response' => $result);
		}
	}

	public function getEstSaludByParque($id_parque){
		$result = $this->db->query("SELECT es.servicios, es.fecha, es.latitud, es.longitud from estaciones_salud es where $id_parque = id_parque and activo = 1")->result_array();

		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se encontraron estaciones saludables para este parque');
		}else{
			return array('status' => 200, 'message' => 'Estaciones saludables obtenidas correctamente', 'response' => $result);
		}
	}

	public function getPuntosVerdesByParque($id_parque){
		$result = $this->db->query("SELECT pv.tipo, pv.materiales, pv.dias_horarios, pv.latitud, pv.longitud from puntos_verdes pv where $id_parque = id_parque and activo = 1")->result_array();

		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se encontraron puntos verdes para este parque');
		}else{
			return array('status' => 200, 'message' => 'Puntos verdes obtenidas correctamente', 'response' => $result);
		}
	}

	public function updateParqueLike($id_parque, $increaseLike, $decreaseLike, $increaseHate, $decreaseHate){
		//insert tabla relacion parque/voto

		if ($increaseLike){
			$result = $this->db->query("UPDATE parques p SET p.likes = p.likes + 1 WHERE p.id_parque = $id_parque");
		}

		if ($decreaseLike){
			$result = $this->db->query("UPDATE parques p SET p.likes = p.likes - 1 WHERE p.id_parque = $id_parque");
		}

		if ($increaseHate){
			$result = $this->db->query("UPDATE parques p SET p.hates = p.hates + 1 WHERE p.id_parque = $id_parque");			
		}

		if ($decreaseHate){
			$result = $this->db->query("UPDATE parques p SET p.hates = p.hates - 1 WHERE p.id_parque = $id_parque");
		}

		if(is_null($result) || empty($result)){
			return array('status' => 500, 'message' => 'No se puedo realizar la accion', 'response' => $result);
		} else {
			return array('status' => 200, 'message' => 'updateParqueLike OK', 'response' => $result);
		}

	}

	public function getEncuestasByParque($id_parque){
		$result = $this->db->query("SELECT eup.id_encuesta, COUNT(e.descripcion) as cantidad, e.descripcion FROM encuestas_usuarios_parques eup LEFT JOIN encuestas e on eup.id_encuesta = e.id_encuesta where eup.id_parque = $id_parque and e.activo = 1 group by e.descripcion")->result_array();
		
		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se encontraron encuestas para este parque');
		}else{
			return array('status' => 200, 'message' => 'Encuestas obtenidas correctamente', 'response' => $result);
		}
	}

	public function getEncuestasParaCalificarByParqueAndUsuario($id_parque, $id_usuario){
		//Obtener las encuestas para un usuario y un parque específico. El usuario no debe haber calificado esta encuesta para este parque dentro del mismo mes y año.
		$result = $this->db->query("
			SELECT e.* from encuestas e
			WHERE e.id_encuesta not in (
				SELECT eup.id_encuesta from encuestas_usuarios_parques eup
				WHERE eup.id_usuario = $id_usuario and 
					(month(eup.fecha_creacion) = month(date(now())) and (month(eup.fecha_creacion) <> month(date(now())) or year(eup.fecha_creacion) = year(date(now())))) AND
                	eup.id_parque = $id_parque
            	)
        		AND e.activo = 1
        	")->result_array();
		
		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'Usted no posee encuestas para calificar. Deberá esperar al mes siguiente');
		}else{
			return array('status' => 200, 'message' => 'Encuestas para calificar obtenidas correctamente', 'response' => $result);
		}
	}

	public function getEstadisticasEncuestaByParque($id_parque, $id_encuesta){
		$cantBueno = 0;
		$cantRegular = 0;
		$cantMalo = 0;

		$result = $this->db->query("SELECT c.descripcion, count(eup.id_calificacion) as cantCalificaciones
			FROM encuestas_usuarios_parques eup
			LEFT JOIN calificaciones c ON eup.id_calificacion = c.id_calificacion
			WHERE eup.id_parque = $id_parque AND eup.id_encuesta = $id_encuesta
			GROUP BY c.descripcion, eup.id_calificacion")-> result_array();

		$resultTotal = $this->db->query("SELECT count(id_calificacion) as cantTotal
			FROM encuestas_usuarios_parques eup
			WHERE eup.id_parque = $id_parque AND id_encuesta = $id_encuesta")->row();
		$cantTotal = $resultTotal->cantTotal;

		if ((is_null($result) || empty($result)) || (is_null($resultTotal) || empty($resultTotal)) || $cantTotal == 0) {
			return array('status' => 204, 'message' => 'No se pudieron obtener las estadísticas para esta encuesta');
		} else {
			foreach ($result as $row) {
				if ($row['descripcion'] == 'Bueno'){
					$cantBueno = $row['cantCalificaciones'];
				} else if ($row['descripcion'] == 'Regular'){
					$cantRegular = $row['cantCalificaciones'];
				} else if ($row['descripcion'] == 'Malo'){
					$cantMalo = $row['cantCalificaciones'];
				}
			}

			$porcBueno = $cantBueno * 100 / $cantTotal;
			$porcRegular = $cantRegular * 100 / $cantTotal;
			$porcMalo = $cantMalo * 100 / $cantTotal;

			$porc = array('porcBueno' => $porcBueno, 'porcRegular' => $porcRegular, 'porcMalo' => $porcMalo);

			return array('status' => 200, 'message' => 'Estadisticas de encuestas obtenidas correctamente', 'response'=> $porc);
		}

	}

	public function getCalificaciones(){
		$result = $this->db->query("SELECT * FROM calificaciones")->result_array();
		
		if(is_null($result) || empty($result)){
			return array('status' => 204, 'message' => 'No se pudieron obtener las calificaciones');
		}else{
			return array('status' => 200, 'message' => 'Calificaciones obtenidas correctamente', 'response' => $result);
		}
	}

	public function insertarCalificacionEncuesta($calificacionEncuesta){
		$calificacionEncuesta['fecha_creacion'] = $this->getFechaActual();

		$this->db->trans_start();
		$this->db->insert('encuestas_usuarios_parques', $calificacionEncuesta);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return array('status' => 500,'message' => 'No se pudo calificar la encuesta');
		} else {
			$this->db->trans_commit();
			return array('status' => 200,'message' => 'Calificación registrada');
		}

	}	

}
?>
