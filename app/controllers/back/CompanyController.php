<?php

namespace controllers\back;

use models\EntrepriseModel;
use core\Controller;

class CompanyController extends Controller {

    public function index() {
        $entrepriseModel = new EntrepriseModel();
        $entreprises = $entrepriseModel->all();

        return $this->render('back/companies/index', [
            'entreprises' => $entreprises,
            'title' => 'Entreprises',
            'active' => 'entreprises'
        ]);
    }

    public function create() {
        return $this->render('back/companies/create', [
            'title' => 'Nouvelle entreprise',
            'active' => 'entreprises'
        ]);
    }
    
    public function delete($id) {
        $entrepriseModel = new EntrepriseModel();
        
        //delete a compny with all annonces
        if ($entrepriseModel->delete($id)) {

        //redirect with a msj 
        header('Location: /admin/companies?success=deleted');
        } else {
            header('Location: /admin/companies?error=failed');
        }
    }
}
