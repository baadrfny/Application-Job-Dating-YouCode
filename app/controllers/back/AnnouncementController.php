<?php
namespace controllers\back;

use core\Controller;
use models\AnnonceModel;
use models\EntrepriseModel;
use core\Security;

class AnnouncementController extends Controller {
    
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
            'active' => 'annonces',

            'csrf_token' => Security::generateCSRFToken()
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


    
// store new annonce
    public function store(\core\Request $request) {
        
        // die(var_dump($request->all()));

        if (!Security::verifyCSRFToken($request->input('csrf_token'))) {
        $entrepriseModel = new EntrepriseModel();
        return $this->render('back/announcements/create', [
            'entreprises' => $entrepriseModel->all(),
            'error' => 'Session expirée, veuillez réessayer.',
            'title' => 'Nouvelle annonce',
            'csrf_token' => Security::generateCSRFToken()
        ]);
    }

        $data = [
            'titre'         => $request->input('titre'),
            'description'   => $request->input('description'),
            'entreprise_id' => $request->input('entreprise_id'),
            'type_contrat'  => $request->input('type_contrat'),
            'localisation'  => $request->input('localisation'),
            'competences'   => $request->input('competences'),
            'image'         => $request->input('image') ?? '/public/assets/placeholder.svg'
        ];

        if (empty($data['titre']) || empty($data['entreprise_id'])) {

            $entrepriseModel = new EntrepriseModel();
            return $this->render('back/announcements/create', [
                'entreprises' => $entrepriseModel->all(),
                'error' => 'Veuillez remplir tous les champs obligatoires.',
                'title' => 'Nouvelle annonce'
            ]);
        }

        $model = new AnnonceModel();
        $result = $model->saveAnnonce($data);

        if ($result) {

        \core\Response::redirect('/admin/annonces');
        } else {

        $entrepriseModel = new EntrepriseModel();
            return $this->render('back/announcements/create', [
                'entreprises' => $entrepriseModel->all(),
                'error' => 'Erreur lors de l\'enregistrement dans la base de données.',
                'title' => 'Nouvelle annonce'
            ]);
        }
    }







    
}
