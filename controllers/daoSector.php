<?php
include_once  'conn.php';


class daoSector {

    public function __construct() {
        // No hace nada
    }

    public function registrarSector(Sector $sector) {
        $conexion = getConnection(); // Función que retorna la conexión a MySQL
        
        try {
            // Consulta SQL para insertar en la tabla 'sectores'
            $sql = "INSERT INTO sectores (nombreSector, nombreSectorJuntos, descripcionSector, fechaRegistro)
                    VALUES (
                        '{$sector->getNombreSector()}',
                        '{$sector->getNombreSectorJuntos()}',
                        '{$sector->getDescripcionSector()}',
                        '{$sector->getFechaRegistro()}'
                    )";
            
            $resultado = $conexion->query($sql);
            
            if ($resultado) {
                return true; // Registro exitoso
            } else {
                return false; // Fallo al ejecutar la consulta
            }
    
        } catch (mysqli_sql_exception $e) {
            echo "Error al registrar el sector: " . $e->getMessage();
            return false;
        }
    }
    public function modificarSector(Sector $sector) {
        $conexion = getConnection();
    
        try {
            $sql = "UPDATE sectores SET 
                        nombreSector = '{$sector->getNombreSector()}',
                        nombreSectorJuntos = '{$sector->getNombreSectorJuntos()}',
                        descripcionSector = '{$sector->getDescripcionSector()}',
                        fechaRegistro = '{$sector->getFechaRegistro()}'
                    WHERE id = '{$sector->getId()}'";
    
            $resultado = $conexion->query($sql);
    
            return $resultado ? true : false;
    
        } catch (mysqli_sql_exception $e) {
            echo "Error al actualizar el sector: " . $e->getMessage();
            return false;
        }
    }
    public function eliminarSector($id) {
        $conexion = getConnection();
        
        try {
            $sql = "DELETE FROM sectores WHERE id = '$id'";
            
            $resultado = $conexion->query($sql);
            
            if ($resultado && $conexion->affected_rows > 0) {
                return true; // Eliminación exitosa
            } else {
                return false; // No se eliminó nada o no existe ese ID
            }
        } catch (mysqli_sql_exception $e) {
            echo "Error al eliminar el sector: " . $e->getMessage();
            return false;
        }
    }
        

    public function listarSectores() {
        $conexion = getConnection();
        $listaSectores = [];
    
        try {
            $sectores = $conexion->query("SELECT * FROM sectores");
    
            while ($sector = $sectores->fetch_assoc()) {
                $listaSectores[] = [
                    'id'          => $sector['id'],                         // ID autoincremental
                    'fecha'       => $sector['fechaRegistro'],              // Fecha de registro
                    'nombre'      => $sector['nombreSector'],               // Nombre del sector
                    'descripcion' => $sector['descripcionSector']           // Descripción del sector
                ];
            }
    
            return $listaSectores;
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener sectores: " . $e->getMessage();
            return [];
        }
    }
    
    public function listarSectoresParaSelect() {
        $conexion = getConnection();
        $lista = [];
    
        try {
            $sectores = $conexion->query("SELECT nombreSector, nombreSectorJuntos FROM sectores");
    
            while ($sector = $sectores->fetch_assoc()) {
                $lista[] = [
                    'value' => $sector['nombreSectorJuntos'], // Ej: sector_a
                    'label' => $sector['nombreSector']        // Ej: Sector A
                ];
            }
    
            return $lista;
        } catch (mysqli_sql_exception $e) {
            echo "Error al listar sectores: " . $e->getMessage();
            return [];
        }
    }
    

}