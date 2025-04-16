<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SaleChart extends LineChartWidget
{
    protected static ?string $heading = 'Tren Penjualan Tahunan & Bulanan';

    protected int | string | array $columnSpan = 'full';

    protected function getFilters(): ?array
    {
        return [
            'yearly' => 'Tahunan',
            'monthly' => 'Bulanan',
            'daily' => 'Harian',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        if ($filter === 'yearly') {
            $data = Trend::model(Sale::class)
                ->between(now()->startOfYear(), now()->endOfYear())
                ->perMonth()
                ->count();
        } elseif ($filter === 'monthly') {
            $data = Trend::model(Sale::class)
                ->between(now()->startOfMonth(), now()->endOfMonth())
                ->perDay()
                ->count();
        } else {
            $data = Trend::model(Sale::class)
                ->between(now()->subDays(7), now())
                ->perDay()
                ->count();
        }

        // Konversi data ke array
        $labels = $data->map(fn ($value) => $value->date)->toArray();
        $values = $data->map(fn ($value) => $value->aggregate)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan',
                    'data' => $values,
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => '#36A2EB',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    public function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeInOutQuad',
            ],
            'elements' => [
                'line' => [
                    'tension' => 0.4, // Kurva halus
                ],
                'point' => [
                    'radius' => 5,
                    'hoverRadius' => 7,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
