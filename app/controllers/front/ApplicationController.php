<?php

namespace controllers\front;

use core\Auth;
use core\Controller;
use core\Request;
use core\Response;
use core\Security;
use models\AnnonceModel;
use models\Application;

class ApplicationController extends Controller
{
    protected string $csrfScope = 'app';

    public function index(Request $request): string
    {
        $this->requireAuth();
        $studentId = Auth::id('apprenant');
        $applications = [];
        $studentName = 'Etudiant YouCode';
        if ($studentId) {
            $applications = (new Application())->getByStudent($studentId);
            $user = (new \models\User())->find($studentId);
            if ($user && !empty($user['name'])) {
                $studentName = $user['name'];
            }
        }

        $status = (string) $request->input('status', '');
        $message = '';
        if ($status === 'submitted') {
            $message = 'Candidature envoyee avec succes.';
        } elseif ($status === 'exists') {
            $message = 'Vous avez deja postule a cette offre.';
        } elseif ($status === 'error') {
            $message = 'Impossible de soumettre votre candidature.';
        }

        return $this->render('front/applications/index', [
            'applications' => $applications,
            'message' => $message,
            'title' => 'Mes candidatures',
            'student_name' => $studentName
        ]);
    }

    public function store(Request $request, $announcementId): string
    {
        $this->requireAuth();

        if (!$this->validateCSRF($request)) {
            Response::redirect('/candidatures?status=error');
            return '';
        }

        $studentId = Auth::id('apprenant');
        if (!$studentId) {
            Response::redirect('/login');
            return '';
        }

        $announcement = (new AnnonceModel())->find($announcementId);
        if (!$announcement || (int) ($announcement['deleted_at'] ?? 0) === 1) {
            Response::redirect('/annonces');
            return '';
        }

        $applicationModel = new Application();
        if ($applicationModel->findByStudentAndAnnouncement($studentId, (int) $announcementId)) {
            Response::redirect('/candidatures?status=exists');
            return '';
        }

        $motivation = trim((string) $request->input('motivation', ''));
        if ($motivation === '') {
            Response::redirect('/candidatures?status=error');
            return '';
        }

        $cvPath = $this->handleCvUpload($studentId);
        if ($cvPath === 'invalid') {
            Response::redirect('/candidatures?status=error');
            return '';
        }

        $applicationModel->create([
            'student_id' => $studentId,
            'announcement_id' => (int) $announcementId,
            'motivation' => Security::sanitize($motivation),
            'cv_path' => $cvPath !== '' ? $cvPath : null,
            'status' => 'pending'
        ]);

        Response::redirect('/candidatures?status=submitted');
        return '';
    }

    private function handleCvUpload(int $studentId): string
    {
        if (!isset($_FILES['cv']) || !is_array($_FILES['cv'])) {
            return '';
        }

        $file = $_FILES['cv'];
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return '';
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'invalid';
        }

        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return 'invalid';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            return 'invalid';
        }

        $root = dirname(__DIR__, 3);
        $uploadDir = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cv';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'cv_' . $studentId . '_' . time() . '.pdf';
        $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return 'invalid';
        }

        return 'storage/cv/' . $fileName;
    }
}
