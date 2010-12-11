--
-- MySQL 5.0.51a
-- Sun, 05 Dec 2010 20:41:53 +0000
--

CREATE DATABASE `boev` DEFAULT CHARSET cp1251;

USE `boev`;

CREATE TABLE `cm_comments` (
   `id` char(36) not null,
   `username` varchar(32) CHARSET ascii not null,
   `added` timestamp not null default CURRENT_TIMESTAMP,
   `text` text not null,
   PRIMARY KEY (`id`),
   KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- [Table `cm_comments` is empty]

CREATE TABLE `cm_sp_addons` (
   `id` char(36) not null,
   `page` text not null,
   `options` tinytext not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- [Table `cm_sp_addons` is empty]

CREATE TABLE `cm_sp_images` (
   `id` char(36) not null,
   `filename` tinytext not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- [Table `cm_sp_images` is empty]

CREATE TABLE `cm_sp_links` (
   `id` char(36) not null,
   `link` text not null,
   `target` set('internal','external') not null default 'external',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- [Table `cm_sp_links` is empty]

CREATE TABLE `cm_sp_math` (
   `id` char(36) CHARSET ascii not null,
   `attach_filename_bg` text not null,
   `attach_filename_en` text CHARSET ascii not null,
   `use_text_bg_for_all` tinyint(1) unsigned not null default '1',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `cm_sp_pages` (
   `id` char(36) not null,
   `text_bg` mediumtext not null,
   `text_en` mediumtext CHARSET ascii not null,
   `auto_generated` enum('none','brief','thumbnails','full','slideshow') not null default 'none',
   `use_text_bg_for_all` tinyint(1) not null default '1',
   `subpage_count` smallint(5) unsigned not null default '0',
   `sort_by` enum('position','caption','date') not null default 'position',
   `sort_direction` enum('asc','desc') not null default 'asc',
   PRIMARY KEY (`id`),
   KEY `text_uk` (`text_en`),
   KEY `text_bg` (`text_bg`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `cm_struct_main` (
   `id` char(36) not null,
   `parent` char(36) not null default 'menu',
   `del_parent` char(36),
   `position` smallint(5) unsigned not null,
   `mode` enum('deleted_follow','deleted','inactive','invisible','normal','under_upgrade','under_upgrade_smart') not null default 'normal',
   `type` enum('page','image','addon','link','math') not null default 'page',
   `caption_bg` tinytext not null,
   `caption_en` tinytext CHARSET ascii not null,
   `descr_bg` tinytext not null,
   `descr_en` tinytext not null,
   `size` int(11) not null default '0',
   `delimiter` tinyint(1) not null default '0',
   `modified` timestamp not null default CURRENT_TIMESTAMP,
   `lang_visibility` set('bg','en') default 'bg,en',
   PRIMARY KEY (`id`),
   KEY `parent` (`parent`),
   KEY `del_parent` (`del_parent`),
   KEY `caption_bg` (`caption_bg`),
   KEY `caption_en` (`caption_en`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `cm_struct_search` (
   `id` char(36) not null,
   `searchable` tinyint(1) not null default '1',
   `keywords_bg` text not null,
   `keywords_en` text CHARSET ascii not null,
   PRIMARY KEY (`id`),
   KEY `keywords_bg` (`keywords_bg`),
   KEY `keywords_uk` (`keywords_en`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `cm_struct_view` (
   `id` char(36) not null,
   `subscribed` text not null,
   `registered_only` tinyint(1) not null default '0',
   `can_comment` tinyint(1) not null default '0',
   `show_name` tinyint(1) not null default '1',
   `show_back_link` tinyint(1) not null default '1',
   `show_end_bar` tinyint(1) not null default '1',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `settings` (
   `name` varchar(16) not null,
   `value` varchar(256) CHARSET ascii,
   PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `um_activation` (
   `id` varchar(36) not null,
   `username` varchar(32) CHARSET ascii not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- [Table `um_activation` is empty]

CREATE TABLE `um_details` (
   `username` varchar(32) CHARSET ascii not null,
   `name` text not null,
   `title` text not null,
   `message` text not null,
   `aim` text not null,
   PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `um_details` (`username`, `name`, `title`, `message`, `aim`) VALUES 
('admin', 'Administrator', 'administrator', '', '');

CREATE TABLE `um_main` (
   `username` varchar(32) CHARSET ascii not null,
   `password` varchar(32) CHARSET ascii not null,
   `email` text CHARSET ascii not null,
   `credentials` set('admin','editor','viewer') not null,
   `active` tinyint(1) not null default '0',
   PRIMARY KEY (`username`),
   KEY `password` (`password`),
   KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `um_main` (`username`, `password`, `email`, `credentials`, `active`) VALUES 
('admin', 'password', 'galileostudios@gmail.com', 'admin', '1');