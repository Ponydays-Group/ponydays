/**
 * Создает битовый столбец для хранения прав пользователя
 */

ALTER TABLE prefix_user ADD COLUMN user_perms BIT(8) NOT NULL DEFAULT b'00000000';
