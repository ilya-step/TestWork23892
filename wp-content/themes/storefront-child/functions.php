<?php

// Подключаем стили дочерней темы
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'storefront-child', get_stylesheet_directory_uri() . '/style.css' );
});






add_action( 'init', 'register_post_types' );
function register_post_types(){

	// Регистрируем тип записи "Cities"
	register_post_type( 'cities', [
		'label'  => null,
		'labels' => [
			'name'               => 'Города', // основное название для типа записи
			'singular_name'      => 'Город', // название для одной записи этого типа
			'add_new'            => 'Добавить город', // для добавления новой записи
			'add_new_item'       => 'Добавление города', // заголовка у вновь создаваемой записи в админ-панели.
			'edit_item'          => 'Редактирование города', // для редактирования типа записи
			'new_item'           => 'Новый город', // текст новой записи
			'view_item'          => 'Смотреть город', // для просмотра записи этого типа.
			'search_items'       => 'Искать город', // для поиска по этим типам записи
			'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
			'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
			'parent_item_colon'  => '', // для родителей (у древовидных типов)
			'menu_name'          => 'Города', // название меню
		],
		'description'         => '',
		'public'              => true,
		'publicly_queryable'  => true, // зависит от public
		'exclude_from_search' => true, // зависит от public
		'show_ui'             => true, // зависит от public
		'show_in_nav_menus'   => true, // зависит от public
		'show_in_menu'        => true, // показывать ли в меню админки
		'show_in_admin_bar'   => true, // зависит от show_in_menu
		'show_in_rest'        => true, // добавить в REST API. C WP 4.7
		'rest_base'           => null, // $post_type. C WP 4.7
		'menu_position'       => 4,
		'menu_icon'           => 'dashicons-admin-site',
		//'capability_type'   => 'post',
		//'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
		//'map_meta_cap'      => null, // Ставим true чтобы включить дефолтный обработчик специальных прав
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor','thumbnail','excerpt','custom-fields' ], // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
		'taxonomies'          => Array('countries'),
		'has_archive'         => false,
		'rewrite'             => true,
		'query_var'           => true,
	] );
}

// хук для регистрации таксономии
add_action( 'init', 'create_taxonomy' );
function create_taxonomy(){

	// список параметров: wp-kama.ru/function/get_taxonomy_labels
	register_taxonomy( 'countries', [ 'cities' ], [
		'label'                 => '', // определяется параметром $labels->name
		'labels'                => [
			'name'              => 'Страны',
			'singular_name'     => 'Страна',
			'search_items'      => 'Искать страну',
			'all_items'         => 'Все страны',
			'view_item '        => 'Смотреть страны',
			'parent_item'       => 'Родительская страна',
			'parent_item_colon' => 'Родительская страна:',
			'edit_item'         => 'изменить страну',
			'update_item'       => 'Обновить страну',
			'add_new_item'      => 'Добавить страну',
			'new_item_name'     => 'Новое название страны',
			'menu_name'         => 'Страны',
			'back_to_items'     => '← Назад',
		],
		'description'           => 'Страны', // описание таксономии
		'public'                => true,
		'publicly_queryable'    => null, // равен аргументу public
		'hierarchical'          => true, // многомерность таксономии

		'rewrite'               => true,
		'capabilities'          => array(),
		'meta_box_cb'           => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
		'show_admin_column'     => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
        'show_in_quick_edit'    => true,
		'show_in_rest'          => true, // добавить в REST API для Гутенберг (редактор вп)
		'rest_base'             => null, // $taxonomy
	] );
}







function register_city_meta_box() {
	add_meta_box(
			'city_coordinates_meta_box',  // ID метабокса
			'City Coordinates',           // Заголовок метабокса
			'display_city_meta_box',      // Callback функция для отображения метабокса
			'cities',                     // Тип записи
			'normal',                     // Позиция
			'high'                        // Приоритет
	);
}
add_action('add_meta_boxes', 'register_city_meta_box');

