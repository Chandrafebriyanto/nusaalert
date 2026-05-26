<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Bencana",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="event_id", type="string", example="bmkg-abc123"),
 *     @OA\Property(property="jenis_bencana", type="string", enum={"gempa","tsunami","banjir","cuaca_ekstrem","gunung_api","tanah_longsor"}),
 *     @OA\Property(property="magnitude", type="number", format="float", example=5.2, nullable=true),
 *     @OA\Property(property="kedalaman_km", type="number", format="float", example=10, nullable=true),
 *     @OA\Property(property="latitude", type="number", format="float", example=-6.2088),
 *     @OA\Property(property="longitude", type="number", format="float", example=106.8456),
 *     @OA\Property(property="wilayah", type="string", example="Selatan Bekasi, Jawa Barat"),
 *     @OA\Property(property="sumber_api", type="string", example="bmkg"),
 *     @OA\Property(property="terjadi_pada", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Lokasi",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="nama_lokasi", type="string", example="Rumah Utama"),
 *     @OA\Property(property="latitude", type="number", format="float", example=-6.2088),
 *     @OA\Property(property="longitude", type="number", format="float", example=106.8456),
 *     @OA\Property(property="radius_km", type="integer", example=50),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="Alert",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="bencana_id", type="integer"),
 *     @OA\Property(property="lokasi_id", type="integer"),
 *     @OA\Property(property="jarak_km", type="number", format="float", example=16.5),
 *     @OA\Property(property="status", type="string", enum={"sent","read","dismissed"}),
 *     @OA\Property(property="sent_at", type="string", format="date-time"),
 *     @OA\Property(property="read_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="Laporan",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="jenis_bencana", type="string", example="banjir"),
 *     @OA\Property(property="latitude", type="number", format="float"),
 *     @OA\Property(property="longitude", type="number", format="float"),
 *     @OA\Property(property="wilayah", type="string", nullable=true),
 *     @OA\Property(property="deskripsi", type="string"),
 *     @OA\Property(property="foto_url", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", enum={"pending","verified","rejected"})
 * )
 */
abstract class Controller
{
    //
}
