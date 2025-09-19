<?php

namespace Controllers;

use Core\Controller;
use Models\Formatos as FormatosDAO;

class Formatos extends Controller
{
    public function Cultiva()
    {
        $script = <<<HTML
            <script>
                const tabla = "#historialFormatos"
                let valNuevoFormato = null

                const getFormatos = () => {
                    const fechas = getInputFechas("#filtroFechas", true, true)
                    const parametros = {
                        fechaI: fechas.inicio,
                        fechaF: fechas.fin
                    }

                    consultaServidor("/Formatos/getListaFormatosCultiva", parametros, (respuesta) => {
                        if (!respuesta.success) return showError(respuesta.mensaje)

                        const datos = respuesta.datos.map((formato) => {
                            return [
                                null,
                                formato.ID,
                                formato.NOMBRE,
                                moment(formato.FECHA_SUBIDA).format(MOMENT_FRONT_HORA),
                                moment(formato.VIGENCIA_FIN).format(MOMENT_FRONT),
                                formato.ACCESO,
                                menuAcciones([{
                                    texto: "Formato",
                                    icono: "fa-eye",
                                    funcion: "verFormato(" + formato.ID + ")"
                                }])
                            ]
                        })

                        actualizaDatosTabla(tabla, datos)
                    })
                }

                const verFormato = (id) => {
                    showWait("Obteniendo archivo...")
                    const parametro = new FormData()
                    parametro.append("idFormato", id)
                    mostrarArchivoDescargado("/Formatos/getFormatoCultiva", parametro)
                }

                const setvalNuevoFormato = () => {
                    const campos = {
                        nombre: {
                            notEmpty: {
                                message: "Debe ingresar el nombre del archivo"
                            }
                        },
                        archivoFormato: {
                            notEmpty: {
                                message: "Debe seleccionar un archivo para subir"
                            },
                            file: {
                                maxSize: 5 * 1024 * 1024, // 5 MB
                                message: "El archivo no debe exceder 5MB"
                            }
                        }
                    }

                    valNuevoFormato = setValidacionModal(
                        "#modalSubirFormato",
                        campos,
                        "#subirFormato",
                        subirFormato,
                        "#cancelaSubirFormato"
                    )
                }

                const subirFormato = () => {
                    confirmarMovimiento("¿Desea subir este nuevo formato?").then((continuar) => {
                        if (!continuar) return

                        const archivo = $("#archivoFormato")[0].files[0]
                        //const archivo = $("#archivoFormato").prop("files")[0]
                        const nombre = $("#nombre").val().trim()
                        const fechas = getInputFechas("#fechasVigencia", true, false)

                        const formData = new FormData();
                        formData.append("nombre", nombre);
                        formData.append("archivo", archivo);

                        consultaServidor("/Formatos/registrarFormatoCultiva", formData, (respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)

                            $("#modalSubirFormato").modal("hide")
                            showSuccess(respuesta.mensaje)
                        }, {
                            procesar: false,
                            tipoContenido: false
                        })
                    })
                }

                $(document).ready(function() {
                    setInputFechas("#filtroFechas", { rango: true, iniD: -30 })
                    setInputFechas("#fechasVigencia", { rango: true, enModal: true, finD: 365, minD: 0 })
                    configuraTabla(tabla)
                    setvalNuevoFormato()

                    $("#btnAgregar").on("click", function() {
                        $("#modalSubirFormato").modal("show")
                    })

                    getFormatos()
                })
            </script>
        HTML;

