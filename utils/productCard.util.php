<?php
function getProductCardData($title, $price, $imgSrc, $isNew = false) {
    return [
        'title'   => $title,
        'price'   => $price,
        'imgSrc'  => $imgSrc,
        'isNew'   => $isNew,
        'safeTitle' => htmlspecialchars($title),
        'formattedPrice' => number_format($price, 2)
    ];
}