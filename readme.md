# Тестовое задание на вакансию Middle PHP-Разработчик

## Необходимые знания для выполнения задания:
- [PHP 8](https://habr.com/ru/post/526220/)
- [PostgreSQL](https://www.postgresql.org/docs/)
- [Doctrine 2](https://www.doctrine-project.org/projects/doctrine-orm/en/2.10/index.html)
- [Symfony 5](https://symfony.com/doc/current/index.html)
- [Symfony Messenger](https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber)
- [ApiPlatform](https://api-platform.com/docs/core/)
- [События](https://symfony.com/doc/current/event_dispatcher.html) и [Подписчики на события](https://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber)

## Минимальные требования к серверу:

- PHP 8 и выше
- PostgreSQL 13 и выше
- Openssl

## Установка:
- Клонировать репозиторий
- Установить зависимости:

```bash
composer install
```

- По окончанию установки нужно будет происходить заполнение переменных окружения. Можно оставлять значения по умолчанию - просто нажимайте `Enter` для автоматического заполнения значения переменной.
    - `APP_SECRET` - произвольный набор символов
    - `JWT_PASSPHRASE` - произвольный набор символов
    - `JWT_TOKEN_TTL` - срок действия JWT в секундах
    - `DATABASE_URL` - настройки для подключения к БД
    - `MAILER_DSN` - настройки для отправки писем
    - `MESSENGER_TRANSPORT_DSN` - настройки асинхронного транспорта
    - `MESSENGER_FAILED_DSN` - настройки транспорта для неуспешных асинхронных задач

## Создать БД и выполнить миграции:

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Запуск локального сервера

Можно использовать OpenServer, Docker или [бинарный файл Symfony](https://symfony.com/download).

Пример с использованием бинарного файла Symfony:

В отдельной вкладке терминала выполните команду - `symfony serve`.

После этого откройте в браузере адрес `https://127.0.0.1:8000/api/v1`. Должна открыться страница с интерактивной
документацией к API. 