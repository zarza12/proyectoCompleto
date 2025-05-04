<?php

class Inventario {
    private $idInventario;
    private $totalDesecho;
    private $totalNacional;
    private $totalExportacion;
    private $fecha;
    private $ventaDesecho;
    private $ventaNacional;
    private $ventaExportacion;
    private $totalCajas;
    private $idProduccion;
    private $idEntregas;

    // Constructor
    public function __construct($idInventario, $totalDesecho, $totalNacional, $totalExportacion, $fecha,
                                $ventaDesecho, $ventaNacional, $ventaExportacion, $totalCajas,
                                $idProduccion, $idEntregas) {
        $this->idInventario = $idInventario;
        $this->totalDesecho = $totalDesecho;
        $this->totalNacional = $totalNacional;
        $this->totalExportacion = $totalExportacion;
        $this->fecha = $fecha;
        $this->ventaDesecho = $ventaDesecho;
        $this->ventaNacional = $ventaNacional;
        $this->ventaExportacion = $ventaExportacion;
        $this->totalCajas = $totalCajas;
        $this->idProduccion = $idProduccion;
        $this->idEntregas = $idEntregas;
    }

    // Getters
    public function getIdInventario() { return $this->idInventario; }
    public function getTotalDesecho() { return $this->totalDesecho; }
    public function getTotalNacional() { return $this->totalNacional; }
    public function getTotalExportacion() { return $this->totalExportacion; }
    public function getFecha() { return $this->fecha; }
    public function getVentaDesecho() { return $this->ventaDesecho; }
    public function getVentaNacional() { return $this->ventaNacional; }
    public function getVentaExportacion() { return $this->ventaExportacion; }
    public function getTotalCajas() { return $this->totalCajas; }
    public function getIdProduccion() { return $this->idProduccion; }
    public function getIdEntregas() { return $this->idEntregas; }

    // Setters
    public function setIdInventario($idInventario) { $this->idInventario = $idInventario; }
    public function setTotalDesecho($totalDesecho) { $this->totalDesecho = $totalDesecho; }
    public function setTotalNacional($totalNacional) { $this->totalNacional = $totalNacional; }
    public function setTotalExportacion($totalExportacion) { $this->totalExportacion = $totalExportacion; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setVentaDesecho($ventaDesecho) { $this->ventaDesecho = $ventaDesecho; }
    public function setVentaNacional($ventaNacional) { $this->ventaNacional = $ventaNacional; }
    public function setVentaExportacion($ventaExportacion) { $this->ventaExportacion = $ventaExportacion; }
    public function setTotalCajas($totalCajas) { $this->totalCajas = $totalCajas; }
    public function setIdProduccion($idProduccion) { $this->idProduccion = $idProduccion; }
    public function setIdEntregas($idEntregas) { $this->idEntregas = $idEntregas; }
}

?>
