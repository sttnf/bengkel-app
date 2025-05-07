<?php

namespace App\Models;

use App\Core\Model;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    public function getAvailableServiceTimes(int $serviceId, ?string $date = null, ?int $categoryId = null): array
    {
        $params = [
            'id' => $serviceId
        ];

        $query = "SELECT sr.scheduled_date, sr.scheduled_time, t.name AS technician_name
                               FROM service_requests sr
                               LEFT JOIN technicians t ON sr.id = t.id
                               JOIN services s ON sr.id = s.id
                               WHERE sr.status NOT IN ('cancelled', 'completed')";
        // Filter by service ID or category ID
        if ($categoryId !== null) {
            $query .= " AND s.id = :id";
            $params['id'] = $categoryId;
        } else {
            $query .= " AND sr.id = :id";
        }

        // Filter by date if provided
        if ($date !== null) {
            $query .= " AND sr.scheduled_date = :date";
            $params['date'] = $date;
        }

        $query .= " ORDER BY sr.scheduled_date, sr.scheduled_time";

        // Get all booked time slots
        $bookedTimes = $this->db->query($query, $params)->fetchAll();

        // Define standard working hours (8 AM to 5 PM, hourly slots)
        $workingHours = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '13:00:00',
            '14:00:00', '15:00:00', '16:00:00', '17:00:00'];

        // Generate available times by removing booked slots from working hours
        $availableTimes = [];
        $targetDate = $date ?? date('Y-m-d');
        $isToday = $targetDate === date('Y-m-d');
        $currentTime = $isToday ? date('H:i:s') : null;

        foreach ($workingHours as $time) {
            // Skip time slots that have already passed if the date is today
            if ($isToday && $time <= $currentTime) {
                continue;
            }

            $isBooked = false;
            foreach ($bookedTimes as $bookedSlot) {
                if ($bookedSlot['scheduled_date'] === $targetDate &&
                    $bookedSlot['scheduled_time'] === $time) {
                    $isBooked = true;
                    break;
                }
            }
            if (!$isBooked) {
                $availableTimes[] = [
                    'date' => $targetDate,
                    'time' => $time
                ];
            }
        }

        return $availableTimes;
    }

    public function getByUser(int $userId)
    {
        return $this->db->query("SELECT * FROM service_requests WHERE user_id = :user_id ORDER BY scheduled_date DESC", [
            'user_id' => $userId
        ])->fetchAll();
    }

    public function find(int $requestId)
    {
        return $this->db->query("SELECT * FROM service_requests WHERE request_id = :request_id", [
            'request_id' => $requestId
        ])->fetch();
    }

    public function updateStatus(int $requestId, string $status)
    {
        return $this->db->query("UPDATE service_requests SET status = :status WHERE request_id = :request_id", [
            'status' => $status,
            'request_id' => $requestId
        ]);
    }

    public function assignTechnician(int $requestId, int $technicianId)
    {
        return $this->db->query("UPDATE service_requests SET technician_id = :technician_id WHERE request_id = :request_id", [
            'technician_id' => $technicianId,
            'request_id' => $requestId
        ]);
    }

    public function completeRequest(int $requestId, float $actualPrice)
    {
        return $this->db->query("UPDATE service_requests SET status = 'completed', actual_price = :price, completion_date = NOW() WHERE request_id = :request_id", [
            'price' => $actualPrice,
            'request_id' => $requestId
        ]);
    }
}