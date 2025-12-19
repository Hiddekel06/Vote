<?php

namespace App\Helpers;

/**
 * Helper pour les calculs géographiques
 */
class GeoHelper
{
    /**
     * Calcule la distance entre deux points GPS avec la formule de Haversine
     * @param float $lat1 Latitude point 1 (degrés décimaux)
     * @param float $lon1 Longitude point 1 (degrés décimaux)
     * @param float $lat2 Latitude point 2 (degrés décimaux)
     * @param float $lon2 Longitude point 2 (degrés décimaux)
     * @return float Distance en mètres
     */
    public static function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Rayon de la Terre en mètres

        // Convertir degrés en radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Différences
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        // Formule de Haversine
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance en mètres
    }

    /**
     * Vérifie si un point est dans un rayon autour d'une autre position
     * @param float $userLat Latitude utilisateur
     * @param float $userLon Longitude utilisateur
     * @param float $centerLat Latitude du centre
     * @param float $centerLon Longitude du centre
     * @param float $radiusMeters Rayon en mètres
     * @return bool True si dans le rayon
     */
    public static function isWithinRadius($userLat, $userLon, $centerLat, $centerLon, $radiusMeters): bool
    {
        $distance = self::haversineDistance($centerLat, $centerLon, $userLat, $userLon);
        return $distance <= $radiusMeters;
    }
}
