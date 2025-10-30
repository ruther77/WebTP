<?php

require_once __DIR__ . '/../DAO/DAO.php';
require_once __DIR__ . '/approcom.php';

class Approvis extends ApproCom{
    
    public function save(){
        $this->dao->ajouterApprovis($this);
    }

    public static function afficher(){
        $dao = new DAO();
        return $dao->afficherApprovis();
    }

    public static function total(){
        $dao = new DAO();
        return $dao->getApprovisTotal();
    }

    public static function getApprovis($id){
        $dao = new DAO();
        return $dao->getApprovis($id);
    }

    public static function delete($id){
        $dao = new DAO();
        $dao->deleteApprovis($id);
    }

}

?>