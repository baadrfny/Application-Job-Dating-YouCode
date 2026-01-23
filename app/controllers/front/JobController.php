<?php
namespace controllers\front;

use core\Controller;
use core\Auth;
use core\Response;
use models\AnnonceModel;
use models\Application;
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

        if (!$offer || (int) ($offer['deleted_at'] ?? 0) === 1) {
            Response::redirect('/annonces');
            return '';
        }

        $company = $this->companyModel->findById($offer['entreprise_id']);
        $studentId = Auth::id('apprenant');
        $alreadyApplied = false;
        if ($studentId) {
            $alreadyApplied = (bool) (new Application())
                ->findByStudentAndAnnouncement($studentId, (int) $id);
        }

        return $this->render('front/jobs/show', [
            'offer' => $offer,
            'company' => $company,
            'title' => $offer['titre'] ?? 'Offre',
            'already_applied' => $alreadyApplied
        ]);
    }

    public function filter($request) {
        $search = $request->input('search');
        $company_id = $request->input('company_id');
        $contract_type = $request->input('contract_type');
        
        // If search query is provided, use search method
        if ($search) {
            $offers = $this->announceModel->searchAnnonces($search);
        } else {
            // Otherwise use filtered search
            $offers = $this->announceModel->getFilteredAnnonces($company_id, $contract_type);
        }
        
        header('Content-Type: application/json');
        return json_encode($offers);
    }
}
