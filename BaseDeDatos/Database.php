<?php
class Database{
    private $conexion;

    public function __construct($host = "localhost",
                                $user = "root",
                                $password = "",
                                $dbname = "test")
    {
        $this->conexion = new mysqli
        ($host, $user, $password, $dbname);
        // Verificar si hay un error en la conexión
        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    public function query($query){
        return $this -> conexion->query($query);
    }

    public function escape($string) {
        return $this->conexion->real_escape_string($string);
    }

    public function __destruct(){
        $this->conexion->close();
    }
}