        self::set("titulo", "Formatos CULTIVA");
        self::set("script", $script);
        self::render("formatos_cultiva");
    }

    public function getListaFormatosCultiva()
    {
        self::respuestaJSON(FormatosDAO::getListaFormatosCultiva($_POST));
    }

    public function getFormatoCultiva()
    {
        $datos = $_SERVER['REQUEST_METHOD'] !== 'POST' ? $_GET : $_POST;

        $formato = FormatosDAO::getFormatoCultiva($datos);
        if (!$formato['success']) return self::respuestaJSON($formato);

        $archivo = $formato['datos']['ARCHIVO'];
        $archivo = is_resource($archivo) ? stream_get_contents($archivo) : $archivo;
        if ($archivo === false) {
            return self::respuestaJSON(self::respuesta(false, 'Error al leer el archivo del formato.'));
        }

        header('Content-Transfer-Encoding: binary');
        header("Content-Type: {$formato['datos']['TIPO']}");
        header("Content-Disposition: inline; filename={$formato['datos']['NOMBRE']}");
        echo $archivo;

        if (is_resource($archivo)) fclose($archivo);
    }

    public function registrarFormatoCultiva()
    {
        $validacion = self::getDatosSubirArchivo();
        if (!$validacion['success']) {
            return self::respuestaJSON($validacion);
        }

        $datos = $validacion['datos'];
        $resultado = FormatosDAO::registraFormatoCultiva($datos);
        if (is_resource($datos['archivo'])) fclose($datos['archivo']);

        self::respuestaJSON($resultado);
    }

    public function MCM()
    {
        $script = <<<HTML
            <script>
                const tabla = "#historialFormatos"
                let valNuevoFormato = null

                const getFormatos = () => {
                    const fechas = getInputFechas("#filtroFechas", true, true)
                    const parametros = {
                        fechaI: fechas.inicio,
                        fechaF: fechas.fin
                    }

                    consultaServidor("/Formatos/getListaFormatosMCM", parametros, (respuesta) => {
                        if (!respuesta.success) return showError(respuesta.mensaje)

                        const datos = respuesta.datos.map((formato) => {
                            return [
                                null,
                                formato.ID,
                                formato.NOMBRE,
                                moment(formato.FECHA_SUBIDA).format(MOMENT_FRONT_HORA),
                                moment(formato.VIGENCIA_FIN).format(MOMENT_FRONT),
                                formato.ACCESO,
                                menuAcciones([{
                                    texto: "Formato",
                                    icono: "fa-eye",
                                    funcion: "verFormato(" + formato.ID + ")"
                                }])
                            ]
                        })

                        actualizaDatosTabla(tabla, datos)
                    })
                }

                const verFormato = (id) => {
                    showWait("Obteniendo archivo...")
                    const parametro = new FormData()
                    parametro.append("idFormato", id)
                    mostrarArchivoDescargado("/Formatos/getFormatoMCM", parametro)
                }

                const setvalNuevoFormato = () => {
                    const campos = {
                        nombre: {
                            notEmpty: {
                                message: "Debe ingresar el nombre del archivo"
                            }
                        },
                        archivoFormato: {
                            notEmpty: {
                                message: "Debe seleccionar un archivo para subir"
                            },
                            file: {
                                maxSize: 5 * 1024 * 1024, // 5 MB
                                message: "El archivo no debe exceder 5MB"
                            }
                        }
                    }

                    valNuevoFormato = setValidacionModal(
                        "#modalSubirFormato",
                        campos,
                        "#subirFormato",
                        subirFormato,
                        "#cancelaSubirFormato"
                    )
                }

                const subirFormato = () => {
                    confirmarMovimiento("¿Desea subir este nuevo formato?").then((continuar) => {
                        if (!continuar) return

                        const archivo = $("#archivoFormato")[0].files[0]
                        //const archivo = $("#archivoFormato").prop("files")[0]
                        const nombre = $("#nombre").val().trim()
                        const fechas = getInputFechas("#fechasVigencia", true, false)

                        const formData = new FormData();
                        formData.append("nombre", nombre);
                        formData.append("archivo", archivo);

                        consultaServidor("/Formatos/registrarFormatoMCM", formData, (respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)

                            $("#modalSubirFormato").modal("hide")
                            showSuccess(respuesta.mensaje)
                        }, {
                            procesar: false,
                            tipoContenido: false
                        })
                    })
                }

                $(document).ready(function() {
                    setInputFechas("#filtroFechas", { rango: true, iniD: -30 })
                    setInputFechas("#fechasVigencia", { rango: true, enModal: true, finD: 365, minD: 0 })
                    configuraTabla(tabla)
                    setvalNuevoFormato()

                    $("#btnAgregar").on("click", function() {
                        $("#modalSubirFormato").modal("show")
                    })

                    getFormatos()
                })
            </script>
        HTML;

        self::set("titulo", "Formatos MCM");
        self::set("script", $script);
        self::render("formatos_mcm");
    }

    public function getListaFormatosMCM()
    {
        self::respuestaJSON(FormatosDAO::getListaFormatosMCM($_POST));
    }

    public function getFormatoMCM()
    {
        $datos = $_SERVER['REQUEST_METHOD'] !== 'POST' ? $_GET : $_POST;

        $formato = FormatosDAO::getFormatoMCM($datos);
        if (!$formato['success']) return self::respuestaJSON($formato);

        $archivo = $formato['datos']['ARCHIVO'];
        $archivo = is_resource($archivo) ? stream_get_contents($archivo) : $archivo;
        if ($archivo === false) {
            return self::respuestaJSON(self::respuesta(false, 'Error al leer el archivo del formato.'));
        }

        header('Content-Transfer-Encoding: binary');
        header("Content-Type: {$formato['datos']['TIPO']}");
        header("Content-Disposition: inline; filename={$formato['datos']['NOMBRE']}");
        echo $archivo;

        if (is_resource($archivo)) fclose($archivo);
    }

    public function registrarFormatoMCM()
    {
        $datos = self::getDatosSubirArchivo();
        $resultado = FormatosDAO::registraFormatoMCM($datos);

        if (is_resource($datos['archivo'])) fclose($datos['archivo']);
        self::respuestaJSON($resultado);
    }

    public function getDatosSubirArchivo()
    {
        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            return self::respuestaJSON(false, 'Archivo no recibido o error en la carga.');
        }

        if ($_FILES['archivo']['size'] > 5 * 1024 * 1024) {
            return self::respuestaJSON(false, "El archivo {$_FILES['archivo']['name']} excede el tamaño máximo permitido de 5 MB.");
        }

        return [
            'nombre' => $_POST['nombre'] ?? '',
            'archivo' => fopen($_FILES['archivo']['tmp_name'], 'rb'),
            'tipo' => $_FILES['archivo']['type']
        ];
    }
}
