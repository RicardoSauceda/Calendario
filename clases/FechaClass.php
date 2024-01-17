<?php
include_once("ConexionClass.php");

$studen = true;

class FechaClass
{
    public $conexion;
    public $id_fecha;
    public $id_evento;
    public $dia_semana;
    public $dia_mes;
    public $mes;
    public $anio;
    public $disponibilidad;
    public $fecha;
    public $maxPart;

    public function get()
    {
        $qry = "SELECT id_fecha, id_evento, dia_semana, dia_mes, anio, disponibilidad, date(fecha) as amd,
        CASE
          WHEN mes = 'January' THEN 'ENERO'
          WHEN mes = 'February' THEN 'FEBRERO'
          WHEN mes = 'March' THEN 'MARZO'
          WHEN mes = 'April' THEN 'ABRIL'
          WHEN mes = 'May' THEN 'MAYO'
          WHEN mes = 'June' THEN 'JUNIO'
          WHEN mes = 'July' THEN 'JULIO'
          WHEN mes = 'August' THEN 'AGOSTO'
          WHEN mes = 'September' THEN 'SEPTIEMBRE'
          WHEN mes = 'October' THEN 'OCTUBRE'
          WHEN mes = 'November' THEN 'NOVIEMBRE'
          WHEN mes = 'December' THEN 'DICIEMBRE'
        END AS mes
        FROM tbl_fechas WHERE id_evento = $this->id_evento";
        $result = $this->conexion->consulta($qry);
        return $result;
    }
    
    public function getPaginate($pagina_actual, $resultados_por_pagina)
    {
        $offset = ($pagina_actual - 1) * $resultados_por_pagina;
        $qry = "SELECT id_fecha, id_evento, dia_semana, dia_mes, anio, disponibilidad,
        CASE
          WHEN mes = 'January' THEN 'ENERO'
          WHEN mes = 'February' THEN 'FEBRERO'
          WHEN mes = 'March' THEN 'MARZO'
          WHEN mes = 'April' THEN 'ABRIL'
          WHEN mes = 'May' THEN 'MAYO'
          WHEN mes = 'June' THEN 'JUNIO'
          WHEN mes = 'July' THEN 'JULIO'
          WHEN mes = 'August' THEN 'AGOSTO'
          WHEN mes = 'September' THEN 'SEPTIEMBRE'
          WHEN mes = 'October' THEN 'OCTUBRE'
          WHEN mes = 'November' THEN 'NOVIEMBRE'
          WHEN mes = 'December' THEN 'DICIEMBRE'
        END AS mes
        FROM tbl_fechas WHERE id_evento = $this->id_evento
        LIMIT $resultados_por_pagina OFFSET $offset";
        $result = $this->conexion->consulta($qry);
        return $result;
    }

    public function store()
    {
        $qry = "INSERT INTO tbl_fechas (id_evento, dia_semana, dia_mes, mes, anio, disponibilidad, fecha) 
                VALUES ($this->id_evento, '$this->dia_semana', $this->dia_mes, '$this->mes', $this->anio, $this->disponibilidad, '$this->fecha 00:00:00');";
        $result = $this->conexion->consulta($qry);
        return 'Deshabilitado';
    }

    public function update()
    {
        $qry = "UPDATE tbl_fechas
        SET disponibilidad = $this->disponibilidad
        WHERE id_fecha = $this->id_fecha;";
        $result = $this->conexion->consulta($qry);
        return 'Deshabilitado';
    }

    public function disponible()
    {
        $qry = "SELECT COUNT(*) < $this->maxPart AS disp
                FROM tbl_participantes
                WHERE id_fecha = $this->id_fecha AND id_evento = $this->id_evento";
        $result = $this->conexion->consulta($qry);
        $row = $this->conexion->fetch_assoc($result);
        if ($row['disp'] == 1) {
            $qry = "SELECT disponibilidad
                    FROM tbl_fechas
                    WHERE id_fecha = $this->id_fecha AND id_evento = $this->id_evento";
            $result = $this->conexion->consulta($qry);
            $row = $this->conexion->fetch_assoc($result);
            return $row['disponibilidad'] == 1 ? 1 : 0;
        } else {
            return 0;
        }
    }
}
