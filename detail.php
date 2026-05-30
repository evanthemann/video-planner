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
    <style>
        body.dark { background-color: #1e1e1e !important; color: #e0e0e0 !important; }
        body.dark .w3-white { background-color: #2a2a2a !important; color: #e0e0e0 !important; }
        body.dark .w3-input, body.dark textarea.w3-input { background-color: #333 !important; color: #e0e0e0 !important; border-color: #555 !important; }
        body.dark .w3-hover-light-grey:hover { background-color: #3a3a3a !important; }
        body.dark .w3-text-grey { color: #aaa !important; }
        body.dark p { color: #e0e0e0; }
        body.dark a { color: #e0e0e0; }
        body.dark hr { border-color: #444; }
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
    <a href="index.php" class="w3-text-white" style="text-decoration:none">← Back</a>
    <button id="theme-btn" onclick="toggleDark()" class="w3-button w3-dark-grey w3-small">Dark mode</button>
</div>
<script>if (localStorage.getItem('darkMode') === '1') document.getElementById('theme-btn').textContent = 'Light mode';</script>

<div class="w3-container w3-padding">
    <div class="w3-card w3-white w3-padding">
        <form method="POST">
            <label class="w3-text-grey w3-small">Title</label>
            <input type="text" name="title" id="title-input" value="<?= htmlspecialchars($idea['title'] ?? '') ?>" class="w3-input w3-border w3-margin-bottom">
            <button type="button" id="gen-btn" class="w3-button w3-dark-grey w3-small w3-margin-bottom" onclick="generateTitle()">Generate Name with AI</button>
            <span id="gen-status" class="w3-small w3-text-grey w3-margin-left"></span>

            <label class="w3-text-grey w3-small">Brain Dump</label>
            <textarea name="brain_dump" class="w3-input w3-border w3-margin-bottom" rows="8"><?= htmlspecialchars($idea['brain_dump'] ?? '') ?></textarea>

            <button type="submit" name="save" class="w3-button w3-black">Save</button>
            <span id="save-status" class="w3-small w3-text-grey w3-margin-left"></span>
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
var saveTimer = null;
var ideaId = <?= $idea['id'] ?>;

function autosave() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(function() {
        var status = document.getElementById('save-status');
        status.textContent = 'Saving...';
        var form = new FormData();
        form.append('id', ideaId);
        form.append('title', document.getElementById('title-input').value);
        form.append('brain_dump', document.querySelector('[name="brain_dump"]').value);
        fetch('save.php', { method: 'POST', body: form })
            .then(function(r) { return r.json(); })
            .then(function() { status.textContent = 'Saved'; })
            .catch(function() { status.textContent = 'Save failed'; });
    }, 1500);
}

document.getElementById('title-input').addEventListener('input', autosave);
document.querySelector('[name="brain_dump"]').addEventListener('input', autosave);

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
            prompt: 'Give this note a descriptive title. Use words from the note where possible. You can use spaces. Do not combine words into one (no CamelCase or compound words). Avoid catchy or marketing language — just describe what the note is about. Keep it under 70 characters. Reply with only the title, nothing else:\n\n' + brainDump,
            stream: false
        })
    })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var title = data.response.trim();
            document.getElementById('title-input').value = title;
            var dump = document.querySelector('[name="brain_dump"]');
            dump.value = dump.value.trimEnd() + '\n\nAI Generated Title: ' + title;
            status.textContent = 'Done! Edit it if you like, then save.';
            autosave();
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
