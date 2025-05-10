<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\{Inventory, Payment, Service, ServiceRequest, Technicians, User, Vehicle};
use JetBrains\PhpStorm\NoReturn;

class DashboardController extends Controller
{
    public function index()
    {
        $this->ensureLoggedIn();
        $user = $_SESSION['user'];
        $this->ensureRoleSet($user);

        $data = match ($user["role"]) {
            'admin' => $this->adminData(),
            'technician' => $this->technicianData($user['id']),
            default => $this->userData($user['id']),
        };

        return $this->render("dashboard/{$user['role']}", $data, $user['role'] === 'admin' ? 'dashboard' : 'main');
    }

    private function adminData(): array
    {
        $serviceModel = new ServiceRequest();
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

    public function serviceRequests(): string
    {
        $this->ensureLoggedIn();
        $serviceModel = new ServiceRequest();

        $data = [
            'active_requests' => $serviceModel->getActiveRequests(),
            'available_technicians' => $serviceModel->getAvailableTechnicians(),
            'history_requests' => $serviceModel->getHistoryRequests(),
        ];

        return $this->render('dashboard/admin/service-requests', $data, 'dashboard');
    }

    #[NoReturn] public function updateServiceRequest(): void
    {
        $this->ensureLoggedIn();
        $serviceModel = new ServiceRequest();
        $requestId = $_POST['id'] ?? null;

        if ($requestId) {
            $updates = array_filter([
                'status' => $_POST['status'] ?? null,
                'technician_id' => $_POST['assign_mechanic_id'] ?? null,
                'completion_datetime' => ($_POST['status'] ?? '') === 'completed' ? date('Y-m-d H:i:s') : null,
            ]);

            if ($updates) {
                $serviceModel->update($requestId, $updates);
                $this->toast(['success' => ['title' => 'Berhasil', 'message' => 'Data berhasil disimpan.']]);
            }
        }

        $this->redirect('/dashboard/service-requests');
    }

    public function customers(): string
    {
        $this->ensureLoggedIn();
        $userModel = new User();

        return $this->render('dashboard/admin/customers', [
            'customers' => $userModel->getActiveUsers('customer')
        ], 'dashboard');
    }

    public function inventory(): string
    {
        $this->ensureLoggedIn();
        $inventoryModel = new Inventory();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleInventoryPost($inventoryModel);
        }

        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $criteria = array_filter([
            'name' => isset($_GET['search']) ? ['like' => "%" . $_GET['search'] . "%"] : null,
            'category' => $_GET['category'] ?? null,
        ]);

        if (isset($_GET['status'])) {
            $criteria['current_stock'] = match ($_GET['status']) {
                'available' => ['operator' => '>', 'value' => 'reorder_level'],
                'low_stock' => ['operator' => '<=', 'value' => 'reorder_level', 'and' => ['operator' => '>', 'value' => 0]],
                'out_of_stock' => ['operator' => '<=', 'value' => 0],
            };
            unset($criteria['status']);
        }

        return $this->render('dashboard/admin/inventory', [
            'inventory_items' => $inventoryModel->findAllPagination($limit, $offset, $criteria),
            'pagination' => [
                'total' => $inventoryModel->count(),
                'page_count' => ceil($inventoryModel->count() / $limit),
                'page' => $page,
                'limit' => $limit,
            ],
        ], 'dashboard');
    }

    #[NoReturn] private function handleInventoryPost(Inventory $inventoryModel): void
    {
        $action = $_POST['action'] ?? null;
        $id = $_POST['id'] ?? null;

        if ($action === 'delete' && $id) {
            $inventoryModel->delete((int)$id);
        } else {
            $data = array_filter([
                'part_number' => $_POST['part_number'] ?? null,
                'name' => $_POST['name'] ?? null,
                'category' => $_POST['category'] ?? null,
                'supplier' => $_POST['supplier'] ?? null,
                'unit' => $_POST['unit'] ?? null,
                'current_stock' => $_POST['current_stock'] ?? null,
                'reorder_level' => $_POST['reorder_level'] ?? null,
                'unit_price' => $_POST['unit_price'] ?? null,
                'location' => $_POST['location'] ?? null,
            ]);

            $id ? $inventoryModel->update((int)$id, $data) : $inventoryModel->create($data);
            $this->toast(['success' => ['title' => 'Berhasil', 'message' => 'Data berhasil disimpan.']]);
        }

        $this->redirect('/dashboard/inventory');
    }