function display_city_meta_box($post) {
	// Получаем текущие значения метаполей
	$latitude = get_post_meta($post->ID, '_city_latitude', true);
	$longitude = get_post_meta($post->ID, '_city_longitude', true);

	// Используем nonce для безопасности
	wp_nonce_field('city_coordinates_nonce_action', 'city_coordinates_nonce');

	echo '<p>* Если оставить пустым, заполнится автоматически</p>';

	echo '<p>';
	echo '  <label for="city_latitude">Latitude:</label>';
	echo '  <input type="text" id="city_latitude" name="city_latitude" value="' . esc_attr($latitude) . '" />';
	echo '</p>';
	
	echo '<p>';
	echo '  <label for="city_longitude">Longitude:</label>';
	echo '  <input type="text" id="city_longitude" name="city_longitude" value="' . esc_attr($longitude) . '" />';
	echo '</p>';
}

function save_city_meta_box_data($post_id) {
	// Проверяем nonce
	if (!isset($_POST['city_coordinates_nonce']) || !wp_verify_nonce($_POST['city_coordinates_nonce'], 'city_coordinates_nonce_action')) {
			return;
	}

	// Проверяем авто-сохранение
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
	}

	// Проверяем права пользователя
	if (!current_user_can('edit_post', $post_id)) {
			return;
	}

	// Сохраняем данные
	if (isset($_POST['city_latitude'])) {
			update_post_meta($post_id, '_city_latitude', sanitize_text_field($_POST['city_latitude']));
	}
	
	if (isset($_POST['city_longitude'])) {
			update_post_meta($post_id, '_city_longitude', sanitize_text_field($_POST['city_longitude']));
	}
}
add_action('save_post', 'save_city_meta_box_data');





// Функция определения точных координат города с помощью Geocoding API от OpenWeatherMap по названию города
function get_city_coordinates($city_name) {
	// Задаем API-ключ для доступа к OpenWeatherMap. 
	// Замените 'YOUR_OPENWEATHERMAP_API_KEY' на ваш реальный API-ключ
	$api_key = '6f234b09416c1674bb1db6128c8ee09d'; 
	
	// Формируем URL для запроса, используя название города.
	// urlencode() используется для кодирования пробелов и специальных символов в названии города
	$url = "http://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($city_name) . "&limit=1&appid={$api_key}";
	
	// Выполняем HTTP-запрос к API
	$response = wp_remote_get($url);
	
	// Проверяем, произошла ли ошибка при выполнении запроса
	if (is_wp_error($response)) {
			return false; // Если ошибка, возвращаем false
	}
	
	// Декодируем JSON-ответ в ассоциативный массив
	$data = json_decode(wp_remote_retrieve_body($response), true);
	
	// Проверяем, что данные не пустые и содержат координаты
	if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
			// Извлекаем широту и долготу из данных
			$latitude = $data[0]['lat'];
			$longitude = $data[0]['lon'];
			
			// Возвращаем массив с координатами
			return array(
					'latitude' => $latitude,
					'longitude' => $longitude
			);
	} else {
			return false; // Если данных нет, возвращаем false
	}
}

// Функция определения точной текущей температуры по координатам города
function get_current_temperature($latitude, $longitude) {
	// Задаем API-ключ для доступа к OpenWeatherMap. 
	// Замените 'YOUR_WEATHER_API_KEY' на ваш реальный API-ключ
	$api_key = '6f234b09416c1674bb1db6128c8ee09d';  
	
	// Формируем URL для запроса текущей погоды, используя широту и долготу
	$url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid={$api_key}";
	
	// Выполняем HTTP-запрос к API
	$response = wp_remote_get($url);
	
	// Проверяем, произошла ли ошибка при выполнении запроса
	if (is_wp_error($response)) {
			return false; // Если ошибка, возвращаем false
	}
	
	// Декодируем JSON-ответ в ассоциативный массив
	$data = json_decode(wp_remote_retrieve_body($response), true);
	
	// Проверяем, есть ли температура в ответе
	if (isset($data['main']['temp'])) {
			// Возвращаем значение температуры
			return $data['main']['temp'];
	} else {
			return false; // Если температура отсутствует, возвращаем false
	}
}

