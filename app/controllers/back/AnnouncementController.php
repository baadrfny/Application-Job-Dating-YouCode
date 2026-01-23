<?php
namespace controllers\back;

use core\Controller;
use core\Request;
use core\Response;
use core\Security;
use models\AnnonceModel;
use models\EntrepriseModel;

class AnnouncementController extends Controller {
    protected string $csrfScope = 'admin';
    
    // show annonces liste
    public function index(Request $request) {
        $model = new AnnonceModel();
        $annonces = $model->getActiveAnnonces();
        
        return $this->render('back/announcements/index', [
            'annonces' => $annonces,
            'title' => 'Annonces',
            'active' => 'annonces'
        ]);
    }

    // show add newAnnonce Page
    public function create(Request $request) {
        $entrepriseModel = new EntrepriseModel();
        $entreprises = $entrepriseModel->all();
        return $this->render('back/announcements/create', [
            'entreprises' => $entreprises,
            'title' => 'Nouvelle annonce',
            'active' => 'annonces'
        ]);
    }

    public function store(Request $request): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/annonces');
            return '';
        }

        $data = $this->extractAnnonceData($request, false);
        if ($data === null) {
            Response::redirect('/admin/annonces/create');
            return '';
        }

        (new AnnonceModel())->create($data);
        Response::redirect('/admin/annonces');
        return '';
    }

    public function edit(Request $request, $id): string
    {
        $annonceModel = new AnnonceModel();
        $entrepriseModel = new EntrepriseModel();
        $annonce = $annonceModel->find($id);
        $entreprises = $entrepriseModel->all();

        return $this->render('back/announcements/edit', [
            'annonce' => $annonce,
            'entreprises' => $entreprises,
            'title' => 'Modifier annonce',
            'active' => 'annonces'
        ]);
    }

    public function update(Request $request, $id): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/annonces');
            return '';
        }

        $data = $this->extractAnnonceData($request, true);
        if ($data === null) {
            Response::redirect('/admin/annonces/edit/' . $id);
            return '';
        }

        (new AnnonceModel())->update($id, $data);
        Response::redirect('/admin/annonces');
        return '';
    }

    public function archive(Request $request, $id): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/annonces');
            return '';
        }

        (new AnnonceModel())->archive((int) $id);
        Response::redirect('/admin/annonces');
        return '';
    }

    public function restore(Request $request, $id): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/archives');
            return '';
        }

        (new AnnonceModel())->restore((int) $id);
        Response::redirect('/admin/archives');
        return '';
    }

    public function archived(Request $request) {
        $model = new AnnonceModel();
        $annonces = $model->getArchivedAnnonces();

        return $this->render('back/announcements/archived', [
            'annonces' => $annonces,
            'title' => 'Archives',
            'active' => 'archives'
        ]);
    }

    private function extractAnnonceData(Request $request, bool $isUpdate): ?array
    {
        $titre = Security::sanitize((string) $request->input('titre', ''));
        $entrepriseId = (int) $request->input('entreprise_id', 0);
        $description = Security::sanitize((string) $request->input('description', ''));
        $typeContrat = Security::sanitize((string) $request->input('type_contrat', ''));
        $localisation = Security::sanitize((string) $request->input('localisation', ''));
        $competences = Security::sanitize((string) $request->input('competences', ''));
        $imagePath = $this->handleImageUpload('image', 'annonces');
        if ($imagePath === 'invalid') {
            return null;
        }

        if ($titre === '' || $entrepriseId <= 0) {
            return null;
        }

        $data = [
            'titre' => $titre,
            'entreprise_id' => $entrepriseId,
            'description' => $description,
            'type_contrat' => $typeContrat,
            'localisation' => $localisation,
            'competences' => $competences,
        ];

        if ($imagePath !== '') {
            $data['image'] = $imagePath;
        } elseif (!$isUpdate) {
            $data['image'] = null;
        }

        return $data;
    }

    private function handleImageUpload(string $field, string $folder): string
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return '';
        }

        $file = $_FILES[$field];
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return '';
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'invalid';
        }

        $maxSize = 3 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return 'invalid';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            return 'invalid';
        }

        $root = dirname(__DIR__, 3);
        $uploadDir = $root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = $folder . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return 'invalid';
        }

        return '/uploads/' . $folder . '/' . $fileName;
    }
}
