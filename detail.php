<?php
include "config.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

$query = "SELECT * FROM visual_assets WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$asset = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Visual</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar">
        <h1>AI Visual Generator</h1>
        <a href="index.php" class="button">Kembali</a>
    </div>

    <div class="container">

        <div class="card">

            <h2><?php echo htmlspecialchars($asset['event_name']); ?></h2>

            <p class="muted">
                <?php echo htmlspecialchars($asset['visual_type']); ?> -
                <?php echo htmlspecialchars($asset['theme']); ?>
            </p>

            <span class="status <?php echo htmlspecialchars($asset['status']); ?>">
                <?php echo strtoupper(htmlspecialchars($asset['status'])); ?>
            </span>

            <br><br>

            <?php if ($asset['status'] == "failed") { ?>
                <div class="alert danger">
                    Generate gagal. Cek pesan error di bawah ini.
                </div>
            <?php } ?>

            <?php if (!empty($asset['image_path'])) { ?>
                <h3>Hasil Gambar</h3>
                <img class="result-image" src="<?php echo htmlspecialchars($asset['image_path']); ?>" alt="Generated Image">
            <?php } ?>

            <h3>Detail Input</h3>

            <div class="info-grid">
                <div class="info-box">
                    <strong>Unsur Budaya</strong>
                    <p><?php echo htmlspecialchars($asset['culture_element']); ?></p>
                </div>

                <div class="info-box">
                    <strong>Unsur Sains / Teknologi</strong>
                    <p><?php echo htmlspecialchars($asset['science_element']); ?></p>
                </div>

                <div class="info-box">
                    <strong>Warna Dominan</strong>
                    <p><?php echo htmlspecialchars($asset['main_color']); ?></p>
                </div>
            </div>

            <h3>Prompt yang Digunakan</h3>

            <div class="prompt-box">
                <?php echo htmlspecialchars($asset['prompt']); ?>
            </div>

            <?php if (!empty($asset['error_message'])) { ?>
                <h3>Error Message</h3>
                <div class="prompt-box error-box">
                    <?php echo htmlspecialchars($asset['error_message']); ?>
                </div>
            <?php } ?>

            <br>

            <a href="index.php" class="button button-secondary">Kembali ke Dashboard</a>

        </div>

    </div>

</body>
</html>