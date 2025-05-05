<?php

class Personas {
    // Atributos
    private $id;
    private $nombre_completo;
    private $fecha_nacimiento;
    private $genero;
    private $puesto;
    private $fecha_ingreso;
    private $correo_electronico;
    private $telefono_celular;
    private $telefono_emergencia;
    private $direccion;
    private $activo;
    private $crup;
    
    // Constructor
    public function __construct($id = '', $nombre_completo = '', $fecha_nacimiento = '', $genero = '', $crup = '',
                               $puesto = '', $fecha_ingreso = '', $correo_electronico = '', 
                               $telefono_celular = '', $telefono_emergencia = '', $direccion = '', $activo = true) {
        $this->id = $id;
        $this->nombre_completo = $nombre_completo;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->genero = $genero;
        $this->puesto = $puesto;
        $this->fecha_ingreso = $fecha_ingreso;
        $this->correo_electronico = $correo_electronico;
        $this->telefono_celular = $telefono_celular;
        $this->telefono_emergencia = $telefono_emergencia;
        $this->direccion = $direccion;
        $this->activo = $activo;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getNombreCompleto() {
        return $this->nombre_completo;
    }
    
    public function getFechaNacimiento() {
        return $this->fecha_nacimiento;
    }
    
    public function getGenero() {
        return $this->genero;
    }
    
    public function getPuesto() {
        return $this->puesto;
    }
    
    public function getFechaIngreso() {
        return $this->fecha_ingreso;
    }
    
    public function getCorreoElectronico() {
        return $this->correo_electronico;
    }
    
    public function getTelefonoCelular() {
        return $this->telefono_celular;
    }
    
    public function getTelefonoEmergencia() {
        return $this->telefono_emergencia;
    }
    
    public function getDireccion() {
        return $this->direccion;
    }
    
    
    public function getActivo() {
        return $this->activo;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setNombreCompleto($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
    }
    
    public function setFechaNacimiento($fecha_nacimiento) {
        $this->fecha_nacimiento = $fecha_nacimiento;
    }
    
    public function setGenero($genero) {
        $this->genero = $genero;
    }
    
    public function setPuesto($puesto) {
        $this->puesto = $puesto;
    }
    
    public function setFechaIngreso($fecha_ingreso) {
        $this->fecha_ingreso = $fecha_ingreso;
    }
    
    public function setCorreoElectronico($correo_electronico) {
        $this->correo_electronico = $correo_electronico;
    }
    
    public function setTelefonoCelular($telefono_celular) {
        $this->telefono_celular = $telefono_celular;
    }
    
    public function setTelefonoEmergencia($telefono_emergencia) {
        $this->telefono_emergencia = $telefono_emergencia;
    }
    
    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }
    
    
    public function setActivo($activo) {
        $this->activo = $activo;
    }
    


    //------------------------------------------------

    public function getCurp() {
        return $this->crup;
    }
    
    // Setters
    public function setCrup($curp) {
        $this->curp = $curp;
    }
  
}
?>