<?php
/*
* Dependencies generated by the foreign keys
*/

require_once 'base/fs_model.php';
require_model('agente.php');
require_model('almacen.php');

class cajas_general extends fs_model
{
   /**
     * Clave primaria.
     * @var type 
     */
    public $id;

    /**
     * Codigo del agente que abre y usa la caja.
     * El agente asociado al usuario.
     * @var type 
     */
    public $codagente;
    public $codagente_fin;    
   /**
    * Identificador del terminal. En la tabla cajas_terminales.
    * @var type 
    */
    public $codalmacen;  
    
    public $f_inicio;
    public $d_inicio;
    public $f_fin;
    public $d_fin;

    /**
     * Numero de tickets emitidos en esta caja.
     * @var type 
     */
    public $apuntes;

    /**
     * Ultima IP del usuario de la caja.
     * @var type 
     */
    public $ip;

    /**
     * El objeto agente asignado.
     * @var type 
     */
    public $agente;

    /**
     * UN array con todos los agentes utilizados, para agilizar la carga.
     * @var type 
     */
    private static $agentes;

    public function __construct($data = FALSE) {
        parent::__construct('cajas_general', 'plugins/caja_general/');

        if (!isset(self::$agentes)) {
            self::$agentes = array();
        }

        if ($data) {

            $this->id = $this->intval($data['id']);
            $this->codalmacen = $data['codalmacen'];
            $this->codagente = $data['codagente'];
            $this->f_inicio = Date('d-m-Y H:i:s', strtotime($data['f_inicio']));
            $this->d_inicio = floatval($data['d_inicio']);
            $this->codagente_fin = $data['codagente_fin'];

            if (is_null($data['f_fin'])) {
                $this->f_fin = NULL;
            } else
                $this->f_fin = Date('d-m-Y H:i:s', strtotime($data['f_fin']));

            $this->d_fin = floatval($data['d_fin']);
            $this->apuntes = $this->intval($data['apuntes']);

            $this->ip = NULL;
            if (isset($data['ip'])) {
                $this->ip = $data['ip'];
            }

            foreach (self::$agentes as $ag) {
                if ($ag->codagente == $this->codagente) {
                    $this->agente = $ag;
                    break;
                }
            }

            if (!isset($this->agente)) {
                $ag = new agente();
                $this->agente = $ag->get($this->codagente);
                self::$agentes[] = $this->agente;
            }
        } else {
            $this->id = NULL;
            $this->codalmacen = NULL;
            $this->codagente = NULL;
            $this->f_inicio = Date('d-m-Y H:i:s');
            $this->d_inicio = 0;
            $this->codagente_fin = NULL;
            $this->f_fin = NULL;    
            $this->d_fin = 0;
            $this->apuntes = 0;
            
            $this->ip = NULL;
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $this->ip = $_SERVER['REMOTE_ADDR'];
            }

            $this->agente = NULL;
            
        }
    }

    /**
     * Esta función es llamada al crear una tabla.
     * Permite insertar valores en la tabla.
     */
    protected function install()
    {
        return '';
    }

   public function abierta()
   {
      return is_null($this->f_fin);
   }
   
   public function show_fecha_fin()
   {
      if( is_null($this->f_fin) )
      {
         return '-';
      }
      else
         return $this->f_fin;
   }
   
   public function diferencia()
   {
      return ($this->d_inicio - $this->d_fin);
   }    
   
   public function url()
   {
       if( is_null($this->id) )
      {
         return 'index.php?page=caja_general_mov';
      }
      else
      {
         return 'index.php?page=caja_general_mov&id='.$this->id;
      }
   }  
   
   public function nombre_almacen(){
       $almacen = new almacen();
       return $almacen->get($this->codalmacen);
   }

   public function disponible($almacen = '') {
        if ($almacen != '') {
            if ($this->db->select("SELECT * FROM {$this->table_name} WHERE f_fin IS NULL AND codalmacen = " . $this->var2str($almacen) . ";")) {
                return FALSE;
            } else
                return TRUE;
        }else {
            if ($this->db->select("SELECT * FROM {$this->table_name} WHERE f_fin IS NULL AND codalmacen = " . $this->var2str($this->codalmacen) . ";")) {
                return FALSE;
            } else
                return TRUE;
        }
    }

    /**
     * Esta función devuelve TRUE si los datos del objeto se encuentran
     * en la base de datos.
     */
   public function exists()
   {
      if( is_null($this->id) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM ".$this->table_name." WHERE id = ".$this->var2str($this->id).";");
   }

    /**
     * Esta función sirve tanto para insertar como para actualizar
     * los datos del objeto en la base de datos.
     */
    public function save()
    {
        $sql = "";
        if($this->exists())
        {
            $value = $this->var2str($this->id);
            if($this->id)
            {
                $sql = "UPDATE {$this->table_name} SET id = " . $this->var2str($this->id) . "
                        , codalmacen = " . $this->var2str($this->codalmacen) . "
                        , codagente = " . $this->var2str($this->codagente) . "
                        , f_inicio = " . $this->var2str($this->f_inicio) . "
                        , d_inicio = " . $this->var2str($this->d_inicio) . "
                        , codagente_fin = " . $this->var2str($this->codagente_fin) . "
                        , f_fin = " . $this->var2str($this->f_fin) . "
                        , d_fin = " . $this->var2str($this->d_fin) . "
                        , apuntes = " . $this->var2str($this->apuntes) . "
                        , ip = " . $this->var2str($this->ip) . "
                          WHERE id = $value";
                return $this->db->exec($sql);
            }
            
        }
        else
        {
            $sql = "INSERT INTO {$this->table_name} (
                                    id
                                    , codalmacen
                                    , codagente
                                    , f_inicio
                                    , d_inicio
                                    , codagente_fin
                                    , f_fin
                                    , d_fin
                                    , apuntes
                                    , ip
                                    
                                ) VALUES (
                                     " . $this->var2str($this->id) . "
                                    ,  " . $this->var2str($this->codalmacen) . "
                                    ,  " . $this->var2str($this->codagente) . "
                                    ,  " . $this->var2str($this->f_inicio) . "
                                    ,  " . $this->var2str($this->d_inicio) . "
                                    ,  " . $this->var2str($this->codagente_fin) . "
                                    ,  " . $this->var2str($this->f_fin) . "
                                    ,  " . $this->var2str($this->d_fin) . "
                                    ,  " . $this->var2str($this->apuntes) . "
                                    ,  " . $this->var2str($this->ip) . "
                                    
                                )";
            return $this->db->exec($sql);
        }

        return false;
    }

    /**
     * Esta función sirve para eliminar los datos del objeto de la base de datos
     */
    public function delete()
    {
        return $this->db->exec("DELETE FROM ".$this->table_name." WHERE id = ".$this->var2str($this->id).";");
    }
    
    public function get($cod)
    {
        $cod = $this->var2str($cod);
        return $this->parse($this->db->select("SELECT * FROM {$this->table_name} WHERE id = $cod"));
    }
    
    public function get_all_offset($offset=0, $limit=FS_ITEM_LIMIT)
    {
        return $this->parse($this->db->select_limit("SELECT * FROM {$this->table_name} ORDER BY id DESC", $limit, $offset), true);
    }
    public function get_all()
    {
        return $this->parse($this->db->select("SELECT * FROM {$this->table_name} ORDER BY id DESC"), true);
    }
    public function parse($items, $array = false)
    {
        if(count($items) > 1 || $array)
        {
            $list = array();
            foreach($items as $item)
            {
                $list[] = new cajas_general($item);
            }
            return $list;
        }
        else if(count($items) == 1)
        {
            return new cajas_general($items[0]);
        }
        return null;
    }
    
    public function search($almacen = '', $desde = '', $hasta = '', $orden = "f_inicio") {
        $entidadlist = array();

        $sql = "SELECT *
                FROM {$this->table_name}
                WHERE id > 0";
        
        
        //Primero compruebo si hay texto a buscar
        if ($almacen != '') {
            $sql .= " AND `codalmacen` = " . $this->var2str($almacen);
        }

        if ($desde != '') {
            $sql .= " AND `f_fin` >= " . $this->var2str($desde);
        }

        if ($hasta != '') {
            $sql .= " AND `f_fin` <= " . $this->var2str($hasta);
        }

        //Finalmente compruebo el orden
        $sql.= " ORDER BY " . $orden . " DESC ";

        $data = $this->db->select($sql . ";");
        if ($data) {
            foreach ($data as $d)
                $entidadlist[] = new cajas_general ($d);
        }

        return $entidadlist;
    }     

}