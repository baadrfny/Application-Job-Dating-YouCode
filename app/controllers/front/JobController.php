<?php
namespace controllers\front;

use models\Announcement;
use models\Company;

class JobController {
    private $announceModel;
    private $companyModel;
    
    public function __construct() {
        $this->announceModel = new Announcement();
        $this->companyModel = new Company();
    }
    
    public function index($request) {
        $offers = $this->announceModel->getActiveOffers();
        $companies = $this->companyModel->getAll();
        
        return $this->renderTemplate('front/jobs/index', [
            'offers' => $offers,
            'companies' => $companies
        ]);
    }
    
    public function show($request, $id) {
        $offer = $this->announceModel->findById($id);
        
        if (!$offer || $offer['deleted_at'] == 1) {
            return $this->renderError(404, "Offer not found");
        }
        
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
}