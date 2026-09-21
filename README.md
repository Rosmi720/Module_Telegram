# XC_VM Telegram Bot Integration Module

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](module.json)
[![Platform](https://img.shields.io/badge/platform-XC__VM%20%3E%3D2.0-green.svg)](https://github.com/Rosmi720/XC_VM)
[![PHP](https://img.shields.io/badge/php-%3E%3D8.1-777bb4.svg)](https://www.php.net)
[![License](https://img.shields.io/badge/license-AGPL--3.0-orange.svg)](https://www.gnu.org/licenses/agpl-3.0.html)
[![Localization](https://img.shields.io/badge/localization-English%20%26%20Arabic-teal.svg)](lang/)

The official Telegram Bot management and automated broadcasting integration module for the **XC_VM IPTV Management Platform**.

---

## 📑 Table of Contents
- [📖 Feature Overview](#-feature-overview)
- [✨ Key Features](#-key-features)
- [🧙 4-Step Setup Wizard](#-4-step-setup-wizard)
- [🏷️ Template Engine & Dynamic Tags](#️-template-engine--dynamic-tags)
- [🏗️ Architecture & Component Design](#️-architecture--component-design)
- [🗄️ Database Schema](#️-database-schema)
- [🌐 Self-Contained Localization](#-self-contained-localization)
- [🚀 Installation & Setup Guide](#-installation--setup-guide)
- [📱 Telegram Channel & Bot Setup Walkthrough](#-telegram-channel--bot-setup-walkthrough)
- [📦 Packaging & Release Management](#-packaging--release-management)
- [📄 License](#-license)

---

## 📖 Feature Overview

The **XC_VM Telegram Bot Integration Module** connects your streaming platform directly to Telegram channels, supergroups, and subscriber chats. Whenever a movie (VOD) completes processing and verification, a TV series episode is imported, or a live broadcast stream is launched, the module automatically formats and dispatches a high-impact media announcement with movie posters, IMDb ratings, genre classifications, plot summaries, and direct-watch buttons—**with zero manual overhead**.

### Highlights
- **Interactive Smartphone Mockup**: Live real-time preview in the admin wizard displaying exactly how the broadcast message will appear on subscriber phones before saving.
- **Precision Scoping**: Filter broadcasting by media types and category sets (e.g., dedicated bots for 4K Movies, Kids, or Live Sports).
- **Event-Driven Performance**: Utilizes asynchronous event hooks (`MediaAnalyzedEvent`) instead of resource-heavy database polling.
- **Zero Core Modifications**: Self-contained architecture that requires no alterations to core XC_VM framework code.

---

## ✨ Key Features

### 1. 🤖 Multi-Bot Architecture
- Configure and operate multiple bots concurrently using individual tokens issued by `@BotFather`.
- Toggle any bot active or paused instantly using on-screen toggle switches without deleting settings.
- Trigger on-demand test broadcasts (`Broadcast Test`) at any time to verify poster delivery and text layout.

### 2. ⚡ Event-Driven Automation
- Listens directly to `MediaAnalyzedEvent` emitted when media files finish technical analysis via `VodCronJob`.
- Dispatches messages asynchronously to all qualifying bots with automated error capture and resilience.
- Zero server polling load.

### 3. 🎯 Intelligent Content & Category Filtering
- **Content Types**: Choose to broadcast Movies (VOD), TV Series Episodes, and/or Live Event streams per bot.
- **Category Scoping**:
  - Broadcast all categories without restriction.
  - Or restrict broadcasting to specific categories (e.g., Action Movies, VIP Sports).

### 4. 🖼️ Rich Media Formatting & Custom Action Buttons
- **Media Attachment Modes**:
  - **Poster Image**: Vertical high-definition movie or series cover art.
  - **Backdrop Banner**: Wide cinematic backdrop image.
  - **Text Only**: Clean, formatted text announcement without imagery for low-bandwidth scenarios.
- **Inline Action Buttons**: Add interactive buttons (e.g., `🎬 Watch Now`) with customizable URLs supporting dynamic variables such as `{stream_id}`.
- **Silent Notifications**: Deliver broadcasts without triggering notification rings or vibrations for late-night announcements.

### 5. 📊 KPI Analytics & Broadcast Logs
- **Top Metrics Cards**:
  - Total bots configured.
  - Active bots currently broadcasting.
  - Cumulative broadcast count.
  - Timestamp of the most recent broadcast.
- **Audit Logs**: Inspect the 50 most recent broadcasts with status indicators (`Sent` or `Failed`) and diagnostic error messages.

---

## 🧙 4-Step Setup Wizard

The admin interface offers an intuitive 4-step wizard designed for quick bot provisioning:

| Step | Phase | Key Functions |
| :--- | :--- | :--- |
| **Step 1** | **Bot Profile & Credentials** | Define the friendly bot name, active status, and enter the `@BotFather` token. A live **Verify Token** button connects to the Telegram Bot API to validate the token and reveal the bot's username (`@bot_username`). |
| **Step 2** | **Target Channel / Destination** | Specify the public `@username` or private numerical ID (`-100...`). The **Send Test Message** button dispatches a live test ping to verify administrative posting rights, alongside silent notification controls and a quick setup checklist. |
| **Step 3** | **Content Rules & Filters** | Select media triggers (Movies, TV Episodes, Live Streams) and set category boundaries (all categories vs. explicit selections with Select All / Deselect All tools). |
| **Step 4** | **Visual Styling & Live Mockup** | Select image attachment mode, configure interactive inline buttons, modify the caption template, and preview the live layout on an interactive smartphone simulator. |

---

## 🏷️ Template Engine & Dynamic Tags

The caption generator supports HTML formatting and interpolates the following tags:

| Tag | Description | Example Output |
| :--- | :--- | :--- |
| `{title}` | Title of the movie, episode, or channel | `Inception` |
| `{year}` | Release year | `2010` |
| `{rating}` | TMDB or IMDb rating out of 10 | `8.8` |
| `{genre}` | Associated genres | `Action, Sci-Fi` |
| `{duration}` | Formatted duration | `2h 28m` |
| `{quality}` | Resolution, video codec, and audio details | `FHD (1080p) \| AVC • AAC` |
| `{video}` | Video codec name | `H.264 / AVC` |
| `{audio}` | Audio codec name | `AAC 5.1` |
| `{category}` | Category name | `Top Rated Sci-Fi` |
| `{plot}` | Synopsis or overview | `A thief who steals corporate secrets...` |
| `{stream_id}` | Numeric stream identifier | `14092` |

---

## 🏗️ Architecture & Component Design

The module follows a decoupled design compatible with the **XC_VM** plugin and container ecosystem:

```text
Module_Telegram/
├── module.json                  # Module manifest and auto-updater definition
├── TelegramModule.php           # Module bootstrap, container bindings & route registration
├── TelegramTranslator.php       # Standalone localization engine with core fallback
├── TelegramBotService.php       # Bot CRUD, KPI aggregation & Telegram API communication
├── TelegramClient.php           # Secure cURL wrapper for Telegram HTTP Bot API
├── TelegramController.php       # Admin controller for page rendering & JSON AJAX actions
├── TelegramMessageFormatter.php # HTML caption builder, variable parser & inline buttons
├── TelegramNotifier.php         # Event subscriber handling automated broadcasts
├── database.sql                 # Primary SQL schema (telegram_bots & telegram_logs)
├── database_drop.sql            # Teardown SQL script executed on module uninstall
├── lang/                        # Bundled localization packs
│   ├── ar.ini                   # Complete Arabic dictionary (132 keys)
│   └── en.ini                   # Complete English dictionary (132 keys)
├── views/                       # Modern UI templates and interactive JavaScript
│   ├── telegram_bots.php        # Overview list view, KPI cards & log modal
│   ├── telegram_bots_scripts.php# Client-side handlers for search, toggle, and deletion
│   ├── telegram_bot.php         # 4-step wizard interface with smartphone mockup
│   └── telegram_bot_scripts.php # Wizard step logic, token testing & live preview updates
├── Makefile                     # Build automation script for producing release archives
└── README.md                    # Technical documentation and operations manual
```

---

## 🗄️ Database Schema

### 1. `telegram_bots` Table
Stores bot configurations, tokens, destinations, and filtering parameters:
- `id` (`INT AUTO_INCREMENT PRIMARY KEY`): Unique bot ID.
- `name` (`VARCHAR(100)`): User-friendly display name.
- `bot_token` (`VARCHAR(255)`): Secret Bot token from `@BotFather`.
- `bot_username` (`VARCHAR(100)`): Bot handle retrieved via `getMe`.
- `chat_id` (`VARCHAR(100)`): Target channel username or chat identifier.
- `status` (`TINYINT(1)`): Operational state (`1` = active, `0` = paused).
- `content_types` (`TEXT`): JSON array of permitted media types (`movies`, `episodes`, `live`).
- `categories` (`TEXT`): JSON array of allowed category IDs (empty for all).
- `image_type` (`ENUM('poster', 'backdrop', 'none')`): Visual attachment style.
- `custom_template` (`TEXT`): Custom HTML caption template.
- `include_button` (`TINYINT(1)`): Flag enabling the inline action button.
- `button_text` (`VARCHAR(100)`): Button label.
- `button_url` (`VARCHAR(255)`): Button target URL.
- `silent_notification` (`TINYINT(1)`): Send without notification sound.
- `total_sent` (`INT`): Total successful broadcasts sent by this bot.
- `last_sent_at` (`INT`): Unix timestamp of the last successful transmission.
- `last_error` (`VARCHAR(255)`): Details of the last reported broadcast error.

### 2. `telegram_logs` Table
Maintains an audit log of recent broadcast actions:
- `id` (`INT AUTO_INCREMENT PRIMARY KEY`): Log record ID.
- `bot_id` (`INT`): Associated bot ID.
- `stream_id` (`INT`): Associated media stream ID.
- `content_type` (`VARCHAR(50)`): Media classification (`movie`, `episode`, `live`).
- `title` (`VARCHAR(255)`): Media title sent.
- `chat_id` (`VARCHAR(100)`): Target destination.
- `status` (`ENUM('sent', 'failed')`): Outcome status.
- `details` (`TEXT`): Telegram API response or error trace.
- `created_at` (`TIMESTAMP`): Creation timestamp.

---

## 🌐 Self-Contained Localization

- Bundles complete language packs under `lang/` (`en.ini` and `ar.ini`) containing **132 localized keys**.
- The custom `TelegramTranslator` engine automatically synchronizes with the system language selected in XC_VM (`Translator::current()`).
- Fully isolated: requires no modifications to `Core/Localization/Translator.php`.

---

## 🚀 Installation & Setup Guide

### Method 1: Using the Pre-Packaged Archive
1. Place the release archive in `/home/xc_vm/modules_archives/telegram_3b6df.zip`.
2. Extract the archive into the modules folder:
   ```bash
   mkdir -p /home/xc_vm/Modules/telegram_3b6df
   unzip -q /home/xc_vm/modules_archives/telegram_3b6df.zip -d /home/xc_vm/Modules/telegram_3b6df/
   ```
3. Enable the module in `/home/xc_vm/config/modules.php`:
   ```php
   'telegram' => [
       'installed_version' => '1.0.0',
       'source' => 'local',
       'enabled' => true,
   ],
   ```
4. Execute database migrations:
   ```bash
   mysql -u root xc_vm < /home/xc_vm/Modules/telegram_3b6df/database.sql
   ```

### Method 2: Git Clone
```bash
cd /home/xc_vm/Modules
git clone https://github.com/Rosmi720/Module_Telegram.git telegram_3b6df
mysql -u root xc_vm < telegram_3b6df/database.sql
```

---

## 📱 Telegram Channel & Bot Setup Walkthrough

To connect a Telegram channel to your XC_VM platform:

1. **Create Bot & Generate Token**:
   - Start a conversation with `@BotFather` on Telegram.
   - Send the command `/newbot` and follow the prompts to choose a name and username ending in `bot` (e.g., `MyStreaming_bot`).
   - Copy the generated **HTTP API Token** (e.g., `7123456789:AAHk_...`).

2. **Prepare Target Channel**:
   - Open your target Telegram Channel or Supergroup.
   - Navigate to **Channel Settings ➜ Administrators**.
   - Click **Add Administrator**, search for your bot's username, and add it.
   - Grant the bot the **Post Messages** permission.

3. **Identify the Chat ID**:
   - **Public Channels**: Use the public handle prefixed with `@` (e.g., `@MyMoviesChannel`).
   - **Private Channels**: Use the numerical identifier starting with `-100` (e.g., `-1001928374650`). You can retrieve this ID by forwarding a message from the channel to `@userinfobot` or `@getmyid_bot`.

4. **Verify in the Admin Panel**:
   - Navigate to **XC_VM Admin ➜ Telegram Bots ➜ Add New Bot**.
   - Paste the token and click **Verify Token**.
   - Enter your channel ID and click **Send Test Message**.
   - Once the test message arrives in your channel, finish configuring Steps 3 and 4, then click **Save & Activate Bot**.

---

## 📦 Packaging & Release Management

To package the module into a standardized production zip archive:

```bash
cd /home/Module_Telegram
make zip
```

The output package will be generated at:
`/home/xc_vm/modules_archives/telegram_3b6df.zip`

---

## 📄 License

This module is licensed under the [GNU AGPLv3](https://www.gnu.org/licenses/agpl-3.0.html).  
Developed specifically for the **XC_VM IPTV Management Platform**.
