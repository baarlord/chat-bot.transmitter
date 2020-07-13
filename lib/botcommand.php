<?php

namespace lib;

class BotCommand
{
    public static function parseCommand($request)
    {
        $commands = $request['data']['COMMAND'];
        foreach ($commands as $commandId => $commandParams) {
            switch ($commandParams['COMMAND']) {
                case'help':
                    BotCommand::runCommandHelp($request);
                    break;
                case'fast_stream':
                    BotCommand::runCommandFastStream($request);
                    break;
                case'stream':
                    BotCommand::runCommandStream($commandParams, $request);
                    break;
                default:
                    break;
            }
        }
    }

    private static function runCommandHelp($request)
    {
        $departments = array_merge(
            array(0 => 'Всем авторизованным пользователям'),
            BotCommand::createDepartmentList($request)
        );

        $message = "Список команд, которые я знаю:" . "\n" .
            "[send=/help]/help[/send] - Покажу мой список команд" . "\n" .
            "[put=/stream]/stream[/put] - Отправлю за тебя сообщение в Живую ленту" . "\n\n" .
            "В команде /stream сперва укажи через запятую номера тех отделов, которым хочешь написать." . "\n" .
            "А после отделов ставь знак #" . "\n" .
            "и пиши текст сообщения(пока только текст)" . "\n\n";
        $message .= "Пример:" . "\n" .
            ">>/stream 1,3,15#Я проспал. Приду через час." . "\n";
        $message .= "Отделы, которые я знаю:" . "\n";

        foreach ($departments as $code => $name) {
            $message .= $code . ' - ' . $name . "\n";
        }

        // send answer message
        $result = Rest::restCommand('imbot.message.add',
            array(
                'DIALOG_ID' => $request['data']['PARAMS']['DIALOG_ID'],
                'MESSAGE' => $message,
            ),
            $request['auth']);

    }

    private static function runCommandFastStream($request)
    {
        // send answer message
        $result = Rest::restCommand('imbot.message.add',
            array(
                'DIALOG_ID' => $request['data']['PARAMS']['DIALOG_ID'],
                'MESSAGE' => "Это сложная команда. Я её ещё не выучил. Давай пока попробуем /stream?",
            ),
            $request['auth']);
    }

    private static function runCommandStream($params, $request)
    {
        $departments = BotCommand::createDepartmentList($request);
        $postParams = BotCommand::parseStreamCommandParams($params['COMMAND_PARAMS']);

        // send answer message
        $result = Rest::restCommand('log.blogpost.add',
            array(
                'USER_ID' => $request['data']['USER']['ID'],
                'POST_MESSAGE' => $postParams['POST_MESSAGE'],
                'DEST' => $postParams['DEST'],
            ),
            $request['auth']);

        $result = Rest::restCommand('imbot.message.add',
            array(
                'DIALOG_ID' => $request['data']['PARAMS']['DIALOG_ID'],
                'MESSAGE' => 'Сообщение отправлено',
            ),
            $request['auth']);
    }


    private static function createDepartmentList($request)
    {
        $departments = array();
        $answer = Rest::restCommand('department.get', array(), $request['auth']);

        if (!is_array($answer['result'])) {
            return $departments;
        }

        foreach ($answer['result'] as $index => $department) {
            $departments[$department['ID']] = $department['NAME'];
        }

        asort($departments);

        return $departments;
    }

    private static function parseStreamCommandParams($commandString)
    {
        $commandArray = explode('#', $commandString);
        $departments = explode(',', $commandArray[0]);
        $dest = array();
        foreach ($departments as $department) {
            if ($department === 0) {
                $dest[] = 'UA';
                continue;
            }
            $dest[] = 'DR' . $department;
        }
        return array('DEST' => $dest, 'POST_MESSAGE' => $commandArray[1]);
    }

}