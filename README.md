# Video Planner

A simple self-hosted app for capturing and organizing YouTube video ideas.

Built with PHP, SQLite, and W3.CSS. No frameworks.

## Setup

1. Place the project in `/home/evan/hub/video-planner/`
2. Create the data directory and give Apache write access:
   ```
   mkdir -p data
   sudo chown -R www-data:www-data data
   ```
3. Copy the config template and fill in your Ollama URL:
   ```
   cp config.example.php config.php
   ```
   Edit `config.php` and set your Ollama machine's hostname or IP.

4. Visit `http://localhost/video-planner/`

## AI Title Generation

Uses a local [Ollama](https://ollama.com) instance to suggest titles from your brain dump. Requires Ollama to be reachable on the network with `OLLAMA_ORIGINS=*` set.

## Roadmap

- [ ] Generate a title from the brain dump using local AI (Ollama) ✅
- [ ] Status tracking (idea → scripted → filmed → edited → published)
- [ ] Delete idea from detail page ✅
