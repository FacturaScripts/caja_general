<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Caja General Mov
 *
 * @author Zapasoft
 */

require_model('cajas_general.php');
require_model('cajas_general_mov.php');

class caja_general_mov extends fs_controller
{ 
   public $recogidas_model;
   public $resultado;
   public $almacenes;
   public $allow_delete;
   public $agente;


   public function __construct() {
      parent::__construct(__CLASS__, 'Caja General', 'contabilidad', FALSE, FALSE);
      /// cualquier cosa que pongas aquí se ejecutará DESPUÉS de process()
   }

   /**
    * esta función se ejecuta si el usuario ha hecho login,
    * a efectos prácticos, este es el constructor
    */
   protected function process() {

        /// ¿El usuario tiene permiso para eliminar en esta página?
        $this->allow_delete = $this->user->allow_delete_on(__CLASS__);

        //Conseguimos el agente
        //$this->agente = $this->user->get_agente();

        //cargamos nuestro modelo vacio de tabla caja general
        //$this->recogidas_model = new caja_general_mov();

            /************
            // BUSCAR CAJA
            * ********** */
        if (isset($_POST['filtro_almacen'])) {
            $this->busqueda['filtro_almacen'] = $_POST['filtro_almacen'];
            $this->busqueda['desde'] = $_POST['desde'];
            $this->busqueda['hasta'] = $_POST['hasta'];

            //$this->resultado = $this->recogidas_model->search($this->busqueda['filtro_almacen'], $this->busqueda['desde'], $this->busqueda['hasta']);
            return;
        } 

        $this->offset = 0;
        if (isset($_GET['offset'])) {
            $this->offset = intval($_GET['offset']);
        }

        //$this->resultado = $this->recogidas_model->get_all_offset($this->offset);
    }
}

