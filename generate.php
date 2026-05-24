<?php
include "config.php";

function cleanInput($data)
{
    return htmlspecialchars(trim($data));
}

function getVisualTypeInstruction($visualType)
{
    $visualType = strtolower($visualType);

    if ($visualType == "event logo") {
        return "Create a distinctive professional event logo, centered composition, iconic symbol, strong branding identity, simple but memorable, clean vector-like look, polished emblem style.";
    }

    if ($visualType == "event poster") {
        return "Create a premium event poster with strong visual hierarchy, eye-catching centerpiece, modern layout, dramatic composition, visually rich and attractive like professional campus event promotion.";
    }

    if ($visualType == "campaign banner") {
        return "Create a wide campaign banner with clean layout, modern promotional style, visually striking composition, elegant spacing, suitable for official digital campaign branding.";
    }

    if ($visualType == "studio portrait") {
        return "Create a highly detailed studio portrait with professional lighting, realistic face and outfit styling, polished photography look, premium studio atmosphere.";
    }

    return "Create a professional visual design with premium composition and strong branding.";
}

function getThemeInstruction($theme)
{
    $themeLower = strtolower($theme);

    if (strpos($themeLower, "futuristic") !== false) {
        return "Use futuristic visual language, sleek elements, advanced atmosphere, high-tech details, refined sci-fi mood.";
    }

    if (strpos($themeLower, "cyberpunk") !== false) {
        return "Use cyberpunk aesthetics, neon lighting, bold contrast, glowing futuristic ambiance, stylish urban sci-fi mood.";
    }

    if (strpos($themeLower, "elegant") !== false) {
        return "Use elegant composition, premium aesthetic, refined details, classy mood, luxurious presentation.";
    }

    if (strpos($themeLower, "comic") !== false) {
        return "Use comic-inspired visuals, expressive shapes, dynamic composition, stylized illustration, vibrant and playful artistic mood.";
    }

    if (strpos($themeLower, "minimalist") !== false) {
        return "Use minimalist design, clean layout, simple forms, refined spacing, modern and neat presentation.";
    }

    if (strpos($themeLower, "music") !== false) {
        return "Use artistic and musical visual mood, expressive design, energetic atmosphere, stylish performance-inspired composition.";
    }

    return "Use a visually attractive, polished, and professional design style.";
}

function getNegativePrompt()
{
    return "no watermark, no blurry image, no distorted objects, no ugly layout, no extra fingers, no broken anatomy, no random text, no low quality, no duplicate elements, no messy composition";
}

function buildPrompt($eventName, $visualType, $theme, $cultureElement, $scienceElement, $mainColor)
{
    $visualInstruction = getVisualTypeInstruction($visualType);
    $themeInstruction = getThemeInstruction($theme);
    $negativePrompt = getNegativePrompt();

    $prompt = "A premium professional " . $visualType . " for a campus event titled '" . $eventName . "'. ";
    $prompt .= $visualInstruction . " ";
    $prompt .= "Blend Indonesian cultural identity using " . $cultureElement . " with modern science and technology concept: " . $scienceElement . ". ";
    $prompt .= $themeInstruction . " ";
    $prompt .= "Main color palette is " . $mainColor . ". ";
    $prompt .= "Make it visually stunning, polished, high-end, modern, aesthetic, detailed, cinematic, sharp, professional, premium branding quality, strong composition, clean visual hierarchy, 4k look. ";
    $prompt .= "Suitable for official campus event promotion. ";
    $prompt .= $negativePrompt . ".";

    return $prompt;
}

function getImageSizeByVisualType($visualType)
{
    $visualType = strtolower($visualType);

    if ($visualType == "event poster") {
        return ["width" => 1024, "height" => 1536];
    }

    if ($visualType == "campaign banner") {
        return ["width" => 1536, "height" => 1024];
    }

    if ($visualType == "studio portrait") {
        return ["width" => 1024, "height" => 1536];
    }

    return ["width" => 1024, "height" => 1024];
}

