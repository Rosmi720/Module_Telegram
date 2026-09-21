<?php

namespace XcVm\Module\Telegram;

use XcVm\Core\Http\RequestManager;

/**
 * Telegram Module Dedicated Internal API Script.
 *
 * Handles AJAX requests directly inside the module without depending on core api actions.
 * Supports actions:
 *   - test_token     (verify Telegram bot token)
 *   - test_chat      (send test message to channel/chat)
 *   - save           (create or update bot)
 *   - delete         (delete bot)
 *   - toggle         (toggle active status)
 *   - broadcast_test (send full media broadcast test)
 *   - get            (fetch bot record)
 *   - logs           (fetch recent broadcast logs)
 *
 * @package XC_VM_Module_Telegram
 */

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
}

// Gather all inputs from $_REQUEST, $_POST, $_GET, and JSON body
$input = array_merge($_REQUEST, $_GET, $_POST);
$rawPayload = file_get_contents('php://input');
if (!empty($rawPayload)) {
    $decoded = json_decode($rawPayload, true);
    if (is_array($decoded)) {
        $input = array_merge($input, $decoded);
    }
}

// Support both prefixed and unprefixed action parameter
$action = trim((string)($input['action'] ?? ''));
if (str_starts_with($action, 'telegram_bot_')) {
    $action = substr($action, 13); // e.g. 'telegram_bot_test_token' -> 'test_token'
}

if (!function_exists(__NAMESPACE__ . '\\sendJson')) {
    function sendJson(array $data): never
    {
        echo json_encode($data);
        exit();
    }
}

if (!function_exists(__NAMESPACE__ . '\\sendOk')) {
    function sendOk(array $extra = []): never
    {
        sendJson(['result' => true, 'success' => true] + $extra);
    }
}

if (!function_exists(__NAMESPACE__ . '\\sendFail')) {
    function sendFail(string $message, array $extra = []): never
    {
        sendJson(['result' => false, 'success' => false, 'message' => $message] + $extra);
    }
}

switch ($action) {
    case 'test_token':
        $token = trim((string)($input['bot_token'] ?? ''));
        if ($token === '') {
            sendFail('Please provide a Telegram bot token.');
        }

        $res = TelegramBotService::verifyToken($token);
        if (!empty($res['success'])) {
            $user = $res['result'] ?? [];
            sendOk([
                'message'  => $res['message'] ?? 'Bot token is valid.',
                'username' => $user['username'] ?? '',
                'name'     => $user['name'] ?? '',
                'bot_id'   => $user['id'] ?? '',
                'bot_data' => $user,
            ]);
        } else {
            sendFail($res['message'] ?? 'Failed to verify token with Telegram.');
        }
        break;

    case 'test_chat':
        $token = trim((string)($input['bot_token'] ?? ''));
        $chatId = trim((string)($input['chat_id'] ?? ''));
        $botName = trim((string)($input['bot_name'] ?? 'XC_VM Bot'));

        if ($token === '' || $chatId === '') {
            sendFail('Bot token and Target Channel/Chat ID are required.');
        }

        $res = TelegramBotService::testChat($token, $chatId, $botName);
        if (!empty($res['success'])) {
            sendOk(['message' => $res['message'] ?? 'Test message sent successfully.']);
        } else {
            sendFail($res['message'] ?? 'Failed to send test message to channel.');
        }
        break;

    case 'save':
        $res = TelegramBotService::saveBot($input);
        if (!empty($res['success'])) {
            sendOk($res);
        } else {
            sendFail($res['message'] ?? 'Failed to save bot settings.');
        }
        break;

    case 'delete':
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            sendFail('Invalid bot ID.');
        }

        if (TelegramBotService::deleteBot($id)) {
            sendOk(['message' => 'Bot deleted successfully.']);
        } else {
            sendFail('Failed to delete bot.');
        }
        break;

    case 'toggle':
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            sendFail('Invalid bot ID.');
        }

        $res = TelegramBotService::toggleStatus($id);
        if (!empty($res['success'])) {
            sendOk($res);
        } else {
            sendFail($res['message'] ?? 'Failed to toggle status.');
        }
        break;

    case 'broadcast_test':
        $botId = (int)($input['bot_id'] ?? $input['id'] ?? 0);
        $streamId = !empty($input['stream_id']) ? (int)$input['stream_id'] : null;

        if ($botId <= 0) {
            sendFail('Bot ID is required for test broadcast.');
        }

        $res = TelegramBotService::broadcastTest($botId, $streamId);
        if (!empty($res['success'])) {
            sendOk($res);
        } else {
            sendFail($res['message'] ?? 'Failed to dispatch test broadcast.');
        }
        break;

    case 'get':
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            sendFail('Invalid bot ID.');
        }

        $bot = TelegramBotService::getBotById($id);
        if ($bot) {
            sendOk(['bot' => $bot]);
        } else {
            sendFail('Bot not found.');
        }
        break;

    case 'logs':
        $limit = !empty($input['limit']) ? (int)$input['limit'] : 50;
        $logs = TelegramBotService::getRecentLogs($limit);
        sendOk(['logs' => $logs]);
        break;

    default:
        sendFail('Invalid or missing API action: ' . htmlspecialchars($action));
        break;
}
