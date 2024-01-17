<?php

class HorarioClass
{

    public $conexion;
    public $id;
    public $id_evento;
    public $id_fecha;
    public $id_horario;
    public $maxPart;
    public $hora_inicio;
    public $hora_fin;

    public function get()
    {
        $qry = "SELECT * FROM tbl_horarios WHERE id_evento = $this->id_evento AND id_fecha = $this->id_fecha ORDER BY hora_inicio;";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function ultimoHorario()
    {
        $qry = "SELECT id, hora_inicio, hora_fin FROM tbl_horarios WHERE id_evento = $this->id_evento AND id_fecha = $this->id_fecha ORDER BY hora_fin DESC LIMIT 1;";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function disponibilidad()
    {
        $qry = "SELECT h.*
        FROM tbl_horarios h
        INNER JOIN tbl_eventos e ON h.id_evento = e.id
        LEFT JOIN (
            SELECT id_horario, COUNT(*) as cantidad_participantes
            FROM tbl_participantes
            GROUP BY id_horario
        ) p ON h.id = p.id_horario
        WHERE (p.id_horario IS NULL OR p.cantidad_participantes < e.cantidad_grup)
        AND h.id_evento = $this->id_evento AND h.id_fecha = $this->id_fecha;";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function participantes()
    {
        $qry = "SELECT COUNT(*) FROM tbl_participantes WHERE id_horario = $this->id;";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function store()
    {
        try {
            $qry = "INSERT INTO tbl_horarios (id_evento, id_fecha, hora_inicio, hora_fin) VALUES ($this->id_evento, $this->id_fecha, '$this->hora_inicio', '$this->hora_fin')";
            $this->conexion->consulta($qry);
            $last_id = $this->conexion->ultimo_id();
            return 'Horario creado, ID: ' . $last_id;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
