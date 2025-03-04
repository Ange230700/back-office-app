<?php

namespace Kouak\BackOfficeApp\Views\Components;

class HeadElement
{
    /**
     * Render the head elements.
     */
    public static function render()
    {
?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../styles/style.css">
<?php
    }
}
