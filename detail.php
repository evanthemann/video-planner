<?php
require 'db.php';
require 'config.php';
$db = get_db();

$id = (int) ($_GET['id'] ?? 0);
$idea = $db->prepare("SELECT * FROM ideas WHERE id = ?");
$idea->execute([$id]);
$idea = $idea->fetch(PDO::FETCH_ASSOC);

if (!$idea) {
    header('Location: index.php');
    exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $stmt = $db->prepare("UPDATE ideas SET title=?, brain_dump=?, updated_at=datetime('now') WHERE id=?");
    $stmt->execute([
        trim($_POST['title']),
        trim($_POST['brain_dump']),
        $id,
    ]);
    header('Location: detail.php?id=' . $id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Idea</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-black w3-padding">
    <a href="index.php" class="w3-text-white" style="text-decoration:none">← Back</a>
</div>

<div class="w3-container w3-padding">
    <div class="w3-card w3-white w3-padding">
        <form method="POST">
            <label class="w3-text-grey w3-small">Title</label>
            <input type="text" name="title" id="title-input" value="<?= htmlspecialchars($idea['title'] ?? '') ?>" class="w3-input w3-border w3-margin-bottom">
            <button type="button" id="gen-btn" class="w3-button w3-dark-grey w3-small w3-margin-bottom" onclick="generateTitle()">Generate Title with AI</button>
            <span id="gen-status" class="w3-small w3-text-grey w3-margin-left"></span>

            <label class="w3-text-grey w3-small">Brain Dump</label>
            <textarea name="brain_dump" class="w3-input w3-border w3-margin-bottom" rows="8"><?= htmlspecialchars($idea['brain_dump'] ?? '') ?></textarea>

            <button type="submit" name="save" class="w3-button w3-black">Save</button>
        </form>

        <p class="w3-small w3-text-grey w3-margin-top">Created: <?= $idea['created_at'] ?> &nbsp;|&nbsp; Updated: <?= $idea['updated_at'] ?></p>

        <hr>
        <form method="POST" action="delete.php" onsubmit="return confirm('Delete this idea?')">
            <input type="hidden" name="id" value="<?= $idea['id'] ?>">
            <button type="submit" class="w3-button w3-red">Delete</button>
        </form>
    </div>
</div>

<script>
function generateTitle() {
    var brainDump = document.querySelector('[name="brain_dump"]').value.trim();
    if (!brainDump) {
        document.getElementById('gen-status').textContent = 'Add a brain dump first.';
        return;
    }
    var btn = document.getElementById('gen-btn');
    var status = document.getElementById('gen-status');
    btn.disabled = true;
    status.textContent = 'Thinking...';

    fetch('<?= OLLAMA_URL ?>/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            model: 'qwen2.5:7b-instruct-q4_K_M',
            prompt: 'Generate a short, catchy YouTube video title for the following idea. Reply with only the title, nothing else:\n\n' + brainDump,
            stream: false
        })
    })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('title-input').value = data.response.trim();
            status.textContent = 'Done! Edit it if you like, then save.';
        })
        .catch(function() {
            status.textContent = 'Could not reach Ollama. Are you on Tailscale?';
        })
        .finally(function() {
            btn.disabled = false;
        });
}
</script>
</body>
</html>
