# PC-Builder

Конфигуратор ПК на чистом PHP 8, MySQL, HTML, CSS и JavaScript.

## Запуск в XAMPP

1. Скопируйте папку `pc-builder` в `C:/xampp/htdocs/`.
2. Запустите Apache и MySQL в XAMPP.
3. Откройте phpMyAdmin, создайте/импортируйте `sql/schema.sql`.
4. Проверьте параметры подключения в `config/database.php`.
5. Откройте `http://localhost/pc-builder/`.

Регистрация создаёт аккаунт на Free. Переключение тарифов имитирует оплату и доступно в кабинете. Проверка совместимости учитывает сокет CPU/материнской платы, DDR4/DDR5 и запас мощности БП.
