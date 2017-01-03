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
 * Регистрация хука для вывода ссылки на каталог плагинов
 *
 * @package hooks
 * @since 1.0
 */
class HookCatalog extends Hook {
	/**
	 * Регистрируем хуки
	 */
	public function RegisterHook() {
		$this->AddHook('template_admin_action_item','Catalog',__CLASS__,-100);
	}
	/**
	 * Обработка хука
	 *
	 * @return string
	 */
	public function Catalog() {
		$s='<li style="font-size: 16px;color: #f00;"><br/>  Найти различные плагины можно в нашем каталоге &mdash; <a href="https://catalog.livestreetcms.com/addon/?utm_source=admin&utm_medium=link&utm_campaign=catalog">http://catalog.livestreetcms.com</a>  <br/></li>';
		$s.='<li style="font-size: 16px;color: green;margin-top: 10px;">Хочешь удалить копирайты LiveStreet?! Просто  <a href="https://catalog.livestreetcms.com/addon/view/352/?utm_source=admin&utm_medium=link&utm_campaign=donate">сделай Donate!</a></li>';

		return $s;
	}
}
?>