function generateImageFromAI($prompt, $visualType)
{
    if (!function_exists("curl_init")) {
        throw new Exception("cURL belum aktif. Aktifkan extension=curl di php.ini lalu restart Apache.");
    }

    if (!is_dir("generated")) {
        mkdir("generated", 0777, true);
    }

    $size = getImageSizeByVisualType($visualType);
    $width = $size["width"];
    $height = $size["height"];
    $seed = rand(1000, 999999);

    $encodedPrompt = urlencode($prompt);

    $imageUrl = "https://image.pollinations.ai/prompt/" . $encodedPrompt;
    $imageUrl .= "?width=" . $width;
    $imageUrl .= "&height=" . $height;
    $imageUrl .= "&model=flux";
    $imageUrl .= "&seed=" . $seed;
    $imageUrl .= "&nologo=true";
    $imageUrl .= "&enhance=true";
    $imageUrl .= "&private=true";
    $imageUrl .= "&safe=true";

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $imageUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 180
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: " . $errorMessage);
    }

    curl_close($ch);

    if ($httpCode != 200) {
        throw new Exception("Gagal mengambil gambar dari Pollinations AI. HTTP Code: " . $httpCode);
    }

    if ($contentType && strpos($contentType, "image") === false) {
        throw new Exception("Response Pollinations bukan gambar. Content-Type: " . $contentType);
    }

    $fileName = "generated/visual_" . time() . "_" . rand(100, 999) . ".jpg";
    file_put_contents($fileName, $response);

    return $fileName;
}

function saveToDatabase(
    $conn,
    $eventName,
    $visualType,
    $theme,
    $cultureElement,
    $scienceElement,
    $mainColor,
    $prompt,
    $imagePath,
    $status,
    $errorMessage
) {
    $eventNameSql = mysqli_real_escape_string($conn, $eventName);
    $visualTypeSql = mysqli_real_escape_string($conn, $visualType);
    $themeSql = mysqli_real_escape_string($conn, $theme);
    $cultureElementSql = mysqli_real_escape_string($conn, $cultureElement);
    $scienceElementSql = mysqli_real_escape_string($conn, $scienceElement);
    $mainColorSql = mysqli_real_escape_string($conn, $mainColor);
    $promptSql = mysqli_real_escape_string($conn, $prompt);
    $imagePathSql = mysqli_real_escape_string($conn, $imagePath);
    $statusSql = mysqli_real_escape_string($conn, $status);
    $errorMessageSql = mysqli_real_escape_string($conn, $errorMessage);

    $query = "INSERT INTO visual_assets
        (event_name, visual_type, theme, culture_element, science_element, main_color, prompt, image_path, status, error_message)
        VALUES
        (
            '$eventNameSql',
            '$visualTypeSql',
            '$themeSql',
            '$cultureElementSql',
            '$scienceElementSql',
            '$mainColorSql',
            '$promptSql',
            '$imagePathSql',
            '$statusSql',
            '$errorMessageSql'
        )";

    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }

    throw new Exception("Gagal menyimpan data: " . mysqli_error($conn));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $eventName = cleanInput($_POST["event_name"]);
    $visualType = cleanInput($_POST["visual_type"]);
    $theme = cleanInput($_POST["theme"]);
    $cultureElement = cleanInput($_POST["culture_element"]);
    $scienceElement = cleanInput($_POST["science_element"]);
    $mainColor = cleanInput($_POST["main_color"]);

    $prompt = buildPrompt(
        $eventName,
        $visualType,
        $theme,
        $cultureElement,
        $scienceElement,
        $mainColor
    );

    $imagePath = "";
    $status = "success";
    $errorMessage = "";

    try {
        $imagePath = generateImageFromAI($prompt, $visualType);
        $status = "success";

        $lastId = saveToDatabase(
            $conn,
            $eventName,
            $visualType,
            $theme,
            $cultureElement,
            $scienceElement,
            $mainColor,
            $prompt,
            $imagePath,
            $status,
            $errorMessage
        );

        header("Location: detail.php?id=" . $lastId);
        exit;

    } catch (Exception $error) {
        $status = "failed";
        $errorMessage = $error->getMessage();

        try {
            $lastId = saveToDatabase(
                $conn,
                $eventName,
                $visualType,
                $theme,
                $cultureElement,
                $scienceElement,
                $mainColor,
                $prompt,
                $imagePath,
                $status,
                $errorMessage
            );

            header("Location: detail.php?id=" . $lastId);
            exit;

        } catch (Exception $databaseError) {
            echo "<h2>Terjadi Error</h2>";
            echo "<p>" . htmlspecialchars($databaseError->getMessage()) . "</p>";
            echo "<a href='index.php'>Kembali</a>";
        }
    }

} else {
    header("Location: index.php");
    exit;
}
?>