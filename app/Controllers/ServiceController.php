<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use JetBrains\PhpStorm\NoReturn;

class ServiceController extends Controller
{
    private Service $serviceModel;
    private ServiceRequest $requestServiceModel;

    public function __construct()
    {
        $this->serviceModel = new Service();
        $this->requestServiceModel = new ServiceRequest();
    }

    public function index(): string
    {
        $services = array_map(function ($service) {
            return [
                'id' => $service['id'],
                'name' => $service['name'],
                'description' => $service['description'],
                'duration' => number_format($service['estimated_hours'] ?? 0, 0, ',', '.') . ' jam',
                'price' => 'Rp ' . number_format($service['base_price'] ?? 0, 0, ',', '.'),
            ];
        }, $this->serviceModel->findAll());

        return $this->render('service/booking', [
            'services' => $services
        ]);
    }

    #[NoReturn] public function getAvailableServiceTimes(): void
    {
        $serviceId = $_GET['service_id'] ?? null;
        $date = $_GET['date'] ?? null;
        $categoryId = $_GET['category_id'] ?? null;

        if ($serviceId) {
            $availableTimes = $this->requestServiceModel->getAvailableServiceTimes($serviceId, $date, $categoryId);
            $this->json($availableTimes);
        } else {
            $this->json(['error' => 'Invalid service ID'], 400);
        }
    }

}