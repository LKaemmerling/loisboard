<?php
$sql = array(

    ######################################
    ######### CREATE TABLES ##############
    ######################################

"CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` text,
  `vorname` text,
  `nachname` text,
  `mail` text,
  `passwort` text,
  `registerTime` int(11) NOT NULL DEFAULT '0',
  `lastLogin` int(11) NOT NULL DEFAULT '0',
  `lastUsernameChange` int(11) NOT NULL DEFAULT '0',
  `signature` text,
  `display_fullname` int(11) NOT NULL DEFAULT '0',
  `hide_in_memberslist` int(11) NOT NULL DEFAULT '0',
  `website` text,
  `points` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `accounts_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `lastCheck` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `theme` int(11) NOT NULL DEFAULT '0',
  `post` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `typ` int(11) NOT NULL DEFAULT '0',
  `gesehen` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `alert_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `alert` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `bilderUpload` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) DEFAULT '0',
  `link` text,
  `avatar` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `startTime` int(11) NOT NULL DEFAULT '0',
  `lastUpdate` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `conversation_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `conversation` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `edits` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `conversation_msg_seen` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `msg` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `conversation_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `conversation` int(11) NOT NULL DEFAULT '0',
  `startTime` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `designs` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `fname` text COLLATE utf8_unicode_ci,
  `active` int(11) NOT NULL DEFAULT '0',
  `autor` text COLLATE utf8_unicode_ci,
  `footer_txt` text COLLATE utf8_unicode_ci,
  `standard` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `foren` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `orderId` int(11) NOT NULL DEFAULT '0',
  `cssclass` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `gruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `enter_administration` int(11) NOT NULL DEFAULT '0',
  `edit_themes` int(11) NOT NULL DEFAULT '0',
  `move_themes` int(11) NOT NULL DEFAULT '0',
  `close_themes` int(11) NOT NULL DEFAULT '0',
  `tag_themes` int(11) NOT NULL DEFAULT '0',
  `edit_posts` int(11) NOT NULL DEFAULT '0',
  `del_posts` int(11) NOT NULL DEFAULT '0',
  `autogain` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `gruppen_foren` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `forum` int(11) NOT NULL DEFAULT '0',
  `gruppe` int(11) NOT NULL DEFAULT '0',
  `permission_see` int(11) NOT NULL DEFAULT '0',
  `permission_write` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `gruppen_kats` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `gruppe` int(11) NOT NULL DEFAULT '0',
  `kategory` int(11) NOT NULL DEFAULT '0',
  `permission_see` int(11) NOT NULL DEFAULT '0',
  `permission_write` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `gruppen_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `gruppe` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `guests_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `guestKey` text COLLATE utf8_unicode_ci,
  `lastCheck` int(11) NOT NULL DEFAULT '0',
  `adress` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `kategorien` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `forum` int(11) NOT NULL DEFAULT '0',
  `kategorie` int(11) NOT NULL DEFAULT '0',
  `orderId` int(11) NOT NULL DEFAULT '0',
  `cssclass` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `log_admin_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` text COLLATE utf8_unicode_ci,
  `ipadress` text COLLATE utf8_unicode_ci,
  `stamp` int(11) NOT NULL DEFAULT '0',
  `success` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `menuePoints` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `orderId` int(11) NOT NULL DEFAULT '0',
  `name` text COLLATE utf8_unicode_ci,
  `pageName` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `online_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `onlines` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `foldername` text COLLATE utf8_unicode_ci,
  `author` text COLLATE utf8_unicode_ci,
  `active` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci,
  `thema` int(11) NOT NULL DEFAULT '0',
  `startTime` int(11) NOT NULL DEFAULT '0',
  `edits` int(11) NOT NULL DEFAULT '0',
  `lastEditor` int(11) NOT NULL DEFAULT '0',
  `lastEditTime` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `deletedUser` int(11) NOT NULL DEFAULT '0',
  `deletedReason` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `post_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post` int(11) NOT NULL DEFAULT '0',
  `file` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `forum_title` text COLLATE utf8_unicode_ci,
  `forum_description` text COLLATE utf8_unicode_ci,
  `page_title` text COLLATE utf8_unicode_ci,
  `defaultLang` text COLLATE utf8_unicode_ci,
  `homelinkPage` text COLLATE utf8_unicode_ci,
  `dok_datenschutz` int(11) NOT NULL DEFAULT '0',
  `dok_datenschutz_txt` text COLLATE utf8_unicode_ci,
  `dok_terms` int(11) NOT NULL DEFAULT '0',
  `dok_terms_txt` text COLLATE utf8_unicode_ci,
  `dok_impressum` int(11) NOT NULL DEFAULT '0',
  `dok_impressum_txt` text COLLATE utf8_unicode_ci,
  `allow_change_design_footer` int(11) NOT NULL DEFAULT '0',
  `allow_change_design` int(11) NOT NULL DEFAULT '0',
  `allow_change_lang` int(11) NOT NULL DEFAULT '0',
  `show_statistic_right` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `stay_logged_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `key1` text COLLATE utf8_unicode_ci,
  `key2` text COLLATE utf8_unicode_ci,
  `stamp` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `useable` int(11) NOT NULL DEFAULT '0',
  `typ` int(11) NOT NULL DEFAULT '0',
  `backgroundcolor` text COLLATE utf8_unicode_ci,
  `textcolor` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `tags_foren` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tag` int(11) NOT NULL DEFAULT '0',
  `forum` int(11) NOT NULL DEFAULT '0',
  `kategory` int(11) NOT NULL DEFAULT '0',
  `autogain` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `tags_gruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tag` int(11) NOT NULL DEFAULT '0',
  `gruppe` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `themen` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `user` int(11) NOT NULL DEFAULT '0',
  `startTime` int(11) NOT NULL DEFAULT '0',
  `lastChange` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `deleteUser` int(11) NOT NULL DEFAULT '0',
  `deleteReason` text COLLATE utf8_unicode_ci,
  `kategorie` int(11) NOT NULL DEFAULT '0',
  `tag` int(11) NOT NULL DEFAULT '0',
  `closed` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `themen_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `thema` int(11) NOT NULL DEFAULT '0',
  `label` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `themes_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `thema` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `themes_seen` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `thema` int(11) NOT NULL DEFAULT '0',
  `stamp` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `user_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` int(11) NOT NULL DEFAULT '0',
  `sender` int(11) NOT NULL DEFAULT '0',
  `text` text COLLATE utf8_unicode_ci,
  `time` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `user_profile_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `post` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci,
  `time` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `user_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` text COLLATE utf8_unicode_ci,
  `show_picture` int(11) NOT NULL DEFAULT '0',
  `picture` text COLLATE utf8_unicode_ci,
  `bgcol` text COLLATE utf8_unicode_ci,
  `textcol` text COLLATE utf8_unicode_ci,
  `priority` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
"CREATE TABLE IF NOT EXISTS `user_ranks_gains` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `rank` int(11) NOT NULL DEFAULT '0',
  `gain_posts` int(11) NOT NULL DEFAULT '0',
  `gain_group` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

    ######################################
    ######### INSERT DATA ################
    ######################################

# Standard Settings
"INSERT INTO `settings` (forum_title, forum_description, page_title, defaultLang, homelinkPage, show_statistic_right) VALUES ('LoisBoard', 'Willkommen in deinem LoisBoard!', 'LoisBoard', 'de', 'board', '1')",
# Default Forum/Kategorie
"INSERT INTO `foren` (name, description) VALUES ('Default Board', 'This is the default board. You can delete or change this in the administration.')",
"INSERT INTO `kategorien` (name, description, forum) VALUES ('Default', 'This is the default kategory.', '1')",
# Admin Benutzergruppe
"INSERT INTO `gruppen` (name, enter_administration, edit_themes, move_themes, close_themes, tag_themes, edit_posts, del_posts) VALUES ('Administrators', '1', '1', '1', '1', '1', '1', '1')",
"INSERT INTO `gruppen_user` (user, gruppe) VALUES ('1', '1')",
# Menüpunkte 
"INSERT INTO `menuePoints` (name, pageName) VALUES ('Forum', 'board')",
"INSERT INTO `menuePoints` (name, pageName) VALUES ('Mitglieder', 'members')"




);
?>