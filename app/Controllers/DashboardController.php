<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        $data = match ($userRole) {
            'admin' => $this->adminData(),
            'technician' => $this->technicianData($userId),
            default => $this->userData($userId),
        };

//        CONSOLE.LOG
        echo "<script>console.log(" . json_encode($data) . ");</script>";

        return $this->render("dashboard/$userRole", $data);
    }

    private function adminData(): array
    {
        $serviceModel = new ServiceRequest();
        $userModel = new User();

        return [
            'total_requests' => $serviceModel->countAll(),
            'pending_requests' => $serviceModel->countByStatus('pending'),
            'completed_requests' => $serviceModel->countByStatus('completed'),
            'active_users' => $userModel->getActiveUsers(),
//            'technicians' => $userModel->countByRole('technician'),
        ];
    }

    private function technicianData(int $technicianId): array
    {
        $serviceModel = new ServiceRequest();

        return [
            'assigned_requests' => $serviceModel->getAssignedToTechnician($technicianId),
            'pending' => $serviceModel->countByTechnicianAndStatus($technicianId, 'pending'),
            'in_progress' => $serviceModel->countByTechnicianAndStatus($technicianId, 'in_progress'),
        ];
    }

    private function userData(int $userId): array
    {
        $serviceModel = new ServiceRequest();
        $paymentModel = new Payment();
        $vehicleModel = new Vehicle();

        return [
            'active_requests' => $serviceModel->getByUser($userId, ['pending', 'in_progress']),
            'history_requests' => $serviceModel->getHistoryByUser($userId),
            'payments' => $paymentModel->getPaymentUsers($userId),
            'vehicles' => $vehicleModel->getByUserId($userId),
        ];
    }
}
