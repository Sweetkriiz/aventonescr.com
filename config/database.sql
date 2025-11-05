
CREATE TABLE Usuarios (
  idUsuario INT AUTO_INCREMENT PRIMARY KEY,
  nombreUsuario VARCHAR(50) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,      -- Hash de contraseña
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  cedula VARCHAR(30) NOT NULL UNIQUE,
  fechaNacimiento DATE NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  telefono VARCHAR(20) NOT NULL,
  fotografia VARCHAR(255) NULL,
  rol ENUM('pasajero', 'chofer', 'administrador') NOT NULL DEFAULT 'pasajero',
  fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE Vehiculos (
  idVehiculo INT AUTO_INCREMENT PRIMARY KEY,
  idChofer INT NOT NULL,
  marca VARCHAR(50) NOT NULL,
  modelo VARCHAR(50) NOT NULL,
  anio SMALLINT NOT NULL,
  color VARCHAR(30) NOT NULL,
  placa VARCHAR(20) NOT NULL UNIQUE,
  fotografia VARCHAR(255) NULL,
  estado ENUM('pendiente', 'aprobado', 'rechazado') NOT NULL DEFAULT 'pendiente'
);


CREATE TABLE Viajes (
  idViaje INT AUTO_INCREMENT PRIMARY KEY,
  idChofer INT NOT NULL,
  idVehiculo INT NOT NULL,
  origen VARCHAR(255) NOT NULL,
  destino VARCHAR(255) NOT NULL,
  fecha DATE NOT NULL,
  horaSalida TIME NOT NULL,
  horaLlegada TIME NOT NULL,
  tarifa DECIMAL(10,2) NOT NULL,
  espaciosDisponibles INT NOT NULL,
  estado ENUM('activo','completado','cancelado') DEFAULT 'activo',
  FOREIGN KEY (idChofer) REFERENCES Usuarios(idUsuario),
  FOREIGN KEY (idVehiculo) REFERENCES Vehiculos(idVehiculo)
);


CREATE TABLE Reservas (
  idReserva INT AUTO_INCREMENT PRIMARY KEY,
  idViaje INT NOT NULL,
  idPasajero INT NOT NULL,
  estadoReserva ENUM('pendiente', 'aceptada', 'rechazada', 'cancelada') 
        NOT NULL DEFAULT 'pendiente',
  fechaSolicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (idViaje, idPasajero)
);

-- Vehiculos -> Usuarios
ALTER TABLE Vehiculos
  ADD INDEX idx_vehiculos_idChofer (idChofer),
  ADD CONSTRAINT fk_vehiculo_usuario
    FOREIGN KEY (idChofer) REFERENCES Usuarios(idUsuario)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- Viajes -> Usuarios (Chofer)
ALTER TABLE Viajes
  ADD INDEX idx_viajes_idChofer (idChofer),
  ADD CONSTRAINT fk_viaje_chofer
    FOREIGN KEY (idChofer) REFERENCES Usuarios(idUsuario)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- Viajes -> Vehiculos
ALTER TABLE Viajes
  ADD INDEX idx_viajes_idVehiculo (idVehiculo),
  ADD CONSTRAINT fk_viaje_vehiculo
    FOREIGN KEY (idVehiculo) REFERENCES Vehiculos(idVehiculo)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- Reservas -> Viajes
ALTER TABLE Reservas
  ADD INDEX idx_reservas_idViaje (idViaje),
  ADD CONSTRAINT fk_reserva_viaje
    FOREIGN KEY (idViaje) REFERENCES Viajes(idViaje)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- Reservas -> Usuarios (Pasajero)
ALTER TABLE Reservas
  ADD INDEX idx_reservas_idPasajero (idPasajero),
  ADD CONSTRAINT fk_reserva_pasajero
    FOREIGN KEY (idPasajero) REFERENCES Usuarios(idUsuario)
    ON DELETE CASCADE ON UPDATE CASCADE;


INSERT INTO Usuarios 
  (nombreUsuario, contrasena, nombre, apellidos, cedula, fechaNacimiento, correo, telefono, rol) 
VALUES 
  ('admin', 
   'admin_pass', 
   'Admin', 
   'Aventones', 
   '208450152', 
   '2003-08-27', 
   'krisdaram@gmail.com', 
   '85833041', 
   'administrador');


ALTER TABLE Usuarios
ADD COLUMN estadoSolicitudChofer ENUM('ninguna', 'pendiente', 'aprobado', 'rechazado') 
DEFAULT 'ninguna';

INSERT INTO Vehiculos 
(idChofer, marca, modelo, anio, color, placa, estado)
VALUES 
(3, 'Toyota', 'Corolla', 2020, 'Negro', 'LAR123', 'aprobado');

INSERT INTO Viajes 
(idChofer, idVehiculo, nombreViaje, lugarSalida, horaSalida, destino, horaLlegada, diasSemana, tarifa, espaciosDisponibles)
VALUES 
(3, 3, 'San Jose a Cartago - Mañana', 'San Jose', '07:30:00', 'Cartago', '08:30:00', 'Lunes, Martes, Miércoles', 1500.00, 3);

UPDATE Usuarios
SET contrasena = SHA2(contrasena, 256)
WHERE LOWER(nombreUsuario) = 'admin';


NUEVOS PARA LA BD KRISTEL

ALTER TABLE Vehiculos ADD COLUMN motivoRechazo VARCHAR(255) NULL AFTER estado;

SI TIENE VIAJES ELIMINELOS
DELETE FROM Viajes WHERE idViaje = 1

ALTER TABLE Viajes
CHANGE COLUMN lugarSalida origen VARCHAR(255) NOT NULL,
ADD COLUMN fecha DATE NOT NULL AFTER origen,
DROP COLUMN diasSemana,
ADD COLUMN estado ENUM('activo','completado','cancelado') DEFAULT 'activo';
