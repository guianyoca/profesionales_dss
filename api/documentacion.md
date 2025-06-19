# API Profesionales DSS - Manual de Integración con WhatsApp

Esta API proporciona los servicios necesarios para que un bot de WhatsApp pueda consultar turnos disponibles y reservar turnos para los profesionales del sistema. La URL base de la API es `https://www.divisionserviciossociales.com/profesionales_dss/api/`.

## Endpoints Disponibles

### 1. Listar Profesionales
Obtiene la lista de todos los profesionales disponibles en el sistema.

**URL**: `/profesionales`  
**Método**: `GET`  
**URL Completa**: `https://www.divisionserviciossociales.com/profesionales_dss/api/profesionales`

**Respuesta Exitosa**:
```json
{
    "status": "success",
    "message": "Lista de profesionales",
    "data": [
        {
            "id": 2,
            "nombre": "Dr. Juan Pérez"
        },
        {
            "id": 3,
            "nombre": "Dra. María López"
        }
    ]
}
```

### 2. Consultar Turnos Disponibles
Obtiene los turnos disponibles para una fecha específica y opcionalmente para un profesional específico.

**URL**: `/turnos/disponibles`  
**Método**: `GET`  
**URL Completa**: `https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/disponibles`

**Parámetros de Consulta**:
- `fecha` - Fecha para la que se quieren consultar turnos (formato: YYYY-MM-DD). Si no se especifica, se usa la fecha actual.
- `profesional` - ID del profesional (opcional). Si no se especifica, se muestran los turnos de todos los profesionales.
- `nombre_profesional` - Nombre o parte del nombre del profesional (opcional). Útil para buscar por nombre en lugar de ID.

**Ejemplos**:
- Todos los profesionales para hoy: 
  `https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/disponibles`
- Fecha específica: 
  `https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/disponibles?fecha=2025-06-01`
- Profesional específico por ID: 
  `https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/disponibles?fecha=2025-06-01&profesional=2`
- Profesional específico por nombre: 
  `https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/disponibles?fecha=2025-06-01&nombre_profesional=Juan`

**Respuesta Exitosa**:
```json
{
    "status": "success",
    "message": "Turnos disponibles",
    "data": [
        {
            "profesional": {
                "id": 2,
                "nombre": "Dr. Juan Pérez"
            },
            "fecha": "2025-06-01",
            "horarios": [
                {
                    "hora": "09:00:00",
                    "disponibles": 1
                },
                {
                    "hora": "09:30:00",
                    "disponibles": 2
                }
            ]
        }
    ]
}
```

### 3. Crear un Nuevo Turno
Permite reservar un turno para un paciente.

**URL**: `/turnos/crear`  
**Método**: `POST`  
**URL Completa**: `https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/crear`

**Cuerpo de la Solicitud (JSON)**:
```json
{
    "nombre": "Pedro González",
    "dni": "12345678",
    "telefono": "1122334455",
    "fecha": "2025-06-01",
    "hora": "09:00:00",
    "profesional": "Dr. Juan Pérez",
    "apiKey": "tu_clave_api_si_la_tienes"
}
```

**Campos obligatorios**:
- `nombre` - Nombre completo del paciente
- `dni` - Número de documento del paciente
- `telefono` - Número de teléfono del paciente
- `fecha` - Fecha del turno en formato YYYY-MM-DD
- `hora` - Hora del turno en formato HH:MM:SS
- `profesional` - Nombre exacto del profesional (debe coincidir con uno existente)

**Respuesta Exitosa**:
```json
{
    "status": "success",
    "message": "Turno creado correctamente",
    "data": {
        "id_turno": 123,
        "nombre": "Pedro González",
        "profesional": "Dr. Juan Pérez",
        "fecha": "2025-06-01",
        "hora": "09:00:00"
    }
}
```

**Respuesta de Error**:
```json
{
    "status": "error",
    "message": "No hay turnos disponibles para ese horario",
    "data": null
}
```

## Ejemplos de Integración con Bot de WhatsApp

A continuación se muestran ejemplos de cómo podrías integrar esta API con tu bot de WhatsApp existente.

### Ejemplo 1: Consultar Profesionales Disponibles

Cuando un usuario pide ver los profesionales disponibles, tu bot podría hacer una solicitud a la API y formatear la respuesta:

```javascript
// Ejemplo con JavaScript/Node.js usando axios
const axios = require('axios');

async function obtenerProfesionales() {
    try {
        const response = await axios.get('https://www.divisionserviciossociales.com/profesionales_dss/api/profesionales');
        
        if (response.data.status === 'success') {
            let mensaje = "Profesionales disponibles:\n\n";
            
            response.data.data.forEach(profesional => {
                mensaje += `- ${profesional.nombre}\n`;
            });
            
            return mensaje;
        } else {
            return "No se pudieron obtener los profesionales en este momento.";
        }
    } catch (error) {
        console.error(error);
        return "Ocurrió un error al consultar los profesionales.";
    }
}
```

