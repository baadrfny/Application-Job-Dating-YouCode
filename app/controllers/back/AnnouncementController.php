<?php
namespace controllers\back;

use core\Controller;
use models\AnnonceModel;
use models\EntrepriseModel;

class AnnouncementController extends Controller {
    protected string $csrfScope = 'admin';
    
    // show annonces liste
    public function index() {
        $model = new AnnonceModel();
        $annonces = $model->getActiveAnnonces();
        
        return $this->render('back/announcements/index', [
            'annonces' => $annonces,
            'title' => 'Annonces',
            'active' => 'annonces'
        ]);
    }

    // show add newAnnonce Page
    public function create() {
        $entrepriseModel = new EntrepriseModel();
        $entreprises = $entrepriseModel->all();
        return $this->render('back/announcements/create', [
            'entreprises' => $entreprises,
            'title' => 'Nouvelle annonce',
            'active' => 'annonces'
        ]);
    }

    public function archived() {
        $model = new AnnonceModel();
        $annonces = $model->getArchivedAnnonces();

        return $this->render('back/announcements/archived', [
            'annonces' => $annonces,
            'title' => 'Archives',
            'active' => 'archives'
        ]);
    }
}
