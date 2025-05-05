<?php
include_once  '../../controllers/conn.php';

include_once  '../../models/Produccion.php';
include_once  '../../controllers/daoInventario.php';
include_once  '../../models/Inventario.php';

class daoProduccion {

    public function __construct() {
        // No hace nada
    }

    public function registrarProduccion(Produccion $produccion) {
        $conexion = getConnection(); // Asegúrate de que esta función existe y retorna una instancia de la conexión a la base de datos
        try {
            // Iniciar transacción para garantizar la integridad de la data
          
    
            $sql = "INSERT INTO produccion (sectorProduccion, fechaProduccion, calidadExportacion, 
                                            calidadNacional, calidadDesecho, subTotal) 
                    VALUES (
                        '{$produccion->getSectorProduccion()}',
                        '{$produccion->getFechaProduccion()}',
                        '{$produccion->getCalidadExportacion()}',
                        '{$produccion->getCalidadNacional()}',
                        '{$produccion->getCalidadDesecho()}',
                        '{$produccion->getSubTotal()}'
                    )";
    
            $resultado = $conexion->query($sql);
        
            if ($resultado) {
                // Datos ficticios, necesitas definir cómo obtienes estos valores
                $idProduccion = $conexion->insert_id;
                $fecha = $produccion->getFechaProduccion();
                $totalCajas = $produccion->getSubTotal();  // Ejemplo de cómo podrías calcular esto
                $totalExportacion = $produccion->getCalidadExportacion();
                $totalNacional = $produccion->getCalidadNacional();
                $totalDesecho = $produccion->getCalidadDesecho();

                $inventario=new Inventario(null, $totalDesecho,$totalNacional,$totalExportacion,$fecha,0,0,0, $totalCajas,$idProduccion,1 );
    
               // Registrar en inventario
               $daoInventario = new daoInventario();
               $daoInventario->registrarInventario($inventario);
    
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            $conexion->rollback();  // Revertir la transacción en caso de cualquier excepción
            echo "Error al registrar la producción: " . $e->getMessage();
            return false;
        }
    }

    public function modificarProduccion(Produccion $produccion) {
        $conexion = getConnection();
        try {
            // Iniciar transacción
            //$conexion->begin_transaction();
    
            // Actualizar la fila en produccion
            $sql = "
                UPDATE produccion
                   SET sectorProduccion   = '{$produccion->getSectorProduccion()}',
                       calidadExportacion =  {$produccion->getCalidadExportacion()},
                       calidadNacional    =  {$produccion->getCalidadNacional()},
                       calidadDesecho     =  {$produccion->getCalidadDesecho()},
                       subTotal           =  {$produccion->getSubTotal()}
                 WHERE id       =  {$produccion->getId()}
            ";
            $conexion->query($sql);


            $idProduccion = $produccion->getId();
            $totalCajas = $produccion->getSubTotal();  // Ejemplo de cómo podrías calcular esto
            $totalExportacion = $produccion->getCalidadExportacion();
            $totalNacional = $produccion->getCalidadNacional();
            $totalDesecho = $produccion->getCalidadDesecho();

            $inventario=new Inventario(null, $totalDesecho,$totalNacional,$totalExportacion,null,0,0,0, $totalCajas,$idProduccion,1 );

           // Registrar en inventario
           $daoInventario = new daoInventario();
           $daoInventario->modificarInventarioPorProduccion($inventario);
    

            return true;
        } catch (mysqli_sql_exception $e) {
            // Revertir si algo falla
            $conexion->rollback();
            echo "Error al modificar la producción: " . $e->getMessage();
            return false;
        }
    }
    
    public function eliminarProduccion($idProduccion){
        $conexion = getConnection();
        try {
            // Iniciar transacción
            $conexion->begin_transaction();
    
          // Registrar en inventario
          $daoInventario = new daoInventario();
          $daoInventario->eliminarInventarioPorProduccion($idProduccion);
  
          // 2) Borrar producción
          $sqlProd = "DELETE FROM produccion
                      WHERE id = {$idProduccion}";
          $conexion->query($sqlProd);
            // Confirmar cambios
            $conexion->commit();
            return true;
    
        } catch (mysqli_sql_exception $e) {
            // Deshacer si algo falla
            $conexion->rollback();
            error_log("Error al eliminar producción/inventario (ID {$idProduccion}): " 
                      . $e->getMessage());
            return false;
        }
    }
    
    public function listarProduccion() {
    $conexion = getConnection();
    $listaProducciones = [];

    try {
        $sql = "
            SELECT * FROM produccion";
        $result = $conexion->query($sql);

        while ($row = $result->fetch_assoc()) {
            $listaProducciones[] = new Produccion(
                $row['id'],        // id
                $row['sectorProduccion'],     // sectorProduccion
                $row['fechaProduccion'],      // fechaProduccion
                $row['calidadExportacion'],   // calidadExportacion
                $row['calidadNacional'],      // calidadNacional
                $row['calidadDesecho'],       // calidadDesecho
                $row['subTotal']              // subTotal
            );
        }

        return $listaProducciones;
    } catch (mysqli_sql_exception $e) {
        mostrarMensaje("Error al listar producciones: " . $e->getMessage());
        return [];
    }
    }

    

    function generarNumero() {
        $numero = 0;
        $encontrado = true;
        while($encontrado) {
            $numero = rand(1, 1000);
            $existeID = existeProdiccionID($numero);
            if ($existeID) {
                $encontrado = true;
            } else {
                $encontrado = false;
            }
        }
        return $numero;
    }
    
    public function existeProdiccionID($IDproduccion) {
        $conexion = getConnection();
        try {
            $persona = $conexion->query("SELECT * FROM produccion WHERE id = '{$IDproduccion}'");
            
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
    

    

}