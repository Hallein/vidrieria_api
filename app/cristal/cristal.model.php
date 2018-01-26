<?php

class Cristal{

	private $db;

	public function __construct($db){
		$this->db = $db;
	}

	public function getAll(){

		$query = $this->db->prepare('	
				SELECT 	P.PID 			as id,
						P.PNOMBRE 		as nombre,
						P.PDESCRIPCION 	as descripcion,
						P.PPRECIOM2 	as valor_metro_cuadrado,
						P.PESPESOR 		as espesor,
						C.CDIBUJO 		as dibujo,
						C.CTIPO 		as tipo_cristal
				FROM 	PRODUCTO P, CRISTAL C
				WHERE 	P.PID = C.PID
				ORDER BY 	P.PNOMBRE ASC
		');

		if($query->execute()){
			return $query->fetchAll();
		}else{
			return array('status' => 'error');
		}
	}

	public function getOne($id){

		$query = $this->db->prepare('
				SELECT 	P.PID 			as id,
						P.PNOMBRE 		as nombre,
						P.PDESCRIPCION 	as descripcion,
						P.PPRECIOM2 	as valor_metro_cuadrado,
						P.PESPESOR 		as espesor,
						C.CDIBUJO 		as dibujo,
						C.CTIPO 		as tipo_cristal
				FROM 	PRODUCTO P, CRISTAL C
				WHERE 	P.PID = C.PID
				AND 	P.PID = :id
				ORDER BY 	P.PNOMBRE ASC
		');

		$query -> bindParam(':id', $id);

		if($query -> execute()){
			return $query->fetch();
		}else{
			return array('status' => 'error');
		}
	}

	public function store($data){
		$datos = array();
		$query = $this->db->prepare(' 	
			INSERT INTO PRODUCTO ( PNOMBRE, PDESCRIPCION, PPRECIOM2, PESPESOR )
			VALUES 	( :nombre, :descripcion, :valor_metro_cuadrado, :espesor ) 
		');
		
		$query -> bindParam(':nombre', 					$data['nombre']);
		$query -> bindParam(':descripcion', 			$data['descripcion']);
		$query -> bindParam(':valor_metro_cuadrado', 	$data['valor_metro_cuadrado']);
		$query -> bindParam(':espesor', 				$data['espesor']);

		if($query -> execute()){			
			$id = $this->db->lastInsertId();
			//Llamamos a la función para guardar los datos específicos del cristal
			return $this->storeCristal($id, $data);						
		}else{
			return array('status' => 'error', 'message' => 'Error al insertar el producto');
		}
	}

	private function storeCristal($id, $data){
		$query = $this->db->prepare(' 	
			INSERT INTO CRISTAL ( PID, CDIBUJO, CTIPO )
			VALUES 	( :id, :dibujo, :tipo_cristal ) 
		');
		
		$query -> bindParam(':id', 				$id);
		$query -> bindParam(':dibujo', 			$data['dibujo']);
		$query -> bindParam(':tipo_cristal', 	$data['tipo_cristal']);

		if($query -> execute()){
			return array(
				'status' => 'success', 
				'id' => $id
			);
		}else{
			return array('status' => 'error', 'message' => 'Error al insertar el cristal');
		}
	}

	public function update($id, $data){
		$datos = array();
		$query = $this->db->prepare('	UPDATE 	PRODUCTO 
										SET 	PNOMBRE 		= :nombre,
												PDESCRIPCION 	= :descripcion,
												PPRECIOM2 		= :valor_metro_cuadrado,
												PESPESOR 		= :espesor
										WHERE 	PID 			= :id');

		$query -> bindParam(':nombre', 					$data['nombre']);
		$query -> bindParam(':descripcion', 			$data['descripcion']);
		$query -> bindParam(':valor_metro_cuadrado', 	$data['valor_metro_cuadrado']);
		$query -> bindParam(':espesor', 				$data['espesor']);
		$query -> bindParam(':id', 						$id);

		if($query -> execute()){
			return $this->updateCristal($id, $data);
		}else{
			return array('status' => 'error', 'message' => 'Error al actualizar el producto');
		}
	}

	private function updateCristal($id, $data){
		$datos = array();
		$query = $this->db->prepare('	UPDATE 	CRISTAL 
										SET 	CDIBUJO = :dibujo,
												CTIPO 	= :tipo_cristal,
										WHERE 	PID 	= :id');

		$query -> bindParam(':dibujo', 			$data['dibujo']);
		$query -> bindParam(':tipo_cristal', 	$data['tipo_cristal']);
		$query -> bindParam(':id', 				$id);

		if($query -> execute()){
			return array(
				'status' => 'success', 
				'id' => $id
			);
		}else{
			return array('status' => 'error', 'message' => 'Error al actualizar el cristal');
		}
	}

	private function delete($id){
		$query = $this->db->prepare('	DELETE FROM PRODUCTO
										WHERE PID = :id ');
		
		$query -> bindParam(':id', 	$id);

		if($query -> execute()){
			return array('status' => 'success');
		}else{
			return array('status' => 'error', 'message' => 'Error al eliminar el producto');
		}
	}

	public function deleteCristal($id){
		$query = $this->db->prepare('	DELETE FROM CRISTAL
										WHERE PID = :id ');
		
		$query -> bindParam(':id', 	$id);

		if($query -> execute()){
			//Una vez borrado el cristal, lo borramos de la tabla producto
			return $this->delete($id);
		}else{
			return array('status' => 'error', 'message' => 'Error al eliminar el cristal');
		}
	}
		
}