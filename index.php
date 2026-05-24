<?php
include "config.php";

$query = "SELECT * FROM visual_assets ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Visual Generator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-box">
            <div class="loader"></div>
            <h2>Generating Visual...</h2>
            <p>AI sedang meracik prompt dan membuat aset visual kamu ✨</p>
        </div>
    </div>

    <div class="background-orb orb-one"></div>
    <div class="background-orb orb-two"></div>
    <div class="background-orb orb-three"></div>

    <div class="navbar">
        <div class="brand">
            <div class="brand-icon">AI</div>
            <div>
                <h1>AI Visual Generator</h1>
                <p>Creative Automation Platform</p>
            </div>
        </div>

        <a href="#form" class="button navbar-button">+ Generate Visual</a>
    </div>

    <div class="hero">
        <div class="hero-badge">
            Generative AI • Prompt Engineering • Event Branding
        </div>

        <h2>Otomatisasi Aset Visual dengan Generative AI</h2>

        <p>
            Buat logo, poster, banner, dan visual event secara otomatis
            dengan prompt yang dibentuk dari parameter pengguna.
        </p>

        <div class="hero-actions">
            <a href="#form" class="button">Mulai Generate</a>
            <a href="#gallery" class="button button-secondary">Lihat Gallery</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3>AI</h3>
                <p>Image Generator</p>
            </div>

            <div class="stat-card">
                <h3>Auto</h3>
                <p>Prompt Builder</p>
            </div>

            <div class="stat-card">
                <h3>Fast</h3>
                <p>Visual Output</p>
            </div>
        </div>
    </div>

    <div class="container">

        <div class="card form-card" id="form">
            <div class="section-header">
                <div>
                    <span class="mini-label">CREATE NEW ASSET</span>
                    <h2>Generate Visual Baru</h2>
                    <p class="muted">
                        Masukkan parameter visual. Sistem akan membuat prompt otomatis yang lebih detail
                        lalu menghasilkan aset visual menggunakan AI.
                    </p>
                </div>
            </div>

            <div class="preset-box">
                <div>
                    <h3>Quick Preset</h3>
                    <p class="muted">Klik salah satu ide cepat, atau pakai tombol random.</p>
                </div>

                <div class="preset-actions">
                    <button type="button" class="chip" onclick="setPreset('Techno Batik Festival', 'event poster', 'futuristic', 'Batik Jawa', 'Artificial Intelligence', 'blue neon')">
                        Techno Batik
                    </button>

                    <button type="button" class="chip" onclick="setPreset('Cosmic Nusantara Night', 'campaign banner', 'cyberpunk', 'Wayang', 'Astronomy', 'purple')">
                        Cosmic Nusantara
                    </button>

                    <button type="button" class="chip" onclick="setPreset('Bali Science Expo', 'event logo', 'elegant', 'Batik Bali', 'Robotics', 'gold')">
                        Bali Science
                    </button>

                    <button type="button" class="chip chip-random" onclick="randomPreset()">
                        Random Idea 🎲
                    </button>
                </div>
            </div>

            <form action="generate.php" method="POST" id="generateForm">

                <div class="grid">
                    <div class="form-group">
                        <label>Nama Event</label>
                        <input id="event_name" type="text" name="event_name" placeholder="Contoh: Science Nusantara Festival" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Visual</label>
                        <select id="visual_type" name="visual_type" required>
                            <option value="event logo">Event Logo</option>
                            <option value="event poster">Event Poster</option>
                            <option value="campaign banner">Campaign Banner</option>
                            <option value="studio portrait">Studio Portrait</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tema Desain</label>
                        <input id="theme" type="text" name="theme" placeholder="Contoh: futuristic, cyberpunk, elegant, comic" required>
                    </div>

                    <div class="form-group">
                        <label>Unsur Budaya Lokal</label>
                        <input id="culture_element" type="text" name="culture_element" placeholder="Contoh: batik jawa, wayang, prambanan" required>
                    </div>

                    <div class="form-group">
                        <label>Unsur Sains / Teknologi</label>
                        <input id="science_element" type="text" name="science_element" placeholder="Contoh: robotics, informatics, astronomy" required>
                    </div>

                    <div class="form-group">
                        <label>Warna Dominan</label>
                        <input id="main_color" type="text" name="main_color" placeholder="Contoh: sky blue, gold, purple" required>
                    </div>
                </div>

                <div class="form-footer">
                    <p>
                        Output akan disimpan otomatis ke database dan folder generated.
                    </p>

                    <div class="footer-actions">
                        <button type="button" class="button button-secondary" onclick="clearForm()">Clear</button>
                        <button type="submit">Generate Visual ✨</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="section-title" id="gallery">
            <div>
                <span class="mini-label">GALLERY</span>
                <h2>Generated Visual Assets</h2>
                <p class="muted">Daftar aset visual yang sudah dibuat oleh sistem.</p>
            </div>

            <div class="gallery-tools">
                <input type="text" id="searchInput" placeholder="Cari nama event atau tema..." onkeyup="filterGallery()">

                <select id="statusFilter" onchange="filterGallery()">
                    <option value="all">Semua Status</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
        </div>

        <div class="asset-grid" id="assetGrid">
            <?php if ($result && mysqli_num_rows($result) > 0) { ?>

                <?php while ($asset = mysqli_fetch_assoc($result)) { ?>

                    <div class="card asset-card"
                         data-title="<?php echo strtolower(htmlspecialchars($asset['event_name'])); ?>"
                         data-theme="<?php echo strtolower(htmlspecialchars($asset['theme'])); ?>"
                         data-status="<?php echo strtolower(htmlspecialchars($asset['status'])); ?>">

                        <div class="image-wrapper">
                            <?php if (!empty($asset['image_path'])) { ?>
                                <img src="<?php echo htmlspecialchars($asset['image_path']); ?>" alt="Generated Image">
                            <?php } else { ?>
                                <div class="empty-image">
                                    Belum ada gambar
                                </div>
                            <?php } ?>
                        </div>

                        <div class="asset-content">
                            <h3><?php echo htmlspecialchars($asset['event_name']); ?></h3>

                            <p class="muted">
                                <?php echo htmlspecialchars($asset['visual_type']); ?> -
                                <?php echo htmlspecialchars($asset['theme']); ?>
                            </p>

                            <div class="asset-bottom">
                                <span class="status <?php echo htmlspecialchars($asset['status']); ?>">
                                    <?php echo strtoupper(htmlspecialchars($asset['status'])); ?>
                                </span>

                                <a class="detail-link" href="detail.php?id=<?php echo $asset['id']; ?>">
                                    Detail →
                                </a>
                            </div>
                        </div>
                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="card empty-state">
                    <h3>Belum ada visual yang dibuat</h3>
                    <p class="muted">Coba generate visual pertama kamu dari form di atas.</p>
                </div>

            <?php } ?>
        </div>

    </div>

    <button class="back-to-top" onclick="scrollToTop()">↑</button>

    <script>
        const form = document.getElementById("generateForm");
        const loadingOverlay = document.getElementById("loadingOverlay");

        form.addEventListener("submit", function () {
            loadingOverlay.classList.add("active");
        });

        function setPreset(eventName, visualType, theme, culture, science, color) {
            document.getElementById("event_name").value = eventName;
            document.getElementById("visual_type").value = visualType;
            document.getElementById("theme").value = theme;
            document.getElementById("culture_element").value = culture;
            document.getElementById("science_element").value = science;
            document.getElementById("main_color").value = color;
        }

        function randomPreset() {
            const ideas = [
                ["Cyber Nusantara Expo", "event poster", "cyberpunk", "Batik Jawa", "Robotics", "blue neon"],
                ["Astro Culture Night", "campaign banner", "futuristic", "Wayang", "Astronomy", "purple"],
                ["Bali Tech Summit", "event logo", "elegant", "Batik Bali", "Artificial Intelligence", "gold"],
                ["Informatics Art Fest", "event poster", "comic", "Prambanan Temple", "Informatics", "sky blue"],
                ["Heritage Science Fair", "campaign banner", "minimalist", "Tenun Ikat", "Data Science", "silver"]
            ];

            const selected = ideas[Math.floor(Math.random() * ideas.length)];

            setPreset(
                selected[0],
                selected[1],
                selected[2],
                selected[3],
                selected[4],
                selected[5]
            );
        }

        function clearForm() {
            document.getElementById("generateForm").reset();
        }

        function filterGallery() {
            const searchValue = document.getElementById("searchInput").value.toLowerCase();
            const statusValue = document.getElementById("statusFilter").value;
            const cards = document.querySelectorAll(".asset-card");

            cards.forEach(function (card) {
                const title = card.getAttribute("data-title");
                const theme = card.getAttribute("data-theme");
                const status = card.getAttribute("data-status");

                const matchSearch = title.includes(searchValue) || theme.includes(searchValue);
                const matchStatus = statusValue === "all" || status === statusValue;

                if (matchSearch && matchStatus) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        }
    </script>

</body>
</html>