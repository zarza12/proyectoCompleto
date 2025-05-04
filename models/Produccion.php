<?php

class Produccion {
    private $id;
    private $sectorProduccion;
    private $fechaProduccion;
    private $calidadExportacion;
    private $calidadNacional;
    private $calidadDesecho;
    private $subTotal;

    // Constructor
    public function __construct($id, $sectorProduccion, $fechaProduccion, $calidadExportacion,
                                $calidadNacional, $calidadDesecho, $subTotal) {
        $this->id = $id;
        $this->sectorProduccion = $sectorProduccion;
        $this->fechaProduccion = $fechaProduccion;
        $this->calidadExportacion = $calidadExportacion;
        $this->calidadNacional = $calidadNacional;
        $this->calidadDesecho = $calidadDesecho;
        $this->subTotal = $subTotal;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getSectorProduccion() { return $this->sectorProduccion; }
    public function getFechaProduccion() { return $this->fechaProduccion; }
    public function getCalidadExportacion() { return $this->calidadExportacion; }
    public function getCalidadNacional() { return $this->calidadNacional; }
    public function getCalidadDesecho() { return $this->calidadDesecho; }
    public function getSubTotal() { return $this->subTotal; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setSectorProduccion($sectorProduccion) { $this->sectorProduccion = $sectorProduccion; }
    public function setFechaProduccion($fechaProduccion) { $this->fechaProduccion = $fechaProduccion; }
    public function setCalidadExportacion($calidadExportacion) { $this->calidadExportacion = $calidadExportacion; }
    public function setCalidadNacional($calidadNacional) { $this->calidadNacional = $calidadNacional; }
    public function setCalidadDesecho($calidadDesecho) { $this->calidadDesecho = $calidadDesecho; }
    public function setSubTotal($subTotal) { $this->subTotal = $subTotal; }
}

?>
