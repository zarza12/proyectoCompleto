<?php
class Entregas {
    // Atributos privados
    private $idEntregas;
    private $fechaEntrega;
    private $calidad_producto;
    private $cantidad_productos;
    private $nombre_empresa;
    private $email_comprador;
    private $nombre_transportista;

    // Constructor
    public function __construct($idEntregas = null, $fechaEntrega = null, $calidad_producto = null, 
                               $cantidad_productos = null, $nombre_empresa = null, 
                               $email_comprador = null, $nombre_transportista = null) {
        $this->idEntregas = $idEntregas;
        $this->fechaEntrega = $fechaEntrega;
        $this->calidad_producto = $calidad_producto;
        $this->cantidad_productos = $cantidad_productos;
        $this->nombre_empresa = $nombre_empresa;
        $this->email_comprador = $email_comprador;
        $this->nombre_transportista = $nombre_transportista;
    }

    // Getters
    public function getIdEntregas() {
        return $this->idEntregas;
    }

    public function getFechaEntrega() {
        return $this->fechaEntrega;
    }

    public function getCalidadProducto() {
        return $this->calidad_producto;
    }

    public function getCantidadProductos() {
        return $this->cantidad_productos;
    }

    public function getNombreEmpresa() {
        return $this->nombre_empresa;
    }

    public function getEmailComprador() {
        return $this->email_comprador;
    }

    public function getNombreTransportista() {
        return $this->nombre_transportista;
    }

    // Setters
    public function setIdEntregas($idEntregas) {
        $this->idEntregas = $idEntregas;
    }

    public function setFechaEntrega($fechaEntrega) {
        $this->fechaEntrega = $fechaEntrega;
    }

    public function setCalidadProducto($calidad_producto) {
        $this->calidad_producto = $calidad_producto;
    }

    public function setCantidadProductos($cantidad_productos) {
        $this->cantidad_productos = $cantidad_productos;
    }

    public function setNombreEmpresa($nombre_empresa) {
        $this->nombre_empresa = $nombre_empresa;
    }

    public function setEmailComprador($email_comprador) {
        $this->email_comprador = $email_comprador;
    }

    public function setNombreTransportista($nombre_transportista) {
        $this->nombre_transportista = $nombre_transportista;
    }
}
?>