<?php

namespace Database\Seeders;

use App\Models\BusClass;
use App\Models\SeatLayout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatLayoutSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SeatLayout::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $classes = BusClass::all()->pluck('id', 'name');

        // 1. DOUBLE DECKER (Screenshot 225211)
        SeatLayout::create([
            'name' => 'Double Decker (First Class & Super Top & Exec Plus)',
            'grid_rows' => 12, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => true,
                'decks' => [
                    ['name' => 'Lower Deck', 'rows' => 8, 'cols' => 5, 'mapping' => $this->generateDoubleDeckerLower($classes)],
                    ['name' => 'Upper Deck', 'rows' => 11, 'cols' => 5, 'mapping' => $this->generateDoubleDeckerUpper($classes)],
                ],
            ],
        ]);

        // 2. SUPER / EXEPLUS KOMBI 6+22 (Screenshot 225316)
        SeatLayout::create([
            'name' => 'Kombi (Super Top 1-2 & Executive Plus 2-2) 28 Seats',
            'grid_rows' => 10, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 10, 'cols' => 5, 'mapping' => $this->generateKombi622($classes)]],
            ],
        ]);

        // 3. SUPER TOP 21 SEATS - TOILET TENGAH (Screenshot 225443)
        SeatLayout::create([
            'name' => 'Super Top 21 Seats (1-2) - Toilet Tengah',
            'grid_rows' => 9, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 9, 'cols' => 5, 'mapping' => $this->generateSuperTop21MidToilet($classes)]],
            ],
        ]);

        // 4. SUPER TOP 21 SEATS - TOILET BELAKANG (Screenshot 225497)
        SeatLayout::create([
            'name' => 'Super Top 21 Seats (1-2) - Toilet Belakang',
            'grid_rows' => 10, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 10, 'cols' => 5, 'mapping' => $this->generateSuperTop21RearToilet($classes)]],
            ],
        ]);

        // 5. EXECUTIVE PLUS 28 SEATS (Screenshot 225527)
        SeatLayout::create([
            'name' => 'Executive Plus 28 Seats (2-2) - Toilet Belakang',
            'grid_rows' => 9, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 9, 'cols' => 5, 'mapping' => $this->generateExecPlus28RearToilet($classes)]],
            ],
        ]);

        // 6. EXECUTIVE PLUS 30 SEATS - TOILET TENGAH (Screenshot 225553)
        SeatLayout::create([
            'name' => 'Executive Plus 30 Seats (2-2) - Toilet Tengah',
            'grid_rows' => 10, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 10, 'cols' => 5, 'mapping' => $this->generateExecPlus30MidToilet($classes)]],
            ],
        ]);

        // 7. EXECUTIVE PLUS UHD 36 SEATS (Screenshot 225611)
        SeatLayout::create([
            'name' => 'Executive Plus UHD 36 Seats (2-2)',
            'grid_rows' => 12, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 12, 'cols' => 5, 'mapping' => $this->generateExecPlusUHD36($classes)]],
            ],
        ]);

        // 8. EXECUTIVE PLUS SHD 28 SEATS - TOILET TENGAH (Screenshot 225652)
        SeatLayout::create([
            'name' => 'Executive Plus SHD 28 Seats (2-2) - Toilet Tengah',
            'grid_rows' => 10, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 10, 'cols' => 5, 'mapping' => $this->generateExecPlusSHD28MidToilet($classes)]],
            ],
        ]);

        // 9. EXECUTIVE PLUS SHD 30 SEATS - TOILET BELAKANG (Screenshot 225748)
        SeatLayout::create([
            'name' => 'Executive Plus SHD 30 Seats (2-2) - Toilet Belakang',
            'grid_rows' => 10, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 10, 'cols' => 5, 'mapping' => $this->generateExecPlusSHD30RearToilet($classes)]],
            ],
        ]);

        // 10. TRAVEL HIACE 10 SEATS (Screenshot 225827)
        SeatLayout::create([
            'name' => 'Travel Hiace 11 Seats (1-2)',
            'grid_rows' => 5, 'grid_columns' => 4,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 5, 'cols' => 4, 'mapping' => $this->generateHiace11($classes)]],
            ],
        ]);
        
        // 11. FIRST CLASS SLEEPER 21 SEATS
        SeatLayout::create([
            'name' => 'First Class Sleeper 21 Seats (1-1-1)',
            'grid_rows' => 10, 'grid_columns' => 5,
            'layout_mapping' => [
                'is_double_decker' => false,
                'decks' => [['name' => 'Main Deck', 'rows' => 10, 'cols' => 5, 'mapping' => $this->generateSleeper21($classes)]],
            ],
        ]);
    }

    private function generateDoubleDeckerLower($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 4, 'type' => 'driver'];
        $m[] = ['row' => 2, 'col' => 1, 'type' => 'seat', 'seat_number' => '1A', 'bus_class_id' => $classes['First Class Sleeper'] ?? null];
        $m[] = ['row' => 2, 'col' => 5, 'type' => 'seat', 'seat_number' => '1B', 'bus_class_id' => $classes['First Class Sleeper'] ?? null];
        // Super Top Rows
        for ($r=4;$r<=5;$r++) {
            $sn = $r-3;
            $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Super Top'] ?? null];
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Super Top'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Super Top'] ?? null];
        }
        $m[] = ['row' => 6, 'col' => 1, 'type' => 'door'];
        $m[] = ['row' => 6, 'col' => 4, 'type' => 'toilet'];
        $m[] = ['row' => 7, 'col' => 1, 'type' => 'stairs_up', 'label' => 'UP'];
        return $m;
    }

    private function generateDoubleDeckerUpper($classes) {
        $m = [];
        for ($r=1;$r<=9;$r++) {
            $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $r.'A', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 2, 'type' => 'seat', 'seat_number' => $r.'B', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $r.'C', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $r.'D', 'bus_class_id' => $classes['Executive Plus'] ?? null];
        }
        $m[] = ['row' => 6, 'col' => 1, 'type' => 'stairs_down', 'label' => 'DOWN'];
        return $m;
    }

    private function generateKombi622($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 4, 'type' => 'driver'];
        $m[] = ['row' => 2, 'col' => 1, 'type' => 'seat', 'seat_number' => '1A', 'bus_class_id' => $classes['Super Top'] ?? null];
        $m[] = ['row' => 2, 'col' => 4, 'type' => 'seat', 'seat_number' => '1B', 'bus_class_id' => $classes['Super Top'] ?? null];
        $m[] = ['row' => 2, 'col' => 5, 'type' => 'seat', 'seat_number' => '1C', 'bus_class_id' => $classes['Super Top'] ?? null];
        $m[] = ['row' => 3, 'col' => 1, 'type' => 'seat', 'seat_number' => '2A', 'bus_class_id' => $classes['Super Top'] ?? null];
        $m[] = ['row' => 3, 'col' => 4, 'type' => 'seat', 'seat_number' => '2B', 'bus_class_id' => $classes['Super Top'] ?? null];
        $m[] = ['row' => 3, 'col' => 5, 'type' => 'seat', 'seat_number' => '2C', 'bus_class_id' => $classes['Super Top'] ?? null];
        $m[] = ['row' => 4, 'col' => 1, 'type' => 'toilet'];
        for ($r=4;$r<=9;$r++) {
            $sn = ($r == 4) ? 1 : $r - 3;
            if ($r > 4) {
                $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            }
            $m[] = ['row' => $r, 'col' => 2, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'D', 'bus_class_id' => $classes['Executive Plus'] ?? null];
        }
        return $m;
    }

    private function generateSuperTop21MidToilet($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 4, 'type' => 'driver'];
        for ($r=2;$r<=8;$r++) {
            $sn = $r-1;
            if ($r == 4) {
                $m[] = ['row' => $r, 'col' => 1, 'type' => 'toilet'];
            } else {
                $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Super Top'] ?? null];
            }
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Super Top'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Super Top'] ?? null];
        }
        return $m;
    }

    private function generateSuperTop21RearToilet($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 4, 'type' => 'driver'];
        for ($r=2;$r<=8;$r++) {
            $sn = $r-1;
            $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Super Top'] ?? null];
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Super Top'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Super Top'] ?? null];
        }
        $m[] = ['row' => 9, 'col' => 5, 'type' => 'toilet'];
        return $m;
    }

    private function generateExecPlus21RearToilet($classes) { return $this->generateSuperTop21RearToilet($classes); }

    private function generateExecPlus28RearToilet($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 4, 'type' => 'driver'];
        for ($r=2;$r<=8;$r++) {
            $sn = $r-1;
            $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 2, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'D', 'bus_class_id' => $classes['Executive Plus'] ?? null];
        }
        $m[] = ['row' => 9, 'col' => 1, 'type' => 'toilet'];
        return $m;
    }

    private function generateExecPlus30MidToilet($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 4, 'type' => 'driver'];
        for ($r=2;$r<=9;$r++) {
            $sn = $r-1;
            if ($r == 4) {
                $m[] = ['row' => $r, 'col' => 1, 'type' => 'toilet'];
            } else {
                $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Executive Plus'] ?? null];
                $m[] = ['row' => $r, 'col' => 2, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            }
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'D', 'bus_class_id' => $classes['Executive Plus'] ?? null];
        }
        return $m;
    }

    private function generateExecPlusUHD36($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 4, 'type' => 'driver'];
        for ($r=2;$r<=11;$r++) {
            $sn = $r-1;
            if ($r == 2) { $m[] = ['row' => $r, 'col' => 1, 'type' => 'stairs_up']; }
            elseif ($r == 6) { $m[] = ['row' => $r, 'col' => 1, 'type' => 'toilet']; }
            else {
                $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Executive Plus'] ?? null];
                $m[] = ['row' => $r, 'col' => 2, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            }
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'D', 'bus_class_id' => $classes['Executive Plus'] ?? null];
        }
        return $m;
    }

    private function generateExecPlusSHD28MidToilet($classes) { return $this->generateExecPlus30MidToilet($classes); }
    private function generateExecPlusSHD30RearToilet($classes) { return $this->generateExecPlus30MidToilet($classes); } // Generic for now

    private function generateHiace11($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 3, 'type' => 'driver'];
        $m[] = ['row' => 2, 'col' => 1, 'type' => 'seat', 'seat_number' => '1A', 'bus_class_id' => $classes['Executive Plus'] ?? null];
        for ($r=3;$r<=5;$r++) {
            $sn = $r-1;
            $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 3, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['Executive Plus'] ?? null];
            $m[] = ['row' => $r, 'col' => 4, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['Executive Plus'] ?? null];
        }
        return $m;
    }

    private function generateSleeper21($classes) {
        $m = [];
        $m[] = ['row' => 1, 'col' => 3, 'type' => 'driver'];
        for ($r=2;$r<=8;$r++) {
            $sn = $r-1;
            $m[] = ['row' => $r, 'col' => 1, 'type' => 'seat', 'seat_number' => $sn.'A', 'bus_class_id' => $classes['First Class Sleeper'] ?? null];
            $m[] = ['row' => $r, 'col' => 3, 'type' => 'seat', 'seat_number' => $sn.'B', 'bus_class_id' => $classes['First Class Sleeper'] ?? null];
            $m[] = ['row' => $r, 'col' => 5, 'type' => 'seat', 'seat_number' => $sn.'C', 'bus_class_id' => $classes['First Class Sleeper'] ?? null];
        }
        return $m;
    }
}
