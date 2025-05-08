<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected $table = 'payments';


    public function getByRequestId(int $requestId): array|false
    {
        return $this->db->query(
            "SELECT * FROM payments WHERE request_id = :request_id",
            ['request_id' => $requestId]
        )->fetch();
    }

    public function getPaymentUsers(int $userId): array
    {
        return $this->db->query(
            "SELECT
                p.*,
                s.name AS service_name,
                u.name AS user_name,
                t.name AS technician_name
            FROM
                payments p
            JOIN
                service_requests sr ON p.service_request_id = sr.id
            JOIN
                services s ON sr.service_id = s.id
            JOIN
                users u ON sr.user_id = u.id
            LEFT JOIN
                technicians t ON sr.technician_id = t.user_id
            WHERE
                u.id = :user_id",
            ['user_id' => $userId]
        )->fetchAll();
    }

    public function getInvoiceDetails(int $paymentId): array|false
    {
        return $this->db->query(
            "SELECT
            p.*,
            sr.id AS request_id,
            sr.status AS request_status,
            sr.scheduled_datetime,
            sr.completion_datetime,
            sr.customer_notes,
            
            s.name AS service_name,
            s.description AS service_description,
            s.base_price AS service_price,
            s.estimated_hours AS service_duration,
            
            u.id AS user_id,
            u.name AS user_name,
            u.email AS user_email,
            u.phone_number AS user_phone,
            
            v.brand AS vehicle_brand,
            v.model AS vehicle_model,
            cv.year AS vehicle_year,
            cv.license_plate,
            
            tech.name AS technician_name
        FROM
            payments p
        JOIN
            service_requests sr ON p.request_id = sr.id
        JOIN
            services s ON sr.service_id = s.id
        JOIN
            users u ON sr.user_id = u.id
        JOIN
            customer_vehicles cv ON sr.vehicle_id = cv.id
        JOIN
            vehicles v ON cv.vehicle_id = v.id
        LEFT JOIN
            users tech ON sr.technician_id = tech.id
        WHERE
            p.id = :payment_id",
            ['payment_id' => $paymentId]
        )->fetch();
    }
}