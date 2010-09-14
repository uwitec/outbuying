/*
MySQL Data Transfer
Source Host: 192.168.0.89
Source Database: rofohoco_kwords
Target Host: 192.168.0.89
Target Database: rofohoco_kwords
Date: 2009-3-17 20:42:57
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for kw_content
-- ----------------------------
CREATE TABLE `kw_content` (
  `ct_id` int(11) NOT NULL auto_increment,
  `kws_id` int(11) default '0',
  `ct_score` int(11) default '0',
  `ct_agree` int(11) default '0',
  `ct_oppose` int(11) default '0',
  `ct_stimes` int(11) default '0',
  `ct_adduser` varchar(32) default NULL,
  `ct_email` varchar(64) default NULL,
  `ct_ip` char(15) default NULL,
  `ct_content` text COMMENT '分解，排序后重新组合',
  `created_at` int(11) default NULL,
  `updated_at` int(11) default NULL,
  PRIMARY KEY  (`ct_id`),
  KEY `idx_ct_stimes` (`ct_stimes`),
  KEY `FK_Reference_3` (`kws_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for kw_keyword
-- ----------------------------
CREATE TABLE `kw_keyword` (
  `kw_id` int(11) NOT NULL auto_increment,
  `kw_word` varchar(64) NOT NULL,
  `kw_stimes` int(11) default '0',
  `created_at` int(11) default NULL,
  `updated_at` int(11) default NULL,
  PRIMARY KEY  (`kw_id`),
  KEY `idx_kw_stimes` (`kw_stimes`),
  KEY `idx_kw_word` (`kw_word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for kw_keywords
-- ----------------------------
CREATE TABLE `kw_keywords` (
  `kws_id` int(11) NOT NULL auto_increment,
  `kws_words` varchar(128) NOT NULL COMMENT '分解，排序后重新组合',
  `kws_hash` char(32) default NULL,
  `kws_stimes` int(11) default '0',
  `kws_ct_count` int(11) default '0',
  `created_at` int(11) default NULL,
  `updated_at` int(11) default NULL,
  PRIMARY KEY  (`kws_id`),
  KEY `idx_kw_stimes` (`kws_stimes`),
  KEY `idx_kws_words` (`kws_words`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for kw_kw_kws_rel
-- ----------------------------
CREATE TABLE `kw_kw_kws_rel` (
  `id` int(11) NOT NULL auto_increment,
  `kw_id` int(11) default '0',
  `kws_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `Index_2` (`kw_id`),
  KEY `Index_3` (`kws_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for moneyfund_analyse_mark
-- ----------------------------
CREATE TABLE `moneyfund_analyse_mark` (
  `mf_date` char(10) NOT NULL default '1900-1-1',
  `mf_income_rank_mark` tinyint(1) NOT NULL default '0',
  `mf_yield_rank_mark` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`mf_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for moneyfund_info
-- ----------------------------
CREATE TABLE `moneyfund_info` (
  `mf_code` varchar(64) NOT NULL,
  `mf_name` varchar(255) default NULL,
  `mf_fullname` varchar(255) default NULL,
  `fund_company_code` varchar(64) default NULL,
  `mf_intro` text,
  PRIMARY KEY  (`mf_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for moneyfund_origin
-- ----------------------------
CREATE TABLE `moneyfund_origin` (
  `mf_id` int(11) NOT NULL auto_increment,
  `mf_date` date default NULL COMMENT 'ÈÕÆÚ',
  `mf_code` varchar(64) default NULL COMMENT '»ù½ð´úÂë',
  `mf_name` varchar(255) default NULL COMMENT '»ù½ð¼ò³Æ',
  `mf_day_income_wan` decimal(10,4) default NULL COMMENT 'ÈÕÊÕÒæ(Ôª/Íò·Ý)',
  `mf_year_yield_7day` decimal(10,6) default NULL,
  `mf_day_income` decimal(10,8) default NULL,
  `mf_income_rank` int(11) default NULL,
  `mf_yield_rank` int(11) default NULL,
  `mf_synthesis_rank` int(11) NOT NULL default '0',
  `created_at` int(11) default NULL,
  `updated_at` int(11) default NULL,
  PRIMARY KEY  (`mf_id`),
  KEY `Index_mf_code` (`mf_code`),
  KEY `Index_mf_date` (`mf_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for openfund_analyse_mark
-- ----------------------------
CREATE TABLE `openfund_analyse_mark` (
  `of_date` char(10) NOT NULL default '1900-1-1',
  `of_net_worth_rank_mark` tinyint(1) NOT NULL default '0',
  `of_inc_rate_rank_mark` tinyint(1) NOT NULL default '0',
  `of_total_worth_rank_mark` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`of_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for openfund_info
-- ----------------------------
CREATE TABLE `openfund_info` (
  `of_code` varchar(64) NOT NULL,
  `of_name` varchar(255) default NULL,
  `of_fullname` varchar(255) default NULL,
  `fund_company_code` varchar(64) default NULL,
  `of_intro` text,
  PRIMARY KEY  (`of_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for openfund_origin
-- ----------------------------
CREATE TABLE `openfund_origin` (
  `of_id` int(11) NOT NULL auto_increment,
  `of_date` date default NULL,
  `of_code` varchar(64) default NULL,
  `of_name` varchar(255) default NULL,
  `of_net_worth` decimal(10,4) default NULL COMMENT 'µ¥Î»¾»Öµ\r\n            (TÈÕ,Ôª)',
  `of_net_worth_t1` decimal(10,4) default NULL COMMENT 'µ¥Î»¾»Öµ\r\n            (T-1ÈÕ,Ôª)',
  `of_increase_rate` decimal(10,6) default NULL,
  `of_total_worth` decimal(10,4) default NULL,
  `of_net_worth_rank` int(11) default NULL,
  `of_increase_rate_rank` int(11) default NULL,
  `of_total_worth_rank` int(11) default NULL,
  `of_synthesis_rank` int(11) NOT NULL default '0',
  `updated_at` int(11) default NULL,
  `created_at` int(11) default NULL,
  PRIMARY KEY  (`of_id`),
  KEY `Index_of_name` (`of_code`),
  KEY `Index_of_date` (`of_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_admins
-- ----------------------------
CREATE TABLE `uc_admins` (
  `uid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` char(15) NOT NULL default '',
  `allowadminsetting` tinyint(1) NOT NULL default '0',
  `allowadminapp` tinyint(1) NOT NULL default '0',
  `allowadminuser` tinyint(1) NOT NULL default '0',
  `allowadminbadword` tinyint(1) NOT NULL default '0',
  `allowadmintag` tinyint(1) NOT NULL default '0',
  `allowadminpm` tinyint(1) NOT NULL default '0',
  `allowadmincredits` tinyint(1) NOT NULL default '0',
  `allowadmindomain` tinyint(1) NOT NULL default '0',
  `allowadmindb` tinyint(1) NOT NULL default '0',
  `allowadminnote` tinyint(1) NOT NULL default '0',
  `allowadmincache` tinyint(1) NOT NULL default '0',
  `allowadminlog` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_applications
-- ----------------------------
CREATE TABLE `uc_applications` (
  `appid` smallint(6) unsigned NOT NULL auto_increment,
  `type` char(16) NOT NULL default '',
  `name` char(20) NOT NULL default '',
  `url` char(255) NOT NULL default '',
  `authkey` char(255) NOT NULL default '',
  `ip` char(15) NOT NULL default '',
  `viewprourl` char(255) NOT NULL,
  `apifilename` char(30) NOT NULL default 'uc.php',
  `charset` char(8) NOT NULL default '',
  `dbcharset` char(8) NOT NULL default '',
  `synlogin` tinyint(1) NOT NULL default '0',
  `recvnote` tinyint(1) default '0',
  `extra` mediumtext NOT NULL,
  `tagtemplates` mediumtext NOT NULL,
  PRIMARY KEY  (`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_badwords
-- ----------------------------
CREATE TABLE `uc_badwords` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `admin` varchar(15) NOT NULL default '',
  `find` varchar(255) NOT NULL default '',
  `replacement` varchar(255) NOT NULL default '',
  `findpattern` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `find` (`find`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_domains
-- ----------------------------
CREATE TABLE `uc_domains` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `domain` char(40) NOT NULL default '',
  `ip` char(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_failedlogins
-- ----------------------------
CREATE TABLE `uc_failedlogins` (
  `ip` char(15) NOT NULL default '',
  `count` tinyint(1) unsigned NOT NULL default '0',
  `lastupdate` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_feeds
-- ----------------------------
CREATE TABLE `uc_feeds` (
  `feedid` mediumint(8) unsigned NOT NULL auto_increment,
  `appid` varchar(30) NOT NULL default '',
  `icon` varchar(30) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `hash_template` varchar(32) NOT NULL default '',
  `hash_data` varchar(32) NOT NULL default '',
  `title_template` text NOT NULL,
  `title_data` text NOT NULL,
  `body_template` text NOT NULL,
  `body_data` text NOT NULL,
  `body_general` text NOT NULL,
  `image_1` varchar(255) NOT NULL default '',
  `image_1_link` varchar(255) NOT NULL default '',
  `image_2` varchar(255) NOT NULL default '',
  `image_2_link` varchar(255) NOT NULL default '',
  `image_3` varchar(255) NOT NULL default '',
  `image_3_link` varchar(255) NOT NULL default '',
  `image_4` varchar(255) NOT NULL default '',
  `image_4_link` varchar(255) NOT NULL default '',
  `target_ids` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`feedid`),
  KEY `uid` (`uid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_friends
-- ----------------------------
CREATE TABLE `uc_friends` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `friendid` mediumint(8) unsigned NOT NULL default '0',
  `direction` tinyint(1) NOT NULL default '0',
  `version` int(10) unsigned NOT NULL auto_increment,
  `delstatus` tinyint(1) NOT NULL default '0',
  `comment` char(255) NOT NULL default '',
  PRIMARY KEY  (`version`),
  KEY `uid` (`uid`),
  KEY `friendid` (`friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_mailqueue
-- ----------------------------
CREATE TABLE `uc_mailqueue` (
  `mailid` int(10) unsigned NOT NULL auto_increment,
  `touid` mediumint(8) unsigned NOT NULL default '0',
  `tomail` varchar(32) NOT NULL,
  `frommail` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `charset` varchar(15) NOT NULL,
  `htmlon` tinyint(1) NOT NULL default '0',
  `level` tinyint(1) NOT NULL default '1',
  `dateline` int(10) unsigned NOT NULL default '0',
  `failures` tinyint(3) unsigned NOT NULL default '0',
  `appid` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`mailid`),
  KEY `appid` (`appid`),
  KEY `level` (`level`,`failures`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_memberfields
-- ----------------------------
CREATE TABLE `uc_memberfields` (
  `uid` mediumint(8) unsigned NOT NULL,
  `blacklist` text NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_members
-- ----------------------------
CREATE TABLE `uc_members` (
  `uid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` char(15) NOT NULL default '',
  `password` char(32) NOT NULL default '',
  `email` char(32) NOT NULL default '',
  `myid` char(30) NOT NULL default '',
  `myidkey` char(16) NOT NULL default '',
  `regip` char(15) NOT NULL default '',
  `regdate` int(10) unsigned NOT NULL default '0',
  `lastloginip` int(10) NOT NULL default '0',
  `lastlogintime` int(10) unsigned NOT NULL default '0',
  `salt` char(6) NOT NULL,
  `secques` char(8) NOT NULL default '',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_mergemembers
-- ----------------------------
CREATE TABLE `uc_mergemembers` (
  `appid` smallint(6) unsigned NOT NULL,
  `username` char(15) NOT NULL,
  PRIMARY KEY  (`appid`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_newpm
-- ----------------------------
CREATE TABLE `uc_newpm` (
  `uid` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_notelist
-- ----------------------------
CREATE TABLE `uc_notelist` (
  `noteid` int(10) unsigned NOT NULL auto_increment,
  `operation` char(32) NOT NULL,
  `closed` tinyint(4) NOT NULL default '0',
  `totalnum` smallint(6) unsigned NOT NULL default '0',
  `succeednum` smallint(6) unsigned NOT NULL default '0',
  `getdata` mediumtext NOT NULL,
  `postdata` mediumtext NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `pri` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`noteid`),
  KEY `closed` (`closed`,`pri`,`noteid`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_pms
-- ----------------------------
CREATE TABLE `uc_pms` (
  `pmid` int(10) unsigned NOT NULL auto_increment,
  `msgfrom` varchar(15) NOT NULL default '',
  `msgfromid` mediumint(8) unsigned NOT NULL default '0',
  `msgtoid` mediumint(8) unsigned NOT NULL default '0',
  `folder` enum('inbox','outbox') NOT NULL default 'inbox',
  `new` tinyint(1) NOT NULL default '0',
  `subject` varchar(75) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `related` int(10) unsigned NOT NULL default '0',
  `fromappid` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `msgtoid` (`msgtoid`,`folder`,`dateline`),
  KEY `msgfromid` (`msgfromid`,`folder`,`dateline`),
  KEY `related` (`related`),
  KEY `getnum` (`msgtoid`,`folder`,`delstatus`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_protectedmembers
-- ----------------------------
CREATE TABLE `uc_protectedmembers` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `appid` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `admin` char(15) NOT NULL default '0',
  UNIQUE KEY `username` (`username`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_settings
-- ----------------------------
CREATE TABLE `uc_settings` (
  `k` varchar(32) NOT NULL default '',
  `v` text NOT NULL,
  PRIMARY KEY  (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_sqlcache
-- ----------------------------
CREATE TABLE `uc_sqlcache` (
  `sqlid` char(6) NOT NULL default '',
  `data` char(100) NOT NULL,
  `expiry` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`sqlid`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_tags
-- ----------------------------
CREATE TABLE `uc_tags` (
  `tagname` char(20) NOT NULL,
  `appid` smallint(6) unsigned NOT NULL default '0',
  `data` mediumtext,
  `expiration` int(10) unsigned NOT NULL,
  KEY `tagname` (`tagname`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for uc_vars
-- ----------------------------
CREATE TABLE `uc_vars` (
  `name` char(32) NOT NULL default '',
  `value` char(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

