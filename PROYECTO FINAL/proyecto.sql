CREATE TABLE Usuarios (
    ID_usu INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Contraseña VARCHAR(255) NOT NULL,
    Fecha_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Categorias (
    ID_cat INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE Cursos (
    ID_curso INT PRIMARY KEY AUTO_INCREMENT,
    Titulo VARCHAR(255) NOT NULL,
    Descripción TEXT NOT NULL,
    ID_cat INT NOT NULL,
    Precio DECIMAL(10,2) NOT NULL,
    Fecha_inicio DATE NOT NULL,
    Duracion INT NOT NULL, -- en horas
    Imagen VARCHAR(255), -- ruta del archivo de imagen
    ID_ubi INT NOT NULL,
    FOREIGN KEY (ID_cat) REFERENCES Categorias(ID_cat),
    FOREIGN KEY (ID_ubi) REFERENCES Ubicaciones(ID_ubi)
);

CREATE TABLE Ubicaciones (
    ID_ubi INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE Publicaciones (
    ID_publica INT PRIMARY KEY AUTO_INCREMENT,
    ID_curso INT NOT NULL,
    ID_usu INT NOT NULL,
    Fecha_pub TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_curso) REFERENCES Cursos(ID_curso),
    FOREIGN KEY (ID_usu) REFERENCES Usuarios(ID_usu)
);

CREATE TABLE Inscripciones (
    ID_inscrip INT PRIMARY KEY AUTO_INCREMENT,
    ID_usu INT NOT NULL,
    ID_curso INT NOT NULL,
    Fecha_ins TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_usu) REFERENCES Usuarios(ID_usu),
    FOREIGN KEY (ID_curso) REFERENCES Cursos(ID_curso)
);