### Ejemplo 2: Consultar Turnos Disponibles para un Profesional

Cuando un usuario selecciona un profesional y una fecha, tu bot podría consultar los turnos disponibles:

```javascript
// Ejemplo con JavaScript/Node.js usando axios
const axios = require('axios');

async function obtenerTurnosDisponibles(nombreProfesional, fecha) {
    try {
        const response = await axios.get(`https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/disponibles`, {
            params: {
                fecha: fecha,
                nombre_profesional: nombreProfesional
            }
        });
        
        if (response.data.status === 'success' && response.data.data.length > 0) {
            let mensaje = `Turnos disponibles para ${nombreProfesional} en fecha ${fecha}:\n\n`;
            
            response.data.data.forEach(profesional => {
                mensaje += `Profesional: ${profesional.profesional.nombre}\n`;
                
                if (profesional.horarios.length > 0) {
                    profesional.horarios.forEach(horario => {
                        mensaje += `- ${horario.hora.substr(0, 5)} (${horario.disponibles} disponibles)\n`;
                    });
                } else {
                    mensaje += "- No hay horarios disponibles\n";
                }
            });
            
            return mensaje;
        } else {
            return "No hay turnos disponibles para ese profesional en esa fecha.";
        }
    } catch (error) {
        console.error(error);
        return "Ocurrió un error al consultar los turnos disponibles.";
    }
}
```

### Ejemplo 3: Reservar un Turno

Cuando un usuario confirma que quiere reservar un turno específico:

```javascript
// Ejemplo con JavaScript/Node.js usando axios
const axios = require('axios');

async function reservarTurno(datosPaciente) {
    try {
        const response = await axios.post('https://www.divisionserviciossociales.com/profesionales_dss/api/turnos/crear', {
            nombre: datosPaciente.nombre,
            dni: datosPaciente.dni,
            telefono: datosPaciente.telefono,
            fecha: datosPaciente.fecha,
            hora: datosPaciente.hora,
            profesional: datosPaciente.profesional
        });
        
        if (response.data.status === 'success') {
            return `¡Turno reservado correctamente!\n\n` +
                   `Paciente: ${response.data.data.nombre}\n` +
                   `Profesional: ${response.data.data.profesional}\n` +
                   `Fecha: ${response.data.data.fecha}\n` +
                   `Hora: ${response.data.data.hora.substr(0, 5)}\n\n` +
                   `Por favor, llegue 10 minutos antes de su turno.`;
        } else {
            return "No se pudo reservar el turno: " + response.data.message;
        }
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data && error.response.data.message) {
            return "Error al reservar el turno: " + error.response.data.message;
        } else {
            return "Ocurrió un error al intentar reservar el turno.";
        }
    }
}
```

## Flujo Típico de Conversación en WhatsApp

1. El usuario inicia la conversación: "Hola, quiero reservar un turno"
2. Bot: "¡Hola! ¿Con qué profesional te gustaría reservar un turno?" (y muestra la lista de profesionales)
3. Usuario: "Con el Dr. Juan Pérez"
4. Bot: "¿Para qué fecha deseas el turno?" (puede mostrar fechas disponibles)
5. Usuario: "Para el 1 de junio"
6. Bot: Consulta la API y muestra los horarios disponibles para esa fecha y profesional
7. Usuario: Selecciona un horario "9:00"
8. Bot: "Por favor, indícame tu nombre completo"
9. Usuario: "Pedro González"
10. Bot: "Por favor, indícame tu número de DNI"
11. Usuario: "12345678"
12. Bot: "¿Es correcto este número de teléfono: 1122334455?" (puede usar el mismo número de WhatsApp)
13. Usuario: "Sí"
14. Bot: Realiza la reserva usando la API y confirma al usuario
15. Bot: "¡Turno reservado correctamente! [detalles del turno]"

## Consideraciones de Seguridad

1. **Validación de Origen**: Considera implementar un sistema de API Keys para asegurar que solo tu bot pueda hacer reservas.
2. **Limitación de Solicitudes**: Implementa limitación de solicitudes por IP para prevenir abusos.
3. **Validación de Datos**: Siempre valida y sanitiza los datos antes de procesarlos.

## Manejo de Errores Comunes

- **Profesional no encontrado**: Verifica que estés usando el nombre exacto del profesional como aparece en el sistema.
- **No hay turnos disponibles**: El horario seleccionado ya está completo. Sugiere otros horarios al usuario.
- **Formato de fecha incorrecto**: Asegúrate de usar el formato YYYY-MM-DD para las fechas.
- **Formato de hora incorrecto**: Asegúrate de usar el formato HH:MM:SS para las horas.

---

Si tienes alguna duda o sugerencia para mejorar esta API, por favor contáctanos.
