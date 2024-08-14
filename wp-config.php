<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки базы данных
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'ilyas11p_belgiya' );

/** Имя пользователя базы данных */
define( 'DB_USER', 'ilyas11p_belgiya' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', '&dl7cEoL' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '|e^gtp;D~quy.<!f9RWir|C6B}` `mMk$731Kp$ZkGzo_Y*mOq3U?lg69^@h>|LD' );
define( 'SECURE_AUTH_KEY',  'sIy>Td|{ Ws?[^$jLB`XSr=7 B1g.vm4*}|QK8v0>&!H0mbCz>Z.~[WZ1gE1Q^%M' );
define( 'LOGGED_IN_KEY',    'A%gckxn#([*wcQR%<*y_/ G@0sWy;Q7,&_gKMQ$P!7qGvvU1==PLXBxnH :;67V9' );
define( 'NONCE_KEY',        ',S PE@Nk%-sB?[h!e:!BhldF{rW;E>RFI@L/~H:#_*OAcU$QW#Hvh&gtiu?^zm!%' );
define( 'AUTH_SALT',        'aP-I:aEm:Kj^Z6kXmQR0A}|(bn+xDF#uE*+qf9W{a9DUAg/~-19 3w0Janpn9=i]' );
define( 'SECURE_AUTH_SALT', '!s$BC%-Nb=$Pm>YECD.^}f0gs8.qbP8{1fgJbsagp4}|tOMv6j7,~9Y02;PDsc/c' );
define( 'LOGGED_IN_SALT',   'E14z?m{$;O:1e|ysS@rQEs`El!x2&L.Z04otGANwx=4FBG<YXsS1IBaCA)68Z+#I' );
define( 'NONCE_SALT',       'a_dedUNI@_D7]dku:^&x@ >0S} ?*-&DUXO?[fs_xVB=Si_Pj!/u~Ix[dw1~t%v]' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
