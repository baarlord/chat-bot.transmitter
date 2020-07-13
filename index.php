<?php

use lib\Log;
use lib\Rest;
use lib\Bot;
use lib\BotCommand;

use lib\Application;

require_once('lib/autoloader.php');
require_once('lib/application.php');

$application = new Application();
$application->run();

try {
    Log::deleteOldFiles(7, $_REQUEST['auth']['domain']);
} catch (Exception $e) {
    Log::add($e->getMessage(), $_REQUEST['auth']['domain']);
}

switch (strtoupper($_REQUEST['event'])) {

    case 'ONAPPINSTALL':
        // bot registration
        $installResult = Bot::installBot();
        $botId = $installResult['result'];

        //region registration of the commands
        $result[] = $botId;
        $result[] = Bot::registrationCommand(
            $botId,
            array(
                // Text of the command that the user will enter in chats
                'COMMAND' => 'stream',
                // If Y is specified, the command is available in all chats, if N is available only in those where the chatbot is present
                'COMMON' => 'N',
                // Hidden command or not-default N
                'HIDDEN' => 'N',
                // Whether the command is available to Extranet users, by default N
                'EXTRANET_SUPPORT' => 'N',
                // the string ID of the chat bot is only used in Webook mode.
                'CLIENT_ID' => '',
                // Array of translations, must be specified at least for RU and EN
                'LANG' => array(
                    // Language, command description, and what data to enter after the command.
                    array(
                        'LANGUAGE_ID' => 'en',
                        'TITLE' => 'Write to the Department\'s Live feed',
                        'PARAMS' => 'some text'
                    ),
                    array(
                        'LANGUAGE_ID' => 'ru',
                        'TITLE' => 'Написать в Живую ленту отдела',
                        'PARAMS' => 'Текст сообщения'
                    ),
                ),
                // Link to the handler for the command
                'EVENT_COMMAND_ADD' => Bot::buildHandlerBackupUrl(),
            ),
            $_REQUEST['auth']
        );

        $result[] = Bot::registrationCommand(
            $botId,
            array(
                // Text of the command that the user will enter in chats
                'COMMAND' => 'fast_stream',
                // If Y is specified, the command is available in all chats, if N is available only in those where the chatbot is present
                'COMMON' => 'N',
                // Hidden command or not-default N
                'HIDDEN' => 'N',
                // Whether the command is available to Extranet users, by default N
                'EXTRANET_SUPPORT' => 'N',
                // the string ID of the chat bot is only used in Webook mode.
                'CLIENT_ID' => '',
                // Array of translations, must be specified at least for RU and EN
                'LANG' => array(
                    // Language, command description, and what data to enter after the command.
                    array(
                        'LANGUAGE_ID' => 'en',
                        'TITLE' => 'Fast write to the Department\'s Live feed',
                        'PARAMS' => 'some text'
                    ),
                    array(
                        'LANGUAGE_ID' => 'ru',
                        'TITLE' => 'Быстро написать в Живую ленту отдела',
                        'PARAMS' => 'Текст сообщения'
                    ),
                ),
                // Link to the handler for the command
                'EVENT_COMMAND_ADD' => Bot::buildHandlerBackupUrl(),
            ),
            $_REQUEST['auth']
        );

        $result[] = Bot::registrationCommand(
            $botId,
            array(
                // Text of the command that the user will enter in chats
                'COMMAND' => 'help',
                // If Y is specified, the command is available in all chats, if N is available only in those where the chatbot is present
                'COMMON' => 'N',
                // Hidden command or not-default N
                'HIDDEN' => 'N',
                // Whether the command is available to Extranet users, by default N
                'EXTRANET_SUPPORT' => 'N',
                // the string ID of the chat bot is only used in Webook mode.
                'CLIENT_ID' => '',
                // Array of translations, must be specified at least for RU and EN
                'LANG' => array(
                    // Language, command description, and what data to enter after the command.
                    array(
                        'LANGUAGE_ID' => 'en',
                        'TITLE' => 'Help with working with the bot',
                        'PARAMS' => 'some text'
                    ),
                    array(
                        'LANGUAGE_ID' => 'ru',
                        'TITLE' => 'Помощь в работе с ботом',
                        'PARAMS' => 'Текст сообщения'
                    ),
                ),
                // Link to the handler for the command
                'EVENT_COMMAND_ADD' => Bot::buildHandlerBackupUrl(),
            ),
            $_REQUEST['auth']
        );
        //endregion

        break;

    case 'ONIMBOTJOINCHAT':
        // send help message how to use chat-bot. For private chat and for group chat need send different instructions.
        $result = Rest::restCommand(
            'imbot.message.add',
            array(
                'DIALOG_ID' => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
                'MESSAGE' => 'Привет! Я Передатчик Бот. Продублирую твоё сообщение в живую ленту выбранного отдела.',
                'KEYBOARD' => array(
                    array(
                        'TEXT' => 'Написать быстрое сообщение в Живую ленту',
                        'COMMAND' => 'fast_stream',
                        'COMMAND_PARAMS' => 'fast_stream',
                        'BG_COLOR' => '#29619b',
                        'TEXT_COLOR' => '#fff',
                        'DISPLAY' => 'LINE',
                    ),
                    array('TYPE' => 'NEWLINE'),
                    array(
                        'TEXT' => 'Смоделировать сообщение в Живую ленту',
                        'COMMAND' => 'stream',
                        'COMMAND_PARAMS' => 'stream',
                        'BG_COLOR' => '#2a4c7c',
                        'TEXT_COLOR' => '#fff',
                        'DISPLAY' => 'LINE',
                    ),
                    array('TYPE' => 'NEWLINE'),
                    array('TEXT' => 'Помощь', 'COMMAND' => 'help', 'DISPLAY' => 'LINE'),
                ),
            ),
            $_REQUEST['auth']
        );

        break;

    case 'ONIMCOMMANDADD':
        BotCommand::parseCommand($_REQUEST);
        break;

    case 'ONIMBOTMESSAGEADD':
    default:
        break;

}
