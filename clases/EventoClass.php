<?php
include_once("ConexionClass.php");

class EventoClass
{
    public $conexion;
    public $id;
    public $fecha_inicio;
    public $fecha_fin;
    public $nombre_evento;
    public $estatus;
    public $num_part;
    public $minutos;
    public $cantidad_grup;
    public $hora_inicio;
    public $hora_fin;

    public function get()
    {
        $qry = "SELECT id, DATE(fecha_inicio) AS fecha_inicio, DATE(fecha_fin) AS fecha_fin, nombre_evento, estatus, cantidad_grup, min_grupo, num_part, hora_inicio, hora_fin
        FROM tbl_eventos
        WHERE estatus = 1";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function getAll()
    {
        $qry = "SELECT id, DATE(fecha_inicio) AS fecha_inicio, DATE(fecha_fin) AS fecha_fin, nombre_evento, estatus, cantidad_grup, num_part, hora_inicio, hora_fin
        FROM tbl_eventos";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function getId()
    {
        $qry = "SELECT id FROM tbl_eventos WHERE estatus = 1";
        $result = $this->conexion->consulta($qry);
        $resultRow = $this->conexion->fetch_assoc($result);
        return $resultRow['id'];
    }

    public function store()
    {
        $qry = "INSERT INTO tbl_eventos (fecha_inicio, fecha_fin, nombre_evento, estatus, num_part, cantidad_grup, min_grupo, hora_inicio, hora_fin) 
                VALUES ('$this->fecha_inicio', '$this->fecha_fin', '$this->nombre_evento', 1, $this->num_part, $this->cantidad_grup, $this->minutos, '$this->hora_inicio', '$this->hora_fin');";
        $this->conexion->consulta($qry);
        return 'Registrado';
    }

    public function update()
    {
        if (isset($this->id)) {

            if ($this->estatus == 1) {
                $qry1 = "UPDATE tbl_eventos SET estatus = 0;";
                $qry2 = "UPDATE tbl_fechas SET disponibilidad = 0;";
                $this->conexion->consulta($qry1);
                $this->conexion->consulta($qry2);
            }

            $qry1 = "UPDATE tbl_eventos SET estatus = $this->estatus WHERE id = $this->id;";
            $qry2 = "UPDATE tbl_fechas SET disponibilidad = $this->estatus WHERE id_evento = $this->id AND dia_semana NOT IN ('Saturday', 'Sunday');";
        } else {
            $qry1 = "UPDATE tbl_eventos SET estatus = 0 WHERE estatus = 1;";
            $qry2 = "UPDATE tbl_fechas SET disponibilidad = 0 WHERE disponibilidad = 1;";
        }

        $this->conexion->consulta($qry1);
        $this->conexion->consulta($qry2);
        return 'Deshabilitado';
    }
}
