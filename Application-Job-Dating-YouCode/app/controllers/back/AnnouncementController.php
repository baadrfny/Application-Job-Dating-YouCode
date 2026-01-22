<?php
namespace controllers\back;

use core\Controller;
use models\AnnonceModel;
use models\EntrepriseModel;

class AnnouncementController extends Controller {
    
    // show annonces liste
    public function index() {
        $model = new AnnonceModel();
        $annonces = $model->getActiveAnnonces();
        
        return $this->render('back/announcements/index', [
            'annonces' => $annonces
        ]);
    }

    // show add newAnnonce Page
    public function create() {
        $entrepriseModel = new EntrepriseModel();
        $entreprises = $entrepriseModel->all();
        return $this->render('back/announcements/create', [
            'entreprises' => $entreprises
        ]);
    }
}