<?php
include_once  'conn.php';


class daoInventario {

    public function __construct() {
        // No hace nada
    }

    
    public function registrarInventario(Inventario $inventario) {
        $conexion = getConnection();
        try {
            $sql = "INSERT INTO inventario (
                        totalDesecho, totalNacional, totalExportacion, fecha, 
                        ventaDesecho, ventaNacional, ventaExportacion, totalCajas, 
                        idProduccion, idEntregas
                    ) VALUES (
                        '{$inventario->getTotalDesecho()}',
                        '{$inventario->getTotalNacional()}',
                        '{$inventario->getTotalExportacion()}',
                        '{$inventario->getFecha()}',
                        '{$inventario->getVentaDesecho()}',
                        '{$inventario->getVentaNacional()}',
                        '{$inventario->getVentaExportacion()}',
                        '{$inventario->getTotalCajas()}',
                        '{$inventario->getIdProduccion()}',
                        '{$inventario->getIdEntregas()}'
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
    
    public function registrarInventarioEntregas(Inventario $inventario,mysqli $conexion) {
        try {
            $sql = "INSERT INTO inventario (
                        totalDesecho, totalNacional, totalExportacion, fecha, 
                        ventaDesecho, ventaNacional, ventaExportacion, totalCajas, 
                        idProduccion, idEntregas
                    ) VALUES (
                        '{$inventario->getTotalDesecho()}',
                        '{$inventario->getTotalNacional()}',
                        '{$inventario->getTotalExportacion()}',
                        '{$inventario->getFecha()}',
                        '{$inventario->getVentaDesecho()}',
                        '{$inventario->getVentaNacional()}',
                        '{$inventario->getVentaExportacion()}',
                        '{$inventario->getTotalCajas()}',
                        '{$inventario->getIdProduccion()}',
                        '{$inventario->getIdEntregas()}'
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
    
    
    public function modificarInventarioPorProduccion(Inventario $inventario) {
        $conexion = getConnection();
        try {
            $sql = "UPDATE inventario SET 
                    totalDesecho = '{$inventario->getTotalDesecho()}',
                    totalNacional = '{$inventario->getTotalNacional()}',
                    totalExportacion = '{$inventario->getTotalExportacion()}',
                    totalCajas = '{$inventario->getTotalCajas()}'
                    WHERE idProduccion = '{$inventario->getIdProduccion()}'";
    
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
    
    public function modificarInventarioPorEntrega(Inventario $inventario) {
        $conexion = getConnection();
        try {
            $sql = "UPDATE inventario SET 
                    totalDesecho = '{$inventario->getTotalDesecho()}',
                    totalNacional = '{$inventario->getTotalNacional()}',
                    totalExportacion = '{$inventario->getTotalExportacion()}',
                    fecha = '{$inventario->getFecha()}',
                    ventaDesecho = '{$inventario->getVentaDesecho()}',
                    ventaNacional = '{$inventario->getVentaNacional()}',
                    ventaExportacion = '{$inventario->getVentaExportacion()}',
                    totalCajas = '{$inventario->getTotalCajas()}'
                    WHERE idEntregas = '{$inventario->getIdEntregas()}'";
    
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
    
    public function eliminarInventarioPorProduccion($idProduccion) {
        $conexion = getConnection();
        try {
            $sql = "DELETE FROM inventario 
                    WHERE idProduccion = '{$idProduccion}'";
    
            $resultado = $conexion->query($sql);

            mostrarMensaje("si trate de eliminar inventario  ");
    
            if ($resultado) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al eliminar: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarInventarioPorEntrega($idEntrega,mysqli $conexion) {
        try {
            $sql = "DELETE FROM inventario 
                    WHERE idEntregas = '{$idEntrega}'";
    
            $resultado = $conexion->query($sql);

            mostrarMensaje("si trate de eliminar inventario  ");
    
            if ($resultado) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al eliminar: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerTotalesInventario() {
        $conexion = getConnection();
        $listaTotales = [];
    
        try {
            $totales = $conexion->query("SELECT idInventario, totalCajas, totalDesecho, totalNacional, totalExportacion, fecha FROM inventario");
    
            while ($total = $totales->fetch_assoc()) {
                $listaTotales[] = [
                    'idInventario' => $total['idInventario'],
                    'totalCajas'=> $total['totalCajas'],
                    'totalDesecho' => $total['totalDesecho'],
                    'totalNacional' => $total['totalNacional'],
                    'totalExportacion' => $total['totalExportacion'],
                    'fecha' => $total['fecha']
                ];
            }
    
            return $listaTotales;
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al listar totales del inventario: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerVentasInventario() {
        $conexion = getConnection();
        $listaVentas = [];
    
        try {
            $ventas = $conexion->query("SELECT idInventario, ventaDesecho, ventaNacional, ventaExportacion, fecha FROM inventario");
    
            while ($venta = $ventas->fetch_assoc()) {
                $listaVentas[] = [
                    'idInventario' => $venta['idInventario'],
                    'ventaDesecho' => $venta['ventaDesecho'],
                    'ventaNacional' => $venta['ventaNacional'],
                    'ventaExportacion' => $venta['ventaExportacion'],
                    'fecha' => $venta['fecha']
                ];
            }
    
            return $listaVentas;
        } catch (mysqli_sql_exception $e) {
            mostrarMensaje("Error al listar ventas del inventario: " . $e->getMessage());
            return [];
        }
    }
    

    
}
