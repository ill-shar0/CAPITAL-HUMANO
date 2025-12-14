<?php
require_once BASE_PATH . '/config/db.php';

class Resuelto
{
    public static function create(string $colabId, string $diasVacaciones, string $inicio, string $fin, string $pdfPath = ''): ?string
    {
        try {
            $db = get_db();
            $sql = 'INSERT INTO resueltos (resu_colab_id, resu_dias_vacaciones, resu_periodo_inicio, resu_periodo_fin, resu_pdf_path, resu_fecha_creacion, resu_ultima_actualizacion)
                    VALUES (:colab, :dias, :inicio, :fin, :pdf, :fecha, :fecha)';
            $stmt = $db->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                'colab' => $colabId,
                'dias' => $diasVacaciones,
                'inicio' => $inicio,
                'fin' => $fin,
                'pdf' => $pdfPath,
                'fecha' => $now,
            ]);
            return $db->lastInsertId();
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function deleteResuelto(string $resueltoId): bool
    {
        try {
            $db = get_db();
            $stmt = $db->prepare('DELETE FROM resueltos WHERE resuelto_id = :id');
            return $stmt->execute(['id' => $resueltoId]);
        } catch (Throwable $e) {
            return false;
        }
    }
}

