<?php
include_once  '../../controllers/conn.php';
include_once  '../../models/Entregas.php';
include_once  '../../controllers/daoInventario.php';
include_once  '../../models/Inventario.php';

class daoEntregas {

    public function __construct() {
        // No hace nada
    }

    public function registrarEntrega(Entregas $entrega) {
        $conexion = getConnection();
        try {
            $sql = "INSERT INTO entregas (
                        fechaEntrega, calidad_producto, cantidad_productos, 
                        nombre_empresa, email_comprador, nombre_transportista
                    ) VALUES (
                        '{$entrega->getFechaEntrega()}',
                        '{$entrega->getCalidadProducto()}',
                        '{$entrega->getCantidadProductos()}',
                        '{$entrega->getNombreEmpresa()}',
                        '{$entrega->getEmailComprador()}',
                        '{$entrega->getNombreTransportista()}'
                    )";
        $conexion->begin_transaction();
            $resultado = $conexion->query($sql);
    
                $idEntregas = $conexion->insert_id;
                $fecha = $entrega->getFechaEntrega();
                $ventaDesecho=0;
                $ventaNacional=0;
                $ventaExportacion=0;
                $venta='';
                $produccion='';
                if($entrega->getCalidadProducto()=='exportacion'){
                    $ventaExportacion=$entrega->getCantidadProductos();
                    $venta='ventaExportacion';
                    $produccion='totalExportacion';
                }else{
                    if($entrega->getCalidadProducto()=='nacional'){
                        $ventaNacional=$entrega->getCantidadProductos();
                        $venta='ventaNacional';
                        $produccion='totalNacional';
                    }else{
                        if($entrega->getCalidadProducto()=='desecho'){
                            $ventaDesecho=$entrega->getCantidadProductos();
                            $venta='ventaDesecho';
                            $produccion='totalDesecho';
                        }
                    }
                }

                $inventario=new Inventario(null, 0, 0, 0, $fecha, $ventaDesecho, $ventaNacional, $ventaExportacion, 0, 1, $idEntregas );
    
               // Registrar en inventario
               $daoInventario = new daoInventario();
               $daoInventario->registrarInventarioEntregas($inventario,$conexion);


               //-----------------------
              
               $obVenta=$this->calcularTotalesPorFecha($fecha,$venta,$conexion);
               $obTotal=$this->calcularTotalesPorFecha($fecha,$produccion,$conexion);
               $sum=$obTotal-$obVenta;

               if($sum<0){
                $conexion->rollback();
                mostrarMensaje("No tiene suficiente en inventario");
                return false;
               }else{
                $conexion->commit();
                return true;
               }

                //-------------------
                
    
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al registrar entrega: " . $e->getMessage());
            return false;
        }
    }


    function updateEntrega(Entregas $entrega) {
        $conexion = getConnection();
        try {
            $sql = "UPDATE entregas SET 
                    calidad_producto = '{$entrega->getCalidadProducto()}',
                    cantidad_productos = {$entrega->getCantidadProductos()},
                    nombre_empresa = '{$entrega->getNombreEmpresa()}',
                    email_comprador = '{$entrega->getEmailComprador()}',
                    nombre_transportista = '{$entrega->getNombreTransportista()}'
                    WHERE idEntregas = '{$entrega->getIdEntregas()}'";
            
            $resultado = $conexion->query($sql);
            
            if ($resultado) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al actualizar entrega: " . $e->getMessage());
            return false;
        }
    }

    function deleteEntrega($idEntrega) {
        $conexion = getConnection();
        try {
            // Escapar el ID para prevenir inyección SQL
            $idEntrega = $conexion->real_escape_string($idEntrega);
            
            $sql = "DELETE FROM entregas WHERE idEntregas = '{$idEntrega}'";
            
            $resultado = $conexion->query($sql);
            
            if ($resultado) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al eliminar entrega: " . $e->getMessage());
            return false;
        }
    }


    public function listarEntregas() {
        $conexion = getConnection();
        $listaEntregas = [];
    
        try {
            // Utilizamos los nombres reales de las columnas en la consulta SQL
            $entregas = $conexion->query("SELECT * FROM entregas");
    
            while ($entrega = $entregas->fetch_assoc()) {
                // Pero formateamos el resultado con los nombres que deseas
                if($entrega['idEntregas']!='1')
                $listaEntregas[] = [
                    'id' => $entrega['idEntregas'],
                    'calidad' => $entrega['calidad_producto'],
                    'cantidad' => $entrega['cantidad_productos'],
                    'fecha' => $entrega['fechaEntrega'],
                    'empresa' => $entrega['nombre_empresa'],
                    'email' => $entrega['email_comprador'],
                    'transportista' => $entrega['nombre_transportista']
                ];
            }
    
            return $listaEntregas;
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al obtener datos de entregas: " . $e->getMessage());
            return [];
        }
    }


    public function calcularTotalesPorFecha($fecha, $tipoTotal, mysqli $conexion) {
        $totales = [
            // Totales de ventas
            'ventaDesecho' => 0,
            'ventaNacional' => 0,
            'ventaExportacion' => 0,
            // Totales de producción
            'totalDesecho' => 0,
            'totalNacional' => 0,
            'totalExportacion' => 0
        ];
        
        try {
            $sql = "SELECT 
                    SUM(ventaDesecho) as sumaVentaDesecho,
                    SUM(ventaNacional) as sumaVentaNacional,
                    SUM(ventaExportacion) as sumaVentaExportacion,
                    SUM(totalDesecho) as sumaTotalDesecho,
                    SUM(totalNacional) as sumaTotalNacional,
                    SUM(totalExportacion) as sumaTotalExportacion
                    FROM inventario 
                    WHERE fecha = '$fecha'";
            
            $resultado = $conexion->query($sql);
            
            if ($resultado && $resultado->num_rows > 0) {
                $fila = $resultado->fetch_assoc();
                // Asignar valores de ventas
                $totales['ventaDesecho'] = $fila['sumaVentaDesecho'] ?: 0;
                $totales['ventaNacional'] = $fila['sumaVentaNacional'] ?: 0;
                $totales['ventaExportacion'] = $fila['sumaVentaExportacion'] ?: 0;
                // Asignar valores de producción
                $totales['totalDesecho'] = $fila['sumaTotalDesecho'] ?: 0;
                $totales['totalNacional'] = $fila['sumaTotalNacional'] ?: 0;
                $totales['totalExportacion'] = $fila['sumaTotalExportacion'] ?: 0;
            }
            
            // Si se especificó un tipo de total, devolver solo ese valor
            if ($tipoTotal && isset($totales[$tipoTotal])) {
                return $totales[$tipoTotal];
            }
            
            return $totales;
            
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al calcular totales: " . $e->getMessage());
            
            // Si se especificó un tipo de total, devolver 0
            if ($tipoTotal) {
                return 0;
            }
            
            return $totales;
        }
    }

    public function calcularDisponibilidadPorFecha(string $fecha): array
    {
        // Consulta unificada para traerte sumas de ventas y de producción
        $sql = "SELECT
                    SUM(ventaExportacion)    AS sumaVentaExportacion,
                    SUM(ventaNacional)       AS sumaVentaNacional,
                    SUM(ventaDesecho)        AS sumaVentaDesecho,
                    SUM(totalExportacion)    AS sumaTotalExportacion,
                    SUM(totalNacional)       AS sumaTotalNacional,
                    SUM(totalDesecho)        AS sumaTotalDesecho
                FROM inventario
                WHERE fecha = '$fecha'";
    
        $res = $conexion->query($sql);
        $fila = $res && $res->fetch_assoc() ? $res->fetch_assoc() : [];
    
        // Leer cada suma (0 si viene null)
        $ventaExp   = (int) ($fila['sumaVentaExportacion']  ?? 0);
        $ventaNac   = (int) ($fila['sumaVentaNacional']     ?? 0);
        $ventaDes   = (int) ($fila['sumaVentaDesecho']      ?? 0);
        $totalExp   = (int) ($fila['sumaTotalExportacion']  ?? 0);
        $totalNac   = (int) ($fila['sumaTotalNacional']     ?? 0);
        $totalDes   = (int) ($fila['sumaTotalDesecho']      ?? 0);
    
        // Calcular y devolver sólo las disponibilidades
        return [
            'exportacion' => $totalExp - $ventaExp,
            'nacional'    => $totalNac - $ventaNac,
            'desecho'     => $totalDes - $ventaDes,
        ];
    }
    

}