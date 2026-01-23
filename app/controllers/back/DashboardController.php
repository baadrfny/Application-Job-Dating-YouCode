<?php
namespace controllers\back;

use models\AnnonceModel;
use models\EntrepriseModel;
use models\ApprenantModel;
use core\Controller;
use core\Auth;
use models\User;

class DashboardController extends Controller {
    
    public function index() {

   
        
        $annonceModel = new AnnonceModel();
        $entrepriseModel = new EntrepriseModel();
        $apprenantModel = new ApprenantModel();


        //statistiques
        $stats = [
            'active_annonces'   => $annonceModel->count("deleted_at = 0"),
            'archived_annonces' => $annonceModel->count("deleted_at = 1"),
            'total_entreprises' => $entrepriseModel->count(),
            'total_apprenants'  => $apprenantModel->count()
        ];

        //last 3 annonces
        $latestAnnonces = $annonceModel->getLatest(3);
        $adminName = 'Administrateur';
        $adminId = Auth::id('admin');
        if ($adminId) {
            $user = (new User())->find($adminId);
            if ($user && !empty($user['name'])) {
                $adminName = $user['name'];
            }
        }

        // send all taht to Twig

        return $this->render('back/dashboard/index', [
            'stats' => $stats,
            'latestAnnonces' => $latestAnnonces,
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'admin_name' => $adminName
        ]);
    }
}
