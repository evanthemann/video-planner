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

## Features

- **Quick-add** — paste a brain dump from the home page to capture ideas fast
- **Responsive card grid** — ideas display as tiles, reflowing from 1 column on mobile to multi-column on desktop
- **Detail editor** — edit title and brain dump with autosave (1.5s debounce)
- **AI name generation** — generates a descriptive title from your brain dump via a local [Ollama](https://ollama.com) instance (requires Ollama reachable on the network with `OLLAMA_ORIGINS=*`)
- **Dark mode** — toggle in the header; preference persists across sessions

## Roadmap

- [ ] Status tracking (idea → scripted → filmed → edited → published)
- [ ] Channel tagging (Edit with Evan / Evan Mann)
- [ ] Tags and reference URLs