// Основная функция полного получения погоды
function get_city_weather_data($post_id) {
	// Проверяем наличие координат в метаполях записи
	$latitude = get_post_meta($post_id, '_city_latitude', true);
	$longitude = get_post_meta($post_id, '_city_longitude', true);
	
	// Если координаты не заданы, запрашиваем их через геокодинг API
	if (empty($latitude) || empty($longitude)) {
			// Получаем координаты через функцию геокодинга на основе названия города
			$coordinates = get_city_coordinates(get_the_title($post_id));
			
			// Если координаты успешно получены
			if ($coordinates) {
					// Извлекаем широту и долготу
					$latitude = $coordinates['latitude'];
					$longitude = $coordinates['longitude'];
					
					// Сохраняем координаты в метаполях записи для последующего использования
					update_post_meta($post_id, '_city_latitude', $latitude);
					update_post_meta($post_id, '_city_longitude', $longitude);
			} else {
					return false; // Если не удалось получить координаты, возвращаем false
			}
	}

	// Получаем текущую температуру по заданным координатам
	$current_temperature = get_current_temperature($latitude, $longitude);
	
	// Если температура была успешно получена
	if ($current_temperature !== false) {
			return array(
					'temperature' => $current_temperature,
					'latitude' => $latitude,
					'longitude' => $longitude
			);
	} else {
			return false;
	}
}












// Виджет вывода города с текущей температурой
class City_Widget extends WP_Widget {

	// Конструктор класса виджета
	function __construct() {
			parent::__construct(
					'city_widget', // Уникальный ID виджета
					__('City Widget', 'text_domain'), // Название виджета
					array('description' => __('A Widget to display a selected city and its ID', 'text_domain'),) // Описание виджета
			);
	}

	// Метод для вывода содержимого виджета на фронте
	public function widget($args, $instance) {
			// Получаем ID города из настроек виджета
			$city_id = !empty($instance['city']) ? $instance['city'] : '';

			// Проверяем, установлен ли город
			if ($city_id) {
					// Получаем объект записи города по его ID
					$city = get_post($city_id);
					
					// Начинаем вывод виджета с HTML-обертки
					echo $args['before_widget'];

					// Если город существует, выводим его название и ID
					if (!empty($city)) {
							?>
<div class="city-widget">
	<?php // Получаем текущую температуру ?>
	<?php $current_temperature = get_city_weather_data($city->ID); ?>
	<?php if ($current_temperature !== false): ?>
	<h2 class="city-title"><?php echo esc_html($city->post_title); ?></h2>
	<p><?php echo $current_temperature['temperature']; ?> °C</p>
	<?php else: ?>
	<h2 class="city-title"><?php echo esc_html($city->post_title); ?></h2>
	<p>Не удалось получить данные о температуре.</p>
	<?php endif; ?>
</div>
<?php
					}
					
					// Завершаем вывод виджета
					echo $args['after_widget'];
			}
	}
	// Метод для вывода формы настройки виджета в админке
	public function form($instance) {
			// Получаем текущий ID города, если он задан
			$city_id = !empty($instance['city']) ? $instance['city'] : '';
			
			// Запрашиваем все записи типа "Cities"
			$cities = get_posts(array('post_type' => 'cities', 'numberposts' => -1));
			?>
<p>
	<label for="<?php echo esc_attr($this->get_field_id('city')); ?>"><?php _e('Select City:'); ?></label>
	<select class="widefat" id="<?php echo esc_attr($this->get_field_id('city')); ?>"
		name="<?php echo esc_attr($this->get_field_name('city')); ?>">
		<option value=""><?php _e('Select a city'); ?></option>
		<?php foreach ($cities as $city): ?>
		<option value="<?php echo esc_attr($city->ID); ?>" <?php selected($city_id, $city->ID); ?>>
			<?php echo esc_html($city->post_title); ?></option>
		<?php endforeach; ?>
	</select>
</p>
<?php
	}
	// Метод для обновления данных виджета при сохранении
	public function update($new_instance, $old_instance) {
			$instance = array();
			// Сохраняем ID города, удаляя лишние теги
			$instance['city'] = (!empty($new_instance['city'])) ? strip_tags($new_instance['city']) : '';
			return $instance;
	}
}
// Функция для регистрации виджета
function register_city_widget() {
	register_widget('City_Widget');
}
// Хук, который инициализирует регистрацию виджетов
add_action('widgets_init', 'register_city_widget');










// Добавляем действия для AJAX запросов как для неавторизованных пользователей, так и для авторизованных
add_action('wp_ajax_nopriv_city_search', 'ajax_city_search');
add_action('wp_ajax_city_search', 'ajax_city_search');

