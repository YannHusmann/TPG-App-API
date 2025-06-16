<?php

namespace App\Enums;

enum ReportType: string
{
    case VITRE_CASSEE = 'vitre cassée';
    case GRAFFITI = 'graffiti';
    case ECLAIRAGE_DEFECTUEUX = 'éclairage défectueux';
    case MOBILIER_ENDOMMAGE = 'mobilier endommagé';
    case PANNE_AFFICHAGE = 'panne d’affichage';
    case DECHETS = 'déchets / saleté';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
