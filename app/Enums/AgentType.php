<?php

namespace App\Enums;

enum AgentType: string
{
    case BRANCH_OFFICE = 'branch_office';
    case PARTNER_EXCLUSIVE = 'partner_exclusive';
    case PARTNER_GENERAL = 'partner_general';

    public function label(): string
    {
        return match($this) {
            self::BRANCH_OFFICE => 'Kantor Cabang Internal',
            self::PARTNER_EXCLUSIVE => 'Mitra Agen Eksklusif',
            self::PARTNER_GENERAL => 'Mitra Agen Reguler',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::BRANCH_OFFICE => 'Kantor resmi milik perusahaan untuk operasional pusat/cabang.',
            self::PARTNER_EXCLUSIVE => 'Mitra agen pihak ketiga dengan hak penjualan eksklusif.',
            self::PARTNER_GENERAL => 'Mitra agen umum/reguler dengan komisi standar.',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::BRANCH_OFFICE => 'heroicon-o-building-office-2',
            self::PARTNER_EXCLUSIVE => 'heroicon-o-star',
            self::PARTNER_GENERAL => 'heroicon-o-user-group',
        };
    }
}
