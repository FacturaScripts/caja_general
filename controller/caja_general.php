<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Caja General
 *
 * @author Zapasoft
 */

require_model('cajas_general.php');
require_model('cajas_general_mov.php');

class caja_general extends fs_controller
{ 
   public $recogidas_model;
   public $resultado;
   public $almacenes;
   public $allow_delete;
   public $busqueda;
   public $offset;
   public $agente;


   public function __construct() {
      parent::__construct(__CLASS__, 'Caja General', 'contabilidad', FALSE, TRUE);
      /// cualquier cosa que pongas aquí se ejecutará DESPUÉS de process()
   }

   /**
    * esta función se ejecuta si el usuario ha hecho login,
    * a efectos prácticos, este es el constructor
    */
   protected function process() {
        $this->busqueda = array(
            'contenido' => '',
            'filtro_almacen' => '',
            'desde' => '',
            'hasta' => '',
            'orden' => 'fecha'
        );
        /// ¿El usuario tiene permiso para eliminar en esta página?
        $this->allow_delete = $this->user->allow_delete_on(__CLASS__);

        //Consultamos almacenes existentes
        $almacenes = new almacen();
        $this->almacenes = $almacenes->all();
        //Conseguimos el agente
        $this->agente = $this->user->get_agente();

        //cargamos nuestro modelo vacio de tabla caja general
        $this->recogidas_model = new cajas_general();

            /************
            // BUSCAR CAJA
            * ********** */
        if (isset($_POST['filtro_almacen'])) {
            $this->busqueda['filtro_almacen'] = $_POST['filtro_almacen'];
            $this->busqueda['desde'] = $_POST['desde'];
            $this->busqueda['hasta'] = $_POST['hasta'];

            $this->resultado = $this->recogidas_model->search($this->busqueda['filtro_almacen'], $this->busqueda['desde'], $this->busqueda['hasta']);
            return;
        } elseif($_POST['almacen']!= '') {
            /************
            // ABRIR CAJA
            * ********** */
            if ($this->recogidas_model->disponible($_POST['almacen'])) {
                $this->recogidas_model->codalmacen = $_POST['almacen'];
                $this->recogidas_model->d_inicio = floatval($_POST['d_inicio']);
                $this->recogidas_model->codagente = $this->agente->codagente;
                if ($this->recogidas_model->save()) {
                    $this->new_message("Caja iniciada con " . $this->show_numero($this->recogidas_model->d_inicio, 2) . ' €');
                } else
                    $this->new_error_msg("¡Imposible guardar los datos de caja!");
            } else
                $this->new_error_msg("¡Caja ya abierta para este Almacen!");
        } else if (isset($_GET['delete'])) {
            /*             * ***********
              // ELIMINAR CAJA
             * ********** */
            $caja2 = $this->recogidas_model->get($_GET['delete']);
            if ($caja2) {
                if ($caja2->delete()) {
                    $this->new_message("Caja eliminada correctamente.");
                } else
                    $this->new_error_msg("¡Imposible eliminar la caja!");
            } else
                $this->new_error_msg("Caja no encontrada.");
        } else if (isset($_GET['cerrar'])) {
            /*             * ***********
              // CERRAR CAJA
             * ********** */
            $caja2 = $this->recogidas_model->get($_GET['cerrar']);
            if ($caja2) {
                $caja2->f_fin = Date('d-m-Y H:i:s');
                $caja2->codagente_fin = $this->agente->codagente;
                if ($caja2->save()) {
                    $this->new_message("Caja cerrada correctamente.");
                } else
                    $this->new_error_msg("¡Imposible cerrar la caja!");
            } else
                $this->new_error_msg("Caja no encontrada.");
        }

        $this->offset = 0;
        if (isset($_GET['offset'])) {
            $this->offset = intval($_GET['offset']);
        }

        $this->resultado = $this->recogidas_model->get_all_offset($this->offset);
    }

    public function anterior_url()
   {
      $url = '';
      
      if($this->offset > 0)
      {
         $url = $this->url()."&offset=".($this->offset-FS_ITEM_LIMIT);
      }
      
      return $url;
   }
   
   public function siguiente_url()
   {
      $url = '';
      
      if( count($this->resultados) == FS_ITEM_LIMIT )
      {
         $url = $this->url()."&offset=".($this->offset+FS_ITEM_LIMIT);
      }
      
      return $url;
   }    

}
