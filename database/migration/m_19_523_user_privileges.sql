/**
 * Создает битовый столбец для хранения прав пользователя.
 * Измените это поле для пользователей из config(moderator) и config(quotes_admin) соответствующе.
 * Значения битов описаны в классе ModuleUser.
 * Уберите поля config(moderator) и config(quotes_admin) из конфига после, так как они бесполезны в дальнейшем.
 */

ALTER TABLE prefix_user ADD COLUMN user_perms BIT(8) NOT NULL DEFAULT b'00000000';
