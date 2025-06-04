<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para generar CURP
function generarCURP($nombre, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento, $sexo, $entidadNacimiento) {
    // Implementar lógica de generación de CURP
    // Esta es una versión simplificada
    
    $curp = '';
    
    // Primera letra y primera vocal del apellido paterno
    $curp .= strtoupper(substr($apellidoPaterno, 0, 1));
    $vocales = ['A', 'E', 'I', 'O', 'U'];
    for ($i = 1; $i < strlen($apellidoPaterno); $i++) {
        if (in_array(strtoupper($apellidoPaterno[$i]), $vocales)) {
            $curp .= strtoupper($apellidoPaterno[$i]);
            break;
        }
    }
    
    // Primera letra del apellido materno
    $curp .= strtoupper(substr($apellidoMaterno, 0, 1));
    
    // Primera letra del nombre
    $curp .= strtoupper(substr($nombre, 0, 1));
    
    // Fecha de nacimiento (AAMMDD)
    $fecha = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
    $curp .= $fecha->format('ymd');
    
    // Sexo
    $curp .= strtoupper($sexo);
    
    // Entidad federativa (código de 2 letras)
    $entidades = [
        'AGUASCALIENTES' => 'AS',
        'BAJA CALIFORNIA' => 'BC',
        'BAJA CALIFORNIA SUR' => 'BS',
        'CAMPECHE' => 'CC',
        'CHIAPAS' => 'CS',
        'CHIHUAHUA' => 'CH',
        'CIUDAD DE MEXICO' => 'DF',
        'COAHUILA' => 'CL',
        'COLIMA' => 'CM',
        'DURANGO' => 'DG',
        'ESTADO DE MEXICO' => 'MC',
        'GUANAJUATO' => 'GT',
        'GUERRERO' => 'GR',
        'HIDALGO' => 'HG',
        'JALISCO' => 'JC',
        'MICHOACAN' => 'MN',
        'MORELOS' => 'MS',
        'NAYARIT' => 'NT',
        'NUEVO LEON' => 'NL',
        'OAXACA' => 'OC',
        'PUEBLA' => 'PL',
        'QUERETARO' => 'QT',
        'QUINTANA ROO' => 'QR',
        'SAN LUIS POTOSI' => 'SP',
        'SINALOA' => 'SL',
        'SONORA' => 'SR',
        'TABASCO' => 'TC',
        'TAMAULIPAS' => 'TS',
        'TLAXCALA' => 'TL',
        'VERACRUZ' => 'VZ',
        'YUCATAN' => 'YN',
        'ZACATECAS' => 'ZS'
    ];
    
    $curp .= $entidades[strtoupper($entidadNacimiento)] ?? 'NE';
    
    // Consonantes internas
    $consonantes = 'BCDFGHJKLMNPQRSTVWXYZ';
    
    // Primera consonante interna del apellido paterno
    for ($i = 1; $i < strlen($apellidoPaterno); $i++) {
        if (strpos($consonantes, strtoupper($apellidoPaterno[$i])) !== false) {
            $curp .= strtoupper($apellidoPaterno[$i]);
            break;
        }
    }
    
    // Primera consonante interna del apellido materno
    for ($i = 1; $i < strlen($apellidoMaterno); $i++) {
        if (strpos($consonantes, strtoupper($apellidoMaterno[$i])) !== false) {
            $curp .= strtoupper($apellidoMaterno[$i]);
            break;
        }
    }
    
    // Primera consonante interna del nombre
    for ($i = 1; $i < strlen($nombre); $i++) {
        if (strpos($consonantes, strtoupper($nombre[$i])) !== false) {
            $curp .= strtoupper($nombre[$i]);
            break;
        }
    }
    
    // Dígito verificador (simplificado)
    $curp .= rand(0, 9);
    
    return $curp;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch($method) {
        case 'GET':
            // Ejemplo: /curp.php?nombre=Juan&apellido_paterno=Perez&apellido_materno=Lopez&fecha_nacimiento=1990-01-01&sexo=M&entidad_nacimiento=QUERETARO
            if (isset($_GET['nombre']) && isset($_GET['apellido_paterno']) && isset($_GET['apellido_materno']) && 
                isset($_GET['fecha_nacimiento']) && isset($_GET['sexo']) && isset($_GET['entidad_nacimiento'])) {
                
                $curp = generarCURP(
                    $_GET['nombre'],
                    $_GET['apellido_paterno'],
                    $_GET['apellido_materno'],
                    $_GET['fecha_nacimiento'],
                    $_GET['sexo'],
                    $_GET['entidad_nacimiento']
                );
                
                echo json_encode([
                    'success' => true,
                    'curp' => $curp,
                    'datos' => [
                        'nombre' => $_GET['nombre'],
                        'apellido_paterno' => $_GET['apellido_paterno'],
                        'apellido_materno' => $_GET['apellido_materno'],
                        'fecha_nacimiento' => $_GET['fecha_nacimiento'],
                        'sexo' => $_GET['sexo'],
                        'entidad_nacimiento' => $_GET['entidad_nacimiento']
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan parámetros requeridos',
                    'required' => ['nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'sexo', 'entidad_nacimiento']
                ]);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (isset($input['nombre']) && isset($input['apellido_paterno']) && isset($input['apellido_materno']) && 
                isset($input['fecha_nacimiento']) && isset($input['sexo']) && isset($input['entidad_nacimiento'])) {
                
                $curp = generarCURP(
                    $input['nombre'],
                    $input['apellido_paterno'],
                    $input['apellido_materno'],
                    $input['fecha_nacimiento'],
                    $input['sexo'],
                    $input['entidad_nacimiento']
                );
                
                echo json_encode([
                    'success' => true,
                    'curp' => $curp,
                    'datos' => $input
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan parámetros requeridos en el JSON',
                    'required' => ['nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'sexo', 'entidad_nacimiento']
                ]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>
