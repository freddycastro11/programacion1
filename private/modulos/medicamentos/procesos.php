<?php 
/**
 * @author 5 tech <usis003118@ugb.edu.sv>
    *prosesos registrar en la base de datos de controles
 */

/**
 * conexion a la base de datos desde config
 */
include('../../config/config.php');
$medicamento = new medicamento($conexion);

$proceso = '';
if( isset($_GET['proceso']) && strlen($_GET['proceso'])>0 ){
	$proceso = $_GET['proceso'];
}
$medicamento->$proceso( $_GET['medicamento'] );
print_r(json_encode($medicamento->respuesta));
/**
 * @class control
 */
class medicamento{
    private $datos = array(), $db;
    public $respuesta = ['msg'=>'correcto'];
    
    public function __construct($db){
        $this->db=$db;
    }
    /**
     * @function recibirDatos recibe los datos del control
     * @param object $control representa los datos en si
     */
    public function recibirDatos($medicamento){
        $this->datos = json_decode($medicamento, true);
        $this->validar_datos();
    }
    /**
     * funcion para validar que todos los campos no esten vacios
     */
    private function validar_datos(){
        if( empty($this->datos['codigom']) ){
            $this->respuesta['msg'] = 'Por favor ingrese el codigo del medicamento.';
        }
        if( empty($this->datos['nombrem']) ){
            $this->respuesta['msg'] = 'Por favor ingrese el nombre del medicamento. ';
        }
        if( empty($this->datos['cantidad']) ){
            $this->respuesta['msg'] = 'Por favor ingrese la cantidad del medicamento.';
        }
        if( empty(trim($this->datos['tipo'])) ){
            $this->respuesta['msg'] = 'Por favor ingrese el tipo de medicamento.';
        }
        if( empty(trim($this->datos['ingreso'])) ){
            $this->respuesta['msg'] = 'Por favor ingrese el ingreso de medicamento.';
        }
        if( empty(trim($this->datos['fecha'])) ){
            $this->respuesta['msg'] = 'Por favor ingrese la caducidad de medicamento.';
        }
        if( empty(trim($this->datos['registro'])) ){
            $this->respuesta['msg'] = 'Por favor ingrese la fecha de registro del medicamento.';
        }
        $this->almacenar_medicamento();
    }
    /**
     * funcion para almacenar en la tabla de medicamentos
     * se introducen los datos obtenidos a los campos de la tabla en myqsl 
     */
    private function almacenar_medicamento(){
        if( $this->respuesta['msg']==='correcto' ){
            if( $this->datos['accion']==='nuevo' ){
                $this->db->consultas('
                    INSERT INTO medicamentos (codigom,nombrem,cantidad,tipo,ingreso,fecha,registro) VALUES(
                        "'. $this->datos['codigom'] .'",
                        "'. $this->datos['nombrem'] .'",
                        "'. $this->datos['cantidad'] .'",
                        "'. $this->datos['tipo'] .'",
                        "'. $this->datos['ingreso'] .'",
                        "'. $this->datos['fecha'] .'",
                        "'. $this->datos['registro'] .'"
                    )
                ');
                $this->respuesta['msg'] = 'Registro insertado correctamente';//mensaje de registrado
                /**
                 * se obtienen los datos que se actualizan y los actualiza los campos de la tabla
                 */
            } else if( $this->datos['accion']==='modificar' ){
                $this->db->consultas('
                    UPDATE medicamentos SET
                        codigom     = "'. $this->datos['codigom'] .'",
                        nombrem     = "'. $this->datos['nombrem'] .'",
                        cantidad   = "'. $this->datos['cantidad'] .'",
                        tipo   = "'. $this->datos['tipo'] .'",
                        ingreso   = "'. $this->datos['ingreso'] .'",
                        fecha      = "'. $this->datos['fecha'] .'",
                        registro      = "'. $this->datos['registro'] .'"
                    WHERE idMedicamento = "'. $this->datos['idMedicamento'] .'"
                ');
                $this->respuesta['msg'] = 'Registro actualizado correctamente';//mensaje que se actualizo
            }
        }
    }
    /**
     * funcion de buscar los datos de de la tabla medicamentos y se realiza la consuta para que muetre 
     * todos los campos
     */
    public function buscarMedicamento($valor = ''){
        $this->db->consultas('
            select medicamentos.idMedicamento, medicamentos.codigom, medicamentos.nombrem, medicamentos.cantidad, medicamentos.tipo, medicamentos.ingreso, medicamentos.fecha, medicamentos.registro
            from medicamentos
            where medicamentos.codigom like "%'. $valor .'%" or medicamentos.nombrem like "%'. $valor .'%" or medicamentos.tipo like "%'. $valor .'%"
            order by fecha
            ');
        return $this->respuesta = $this->db->obtener_data();
    }
    /**
     * funcion para eliminar un registro de la tabla control
     */
    public function eliminarMedicamento($idMedicamento = 0){
        $this->db->consultas('
            DELETE medicamentos
            FROM medicamentos
            WHERE medicamentos.idMedicamento="'.$idMedicamento.'"
        ');
        return $this->respuesta['msg'] = 'Registro eliminado correctamente';
    }
}
?>