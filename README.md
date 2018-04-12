Тестовое задание для reklama.guru
---------------------------------

### Условия задачи ###
На вход от пользователя поступает имя файла (с путем).
Файл содержит столбец с датой, и несколько измерений
Например:
```
date; A; B; C
2018-03-01; 3; 4; 5.05
2018-03-01; 1; 2; 1
2018-03-01; 2; 2; -0.05
2018-03-02; 5; 7; 6.06
2018-03-03; 1; 2; 1.06
```
Нужно вывести данные сгруппированные и просумированные по дате:
```
date; A; B; C
2018-03-01; 6; 8; 6
2018-03-02; 5; 7; 6.06
2018-03-03; 1; 2; 1.06
```
Размер данных таков, что ни исходный файл, ни даже результат аггрегации не поместятся в память.
Код покрыть тестами.
Желательна версия на php и go

### Решение ###

В рамках задачи было написано несколько алгоритмов для сравнения эффективности по скорости обработки и потреблению памяти на разных размерах входных данных:

- **in-memory** - самое простое решение "в лоб" - посчитать всё в памяти. Формально не является решением задачи, т.к. не влезает по памяти, но может служить отправной точкой при сравнении эффективности других алгоритмов.
- **seeker** - проходим входной файл и для каждой даты запоминаем все позиции в файле, где эта дата встречается. Затем для каждой даты вычитываем соответствующие строчки, суммируем и записываем в выходной файл.
- **binary** -  Группировка и суммирование за один проход по входному файлу. Используется промежуточный двоичный буфер, в котором заведомо известен размер одной строки данных в байтах, что позволяет мгновенно адресовать данные. Также используется "индексный" файл, в котором для каждой даты хранится смещение блока с результирующими данными в двоичном буфере. Сделан для уменьшения количества считываемых данных при поиске в индексе и с заделом на оптимизацию.
Для каждой строки входящих данных мы ищем в индексном файле адрес выходных
данных. Если нашли, то считываем их из буфера, суммируем с текущей строчкой
и записываем обратно. Если не нашли, то дописываем блок в конец буфера и добавляем
в индекс адрес блока. После обработки всех входных данных разворачиваем
двоичный буфер в человекочитаемый формат.
- **binary-indexed** - то же самое, что и **binary**, только индекс (адреса результирующих блоков) хранится в памяти (в обычном ассоциативном массиве).

В среднем самым толковым оказался алгоритм **seeker**, который и используется по умолчанию. Но он предполагает, что в память влезут все даты с позициями всех строк во входном файле. Если это проблема, то её решает **binary-indexed** алгоритм. Он ест значительно меньше памяти, но и выполняется значительно медленнее.

### Запуск ###

* Установка зависимостей: `composer install`
* Обработка данных: `bin/console aggregate input.txt output.txt`
* Генерация случайных входных данных: `bin/console generate-input-data input.txt`

### Тестирование ###

* Запуск юнит-тестов: `vendor/bin/phpunit`
