<?php

/**
 * Telegram Bot Multi-step Wizard View (Admin).
 *
 * Provides a 4-step wizard for configuring a Telegram bot:
 * 1. Bot Credentials & Token Verification
 * 2. Target Channel / Chat ID & Permission Test
 * 3. Content Rules & Category Scoping
 * 4. Visual Formatting, Media Type & Live Telegram Mockup Preview
 */

$language = (!empty($language) && class_exists($language)) ? $language : \XcVm\Module\Telegram\TelegramTranslator::class;
$rBot = $bot ?? null;
$rIsEdit = !empty($isEdit);
$rCategories = $categories ?? ['movies' => [], 'series' => [], 'live' => []];

$botId = $rBot['id'] ?? 0;
$name = $rBot['name'] ?? '';
$token = $rBot['bot_token'] ?? '';
$username = $rBot['bot_username'] ?? '';
$chatId = $rBot['chat_id'] ?? '';
$types = $rBot['content_types_array'] ?? ['movies'];
$cats = $rBot['categories_array'] ?? [];
$imageType = $rBot['image_type'] ?? 'poster';
$notifyMovie = isset($rBot['notify_on_movie_complete']) ? (int)$rBot['notify_on_movie_complete'] : 1;
$notifyEpisode = isset($rBot['notify_on_episode']) ? (int)$rBot['notify_on_episode'] : 0;
$notifyLive = isset($rBot['notify_on_live']) ? (int)$rBot['notify_on_live'] : 0;
$customTemplate = $rBot['custom_template'] ?? '';
$silent = !empty($rBot['silent_notification']);
$includeButton = !empty($rBot['include_button']);
$buttonText = $rBot['button_text'] ?? '🎬 Watch Now';
$buttonUrl = $rBot['button_url'] ?? '';
$status = isset($rBot['status']) ? (int)$rBot['status'] : 1;
$hasCustomCats = !empty($cats);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-brand-telegram text-primary fs-2"></i>
                <span><?= $rIsEdit ? $language::get('edit_telegram_bot') . ': ' . htmlspecialchars($name) : $language::get('add_new_telegram_bot'); ?></span>
            </h4>
            <p class="text-muted mb-0"><?= $language::get('telegram_bot_wizard_desc'); ?></p>
        </div>
        <a href="telegram_bots" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
            <i class="icon-base ti tabler-arrow-left"></i>
            <span><?= $language::get('back_to_bots'); ?></span>
        </a>
    </div>

    <!-- Wizard Stepper Navigation -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="stepper-nav d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="step-indicator active d-flex align-items-center gap-2" data-step="1">
                    <span class="step-number badge rounded-pill bg-primary fs-7">1</span>
                    <div class="step-label">
                        <span class="fw-bold d-block fs-7"><?= $language::get('bot_profile'); ?></span>
                        <span class="text-muted fs-8"><?= $language::get('credentials_and_token'); ?></span>
                    </div>
                </div>
                <div class="step-separator d-none d-md-block flex-grow-1 mx-3 border-top"></div>

                <div class="step-indicator d-flex align-items-center gap-2 text-muted" data-step="2">
                    <span class="step-number badge rounded-pill bg-label-secondary fs-7">2</span>
                    <div class="step-label">
                        <span class="fw-bold d-block fs-7"><?= $language::get('destination'); ?></span>
                        <span class="text-muted fs-8"><?= $language::get('target_channel_chat'); ?></span>
                    </div>
                </div>
                <div class="step-separator d-none d-md-block flex-grow-1 mx-3 border-top"></div>

                <div class="step-indicator d-flex align-items-center gap-2 text-muted" data-step="3">
                    <span class="step-number badge rounded-pill bg-label-secondary fs-7">3</span>
                    <div class="step-label">
                        <span class="fw-bold d-block fs-7"><?= $language::get('content_and_rules'); ?></span>
                        <span class="text-muted fs-8"><?= $language::get('content_rules_desc'); ?></span>
                    </div>
                </div>
                <div class="step-separator d-none d-md-block flex-grow-1 mx-3 border-top"></div>

                <div class="step-indicator d-flex align-items-center gap-2 text-muted" data-step="4">
                    <span class="step-number badge rounded-pill bg-label-secondary fs-7">4</span>
                    <div class="step-label">
                        <span class="fw-bold d-block fs-7"><?= $language::get('visual_preview'); ?></span>
                        <span class="text-muted fs-8"><?= $language::get('visual_preview_desc'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wizard Form -->
    <form id="botWizardForm">
        <input type="hidden" name="id" value="<?= (int)$botId; ?>">

        <!-- STEP 1: Bot Credentials -->
        <div class="wizard-step-pane" id="stepPane1">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="icon-base ti tabler-robot text-primary"></i>
                        <span><?= $language::get('step_1_title'); ?></span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="bot_name"><?= $language::get('bot_friendly_name'); ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bot_name" name="name" placeholder="<?= htmlspecialchars($language::get('bot_name_placeholder')); ?>" value="<?= htmlspecialchars($name); ?>" required>
                            <small class="text-muted"><?= $language::get('bot_name_help'); ?></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="bot_status"><?= $language::get('bot_status'); ?></label>
                            <select class="form-select" id="bot_status" name="status">
                                <option value="1" <?= $status === 1 ? 'selected' : ''; ?>><?= $language::get('active_broadcasting_enabled'); ?></option>
                                <option value="0" <?= $status === 0 ? 'selected' : ''; ?>><?= $language::get('paused_broadcasting_disabled'); ?></option>
                            </select>
                            <small class="text-muted"><?= $language::get('bot_status_help'); ?></small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="bot_token"><?= $language::get('telegram_bot_token'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="icon-base ti tabler-key"></i></span>
                                <input type="text" class="form-control" id="bot_token" name="bot_token" placeholder="e.g. 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ" value="<?= htmlspecialchars($token); ?>" required>
                                <button type="button" class="btn btn-outline-primary" id="btnVerifyToken">
                                    <i class="icon-base ti tabler-check me-1"></i> <?= $language::get('verify_token'); ?>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <?= $language::get('token_obtained_from'); ?>
                            </small>

                            <!-- Live Verified Card -->
                            <div id="tokenVerifyResult" class="mt-3 <?= !empty($username) ? '' : 'd-none'; ?>">
                                <div class="alert alert-success d-flex align-items-center gap-3 p-3 mb-0">
                                    <div class="avatar avatar-sm bg-success text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                                        <i class="icon-base ti tabler-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-bold d-block" id="verifiedBotName"><?= htmlspecialchars($name ?: 'Verified Bot'); ?></span>
                                        <span class="fs-7 text-muted" id="verifiedBotUsername">@<?= htmlspecialchars($username); ?></span>
                                    </div>
                                    <span class="badge bg-success rounded-pill"><?= $language::get('token_verified'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top d-flex justify-content-end p-3">
                    <button type="button" class="btn btn-primary js-next-step" data-target="2">
                        <span><?= $language::get('next_target_channel'); ?></span>
                        <i class="icon-base ti tabler-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 2: Destination Channel / Chat -->
        <div class="wizard-step-pane d-none" id="stepPane2">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="icon-base ti tabler-broadcast text-primary"></i>
                        <span><?= $language::get('step_2_title'); ?></span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-7">
                            <label class="form-label fw-semibold" for="chat_id"><?= $language::get('chat_id_label'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="icon-base ti tabler-at"></i></span>
                                <input type="text" class="form-control" id="chat_id" name="chat_id" placeholder="<?= htmlspecialchars($language::get('chat_id_placeholder')); ?>" value="<?= htmlspecialchars($chatId); ?>" required>
                                <button type="button" class="btn btn-outline-success" id="btnTestChat">
                                    <i class="icon-base ti tabler-send me-1"></i> <?= $language::get('send_test_message'); ?>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <?= $language::get('chat_id_help'); ?>
                            </small>

                            <!-- Test Chat Result alert -->
                            <div id="chatTestAlert" class="mt-3 d-none"></div>

                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="silent_notification" name="silent_notification" value="1" <?= $silent ? 'checked' : ''; ?>>
                                <label class="form-check-label fw-semibold" for="silent_notification">
                                    <?= $language::get('silent_notifications'); ?>
                                </label>
                                <small class="text-muted d-block"><?= $language::get('silent_notifications_help'); ?></small>
                            </div>
                        </div>

                        <!-- Channel Setup Instructions Card -->
                        <div class="col-12 col-md-5">
                            <div class="border rounded-3 p-3 bg-light-subtle h-100">
                                <h6 class="fw-bold mb-2 d-flex align-items-center gap-1 text-primary">
                                    <i class="icon-base ti tabler-info-circle"></i> <?= $language::get('quick_setup_guide'); ?>
                                </h6>
                                <ol class="ps-3 mb-0 fs-7 text-muted lh-lg">
                                    <li><?= $language::get('tg_setup_step_1'); ?></li>
                                    <li><?= $language::get('tg_setup_step_2'); ?></li>
                                    <li><?= $language::get('tg_setup_step_3'); ?></li>
                                    <li><?= $language::get('tg_setup_step_4'); ?></li>
                                    <li><?= $language::get('tg_setup_step_5'); ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top d-flex justify-content-between p-3">
                    <button type="button" class="btn btn-outline-secondary js-prev-step" data-target="1">
                        <i class="icon-base ti tabler-arrow-left me-1"></i>
                        <span><?= $language::get('previous_step'); ?></span>
                    </button>
                    <button type="button" class="btn btn-primary js-next-step" data-target="3">
                        <span><?= $language::get('next_content_rules'); ?></span>
                        <i class="icon-base ti tabler-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 3: Content Types & Categories -->
        <div class="wizard-step-pane d-none" id="stepPane3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="icon-base ti tabler-category text-primary"></i>
                        <span><?= $language::get('step_3_title'); ?></span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Content Types Selection -->
                    <label class="form-label fw-bold mb-3 d-block"><?= $language::get('select_content_types'); ?></label>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="card border p-3 h-100 content-type-card cursor-pointer">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="type_movies" name="type_movies" value="1" <?= in_array('movies', $types, true) || $notifyMovie ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold d-block ms-2" for="type_movies">
                                        🎬 <?= $language::get('broadcasting_movies'); ?>
                                    </label>
                                </div>
                                <p class="text-muted fs-7 mt-2 mb-0 ms-4">
                                    <?= $language::get('broadcast_movies_help'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card border p-3 h-100 content-type-card cursor-pointer">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="type_episodes" name="type_episodes" value="1" <?= in_array('episodes', $types, true) || $notifyEpisode ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold d-block ms-2" for="type_episodes">
                                        📺 <?= $language::get('broadcasting_episodes'); ?>
                                    </label>
                                </div>
                                <p class="text-muted fs-7 mt-2 mb-0 ms-4">
                                    <?= $language::get('broadcast_episodes_help'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card border p-3 h-100 content-type-card cursor-pointer">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="type_live" name="type_live" value="1" <?= in_array('live', $types, true) || $notifyLive ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold d-block ms-2" for="type_live">
                                        📡 <?= $language::get('broadcasting_live'); ?>
                                    </label>
                                </div>
                                <p class="text-muted fs-7 mt-2 mb-0 ms-4">
                                    <?= $language::get('broadcast_live_help'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Category Scope -->
                    <label class="form-label fw-bold mb-3 d-block"><?= $language::get('category_filtering_scope'); ?></label>
                    <div class="d-flex gap-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="categories_mode" id="catModeAll" value="all" <?= !$hasCustomCats ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-semibold" for="catModeAll">
                                <?= $language::get('broadcast_all_categories'); ?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="categories_mode" id="catModeCustom" value="custom" <?= $hasCustomCats ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-semibold" for="catModeCustom">
                                <?= $language::get('specific_categories_only'); ?>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Category Selection Box -->
                    <div id="categorySelectionContainer" class="border rounded-3 p-3 bg-light-subtle <?= $hasCustomCats ? '' : 'd-none'; ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-7 fw-bold text-muted"><?= $language::get('select_permitted_categories'); ?></span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary" id="btnSelectAllCats"><?= $language::get('select_all'); ?></button>
                                <button type="button" class="btn btn-xs btn-outline-secondary" id="btnDeselectAllCats"><?= $language::get('clear_selection'); ?></button>
                            </div>
                        </div>

                        <!-- Movies Categories -->
                        <?php if (!empty($rCategories['movies'])): ?>
                            <div class="mb-3">
                                <span class="fw-bold fs-7 d-block mb-2 text-primary">🎬 <?= $language::get('movies_categories'); ?></span>
                                <div class="row g-2">
                                    <?php foreach ($rCategories['movies'] as $cat): ?>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline fs-7 m-0">
                                                <input class="form-check-input js-cat-checkbox" type="checkbox" name="categories[]" id="cat_<?= (int)$cat['id']; ?>" value="<?= (int)$cat['id']; ?>" <?= in_array((int)$cat['id'], $cats, true) ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-truncate" for="cat_<?= (int)$cat['id']; ?>" title="<?= htmlspecialchars((string)$cat['category_name']); ?>">
                                                    <?= htmlspecialchars((string)$cat['category_name']); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Series Categories -->
                        <?php if (!empty($rCategories['series'])): ?>
                            <div class="mb-3">
                                <span class="fw-bold fs-7 d-block mb-2 text-info">📺 <?= $language::get('series_categories'); ?></span>
                                <div class="row g-2">
                                    <?php foreach ($rCategories['series'] as $cat): ?>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline fs-7 m-0">
                                                <input class="form-check-input js-cat-checkbox" type="checkbox" name="categories[]" id="cat_<?= (int)$cat['id']; ?>" value="<?= (int)$cat['id']; ?>" <?= in_array((int)$cat['id'], $cats, true) ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-truncate" for="cat_<?= (int)$cat['id']; ?>" title="<?= htmlspecialchars((string)$cat['category_name']); ?>">
                                                    <?= htmlspecialchars((string)$cat['category_name']); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Live Categories -->
                        <?php if (!empty($rCategories['live'])): ?>
                            <div>
                                <span class="fw-bold fs-7 d-block mb-2 text-success">📡 <?= $language::get('live_tv_categories'); ?></span>
                                <div class="row g-2">
                                    <?php foreach ($rCategories['live'] as $cat): ?>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline fs-7 m-0">
                                                <input class="form-check-input js-cat-checkbox" type="checkbox" name="categories[]" id="cat_<?= (int)$cat['id']; ?>" value="<?= (int)$cat['id']; ?>" <?= in_array((int)$cat['id'], $cats, true) ? 'checked' : ''; ?>>
                                                <label class="form-check-label text-truncate" for="cat_<?= (int)$cat['id']; ?>" title="<?= htmlspecialchars((string)$cat['category_name']); ?>">
                                                    <?= htmlspecialchars((string)$cat['category_name']); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top d-flex justify-content-between p-3">
                    <button type="button" class="btn btn-outline-secondary js-prev-step" data-target="2">
                        <i class="icon-base ti tabler-arrow-left me-1"></i>
                        <span><?= $language::get('previous_step'); ?></span>
                    </button>
                    <button type="button" class="btn btn-primary js-next-step" data-target="4">
                        <span><?= $language::get('next_visual_styling'); ?></span>
                        <i class="icon-base ti tabler-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 4: Visual Formatting & Live Mockup Preview -->
        <div class="wizard-step-pane d-none" id="stepPane4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="icon-base ti tabler-palette text-primary"></i>
                        <span><?= $language::get('step_4_title'); ?></span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Left column: Settings -->
                        <div class="col-12 col-lg-7">
                            <!-- Image Attachment Choice -->
                            <label class="form-label fw-bold mb-2"><?= $language::get('media_attachment'); ?></label>
                            <div class="row g-3 mb-4">
                                <div class="col-4">
                                    <label class="card border p-3 text-center cursor-pointer media-choice-card <?= $imageType === 'poster' ? 'border-primary bg-label-primary' : ''; ?>" for="img_poster">
                                        <input class="form-check-input d-none" type="radio" name="image_type" id="img_poster" value="poster" <?= $imageType === 'poster' ? 'checked' : ''; ?>>
                                        <i class="icon-base ti tabler-photo fs-2 d-block mb-1"></i>
                                        <span class="fw-bold fs-7 d-block"><?= $language::get('poster_image'); ?></span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="card border p-3 text-center cursor-pointer media-choice-card <?= $imageType === 'backdrop' ? 'border-primary bg-label-primary' : ''; ?>" for="img_backdrop">
                                        <input class="form-check-input d-none" type="radio" name="image_type" id="img_backdrop" value="backdrop" <?= $imageType === 'backdrop' ? 'checked' : ''; ?>>
                                        <i class="icon-base ti tabler-wallpaper fs-2 d-block mb-1"></i>
                                        <span class="fw-bold fs-7 d-block"><?= $language::get('backdrop_banner'); ?></span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <label class="card border p-3 text-center cursor-pointer media-choice-card <?= $imageType === 'none' ? 'border-primary bg-label-primary' : ''; ?>" for="img_none">
                                        <input class="form-check-input d-none" type="radio" name="image_type" id="img_none" value="none" <?= $imageType === 'none' ? 'checked' : ''; ?>>
                                        <i class="icon-base ti tabler-file-text fs-2 d-block mb-1"></i>
                                        <span class="fw-bold fs-7 d-block"><?= $language::get('text_only_no_image'); ?></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Inline Action Button -->
                            <div class="card border p-3 mb-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="include_button" name="include_button" value="1" <?= $includeButton ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold ms-2" for="include_button">
                                        <?= $language::get('inline_action_button'); ?>
                                    </label>
                                </div>
                                <div id="buttonOptionsContainer" class="<?= $includeButton ? '' : 'd-none'; ?>">
                                    <div class="row g-2">
                                        <div class="col-12 col-md-5">
                                            <label class="form-label fs-7"><?= $language::get('button_label'); ?></label>
                                            <input type="text" class="form-control form-control-sm" id="button_text" name="button_text" placeholder="🎬 Watch Now" value="<?= htmlspecialchars($buttonText); ?>">
                                        </div>
                                        <div class="col-12 col-md-7">
                                            <label class="form-label fs-7"><?= $language::get('button_url'); ?> (supports <code>{stream_id}</code>)</label>
                                            <input type="text" class="form-control form-control-sm" id="button_url" name="button_url" placeholder="https://myportal.com/play?id={stream_id}" value="<?= htmlspecialchars($buttonUrl); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Template Option -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold m-0" for="custom_template"><?= $language::get('message_template'); ?></label>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btnResetTemplate">Use Standard Template</button>
                                </div>
                                <textarea class="form-control font-monospace fs-7" id="custom_template" name="custom_template" rows="5" placeholder="Leave blank to use the built-in rich template, or write custom HTML with emojis..."><?= htmlspecialchars($customTemplate); ?></textarea>
                                <div class="mt-2 d-flex flex-wrap gap-1">
                                    <span class="fs-8 text-muted align-self-center me-1"><?= $language::get('available_tags'); ?>:</span>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{title}">{title}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{year}">{year}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{rating}">{rating}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{genre}">{genre}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{duration}">{duration}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{quality}">{quality}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{video}">{video}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{audio}">{audio}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{category}">{category}</button>
                                    <button type="button" class="badge bg-label-secondary border-0 js-tag-chip" data-tag="{plot}">{plot}</button>
                                </div>
                            </div>
                        </div>

                        <!-- Right column: Live Telegram Smartphone Mockup -->
                        <div class="col-12 col-lg-5">
                            <label class="form-label fw-bold mb-2"><?= $language::get('live_telegram_preview'); ?>:</label>
                            <div class="telegram-phone-mockup border rounded-4 shadow-sm overflow-hidden bg-body-tertiary">
                                <!-- Telegram Header Bar -->
                                <div class="bg-primary text-white p-3 d-flex align-items-center gap-3">
                                    <i class="icon-base ti tabler-arrow-left fs-5"></i>
                                    <div class="avatar avatar-sm bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                        <i class="icon-base ti tabler-brand-telegram"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block fs-7" id="mockupChannelName"><?= htmlspecialchars($chatId ?: '@MyMoviesChannel'); ?></span>
                                        <span class="fs-8 opacity-75">channel • 1,420 subscribers</span>
                                    </div>
                                </div>

                                <!-- Telegram Chat Area -->
                                <div class="p-3" style="background: url('assets/img/telegram-chat-pattern.png') #eef2f5; min-height: 420px;">
                                    <div class="telegram-bubble bg-white rounded-3 shadow-sm overflow-hidden" style="max-width: 320px;">
                                        <!-- Poster Image in Mockup -->
                                        <div id="mockupImageHolder" class="text-center bg-dark">
                                            <img id="mockupImage" src="https://image.tmdb.org/t/p/w600_and_h900_bestv2/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg" alt="Preview Poster" class="img-fluid" style="max-height: 240px; width: 100%; object-fit: cover;">
                                        </div>

                                        <!-- Caption Text -->
                                        <div class="p-3 fs-8 lh-base text-dark" id="mockupCaption">
                                            🎬 <b>Inception (2010)</b><br>
                                            ━━━━━━━━━━━━━━━━━━<br>
                                            ⭐ <b>Rating:</b> 8.8 / 10<br>
                                            🎭 <b>Genre:</b> Action, Sci-Fi<br>
                                            ⏱ <b>Duration:</b> 2h 28m<br>
                                            📺 <b>Quality:</b> FHD (1080p) | AVC • AAC<br>
                                            📁 <b>Category:</b> Top Rated Sci-Fi<br><br>
                                            📝 <b>Overview:</b><br>
                                            A thief who steals corporate secrets through the use of dream-sharing technology...<br><br>
                                            ✨ <i>Now downloaded & ready to stream in highest quality!</i>
                                        </div>

                                        <!-- Mockup Inline Button -->
                                        <div id="mockupButtonHolder" class="p-2 border-top text-center bg-light <?= $includeButton ? '' : 'd-none'; ?>">
                                            <span class="btn btn-sm btn-primary w-100 py-1 fs-8" id="mockupButtonText">
                                                <?= htmlspecialchars($buttonText); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top d-flex justify-content-between p-3">
                    <button type="button" class="btn btn-outline-secondary js-prev-step" data-target="3">
                        <i class="icon-base ti tabler-arrow-left me-1"></i>
                        <span><?= $language::get('previous_step'); ?></span>
                    </button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-sm" id="btnSaveBot">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        <i class="icon-base ti tabler-device-floppy"></i>
                        <span><?= $rIsEdit ? $language::get('edit_telegram_bot') : $language::get('save_and_activate_bot'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

