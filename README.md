# XC_VM Telegram Bot Integration Module
# إضافة بوتات تيليجرام للبث التلقائي لمنصة XC_VM

Official Telegram Bot management and automated broadcasting module for the **XC_VM IPTV Management Platform**.

---

## 🌟 Features / المميزات

- 🤖 **Multi-Bot Management**: Add and manage multiple Telegram bots simultaneously (via `@BotFather` tokens).
- 📢 **Channel & Group Broadcasting**: Automatically broadcast to public channels (`@channel`), private channels, and supergroups (`-100...`).
- 🎬 **Automated VOD Notifications**: Instant notifications triggered upon VOD movie and TV episode download completion.
- 🖼️ **Rich Media Preview**: Attach high-resolution movie/series posters or backdrops, IMDb ratings, plot summaries, and custom direct-watch buttons.
- ⚡ **Decoupled Architecture**: Built natively for XC_VM modular system, utilizing event-driven architecture (`MediaAnalyzedEvent`).
- 🔄 **Real-Time Testing**: Live connection and token validator directly from the admin wizard interface.

---

## 📂 Module Structure / هيكلية الإضافة

```text
Module_Telegram/
├── module.json                  # Module manifest and auto-update configuration
├── TelegramModule.php           # Core Module entry point, routes & DI container boot
├── TelegramBotService.php       # Bot CRUD, KPI statistics, and Telegram API logic
├── TelegramClient.php           # HTTP cURL wrapper for Telegram Bot API
├── TelegramController.php       # Admin web views and JSON AJAX endpoints
├── TelegramMessageFormatter.php # HTML caption generator (Title, Rating, Plot, Buttons)
├── TelegramNotifier.php         # Event listener handler on VOD completion
├── database.sql                 # Primary database schema
├── database_drop.sql            # Teardown / cleanup on module uninstall
└── views/                       # Modern administrative UI views & scripts
    ├── telegram_bot.php         # 4-step wizard interface
    ├── telegram_bot_scripts.php
    ├── telegram_bots.php        # Bot list & KPI cards
    └── telegram_bots_scripts.php
```

---

## 🚀 Installation & Deployment / التثبيت والتطبيق

### Manual Installation:
1. Place the archive in `modules_archives/telegram_3b6df.zip`.
2. Extract to `Modules/telegram_3b6df/`.
3. Enable in `config/modules.php`:
   ```php
   'telegram' => [
       'installed_version' => '1.0.0',
       'source' => 'local',
   ],
   ```
4. Run migrations:
   ```bash
   php console.php status 1
   ```

---

## 🛠️ Developer & Release Management

To package the module into a production archive:
```bash
make zip
```
Or use the global CLI tool:
```bash
xc-module-telegram package
```

---
*Developed for XC_VM IPTV Platform.*
