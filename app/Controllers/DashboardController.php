<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Vehicle;
use JetBrains\PhpStorm\NoReturn;

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

        echo "<script>console.log(" . json_encode($data) . ");</script>";
        return $this->render("dashboard/$userRole", $data, "dashboard");
    }

    private function adminData(): array
    {
        $serviceModel = new ServiceRequest();
        $userModel = new User();
        $inventoryModel = new Inventory();

        return [
            'recent_requests' => $serviceModel->getRecentRequests(),
            'statuses' => $serviceModel->countStatuses(),
            'lower_stock_items' => $inventoryModel->getLowerStockItems(),
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

    public function serviceRequests()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $serviceModel = new ServiceRequest();

        $data = [
            'active_requests' => $serviceModel->getActiveRequests(),
            'available_technicians' => $serviceModel->getAvailableTechnicians(),
//            'history_requests' => $serviceModel->getHistoryRequests()
        ];

        return $this->render('dashboard/admin/service-requests', $data, 'dashboard');
    }

    #[NoReturn] public function updateServiceRequest()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $assignTechnician = $_POST['assign_mechanic_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $requestId = $_POST['id'] ?? null;

        $serviceModel = new ServiceRequest();

        if ($requestId) {
            $updates = array_filter([
                'status' => $status,
                'technician_id' => $assignTechnician,
            ]);

            if ($updates) {
                $serviceModel->update($requestId, $updates);
            }
        }

        $this->redirect('/dashboard/service-requests');
    }

    public function customers()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $data = [
            'customers' => $userModel->getActiveUsers('customer'),
        ];

        return $this->render('dashboard/admin/customers', $data, 'dashboard');
    }

    public function inventory()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $inventoryModel = new Inventory();
        $data = [
            'low_stock_items' => $inventoryModel->getLowStockItems(),
        ];

        return $this->render('dashboard/admin/inventory', $data, 'dashboard');
    }

    public function technicians()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $data = [
            'technicians' => $userModel->getActiveUsers('technician'),
        ];

        return $this->render('dashboard/admin/technicians', $data, 'dashboard');
    }

    public function services()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $serviceModel = new ServiceRequest();
        $data = [
            'services' => $serviceModel->getAllServices(),
        ];

        return $this->render('dashboard/admin/services', $data, 'dashboard');
    }

    public function analytics()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $serviceModel = new ServiceRequest();
        $data = [
            'analytics' => $serviceModel->getAnalytics(),
        ];

        return $this->render('dashboard/admin/analytics', $data, 'dashboard');
    }

}
