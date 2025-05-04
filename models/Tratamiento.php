<?php
class Tratamiento {
    // Atributos privados
    private $idTratamiento;
    private $fechaRegistroTratamiento;
    private $sectorTratamiento;
    private $frecuenciaTratamiento;
    private $observacionesTratamiento;

    // Arrays de fumigantes
    private $fumigante_nombres     = [];
    private $fumigante_cantidades  = [];
    private $fumigante_unidades    = [];

    // Constructor
    public function __construct(
        $idTratamiento               = null,
        $fechaRegistroTratamiento    = null,
        $sectorTratamiento           = null,
        $frecuenciaTratamiento       = null,
        $observacionesTratamiento    = null,
        array $fumigante_nombres     = [],
        array $fumigante_cantidades  = [],
        array $fumigante_unidades    = []
    ) {
        $this->idTratamiento             = $idTratamiento;
        $this->fechaRegistroTratamiento  = $fechaRegistroTratamiento;
        $this->sectorTratamiento         = $sectorTratamiento;
        $this->frecuenciaTratamiento     = $frecuenciaTratamiento;
        $this->observacionesTratamiento  = $observacionesTratamiento;
        $this->fumigante_nombres         = $fumigante_nombres;
        $this->fumigante_cantidades      = $fumigante_cantidades;
        $this->fumigante_unidades        = $fumigante_unidades;
    }

    // Getters
    public function getIdTratamiento() {
        return $this->idTratamiento;
    }

    public function getFechaRegistroTratamiento() {
        return $this->fechaRegistroTratamiento;
    }

    public function getSectorTratamiento() {
        return $this->sectorTratamiento;
    }

    public function getFrecuenciaTratamiento() {
        return $this->frecuenciaTratamiento;
    }

    public function getObservacionesTratamiento() {
        return $this->observacionesTratamiento;
    }

    public function getFumiganteNombres(): array {
        return $this->fumigante_nombres;
    }

    public function getFumiganteCantidades(): array {
        return $this->fumigante_cantidades;
    }

    public function getFumiganteUnidades(): array {
        return $this->fumigante_unidades;
    }

    // Setters
    public function setIdTratamiento($idTratamiento): void {
        $this->idTratamiento = $idTratamiento;
    }

    public function setFechaRegistroTratamiento($fecha): void {
        $this->fechaRegistroTratamiento = $fecha;
    }

    public function setSectorTratamiento($sector): void {
        $this->sectorTratamiento = $sector;
    }

    public function setFrecuenciaTratamiento($frecuencia): void {
        $this->frecuenciaTratamiento = $frecuencia;
    }

    public function setObservacionesTratamiento($observaciones): void {
        $this->observacionesTratamiento = $observaciones;
    }

    public function setFumiganteNombres(array $nombres): void {
        $this->fumigante_nombres = $nombres;
    }

    public function setFumiganteCantidades(array $cantidades): void {
        $this->fumigante_cantidades = $cantidades;
    }

    public function setFumiganteUnidades(array $unidades): void {
        $this->fumigante_unidades = $unidades;
    }
}
?>