/**
 * Функция для получения данных из базы данных и возврата таблицы.
 * 
 * @param string $query опциональный параметр, строка для поиска в названиях городов.
 * @return string HTML таблица с данными или сообщение "Ничего не найдено".
 */
function render_city_table($query = '') {
	// Подключаем глобальную переменную базы данных $wpdb
	global $wpdb;

	// Основной SQL запрос для получения данных о городах и их странах
	$sql = "SELECT c.post_title AS city_name, c.ID AS city_id, cn.name AS country_name
					FROM {$wpdb->prefix}posts c
					LEFT JOIN {$wpdb->prefix}term_relationships tr ON (c.ID = tr.object_id)
					LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
					LEFT JOIN {$wpdb->prefix}terms cn ON (tt.term_id = cn.term_id)
					WHERE tt.taxonomy = 'countries' AND c.post_type = 'cities' AND c.post_status = 'publish'";
	
	// Если передан параметр $query, добавляем условие LIKE для поиска по названиям городов
	if ($query) {
			$sql .= $wpdb->prepare(" AND c.post_title LIKE %s", '%' . $wpdb->esc_like($query) . '%');
	}

	// Выполняем SQL запрос и получаем результаты
	$results = $wpdb->get_results($sql);

	// Если результаты пусты, возвращаем сообщение "Ничего не найдено"
	if (empty($results)) {
			return '<p>Ничего не найдено</p>';
	}

	// Организуем полученные данные по странам
	$countries = array();
	foreach ($results as $result) {
			$countries[$result->country_name][] = $result;
	}

	// Построение HTML таблицы для отображения данных
	$output = '<table border="1"><tr><th>Страна</th><th>Города</th></tr>';
	foreach ($countries as $country_name => $cities) {
		$output .= '<tr>';
		$output .= '<td rowspan="' . count($cities) . '">' . esc_html($country_name) . '</td>';

		// Выводим первый город для страны
		$first_city = array_shift($cities);

		// Получаем текущую температуру
		$current_temperature = get_city_weather_data($first_city->city_id);
		$output .= '<td>' . esc_html($first_city->city_name) . '<br>Температура: ' . esc_html($current_temperature['temperature']) . ' °C</td>';
		$output .= '</tr>';

		// Выводим остальные города для страны в новых строках
		foreach ($cities as $city) {
			// Получаем текущую температуру
			$current_temperature = get_city_weather_data($city->city_id);

			$output .= '<tr>';
			$output .= '<td>' . esc_html($city->city_name) . '<br>Температура: ' . esc_html($current_temperature['temperature']) . ' °C</td>';
			$output .= '</tr>';
		}
	}
	$output .= '</table>';

	return $output;
}

// Функция для обработки AJAX запроса на поиск городов
function ajax_city_search() {
	// Получаем строку поиска из POST запроса
	$search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
	
	// Генерируем таблицу на основе строки поиска
	$table = render_city_table($search_query);
	
	// Возвращаем таблицу через AJAX
	wp_send_json_success($table);
}

// Добавляем custom action hook для встраивания таблицы и поля поиска
add_action('insert_city_table_with_search', 'insert_city_table_with_search_action');

/**
 * Функция для вставки таблицы и поля поиска на фронтенд
 */
function insert_city_table_with_search_action() { ?>
<script>
// jQuery скрипт для отправки AJAX запроса по каждому изменению в поле поиска
jQuery(document).ready(function($) {
	$('#city_search').on('keyup', function() {
		var search = $(this).val();
		$.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>", // URL для отправки AJAX запроса
			method: 'POST',
			data: {
				action: 'city_search', // Действие для AJAX запроса
				search: search, // Строка поиска
			},
			success: function(response) {
				if (response.success) {
					$('#city_table_container').html(response.data); // Обновляем таблицу на полученные данные
				} else {
					$('#city_table_container').html(
						'<p>Ничего не найдено</p>'); // Выводим сообщение при отсутствии результатов
				}
			}
		});
	});
});
</script>
<div>
	<input type="text" id="city_search" placeholder="Поиск по городу"> <!-- Поле поиска городов -->
</div>
<div id="city_table_container">
	<?php echo render_city_table(); ?>
	<!-- Изначальный вывод таблицы -->
</div>
<?php
}








?>