<?php

// echo $err_messages[http_response_code()];

class Produto{
    // conexión coa táboa da base de datos
    private $conn;
    private $taboa = "produtos";
    // propiedades do obxecto
    public $id;
    public $nome;
    public $descricion;
    public $prezo;
    public $idCategoria;
    public $nomeCategoria;
    public $creado;
    public $modificado;
    //public $creado;
    //public $oldId;

    // constructor con $db como conexión coa base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    function consultarID() {
        $query = "SELECT * FROM " .$this->taboa. " WHERE id = " .$this->id;
        $stmt = $this->conn->query($query);
        $num = $stmt->num_rows;

        if ($num != 0) {
            // echo "testing1";
            // var_dump($num);
            return true;
        }
        else {
            return false;
        }
    }

    // lectura de produtos
    function ler() {
        // consulta select all
        $query = "SELECT
                    c.nome as nomeCategoria, p.id, p.nome, p.descricion, p.prezo, p.idCategoria, p.creado, p.modificado
                FROM
                    " . $this->taboa . " p
                    LEFT JOIN
                        categorias c
                            ON p.idCategoria = c.id
                ORDER BY
                    p.creado DESC";
        // prepara a consulta
        $stmt = $this->conn->query($query);
        // execución da consulta
        //$stmt->execute();
        return $stmt;
    }

    function crear() {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->taboa . " 
          SET 
         id = ? , 
         nome = ? ,
         prezo = ? ,
         descricion = ? ,
         idCategoria = ? ,
         creado = ? ,
         modificado = ? ");

        $this->id = null;
        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $this->prezo=htmlspecialchars(strip_tags($this->prezo));
        $this->descricion=htmlspecialchars(strip_tags($this->descricion));
        $this->idCategoria=htmlspecialchars(strip_tags($this->idCategoria));
        $this->creado = date("Y-m-d H:i:s");
        $this->modificado = date("Y-m-d H:i:s");
    

        $stmt->bind_param("issiiss", $this->id, $this->nome, $this->descricion, $this->prezo, $this->idCategoria, $this->creado, $this->modificado);
        $stmt->execute();
        return $stmt;
    }

    function ler1() {
        $stmt = $this->conn->prepare(
            "SELECT
            c.nome as nomeCategoria, p.id, p.nome, p.descricion, p.prezo, p.idCategoria, p.creado, p.modificado
        FROM
            " . $this->taboa . " p
            LEFT JOIN
                categorias c
                    ON p.idCategoria = c.id
            WHERE p.id = ?
            ORDER BY p.creado DESC");
              
           
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $res = $stmt->get_result(); 
        return $res;  
    }

    function actualizar() {
        $existeID = $this->consultarID();
        if ($existeID) {
            $stmt = $this->conn->prepare("UPDATE ".$this->taboa." SET 
            nome = ?, 
            descricion= ?, 
            prezo = ?, 
            idCategoria= ?, 
            modificado = ?
            WHERE id = ?");

            $this->nome=htmlspecialchars(strip_tags($this->nome));
            $this->prezo=htmlspecialchars(strip_tags($this->prezo));
            $this->descricion=htmlspecialchars(strip_tags($this->descricion));
            $this->idCategoria=htmlspecialchars(strip_tags($this->idCategoria));
            $this->id=htmlspecialchars(strip_tags($this->id));
            $this->modificado = date("Y-m-d H:i:s");

            $stmt->bind_param("ssiiis", $this->nome, $this->descricion, 
                $this->prezo, $this->idCategoria, $this->id, $this->modificado);

            $stmt->execute();
            return $stmt;
        }
    }

    function borrar() {
        $stmt = $this->conn->prepare("DELETE FROM ".$this->taboa." 
        WHERE id = ?");

        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        return $stmt;
    }

    function buscar() {
        $stmt = $this->conn->prepare("SELECT
        c.nome as nomeCategoria, p.id, p.nome, p.descricion, p.prezo, p.idCategoria, p.creado, p.modificado
    FROM
        " . $this->taboa . " p
        LEFT JOIN
            categorias c
                ON p.idCategoria = c.id
        WHERE p.nome LIKE ?
        ORDER BY p.creado DESC");

        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $thisNome = "%{$this->nome}%";
        $stmt->bind_param("s", $thisNome);
        $stmt->execute();
        $res = $stmt->get_result(); 
        return $res;  
    }
}

?>