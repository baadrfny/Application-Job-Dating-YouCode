<?php
namespace controllers\front;

use core\Controller;
use core\Auth;
use models\AnnonceModel;
use models\Company;
use models\User;

class JobController extends Controller{
    private $announceModel;
    private $companyModel;
    
    public function __construct() {
        parent::__construct();
        $this->announceModel = new AnnonceModel();
        $this->companyModel = new Company();
    }
    
    public function index($request) {
        $offers = $this->announceModel->getActiveAnnonces();
        $companies = $this->companyModel->getAll();
        $studentName = 'Etudiant YouCode';
        $userId = Auth::id('apprenant');
        if ($userId) {
            $user = (new User())->find($userId);
            if ($user && !empty($user['name'])) {
                $studentName = $user['name'];
            }
        }
        
        return $this->render('front/jobs/index', [
            'offers' => $offers,
            'companies' => $companies,
            'student_name' => $studentName,
            'title' => 'Offres'
        ]);
    }
    
    public function show($request, $id) {
        $offer = $this->announceModel->find($id);
        
        // if (!$offer || $offer['deleted_at'] == 1) {
        //     http_response_code(404);
        //     return "<h1>Error 404</h1><p>Offer not found</p>";
        // }
        
        $company = $this->companyModel->findById($offer['entreprise_id']);
        return $this->render('front/jobs/show', [
            'offer' => $offer,
            'company' => $company,
            'title' => $offer['titre'] ?? 'Offre'
        ]);
    }

    public function filter($request) {
        $search = $request->input('search');
        $company_id = $request->input('company_id');
        $contract_type = $request->input('contract_type');
        
        // Use combined filtering that handles search + filters
        $offers = $this->announceModel->getFilteredAnnonces($company_id, $contract_type, $search);
        
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        echo json_encode($offers);
        exit;
    }
}
