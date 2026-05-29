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
</head>
<body class="w3-light-grey">

<div class="w3-container w3-black w3-padding">
    <h2>Video Planner</h2>
</div>

<!-- Quick-add form -->
<div class="w3-container w3-padding">
    <div class="w3-card w3-white w3-padding">
        <form method="POST" action="add.php">
            <textarea name="brain_dump" class="w3-input w3-border w3-margin-bottom" rows="3" placeholder="What's the idea?"></textarea>
            <button type="submit" class="w3-button w3-black">Add Idea</button>
        </form>
    </div>
</div>

<!-- Ideas list -->
<div class="w3-container w3-padding">
    <?php if (empty($ideas)): ?>
        <p class="w3-text-grey">No ideas yet.</p>
    <?php else: ?>
        <?php foreach ($ideas as $row): ?>
            <?php
                $preview = $row['title'] ?: $row['brain_dump'];
                $preview = htmlspecialchars(substr($preview, 0, 120));
                if (strlen($row['title'] ?: $row['brain_dump']) > 120) $preview .= '...';
            ?>
            <a href="detail.php?id=<?= $row['id'] ?>" style="display:block; text-decoration:none; color:inherit">
                <div class="w3-card w3-white w3-padding w3-margin-bottom w3-hover-light-grey">
                    <p style="margin:0 0 4px"><?= $preview ?: '<em class="w3-text-grey">Empty idea</em>' ?></p>
                    <p class="w3-small w3-text-grey" style="margin:0"><?= $row['created_at'] ?></p>
                </div>
            </a>
        <?php endforeach ?>
    <?php endif ?>
</div>

</body>
</html>
