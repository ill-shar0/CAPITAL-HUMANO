<?php
require_once __DIR__ . '/../config/db.php';

class AuditService
{
    public static function log(string $actorUserId, string $targetTipo, string $targetId, string $detalle): void
    {
        try {
            $db = get_db();
            $sql = 'INSERT INTO auditoria (aud_actor_user_id, aud_target_tipo, aud_target_id, aud_detalle, aud_fecha)
                    VALUES (:actor, :tipo, :target, :detalle, :fecha)';
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'actor' => $actorUserId,
                'tipo' => $targetTipo,
                'target' => $targetId,
                'detalle' => $detalle,
                'fecha' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            // Silenciar para no romper flujo; en producción se debería loguear.
        }
    }
}

