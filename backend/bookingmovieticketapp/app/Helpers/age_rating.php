<?php
if (!function_exists('getAgeRatingDescription')) {
    function getAgeRatingDescription($rating)
    {
        return match ($rating) {
            'K' => 'Phim dành cho mọi lứa tuổi',
            'T13' => 'Phim dành cho khán giả từ đủ 13 tuổi trở lên (13+)',
            'T16' => 'Phim dành cho khán giả từ đủ 16 tuổi trở lên (16+)',
            'C18' => 'Phim cấm khán giả dưới 18 tuổi',
            'ALL' => 'Chấp nhận mọi lứa tuổi',
            default => 'Chấp nhận mọi lứa tuổi',
        };
    }
}