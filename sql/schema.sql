-- Crear la base de datos para el proyecto Amor en un Click
CREATE DATABASE amor_en_un_click;

-- Seleccionar la base de datos recién creada
USE amor_en_un_click;

-- Tabla para almacenar información de los usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Identificador único del usuario
    nombre VARCHAR(50) NOT NULL,            -- Nombre del usuario
    email VARCHAR(100) NOT NULL UNIQUE,     -- Correo electrónico único del usuario
    contraseña VARCHAR(255) NOT NULL,       -- Contraseña del usuario (debería estar encriptada)
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha de registro del usuario
);

-- Tabla para almacenar las imágenes de perfil
CREATE TABLE imagenes_perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- Identificador único de la imagen
    id_usuario INT,                          -- ID del usuario al que pertenece la imagen
    ruta_imagen VARCHAR(255) NOT NULL,       -- Ruta de la imagen en el sistema de archivos
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE  -- Relación con la tabla de usuarios
);

-- Tabla para almacenar los "likes" entre usuarios
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- Identificador único del like
    id_usuario_quien_likes INT,              -- ID del usuario que da el "like"
    id_usuario_recibe_likes INT,             -- ID del usuario que recibe el "like"
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha en que se dio el "like"
    FOREIGN KEY (id_usuario_quien_likes) REFERENCES usuarios(id) ON DELETE CASCADE, -- Relación con la tabla de usuarios
    FOREIGN KEY (id_usuario_recibe_likes) REFERENCES usuarios(id) ON DELETE CASCADE  -- Relación con la tabla de usuarios
);
