<?php

class ParticipanteClass
{
    public $conexion;
    public $id;
    public $nombre;
    public $id_evento;
    public $id_fecha;
    public $id_horario;

    public function get()
    {
        $qry = "SELECT * FROM tbl_participantes AS part WHERE part.id_evento =  $this->id_evento AND part.id_fecha = $this->id_fecha;";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function evento()
    {
        $qry = "SELECT fecha FROM tbl_participantes AS part
                LEFT JOIN tbl_fechas AS fecha 
                ON (part.id_evento = fecha.id_evento AND fecha.id_fecha = part.id_fecha) AND part.id = $this->id;";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function count()
    {
        $qry = "SELECT COUNT(*) AS participantes FROM tbl_participantes AS part WHERE part.id_evento =  $this->id_evento";
        $result = $this->conexion->consulta($qry);
        $resultRow = $this->conexion->fetch_assoc($result);
        return $resultRow['participantes'];
        // return $qry;
    }

    public function countDate()
    {
        $qry = "SELECT COUNT(*) AS participantes FROM tbl_participantes AS part WHERE part.id_evento =  $this->id_evento AND part.id_fecha = $this->id_fecha;";
        $result = $this->conexion->consulta($qry);
        $resultRow = $this->conexion->fetch_assoc($result);
        return $resultRow['participantes'];
        // return $qry;
    }
    public function store()
    {
        $qry = "INSERT INTO tbl_participantes (id, nombre, id_evento, id_fecha) 
        VALUES ($this->id, '$this->nombre', $this->id_evento, $this->id_fecha);";
        $this->conexion->consulta($qry);
        return 'Registro Completo';
    }

    public function updateHorario()
    {
        try {
            $qry = "UPDATE tbl_participantes
                    SET id_horario = $this->id_horario
                    WHERE id = $this->id";              // ? Aquí podría ser CURP o RFC, etc..
            $this->conexion->consulta($qry);
            return 'Update correcto';
        } catch (Exception $e) {
            return 'Error al actualizar el horario: ' . $e->getMessage();
        }
    }
}
