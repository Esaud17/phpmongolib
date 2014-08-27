<?php #by oerl 2014 © 
  namespace mongodbdado;
   #requiere libreria de cifrado
   require_once cifrado;
   #libreia de conecion a mongodb para grestion de uso de los mentodos principales
   use MongoClient;
   #interface de implementacion de metodos comunes para las clases relacionadas en formato abstracto
    interface conexion_to_parents
    {
      public function find_sort_limit_dat($array_query,$array_filtre,$array_sort,$int_limit);
      public function find_one_dat($array_query,$array_filtre);
      public function find_dat($array_query,$array_filtre);
      public function insert_dat($array_query);
      public function remove_dat($array_query,$array_filtre);
      public function update_dat($array_query,$array_filtre);
    }
    #clase de conexion para el sistema de onmy de manera abstracta
    abstract class conexion
    {
       #atributos de construcion primaria para funcionamiento de datos
       private static $str_db_onload;
       protected static $str_coll_onload;
       #elementos privados y estaticos para gestionar la connecion de mongo
       private  $conn;  #cursor de la connecion
       private  $db;          #database use 
       private  $collection;        #collecion de datos
       
       protected static $filto_set;        #heredado a los modelos consulta consulta de datos
       protected static $filtro_get;       #heredado a los modelos para obtener parametros X en la seleecion del filtro set
      
       protected static $filto_sort;       #herando a los modelos el filtro  de ordenamiento 
       protected static $filto_limit;      #herando a los modelos el atributo de limite de busqueda
      
       private $datagetjson;        #data obtenida y conventrida en json para ser enviados
       protected $errosout;         #control de erroes en procesos de peticiones de datos
      # Constructos de la clase abstracta
      public function __construct (){}
      #constructor crea los cursores necesarios para gestioar mediante php a mongo durante la instanciacion
       private function load_conneccion()
        {
         $this->conn= new MongoClient(uri);
         self::$str_db_onload= db;
         $this->db = $this->conn->selectDB(self::$str_db_onload);
        }
       #metodo de insercion de datos 
       protected function insert_data()
        {
            self::load_conneccion();
            try
            {
             if(is_array(self::$filto_set)&& count(self::$filto_set)>0)
             {
              $this->collection = $this->db->selectCollection(self::$str_coll_onload);
              $this->collection->insert(self::$filto_set);
             }
            }
            catch(Exception $e)
            {$errosout = "Error de para agregar datos en la colleccion: ".self::$str_coll_onload;}
        }
        #metodo de optencion de data 
        protected function get_data()
        {
             self::load_conneccion();
             $this->datagetjson = array();
            $result =array();
            try
            {
              $this->collection = $this->db->selectCollection(self::$str_coll_onload);
              if(is_array(self::$filto_set)&& is_array(self::$filtro_get)){
               $result=$this->collection->find(self::$filto_set,self::$filtro_get);
                $this->datagetjson = array();
               if(!$result==null)
               { 
                  foreach($result as $key=>$dat)
                  {
                    $this->datagetjson['data'][$key] = $dat; 
                  }
               }
               
               if(isset($this->datagetjson['data']))
               {
                 $this->datagetjson['sucess']='ok';
               }
               else
               {
                $this->datagetjson['sucess']='err';
               }
              }
            }
            catch(Exception $e)
            {$errosout = "Error de obtención datos en la colleccion ".self::$str_coll_onload; }
            
            return $this->datagetjson ;
        }
           #metodo de optencion de data con ordenamiento y limite 
        protected function get_sort_limit_data()
        {
            self::load_conneccion();            
            $this->datagetjson = array();
            $result =array();
            try
            {
              $this->collection = $this->db->selectCollection(self::$str_coll_onload);
              if(is_array(self::$filto_set)&& is_array(self::$filtro_get)&&is_array(self::$filto_sort)&&is_integer(self::$filto_limit)){
                $result=$this->collection->find(self::$filto_set,self::$filtro_get)->sort(self::$filto_sort)->limit(self::$filto_limit);
                $this->datagetjson = array();
                if(!$result==null)
                { 
                  foreach($result as $key=>$dat)
                  {
                    $this->datagetjson['data'][$key] = $dat; 
                  }
                }
               if(isset($this->datagetjson['data']))
               {
                 $this->datagetjson['sucess']='ok';
               }
               else
               {
                $this->datagetjson['sucess']='err';
               }
              }
            }
            catch(Exception $e)
            {$errosout = "Error de obtención datos en la colleccion ".self::$str_coll_onload; }
            
            return $this->datagetjson ;
        }
        #metodo de optencion de data pero solo un documento
        protected function getone_data()
        {
             self::load_conneccion();
             $this->datagetjson = array();
            $result =array();
            try
            {
              $this->collection = $this->db->selectCollection(self::$str_coll_onload);
              if(is_array(self::$filto_set)&& is_array(self::$filtro_get)){
               $result=$this->collection->findone(self::$filto_set,self::$filtro_get);
                $this->datagetjson = array();
               if(!$result==null)
                { 
                  foreach($result as $key=>$dat)
                  {
                    $this->datagetjson['data'][$key] = $dat; 
                  }
                }
               if(isset($this->datagetjson['data']))
               {
                 $this->datagetjson['sucess']='ok';
               }
               else
               {
                $this->datagetjson['sucess']='err';
               }
              }
            }
            catch(Exception $e)
            {$errosout = "Error de obtención datos en la colleccion ".self::$str_coll_onload; }
            
            return $this->datagetjson ;
        }
      #metodo de  eliminar data
       protected function delete_data()
        {
             self::load_conneccion();
            try
            {
             if(!empty($strcollection))
             {
               $this->collection = $this->db->selectCollection(self::$str_coll_onload);
               if(is_array(self::$filto_set)&&is_array(self::$filtro_get)){
                $result=$this->collection->remove(self::$filto_set,self::$filtro_get);# filtro set criterio y filtro get solo es uno en mongo client
                }
              }
            }
            catch(Exception $e)
            {$errosout = "Error al remover datos en la colleccion ".self::$str_coll_onload;}
        }
        #metodo de actulizacion de datos
        protected function update_data()
        {
             self::load_conneccion();
            try
            {
              if(empty($strcollection))
              {
                $this->collection = $this->db->selectCollection(self::$str_coll_onload);
                 if(is_array(self::$filto_set)&&is_array(self::$filtro_get)){
                 $result=$this->collection->update(self::$filtro_get,self::$filto_set); # filtro get es el criterio y filtro set es el nuevo documento introducir
                }
              }
            }
            catch(Exception $e)
            {$errosout = "Error al update datos en la colleccion ".self::$str_coll_onload;}
        }
        
    }
?>