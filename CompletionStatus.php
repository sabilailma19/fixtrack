<?php

class CompletionStatus
{
    public static function labels()
    {
        return [
            'pending' => 'Menunggu konfirmasi pelapor',
            'confirmed' => 'Dikonfirmasi selesai',
            'needs_review' => 'Diminta perbaikan ulang'
        ];
    }

    public static function key($row)
    {
        $status = $row['status'] ?? '';
        $confirmation = $row['reporter_confirmation'] ?? null;

        if ($status === 'selesai') {
            if ($confirmation === 'confirmed') {
                return 'confirmed';
            }

            if ($confirmation === 'needs_review') {
                return 'needs_review';
            }

            return 'pending';
        }

        if (
            $status === 'diverifikasi' &&
            $confirmation === 'needs_review'
        ) {
            return 'needs_review';
        }

        return '';
    }

    public static function label($row)
    {
        return self::labels()[self::key($row)] ?? '';
    }

    public static function counts($rows)
    {
        $counts = [
            'pending' => 0,
            'confirmed' => 0,
            'needs_review' => 0
        ];

        foreach ($rows as $row) {
            $key = self::key($row);

            if (isset($counts[$key])) {
                $counts[$key]++;
            }
        }

        return $counts;
    }
}