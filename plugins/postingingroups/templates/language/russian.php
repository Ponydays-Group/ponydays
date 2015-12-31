<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Русский языковой файл.
 * Содержит все текстовки движка.
 */
return array(
	'button' => array(
		'token_new' => 'Получить access_token',
		'token_cut' => 'Вырезать access_token'
	),
	'token_ok' => '<h1 style="font-size: 18px; color: green;">Access Token доступен</h1>',
	'token_info' => '<h1 style="font-size: 18px; color: red;">Инструкция</h1>
				    <br>
				    Нажав на кнопку <b>"Получить access_token"</b> Вы попадете на страницу получения доступа:<br />
					<img src="http://cs402425.vk.me/v402425852/58f1/ScijBvvcjV0.jpg" /><br />
					<br />
					На этой странице Вам необходимо подтвердить права, после чего произойдет переадресация на новую страницу.
					Скопируйте адресную строку полностью, вырежте access_token с помощью инструмента ниже и сохраните в настройки плагина',
	'field' => array(
		'publish_in_vk' => array('label' => 'Опубликовать в группу VK', 'note' => 'Если отметить эту галку, то при каждом редактировании или добавлении топика будет создаваться запись в группе'),
		'vk_access_token' => array('label' => 'Ваш ключ доступа', 'note' => 'Ключ доступен только Вам!'),
		'vk_access_token_url' => array('label' => 'Адресная строка содержащая ключ доступа', 'note' => 'Данный ключ необходим для осуществления голосования на сайте с помощью соцсети Вконтакте'),
		'accesss_token_url'  => array('label' => 'Урл содержаний access_token', 'note' => 'Например: https://api.vk.com/blank.html#access_token=b033e1a...2c9340dd873&expires_in=0&user_id=6232785'),
	),
	'error' => array(
		'not_allowed_to_load_images' => 'Недостаточно прав для загрузки изображений в соцсеть',
		'no_access_token' => 'В урле не найден access_token'
	)
);

?>