<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HealthController extends Controller
{
    public function index()
    {
        $databaseConnection = Database::getInstance();

        if (!$databaseConnection->isConnected()) {
            $this->json([
                'status' => 'unhealthy',
                'message' => 'Database connection failed',
                'timestamp' => date('c'),
            ]);
        } else {
            $this->json([
                'status' => 'healthy',
                'message' => 'OK',
                'timestamp' => date('c'),
            ]);
        }
    }
}