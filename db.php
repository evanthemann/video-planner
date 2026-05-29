<?php

function get_db(): PDO {
    $path = __DIR__ . '/data/planner.db';
    $db = new PDO('sqlite:' . $path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS ideas (
        id               INTEGER PRIMARY KEY AUTOINCREMENT,
        title            TEXT,
        channel          TEXT NOT NULL DEFAULT 'unassigned',
        status           TEXT NOT NULL DEFAULT 'idea',
        brain_dump       TEXT,
        tags             TEXT,
        reference_urls   TEXT,
        estimated_length TEXT,
        created_at       TEXT NOT NULL DEFAULT (datetime('now')),
        updated_at       TEXT NOT NULL DEFAULT (datetime('now'))
    )");

    return $db;
}
