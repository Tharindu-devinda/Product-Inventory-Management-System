<?php

use Core\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->view('dashboard');
    }
}