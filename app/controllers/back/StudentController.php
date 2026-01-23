<?php

namespace controllers\back;

use core\Controller;
use core\Request;
use models\ApprenantModel;

class StudentController extends Controller {
    protected string $csrfScope = 'admin';
    public function index(Request $request) {
        $apprenantModel = new ApprenantModel();
        $apprenants = $apprenantModel->getAllApprenants();

        return $this->render('back/students/index', [
            'apprenants' => $apprenants,
            'title' => 'Apprenants',
            'active' => 'apprenants'
        ]);
    }
}
