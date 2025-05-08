<?php
class Sector {
    private $id;
    private $nombreSector;
    private $nombreSectorJuntos;
    private $descripcionSector;
    private $fechaRegistro;

    // Constructor con todos los parámetros (id primero y obligatorio)
    public function __construct($id, $nombreSector, $nombreSectorJuntos, $descripcionSector, $fechaRegistro) {
        $this->setId($id);
        $this->setNombreSector($nombreSector);
        $this->setNombreSectorJuntos($nombreSectorJuntos);
        $this->setDescripcionSector($descripcionSector);
        $this->setFechaRegistro($fechaRegistro);
    }

    // ID
    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    // Nombre
    public function setNombreSector($nombreSector) {
        $this->nombreSector = $nombreSector;
    }

    public function getNombreSector() {
        return $this->nombreSector;
    }

    // Nombre junto
    public function setNombreSectorJuntos($nombreSectorJuntos) {
        $this->nombreSectorJuntos = $nombreSectorJuntos;
    }

    public function getNombreSectorJuntos() {
        return $this->nombreSectorJuntos;
    }

    // Descripción
    public function setDescripcionSector($descripcionSector) {
        $this->descripcionSector = $descripcionSector;
    }

    public function getDescripcionSector() {
        return $this->descripcionSector;
    }

    // Fecha
    public function setFechaRegistro($fechaRegistro) {
        $this->fechaRegistro = $fechaRegistro;
    }

    public function getFechaRegistro() {
        return $this->fechaRegistro;
    }
}
?>
