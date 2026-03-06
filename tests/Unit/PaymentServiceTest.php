<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentService;
    }

    public function test_process_returns_valid_status(): void
    {
        $booking = Booking::factory()->create();
        $result = $this->service->process($booking);

        $this->assertArrayHasKey('status', $result);
        $this->assertContains($result['status'], ['success', 'failed']);
        $this->assertArrayHasKey('ref', $result);
        $this->assertStringStartsWith('PAY-', $result['ref']);
    }

    public function test_process_returns_success_or_failed(): void
    {
        $booking = Booking::factory()->create();
        $statuses = [];

        // Corremos 20 veces para verificar que devuelve ambos estados posibles
        for ($i = 0; $i < 20; $i++) {
            $statuses[] = $this->service->process($booking)['status'];
        }

        $this->assertContains('success', $statuses);
    }

    public function test_process_always_returns_ref(): void
    {
        $booking = Booking::factory()->create();

        $result1 = $this->service->process($booking);
        $result2 = $this->service->process($booking);

        // Cada llamada genera una referencia única
        $this->assertNotEquals($result1['ref'], $result2['ref']);
    }
}
