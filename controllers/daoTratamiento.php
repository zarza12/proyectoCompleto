<?php
include_once  '../../controllers/conn.php';
include_once  '../../models/Tratamiento.php';
include_once  '../../controllers/daoTratamiento.php';

class daoTratamiento {

    public function __construct() {
        // No hace nada
    }

    public function registrarTratamiento(Tratamiento $tratamineto){
        $conexion = getConnection();
        try {
            $sqlTratamiento = "INSERT INTO tratamientos (
                        fecha_registro, sector, frecuencia, 
                        observaciones
                    ) VALUES (
                        '{$tratamineto->getFechaRegistroTratamiento()}',
                        '{$tratamineto->getSectorTratamiento()}',
                        '{$tratamineto->getFrecuenciaTratamiento()}',
                        '{$tratamineto->getObservacionesTratamiento()}'
                    )";
        $conexion->begin_transaction();
                $resultado1 = $conexion->query($sqlTratamiento);
    
                $idTratamiento = $conexion->insert_id;
                $fumigante_nombres=$tratamineto->getFumiganteNombres();
                $fumigante_cantidades=$tratamineto->getFumiganteCantidades();
                $fumigante_unidades=$tratamineto->getFumiganteUnidades();
                $completoInsercion=0;


                $totalFumigantes = count($fumigante_nombres);
                for ($i = 0; $i < $totalFumigantes; $i++) {
                    $nombreFumigante    = $fumigante_nombres[$i];
                    $cantidadFumigante  = $fumigante_cantidades[$i];
                    $unidadFumigante    = $fumigante_unidades[$i];

                    $sqlFumigante = "INSERT INTO fumigantes (idTratamiento, nombre_fumigante, cantidad, unidad) VALUES (
                        '{$idTratamiento}',
                        '{$nombreFumigante}',
                        '{$cantidadFumigante}',
                        '{$unidadFumigante}'
                    )";
                    $resultado3 = $conexion->query($sqlFumigante);
                    if($resultado3){
                        $completoInsercion=0;
                    }else{
                        $completoInsercion=1;
                        break; 
                    }
                }
               if($resultado1 && $completoInsercion==0){
                $conexion->commit();
                return true;
               }else{
                $conexion->rollback();
                return false;
               }

                //-------------------
                
    
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al registrar entrega: " . $e->getMessage());
            return false;
        }
    }

    public function listarTratamientos(): array {
        $conexion = getConnection();
        $lista = [];

        try {
            // 1) Traemos todos los tratamientos
            $rsT = $conexion->query("
                SELECT 
                    idTratamiento, 
                    fecha_registro, 
                    sector, 
                    frecuencia, 
                    observaciones 
                FROM tratamientos
            ");

            while ($trat = $rsT->fetch_assoc()) {
                // Formateamos el ID como REC-XXX
                $key =$trat['idTratamiento'];

                // 2) Obtenemos los fumigantes de este tratamiento
                $rsF = $conexion->query("
                    SELECT 
                        nombre_fumigante AS nombre, 
                        cantidad, 
                        unidad 
                    FROM fumigantes 
                    WHERE idTratamiento = {$trat['idTratamiento']}
                ");

                $fumigantes = [];
                while ($fum = $rsF->fetch_assoc()) {
                    $fumigantes[] = [
                        'nombre'   => $fum['nombre'],
                        'cantidad' => (string)$fum['cantidad'],
                        'unidad'   => $fum['unidad']
                    ];
                }

                // 3) Construimos la entrada del array
                $lista[$key] = [
                    'id'        => $key,
                    'sector'        => $trat['sector'],
                    'fecha'         => $trat['fecha_registro'],    // YYYY-MM-DD
                    'frecuencia'    => $trat['frecuencia'],
                    'observaciones' => $trat['observaciones'],
                    'fumigantes'    => $fumigantes
                ];
            }

            return $lista;

        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al listar tratamientos: " . $e->getMessage());
            return [];
        }
    }

    public function deleteTratamiento($idTratamiento){
        $conexion = getConnection();

        try {
            // 1) Escapamos el ID para evitar inyección SQL
            $id = $conexion->real_escape_string($idTratamiento);

            // 2) Ejecutamos el DELETE
            $sql = "DELETE FROM tratamientos WHERE idTratamiento = '{$id}'";
            $resultado = $conexion->query($sql);

            $resultado = $conexion->query($sql);
            
            if ($resultado) {
                return true;
            } else {
                return false;
            }

        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al eliminar tratamiento: " . $e->getMessage());
            return false;
        }
    }


}