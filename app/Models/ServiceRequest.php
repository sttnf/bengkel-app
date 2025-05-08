<?php

namespace App\Models;

use App\Core\Model;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    public function getAvailableServiceTimes(int $serviceId, ?string $date = null, ?int $categoryId = null): array
    {
        $params = [];
        $query = "
            SELECT DATE(sr.scheduled_datetime) as scheduled_date, 
                   TIME(sr.scheduled_datetime) as scheduled_time
            FROM service_requests sr
            JOIN services s ON sr.service_id = s.id
            JOIN users u ON sr.user_id = u.id
            LEFT JOIN technicians t ON sr.technician_id = t.user_id
            WHERE sr.status NOT IN ('cancelled', 'completed')
        ";

        if ($categoryId !== null) {
            $query .= " AND s.category_id = :category_id";
            $params['category_id'] = $categoryId;
        } else {
            $query .= " AND sr.service_id = :service_id";
            $params['service_id'] = $serviceId;
        }

        if ($date !== null) {
            $query .= " AND DATE(sr.scheduled_datetime) = :date";
            $params['date'] = $date;
        }

        $query .= " ORDER BY sr.scheduled_datetime";

        $bookedTimes = $this->db->query($query, $params)->fetchAll();

        $workingHours = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00'];

        $availableTimes = [];
        $targetDate = $date ?? date('Y-m-d');
        $isToday = $targetDate === date('Y-m-d');
        $currentTime = $isToday ? date('H:i:s') : null;

        foreach ($workingHours as $time) {
            if ($isToday && $time <= $currentTime) continue;

            $isBooked = array_filter($bookedTimes, fn($slot) => $slot['scheduled_date'] === $targetDate &&
                $slot['scheduled_time'] === $time
            );

            if (!$isBooked) {
                $availableTimes[] = [
                    'date' => $targetDate,
                    'time' => $time
                ];
            }
        }

        return $availableTimes;
    }

    public function getByUser(int $userId): array
    {
        return $this->db->query(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY scheduled_datetime DESC",
            ['user_id' => $userId]
        )->fetchAll();
    }

    public function create(array $data): false|string
    {
        $vehicleId = $this->db->query(
            "SELECT id FROM vehicles WHERE brand = :brand AND model = :model",
            ['brand' => $data['vehicle_brand'], 'model' => $data['vehicle_model']]
        )->fetch()['id'] ?? null;

        if (!$vehicleId) {
            $vehicleId = $this->db->query(
                "INSERT INTO vehicles (brand, model) VALUES (:brand, :model)",
                ['brand' => $data['vehicle_brand'], 'model' => $data['vehicle_model']]
            );
            $vehicleId = $this->db->lastInsertId();
        }

        $customerVehicleId = $this->db->query(
            "SELECT id FROM customer_vehicles WHERE user_id = :user_id AND vehicle_id = :vehicle_id AND license_plate = :license_plate",
            ['user_id' => $data['user_id'], 'vehicle_id' => $vehicleId, 'license_plate' => $data['plate_number']]
        )->fetch()['id'] ?? null;

        if (!$customerVehicleId) {
            $this->db->query(
                "INSERT INTO customer_vehicles (user_id, vehicle_id, year, license_plate, vin_number, color) 
     VALUES (:user_id, :vehicle_id, :year, :license_plate, :vin_number, :color)",
                [
                    'user_id' => $data['user_id'],
                    'vehicle_id' => $vehicleId,
                    'year' => $data['vehicle_year'],
                    'license_plate' => $data['plate_number'],
                    'vin_number' => $data['vehicle_vin'] ?? null,
                    'color' => $data['vehicle_color'] ?? null,
                ]
            );
            $customerVehicleId = $this->db->lastInsertId();
        }

        $this->db->query(
            "INSERT INTO {$this->table} 
             (user_id, vehicle_id, service_id, scheduled_datetime, customer_notes, status) 
             VALUES (:user_id, :vehicle_id, :service_id, :scheduled_datetime, :notes, :status)",
            [
                'user_id' => $data['user_id'],
                'vehicle_id' => $customerVehicleId,
                'service_id' => $data['service_id'],
                'scheduled_datetime' => $data['scheduled_date'] . ' ' . $data['scheduled_time'],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ]
        );

        return $this->db->lastInsertId();
    }

    public function countAll(): int
    {
        return $this->db->query("SELECT COUNT(*) AS total FROM {$this->table}")
            ->fetch()['total'] ?? 0;
    }

    public function countByStatus(string $status): int
    {
        return $this->db->query(
            "SELECT COUNT(*) AS total FROM {$this->table} WHERE status = :status",
            ['status' => $status]
        )->fetch()['total'] ?? 0;
    }

    public function getAssignedToTechnician(int $technicianId): array
    {
        return $this->db->query(
            "SELECT sr.*, s.name AS service_name, u.name AS user_name 
             FROM {$this->table} sr
             JOIN services s ON sr.service_id = s.id
             JOIN users u ON sr.user_id = u.id
             WHERE sr.technician_id = :technician_id 
             AND sr.status IN ('pending', 'in_progress')
             ORDER BY sr.scheduled_datetime",
            ['technician_id' => $technicianId]
        )->fetchAll();
    }

    public function countByTechnicianAndStatus(int $technicianId, string $status): int
    {
        return $this->db->query(
            "SELECT COUNT(*) AS total 
             FROM {$this->table} 
             WHERE technician_id = :technician_id AND status = :status",
            ['technician_id' => $technicianId, 'status' => $status]
        )->fetch()['total'] ?? 0;
    }
}