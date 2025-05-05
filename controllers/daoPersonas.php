<?php

if (file_exists('../controllers/conn.php')) {
    include_once '../controllers/conn.php';
    include_once  '../models/Personas.php';
} else {
    include_once '../../controllers/conn.php';
    include_once  '../../models/Personas.php';
}



class daoPersonas {

    public function __construct() {
        // No hace nada
    }

    public function registrarPersonas(Personas $persona) {
        $conexion = getConnection();
        try {
            $sql = "INSERT INTO personal (idPer, nombrePer, fechaNacimientoPer, generoPer, 
                                          puestoPer, fecha_ingreso, correoPer, telefonoPer, 
                                          telefonoEmergencia, direccion, activo, curp) 
                    VALUES (
                        '{$persona->getId()}',
                        '{$persona->getNombreCompleto()}',
                        '{$persona->getFechaNacimiento()}',
                        '{$persona->getGenero()}',
                        '{$persona->getPuesto()}',
                        '{$persona->getFechaIngreso()}',
                        '{$persona->getCorreoElectronico()}',
                        '{$persona->getTelefonoCelular()}',
                        '{$persona->getTelefonoEmergencia()}',
                        '{$persona->getDireccion()}',
                        '{$persona->getActivo()}',
                        '{$persona->getCurp()}'
                    )";
    
            $resultado = $conexion->query($sql);
    
            if ($resultado) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al registrar: " . $e->getMessage());
            return false;
        }
    }
    public function modificarPersona(Personas $persona) {
        $conexion = getConnection();
        try {
            $sql = "UPDATE personal SET 
                    nombrePer = '{$persona->getNombreCompleto()}',
                    fechaNacimientoPer = '{$persona->getFechaNacimiento()}',
                    generoPer = '{$persona->getGenero()}',
                    puestoPer = '{$persona->getPuesto()}',
                    fecha_ingreso = '{$persona->getFechaIngreso()}',
                    correoPer = '{$persona->getCorreoElectronico()}',
                    telefonoPer = '{$persona->getTelefonoCelular()}',
                    telefonoEmergencia = '{$persona->getTelefonoEmergencia()}',
                    direccion = '{$persona->getDireccion()}',
                    curp = '{$persona->getCurp()}'
                    WHERE idPer = '{$persona->getId()}'";
    
            $resultado = $conexion->query($sql);
    
            if ($resultado) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al actualizar: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarPersona($id) {
        $conexion = getConnection();
        try {
            // Podemos usar una consulta simple para eliminar el registro con el ID proporcionado
            $sql = "DELETE FROM personal WHERE idPer = '$id'";
            
            $resultado = $conexion->query($sql);
            
            if ($resultado && $conexion->affected_rows > 0) {
                return true; // Eliminación exitosa
            } else {
                return false; // No se pudo eliminar o no se encontró el registro
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al eliminar: " . $e->getMessage());
            return false;
        }
    }

    public function listarPersonal() {
        $conexion = getConnection();
        $listaPersonas = [];
    
        try {
            $personas = $conexion->query("SELECT * FROM personal");
    
            while ($persona = $personas->fetch_assoc()) {
                $listaPersonas[] = new Personas(
                    $persona['idPer'], $persona['nombrePer'], $persona['fechaNacimientoPer'],
                    $persona['generoPer'], $persona['curp'], $persona['puestoPer'], $persona['fecha_ingreso'],
                    $persona['correoPer'], $persona['telefonoPer'], $persona['telefonoEmergencia'],
                    $persona['direccion'], $persona['activo']
                );
            }
    
            return $listaPersonas;
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al listar: " . $e->getMessage());
            return [];
        }
    }

    public function existePersonaID($IDpersona) {
        $conexion = getConnection();
        try {
            $persona = $conexion->query("SELECT * FROM personal WHERE idPer = '{$IDpersona}'");
            
            if ($persona->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al registrar: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerLogin() {
        $conexion = getConnection();
        $listaPersonas = [];
    
        try {
            // Solo seleccionamos los campos necesarios
            $personas = $conexion->query("SELECT nombrePer, correoPer, idPer, puestoPer FROM personal");
    
            while ($persona = $personas->fetch_assoc()) {
                // Guardamos solo los datos requeridos en un arreglo asociativo
                $listaPersonas[] = [
                    'nombre' => $persona['nombrePer'],
                    'correo' => $persona['correoPer'],
                    'clave' => $persona['idPer'],
                    'puesto' => $persona['puestoPer']
                ];
            }
    
            return $listaPersonas;
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al listar: " . $e->getMessage());
            return [];
        }
    }
    
    
}
