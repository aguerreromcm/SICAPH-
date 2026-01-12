<?php

namespace Models;

use Core\Model;
use Core\Database;

class Formatos extends Model
{
    public static function getListaFormatosCultiva($datos)
    {
        $qry = <<<SQL
            SELECT
                ID
                , NOMBRE
                , TO_CHAR(FECHA_SUBIDA, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_SUBIDA
                , TO_CHAR(VIGENCIA_FIN, 'YYYY-MM-DD') AS VIGENCIA_FIN
                , ACCESO
            FROM
                REPOSITORIO_CAPITALH
            WHERE
                TRUNC(FECHA_SUBIDA) BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
            ORDER BY
                FECHA_SUBIDA DESC
        SQL;

        $val = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        try {
            $db = new Database('cultiva');
            $res = $db->queryAll($qry, $val);
            return self::resultado(true, 'Formatos obtenidos correctamente.', $res);
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al obtener los formatos.', null, $e->getMessage());
        }
    }

    public static function getFormatoCultiva($datos)
    {
        $qry = "SELECT ARCHIVO FROM REPOSITORIO_CAPITALH WHERE ID = :idFormato";

        $val = [
            'idFormato' => $datos['idFormato']
        ];

        try {
            $db = new Database('cultiva');
            $res = $db->queryOne($qry, $val);
            if (!$res) throw new \Exception("Formato no encontrado.");
            return self::resultado(true, 'Formato obtenido correctamente.', $res['ARCHIVO']);
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al obtener el formato.', null, $e->getMessage());
        }
    }

    public static function registraFormatoCultiva($datos)
    {
        $qryA = <<<SQL
            INSERT INTO REPOSITORIO_CAPITALH (ARCHIVO, NOMBRE, TIPO)
            VALUES (EMPTY_BLOB(), :nombre, :tipo)
            RETURNING ARCHIVO, ID INTO :archivo, :id
        SQL;

        $valA = [
            'nombre' => $datos['nombre'],
            'tipo' => $datos['tipo']
        ];

        $retA = [
            'archivo' => [
                'valor' => $datos['archivo'],
                'tipo' => \PDO::PARAM_LOB
            ],
            'id' => [
                'valor' => '',
                'tipo' => \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT,
                'largo' => 40
            ]
        ];

        try {
            $db = new Database('cultiva');
            $db->beginTransaction();
            $db->CRUD($qryA, $valA, $retA);

            if (!$retA['id']['valor']) throw new \Exception("Error al insertar el formato.");

            $db->commit();
            return self::resultado(true, 'Formato registrado correctamente.', ['formatoId' => $retA['id']['valor']]);
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al registrar el formato.', null, $e->getMessage());
        }
    }

    public static function getFormatoMCM($datos)
    {
        $qry = "SELECT ARCHIVO FROM REPOSITORIO_CAPITALH WHERE ID = :idFormato";

        $val = [
            'idFormato' => $datos['idFormato']
        ];

        try {
            $db = new Database('mcm');
            $res = $db->queryOne($qry, $val);
            if (!$res) throw new \Exception("Formato no encontrado.");
            return self::resultado(true, 'Formato obtenido correctamente.', $res['ARCHIVO']);
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al obtener el formato.', null, $e->getMessage());
        }
    }

    public static function registraFormatoMCM($datos)
    {
        $qryA = <<<SQL
            INSERT INTO REPOSITORIO_CAPITALH (ARCHIVO, NOMBRE, TIPO)
            VALUES (EMPTY_BLOB(), :nombre, :tipo)
            RETURNING ARCHIVO, ID INTO :archivo, :id
        SQL;

        $valA = [
            'nombre' => $datos['nombre'],
            'tipo' => $datos['tipo']
        ];

        $retA = [
            'archivo' => [
                'valor' => $datos['archivo'],
                'tipo' => \PDO::PARAM_LOB
            ],
            'id' => [
                'valor' => '',
                'tipo' => \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT,
                'largo' => 40
            ]
        ];

        try {
            $db = new Database('mcm');
            $db->CRUD($qryA, $valA, $retA);

            if (!$retA['id']['valor']) throw new \Exception("Error al insertar el formato.");

            return self::resultado(true, 'Formato registrado correctamente.', ['formatoId' => $retA['id']['valor']]);
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al registrar el formato.', null, $e->getMessage());
        }
    }

    public static function eliminarFormatoCultiva($datos)
    {
        $qry = "DELETE FROM REPOSITORIO_CAPITALH WHERE ID = :idFormato";

        $val = [
            'idFormato' => $datos['idFormato']
        ];

        try {
            $db = new Database('cultiva');
            $db->CRUD($qry, $val);
            return self::resultado(true, 'Formato eliminado correctamente.');
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al eliminar el formato.', null, $e->getMessage());
        }
    }

    public static function getListaFormatosMCM($datos)
    {
        $qry = <<<SQL
            SELECT
                ID
                , NOMBRE
                , TO_CHAR(FECHA_SUBIDA, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_SUBIDA
                , TO_CHAR(VIGENCIA_FIN, 'YYYY-MM-DD') AS VIGENCIA_FIN
                , ACCESO
            FROM
                REPOSITORIO_CAPITALH
            WHERE
                TRUNC(FECHA_SUBIDA) BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
            ORDER BY
                FECHA_SUBIDA DESC
        SQL;

        $val = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        try {
            $db = new Database('mcm');
            $res = $db->queryAll($qry, $val);
            return self::resultado(true, 'Formatos obtenidos correctamente.', $res);
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al obtener los formatos.', null, $e->getMessage());
        }
    }

    public static function getFormatoMCM($datos)
    {
        $qry = "SELECT ARCHIVO, TIPO FROM REPOSITORIO_CAPITALH WHERE ID = :idFormato";

        $val = [
            'idFormato' => $datos['idFormato']
        ];

        try {
            $db = new Database('mcm');
            $res = $db->queryOne($qry, $val);
            if (!$res) throw new \Exception("Formato no encontrado.");
            return self::resultado(true, 'Formato obtenido correctamente.', $res);
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al obtener el formato.', null, $e->getMessage());
        }
    }

    public static function registraFormatoMCM($datos)
    {
        $qryA = <<<SQL
            INSERT INTO REPOSITORIO_CAPITALH (ARCHIVO, NOMBRE, TIPO)
            VALUES (EMPTY_BLOB(), :nombre, :tipo)
            RETURNING ARCHIVO, ID INTO :archivo, :id
        SQL;

        $valA = [
            'nombre' => $datos['nombre'],
            'tipo' => $datos['tipo']
        ];

        $retA = [
            'archivo' => [
                'valor' => $datos['archivo'],
                'tipo' => \PDO::PARAM_LOB
            ],
            'id' => [
                'valor' => '',
                'tipo' => \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT,
                'largo' => 40
            ]
        ];

        try {
            $db = new Database('mcm');
            $db->beginTransaction();
            $db->CRUD($qryA, $valA, $retA);

            if (!$retA['id']['valor']) throw new \Exception("Error al insertar el formato.");

            $db->commit();
            return self::resultado(true, 'Formato registrado correctamente.', ['formatoId' => $retA['id']['valor']]);
        } catch (\Exception $e) {
            $db->rollBack();
            return self::resultado(false, 'Error al registrar el formato.', null, $e->getMessage());
        }
    }

    public static function eliminarFormatoMCM($datos)
    {
        $qry = "DELETE FROM REPOSITORIO_CAPITALH WHERE ID = :idFormato";

        $val = [
            'idFormato' => $datos['idFormato']
        ];

        try {
            $db = new Database('mcm');
            $db->CRUD($qry, $val);
            return self::resultado(true, 'Formato eliminado correctamente.');
        } catch (\Exception $e) {
            return self::resultado(false, 'Error al eliminar el formato.', null, $e->getMessage());
        }
    }
}
