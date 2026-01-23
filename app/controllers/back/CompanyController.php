<?php

namespace controllers\back;

use models\EntrepriseModel;
use core\Controller;
use core\Request;
use core\Response;
use core\Security;

class CompanyController extends Controller {
    protected string $csrfScope = 'admin';

    public function index(Request $request) {
        $entrepriseModel = new EntrepriseModel();
        $entreprises = $entrepriseModel->all();

        return $this->render('back/companies/index', [
            'entreprises' => $entreprises,
            'title' => 'Entreprises',
            'active' => 'entreprises'
        ]);
    }

    public function create(Request $request) {
        return $this->render('back/companies/create', [
            'title' => 'Nouvelle entreprise',
            'active' => 'entreprises'
        ]);
    }

    public function store(Request $request): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/entreprises');
            return '';
        }

        $data = $this->extractCompanyData($request);
        if ($data === null) {
            Response::redirect('/admin/entreprises/create');
            return '';
        }

        $entrepriseModel = new EntrepriseModel();
        if ($entrepriseModel->isEmailExists($data['email'])) {
            Response::redirect('/admin/entreprises/create');
            return '';
        }

        $entrepriseModel->create($data);
        Response::redirect('/admin/entreprises');
        return '';
    }

    public function edit(Request $request, $id): string
    {
        $entreprise = (new EntrepriseModel())->find($id);
        return $this->render('back/companies/edit', [
            'entreprise' => $entreprise,
            'title' => 'Modifier entreprise',
            'active' => 'entreprises'
        ]);
    }

    public function update(Request $request, $id): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/entreprises');
            return '';
        }

        $data = $this->extractCompanyData($request);
        if ($data === null) {
            Response::redirect('/admin/entreprises/edit/' . $id);
            return '';
        }

        $entrepriseModel = new EntrepriseModel();
        if ($entrepriseModel->isEmailExistsForOther($data['email'], (int) $id)) {
            Response::redirect('/admin/entreprises/edit/' . $id);
            return '';
        }

        $entrepriseModel->update($id, $data);
        Response::redirect('/admin/entreprises');
        return '';
    }

    public function delete(Request $request, $id): string {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/entreprises');
            return '';
        }

        $entrepriseModel = new EntrepriseModel();
        
        //delete a compny with all annonces
        if ($entrepriseModel->delete($id)) {
            Response::redirect('/admin/entreprises?success=deleted');
            return '';
        }

        Response::redirect('/admin/entreprises?error=failed');
        return '';
    }

    private function extractCompanyData(Request $request): ?array
    {
        $nom = Security::sanitize((string) $request->input('nom', ''));
        $secteur = Security::sanitize((string) $request->input('secteur', ''));
        $localisation = Security::sanitize((string) $request->input('localisation', ''));
        $email = trim((string) $request->input('email', ''));
        $telephone = Security::sanitize((string) $request->input('telephone', ''));

        if ($nom === '' || $email === '') {
            return null;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return [
            'nom' => $nom,
            'secteur' => $secteur,
            'localisation' => $localisation,
            'email' => $email,
            'telephone' => $telephone
        ];
    }
}