    public function technicians(): string
    {
        $this->ensureLoggedIn();
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $techniciansModel = new Technicians();
        $technicians = $techniciansModel->findAllPagination($limit, $offset, [], [
            ['type' => 'INNER', 'table' => 'users', 'on' => 'technicians.user_id = users.id'],
        ]);

        $serviceModel = new ServiceRequest();
        foreach ($technicians as &$tech) {
            $tech['active_requests'] = $serviceModel->countByTechnicianAndStatus($tech['id'], 'in_progress');
            $tech['status'] = $tech['active_requests'] > 0 ? 'on_duty' : 'available';
        }

        return $this->render('dashboard/admin/technicians', [
            'technicians' => $technicians,
            'pagination' => [
                'total' => $techniciansModel->count(),
                'page_count' => ceil($techniciansModel->count() / $limit),
                'page' => $page,
                'limit' => $limit,
            ],
        ], 'dashboard');
    }

    public function services(): string
    {
        $this->ensureLoggedIn();
        $serviceModel = new Service();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleServicePost($serviceModel);
        }

        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $criteria = array_filter([
            'name' => isset($_GET['search']) ? ['like' => "%{$_GET['search']}%"] : null,
            'category' => $_GET['category'] ?? null,
            'estimated_hours' => $this->parseEstTime($_GET['est_time'] ?? null),
        ]);

        return $this->render('dashboard/admin/services', [
            'services' => $serviceModel->findAllPagination($limit, $offset, $criteria),
            'pagination' => [
                'total' => $serviceModel->count(),
                'page_count' => ceil($serviceModel->count() / $limit),
                'page' => $page,
                'limit' => $limit,
            ],
        ], 'dashboard');
    }

    #[NoReturn] private function handleServicePost(Service $serviceModel): void
    {
        $action = $_POST['action'] ?? null;
        $id = $_POST['id'] ?? null;

        if ($action === 'delete' && $id) {
            $serviceModel->delete((int)$id);
        } else {
            $data = array_filter([
                'name' => $_POST['name'] ?? null,
                'description' => $_POST['description'] ?? null,
                'base_price' => $_POST['base_price'] ?? null,
                'estimated_hours' => $_POST['estimated_hours'] ?? null,
                'category' => $_POST['category'] ?? null,
            ]);

            $id ? $serviceModel->update((int)$id, $data) : $serviceModel->create($data);
            $this->toast(['success' => ['title' => 'Berhasil', 'message' => 'Data berhasil disimpan.']]);
        }

        $this->redirect('/dashboard/services');
    }

    private function parseEstTime(?string $estTime): ?array
    {
        if (!$estTime) return null;

        if (str_contains($estTime, '-')) {
            [$min, $max] = explode('-', $estTime);
            return ['between' => [trim($min), trim($max)]];
        }

        return match ($estTime[0]) {
            '>' => ['operator' => '>', 'value' => substr($estTime, 1)],
            '<' => ['operator' => '<', 'value' => substr($estTime, 1)],
            default => null,
        };
    }

    public function analytics()
    {
        $this->ensureLoggedIn();

        $analytics = $this->getAnalyticsData();

        return $this->render('dashboard/admin/analytics', [
            'analytics' => $analytics
        ], 'dashboard');
    }

    private function getAnalyticsData(): array
    {
        $serviceModel = new ServiceRequest();
        $paymentModel = new Payment();
        $technicianModel = new Technicians();
        $inventoryModel = new Inventory();

        return [
            'services' => [
                'total' => $serviceModel->count(),
                'completed' => $serviceModel->countByStatus('completed'),
                'pending' => $serviceModel->countByStatus('pending'),
                'in_progress' => $serviceModel->countByStatus('in_progress')
            ],
            'revenue' => [
                'total' => $paymentModel->getTotalRevenue(),
                'monthly' => $paymentModel->getMonthlyRevenue()
            ],
            'technicians' => $this->getTechnicianStats($technicianModel, $serviceModel),
            'inventory' => [
                'low_stock' => $inventoryModel->countLowStock(),
                'out_of_stock' => $inventoryModel->countOutOfStock()
            ]
        ];
    }

    private function getTechnicianStats(Technicians $technicianModel, ServiceRequest $serviceModel): array
    {
        $technicians = $technicianModel->findAll();
        $stats = [];

        foreach ($technicians as $tech) {
            // Access user_id instead of id, based on table structure
            $techId = $tech['user_id'];
            $stats[] = [
                'id' => $techId,
                'name' => $tech['name'] ?? 'Technician #' . $techId,
                'assigned' => $serviceModel->countByTechnician($techId),
                'completed' => $serviceModel->countByTechnicianAndStatus($techId, 'completed'),
                'in_progress' => $serviceModel->countByTechnicianAndStatus($techId, 'in_progress')
            ];
        }

        return $stats;
    }

    private function ensureLoggedIn(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    private function ensureRoleSet(array $user): void
    {
        if (empty($user['role'])) {
            $this->redirect('/login');
        }
    }
}