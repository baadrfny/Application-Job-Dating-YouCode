<?php

namespace controllers\back;

use core\Controller;
use core\Request;
use core\Response;
use models\Application;
use models\AnnonceModel;

class ApplicationController extends Controller
{
    protected string $csrfScope = 'admin';

    public function index(Request $request, $announcementId): string
    {
        $announcement = (new AnnonceModel())->find($announcementId);
        $applications = (new Application())->getByAnnouncement((int) $announcementId);

        return $this->render('back/applications/index', [
            'announcement' => $announcement,
            'applications' => $applications,
            'title' => 'Candidatures',
            'active' => 'annonces'
        ]);
    }

    public function updateStatus(Request $request, $id): string
    {
        if (!$this->validateCSRF($request)) {
            Response::redirect('/admin/dashboard');
            return '';
        }

        $status = (string) $request->input('status', 'pending');
        $announcementId = (int) $request->input('announcement_id', 0);
        $allowed = ['pending', 'accepted', 'rejected'];
        if (!in_array($status, $allowed, true)) {
            Response::redirect('/admin/dashboard');
            return '';
        }

        (new Application())->update($id, ['status' => $status]);

        if ($announcementId > 0) {
            Response::redirect('/admin/annonces/' . $announcementId . '/candidatures');
            return '';
        }

        Response::redirect('/admin/dashboard');
        return '';
    }

    public function downloadCv(Request $request, $id): string
    {
        $application = (new Application())->find($id);
        $cvPath = $application['cv_path'] ?? '';
        if ($cvPath === '') {
            Response::redirect('/admin/dashboard');
            return '';
        }

        $root = dirname(__DIR__, 3);
        $fullPath = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $cvPath);
        if (!is_file($fullPath)) {
            Response::redirect('/admin/dashboard');
            return '';
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="cv.pdf"');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }
}
