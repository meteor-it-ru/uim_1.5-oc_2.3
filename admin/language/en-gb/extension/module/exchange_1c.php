<?php
// Heading
$_['heading_title']				= '<b>M-IT: Управление интернет-магазином 1.3.0</b>';

// Text
$_['text_module']				= 'Модули';
$_['text_success']				= 'Настройки модуля обновлены!';
$_['text_yes']					= 'Да';
$_['text_no']					= 'Нет';
$_['text_install']				= 'Установить модуль';
$_['text_uninstall']			= 'Удалить модуль';

$_['text_template']				= 
'<b style="color: red;">*</b> В поле "Шаблон", кроме произвольных слов, возможно использование тегов:<br>
<b>[category_name]</b> - наименование категории,<br>
<b>[all_category_name]</b> - наименования всех категорий в порядке вложенности,<br>
<b>[manufacturer_name]</b> - производитель,<br>
<b>[product_name]</b> - наименование товара,<br>
<b>[model_name]</b> - модель,<br>
<b>[price]</b> - цена + валюта.';
$_['text_clear']				= 
'<b style="color: red;">**</b> В поле "Очищать" перечисляются символы и группы символов, которые будут удалены из значений тегов при генерации значения параметра.<br>
Указывать, разделяя знаком "|".';
$_['text_replace']				= 
'<b style="color: red;">***</b> Если значение поля "Заменять на" не пусто, то элементы, указанные в поле "Очищать" будут не удаляться, а заменяться на данное значение.<br>
При указании нескольких символов или групп символов, разделенных знаком "|", элементы из поля "Очищать" будут заменяться на соответствующие им в поле "Заменять на".';

// Entry
$_['entry_seo']						= 'Seo';
$_['entry_product_params']			= 'Параметры товара';
$_['entry_other']					= 'Другие';
$_['entry_secure']					= 'Безопасность';

$_['entry_category']				= 'КАТЕГОРИЯ';
$_['entry_product']					= 'ТОВАР';
$_['entry_h1']						= 'HTML-тег H1:';
$_['entry_title']					= 'HTML-тег Title:';
$_['entry_meta_keyword']			= 'Мета-тег Keywords:';
$_['entry_meta_description']		= 'Мета-тег Description:';
$_['entry_description']				= 'Описание:';
$_['entry_tag']		 				= 'Теги товара:';
$_['entry_seo_url']					= 'SEO URL:';
$_['entry_load']					= 'Загружать из 1С';
$_['entry_rewrite']					= 'Перезаписывать непустые';
$_['entry_generate']				= 'Генерировать';
$_['entry_template']				= 'Шаблон <b style="color: red;">*</b>';
$_['entry_all_category_sep']		= 'Разделитель для [all_category_name]:';
$_['entry_clear']					= 'Очищать <b style="color: red;">**</b>';
$_['entry_replace']					= 'Заменять на <b style="color: red;">***</b>';
$_['entry_transliterate']			= 'Транслитерация';
$_['entry_transliterate_simple']	= 'Обычная';
$_['entry_transliterate_url']		= 'Url';

$_['entry_attribute']				= 'Атрибуты';
$_['entry_attribute_group']			= 'Группа для сохранения атрибутов';

$_['entry_stock']					= 'Соответствие статусов на складе 1С и OpenCart';
$_['entry_stock0']					= 'В наличии';
$_['entry_stock1']					= 'Предзаказ';
$_['entry_stock2']					= 'Нет в наличии';

$_['entry_lang']					= 'Языки';
$_['entry_other_lang']				= 'Другие языки (не русский)';
$_['entry_translit_name']			= 'Транслитерировать русское название (товары, категории, атрибуты, опции)';
$_['entry_save_other_lang']			= 'Не перезаписывать существующие данные';

$_['entry_additional']				= 'Дополнительно';
$_['entry_subtract']				= 'Вычитать со склада:';
$_['entry_shipping']				= 'Необходима доставка:';
$_['entry_model']					= 'Если модель не заполнена в 1С, записывать в это поле:';
$_['entry_product_name']			= 'Название товара';
$_['entry_sku']						= 'Артикул (SKU)';
$_['entry_update_date_available']	= 'Обновление даты поступления товара при каждой загрузке:';
$_['entry_status_unavailable']		= 'Статус "отключено" для товаров с остатком 0:';

$_['entry_secure_id']				= 'Идентификатор модуля обмена:';
$_['entry_secure_login']			= 'Имя пользователя:';
$_['entry_secure_pswd']				= 'Пароль:';

// Error
$_['error_permission']				= 'У Вас нет прав для управления этим модулем!';
?>