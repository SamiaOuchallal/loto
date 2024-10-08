<?php

class Model
{
    /**
     * Attribut contenant l'instance PDO
     */
    private $bd;

    /**
     * Attribut statique qui contiendra l'unique instance de Model
     */
    private static $instance = null;

    /**
     * Constructeur du modèle : permet d'effectuer la connexion à la base de données
     */
    private function __construct()
    {
        include "credentials.php";
        $this->bd = new PDO($dsn, $login, $mdp);
        $this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->bd->query("SET nameS 'utf8'");
    }

    /**
     * Méthode permettant de récupérer le modèle
     */
    public static function getModel()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Méthode qui permet de récupérer le mot de passe associé à l'adresse mail d'un professeur
     * @param string $adr qui représente l'adresse mail du professeur
     * @return mixed
     */
    public function loginP($m) {
        $requete = $this->bd->prepare('SELECT motDePasse FROM personne where mail = :m');
        $requete->bindValue(":m", $m);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

}