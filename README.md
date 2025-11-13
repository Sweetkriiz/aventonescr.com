# Aventones CR

**Aventones CR** es una aplicación web desarrollada en PHP y MySQL que permite conectar a pasajeros y choferes para compartir viajes dentro de Costa Rica.  
El sistema busca promover la movilidad sostenible, optimizar los recursos de transporte y reducir la huella de carbono.

---

## Características principales

### 👥 Roles de usuario
- **Administrador:** aprueba o rechaza vehículos y gestiona usuarios.
- **Chofer:** registra vehículos, publica viajes y administra sus rutas.
- **Pasajero:** busca, reserva y cancela viajes disponibles.

---

### Funcionalidades
- Registro e inicio de sesión por roles.
- Creación y aprobación de vehículos (con fotografías).
- Publicación de viajes con control de cupos.
- Reserva, aceptación, rechazo y cancelación de viajes.
- Notificaciones de aprobación/rechazo.
- Paneles personalizados: pasajero, chofer y administrador.
- Validaciones y seguridad en formularios.
- Subida de imágenes a `/public/uploads/`.

---

## Tecnologías utilizadas
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript  
- **Backend:** PHP (sin frameworks)  
- **Base de datos:** MySQL
- **Servidor local:** Apache 
- **Control de versiones:** Git + GitHub  

---

## Estructura del proyecto

config/
 ├── database.php
 ├── database.sql
 ├── funciones_admin.php
 ├── funciones_carro.php
 ├── funciones_ride.php
 └── start_app.php

public/
 ├── CRUD_admin/
 ├── CRUD_pasajero/
 ├── CRUD_rides/
 ├── CRUD_vehiculos/
 ├── css/
 ├── images/
 ├── includes/
 ├── js/
 ├── uploads/
 ├── buscar_viaje.php
 ├── cancelar_reserva.php
 ├── dashboard_admin.php
 ├── dashboard_chofer.php
 ├── dashboard_pasajero.php
 ├── edit_miPerfil.php
 ├── index.php
 ├── login.php
 ├── logout.php
 ├── miPerfil.php
 ├── mis_viajes.php
 ├── procesarSolicitudes.php
 ├── registrarse.php
 ├── reservar.php
 ├── resultados.php
 ├── solicitudes_chofer.php
 └── testdb.php
 
---

##  Funciones destacadas
- `getVehiculosByChofer()` → Obtiene vehículos aprobados.  
- `getVehiculosPendientes()` → Lista los vehículos en revisión.  
- `getVehiculosRechazados()` → Muestra los rechazados y su motivo.  
- `deleteVehiculo()` → Elimina el vehículo y su imagen física.  
- `actualizarVehiculo()` → Marca el vehículo como pendiente tras edición.  

---

## Flujo general
1. Un **pasajero** registra un vehículo → pasa a revisión.  
2. El **administrador** aprueba o rechaza desde el panel.  
3. Si se aprueba → el usuario pasa automáticamente a rol *chofer*.  
4. El **chofer** publica viajes → los **pasajeros** los reservan.  
5. Los estados se actualizan dinámicamente: pendiente, aceptado, cancelado, etc.  

---

## Notas importantes

- Las fotos se guardan en: /public/uploads
- Eliminaciones de vehículos eliminan también la imagen física.
- Roles actualizados automáticamente en sesión.

---

## Desarrollado por
**Krisley Castro Barrantes**  
Estudiante de Ingeniería en Software – UTN 🇨🇷  

**Kristel Ramirez Duarte**  
Estudiante de Ingeniería en Software – UTN 🇨🇷  

---

## Próximas mejoras
- Integrar un sistema de calificaciones de choferes.  
- Agregar chat directo entre pasajero y chofer.  
- Implementar filtrado avanzado por destino, hora o cupos.  
- Versión mobile responsive completa.  

---

## Licencia
Este proyecto es de uso académico y no comercial.  
© 2025 Aventones CR. Todos los derechos reservados.
