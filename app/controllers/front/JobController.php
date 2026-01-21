<?php
namespace controllers\front;

use core\Controller;
use models\AnnonceModel;
use models\Company;

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
        
        return $this->render('front/jobs/index', [
            'offers' => $offers,
            'companies' => $companies
        ]);
    }
    
    public function show($request, $id) {
        $offer = $this->announceModel->find($id);
        
        // if (!$offer || $offer['deleted_at'] == 1) {
        //     http_response_code(404);
        //     return "<h1>Error 404</h1><p>Offer not found</p>";
        // }
        
        $company = $this->companyModel->findById($offer['entreprise_id']);
        
        return $this->renderTemplate('front/jobs/show', [
            'offer' => $offer,
            'company' => $company
        ]);
    }
    
    private function renderTemplate($template, $data = []) {
        extract($data);
        ob_start();
        include __DIR__ . '/../../../app/views/' . $template . '.twig';
        return ob_get_clean();
    }
    
    private function renderError($code, $message) {
        http_response_code($code);
        return "<h1>Error $code</h1><p>$message</p>";
    }


    public function filter($request) {
    $company_id = $request->get('company_id');
    $contract_type = $request->get('contract_type');
    
    $offers = $this->announceModel->getFilteredAnnonces($company_id, $contract_type);
    
    //  JSON take back AJAX
    header('Content-Type: application/json');
    return json_encode($offers);
}
}