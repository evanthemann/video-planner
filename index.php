<?php
require 'db.php';
$db = get_db();
$ideas = $db->query("SELECT * FROM ideas ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Video Planner</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body.dark { background-color: #1e1e1e !important; color: #e0e0e0 !important; }
        body.dark .w3-white { background-color: #2a2a2a !important; color: #e0e0e0 !important; }
        body.dark .w3-input, body.dark textarea.w3-input { background-color: #333 !important; color: #e0e0e0 !important; border-color: #555 !important; }
        body.dark .w3-hover-light-grey:hover { background-color: #3a3a3a !important; }
        body.dark .w3-text-grey { color: #aaa !important; }
        body.dark p { color: #e0e0e0; }
        body.dark a { color: #e0e0e0; }
    </style>
    <script>if (localStorage.getItem('darkMode') === '1') document.documentElement.classList.add('preload-dark');</script>
    <style>.preload-dark body { background-color: #1e1e1e !important; }</style>
</head>
<body class="w3-light-grey">
<script>
    if (localStorage.getItem('darkMode') === '1') document.body.classList.add('dark');
    function toggleDark() {
        var dark = document.body.classList.toggle('dark');
        localStorage.setItem('darkMode', dark ? '1' : '0');
        document.getElementById('theme-btn').textContent = dark ? 'Light mode' : 'Dark mode';
    }
</script>

<div class="w3-container w3-black w3-padding" style="display:flex; align-items:center; justify-content:space-between">
    <h2 style="margin:0">Video Planner</h2>
    <button id="theme-btn" onclick="toggleDark()" class="w3-button w3-dark-grey w3-small">Dark mode</button>
</div>
<script>if (localStorage.getItem('darkMode') === '1') document.getElementById('theme-btn').textContent = 'Light mode';</script>

<!-- Quick-add form -->
<div class="w3-container w3-padding">
    <div class="w3-card w3-white w3-padding">
        <form method="POST" action="add.php">
            <textarea name="brain_dump" class="w3-input w3-border w3-margin-bottom" rows="3" placeholder="What's the idea?"></textarea>
            <button type="submit" class="w3-button w3-black">Add Idea</button>
        </form>
    </div>
</div>

<!-- Ideas grid -->
<div class="w3-container w3-padding">
    <?php if (empty($ideas)): ?>
        <p class="w3-text-grey">No ideas yet.</p>
    <?php else: ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
            <?php foreach ($ideas as $row): ?>
                <?php
                    $title   = $row['title'] ? htmlspecialchars($row['title']) : '';
                    $dump    = $row['brain_dump'] ?? '';
                    $preview = htmlspecialchars(substr($dump, 0, 120));
                    if (strlen($dump) > 120) $preview .= '...';
                ?>
                <a href="detail.php?id=<?= $row['id'] ?>" style="text-decoration:none; color:inherit">
                    <div class="w3-card w3-white w3-padding w3-hover-light-grey" style="height:100%; box-sizing:border-box;">
                        <?php if ($title): ?>
                            <p style="margin:0 0 6px; font-weight:600"><?= $title ?></p>
                            <?php if ($preview): ?>
                                <p class="w3-small w3-text-grey" style="margin:0 0 8px"><?= $preview ?></p>
                            <?php endif ?>
                        <?php else: ?>
                            <p style="margin:0 0 8px"><?= $preview ?: '<em class="w3-text-grey">Empty idea</em>' ?></p>
                        <?php endif ?>
                        <p class="w3-small w3-text-grey" style="margin:0"><?= $row['created_at'] ?></p>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>

</body>
</html>
