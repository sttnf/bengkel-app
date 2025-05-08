<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;
use App\Models\ServiceRequest;

class PaymentController extends Controller
{
    public function index(): ?string
    {
        $this->ensureLoggedIn();

        $serviceId = $_GET['id'] ?? null;
        if (!$serviceId) $this->redirect('/dashboard');

        $service = (new ServiceRequest())->getById($serviceId);
        if (!$service || $service['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/dashboard');
        }

        return $this->render('/dashboard/customer/payment', ['service' => $service]);
    }

    public function process()
    {
        $this->ensureLoggedIn();
        $this->ensurePostRequest();

        $serviceId = $_POST['service_id'] ?? null;
        $amount = $_POST['amount'] ?? 0;
        $paymentMethod = $_POST['payment_method'] ?? '';

        if (!$serviceId || !$amount || !$paymentMethod) {
            return $this->render('dashboard/customer/payment', [
                'error' => 'Semua bidang harus diisi',
                'service_id' => $serviceId
            ]);
        }

        $serviceModel = new ServiceRequest();
        $service = $serviceModel->findById($serviceId);
        if (!$service || $service['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/dashboard');
        }

        $paymentId = new Payment()->create([
            'request_id' => $serviceId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'completed'
        ]);

        $this->redirect('invoice?id=' . $paymentId);
    }

    public function invoice()
    {
        $this->ensureLoggedIn();

        $paymentId = $_GET['id'] ?? null;
        if (!$paymentId) $this->redirect('/dashboard');

        $payment = new Payment()->getInvoiceDetails($paymentId);

        if (!$payment || $payment['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/dashboard');
        }

        return $this->render('dashboard/customer/invoice', ['payment' => $payment]);
    }

    private function ensureLoggedIn()
    {
        if (empty($_SESSION['user_id'])) $this->redirect('/login');
    }

    private function ensurePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/login');
    }
}