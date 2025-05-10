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
            'history_requests' => $serviceModel->getHistoryRequests()
        ];

        echo "<script>console.log(" . json_encode($data) . ");</script>";

        return $this->render('dashboard/admin/service-requests', $data, 'dashboard');
    }

    #[NoReturn] public function updateServiceRequest(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }


        $assignTechnician = !empty($_POST['assign_mechanic_id']) ? (int)$_POST['assign_mechanic_id'] : null;
        $status = $_POST['status'] ?? null;
        $requestId = $_POST['id'] ?? null;

        $serviceModel = new ServiceRequest();

        if ($requestId) {
            $updates = array_filter([
                'status' => $status,
                'technician_id' => $assignTechnician,
                'completion_datetime' => $status === 'completed' ? date('Y-m-d H:i:s') : null,
            ], function ($value) {
                return $value !== null && $value !== '';
            });

            if (!empty($updates)) {
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

        echo "<script>console.log(" . json_encode($data) . ");</script>";

        return $this->render('dashboard/admin/customers', $data, 'dashboard');
    }

    public function inventory()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $inventoryModel = new Inventory();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle delete request
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                if (!empty($_POST['id'])) {
                    $inventoryModel->delete((int)$_POST['id']);
                    $this->redirect('/dashboard/inventory');
                    return;
                }
            }

            // Handle create/update request
            $data = [
                'part_number' => $_POST['part_number'],
                'name' => $_POST['name'],
                'category' => $_POST['category'],
                'supplier' => $_POST['supplier'],
                'unit' => $_POST['unit'],
                'current_stock' => $_POST['current_stock'],
                'reorder_level' => $_POST['reorder_level'],
                'unit_price' => $_POST['unit_price'],
                'location' => $_POST['location'] ?? null,
            ];

            if (!empty($_POST['id'])) {
                // Update existing inventory item
                $inventoryModel->update((int)$_POST['id'], $data);
            } else {
                // Create new inventory item
                $inventoryModel->create($data);
            }

            $this->redirect('/dashboard/inventory');
        }

        $data = [
            'low_stock_items' => $inventoryModel->getLowerStockItems(),
            'inventory_items' => $inventoryModel->findAll()
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
            'services' => $serviceModel->findAll()
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
            'analytics' => []
        ];

        return $this->render('dashboard/admin/analytics', $data, 'dashboard');
    }

}
