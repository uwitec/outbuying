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

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `kw_content` VALUES ('8', '6', '9', '3', '0', '0', null, null, '127.0.0.1', '阿嫂大', '1200153655', '1202807929');
INSERT INTO `kw_content` VALUES ('9', '6', '3', '2', '1', '0', null, null, '127.0.0.1', '萨达', '1200153699', '1202225050');
INSERT INTO `kw_content` VALUES ('10', '7', '4', '2', '0', '0', null, null, '127.0.0.1', '阿斗撒旦', '1200153708', '1201877488');
INSERT INTO `kw_content` VALUES ('11', '8', '0', '0', '0', '0', null, null, '127.0.0.1', 'adasdsf', '1200227181', '1200227181');
INSERT INTO `kw_content` VALUES ('12', '6', '0', '0', '0', '0', null, null, '127.0.0.1', null, '1201432010', '1201432010');
INSERT INTO `kw_content` VALUES ('13', '9', '-3', '0', '1', '0', null, null, '127.0.0.1', 'ASDASD', '1201432030', '1201432049');
INSERT INTO `kw_content` VALUES ('14', '6', '0', '0', '0', '0', null, null, '127.0.0.1', '测试换行\r\n换行', '1202045232', '1202045232');
INSERT INTO `kw_content` VALUES ('15', '6', '0', '0', '0', '0', 'asf', null, '127.0.0.1', 'sadf', '1202220429', '1202220429');
INSERT INTO `kw_content` VALUES ('16', '6', '0', '0', '0', '0', 'asd', null, '127.0.0.1', 'asd', '1202220645', '1202220645');
INSERT INTO `kw_content` VALUES ('17', '6', '0', '0', '0', '0', null, null, '127.0.0.1', 'dfsfg', '1202220667', '1202220667');
INSERT INTO `kw_content` VALUES ('18', '10', '3', '1', '0', '0', null, null, '192.168.0.1', '暂时没有内容', '1217681935', '1217681944');
INSERT INTO `kw_content` VALUES ('19', '10', '0', '0', '0', '0', null, null, '192.168.0.1', '暂时没有内容', '1217681939', '1217681939');
INSERT INTO `kw_keywords` VALUES ('6', '测试一下', null, '92', '13', '1200153629', '1237290305');
INSERT INTO `kw_keywords` VALUES ('7', 'PHP', null, '16', '3', '1200153704', '1217681976');
INSERT INTO `kw_keywords` VALUES ('8', 'asdasfasd', null, '4', '1', '1200227177', '1217681974');
INSERT INTO `kw_keywords` VALUES ('9', 'WHAT?', null, '3', '2', '1201432023', '1237206310');
INSERT INTO `kw_keywords` VALUES ('10', '测试一下2', null, '3', '0', '1217681930', '1237206313');
INSERT INTO `uc_memberfields` VALUES ('1', '');
INSERT INTO `uc_memberfields` VALUES ('2', '');
INSERT INTO `uc_members` VALUES ('1', 'y31x', 'd0e84aee73d69411040053d466973e69', 'y31x@163.com', '', '', '192.168.0.1', '1233491470', '0', '0', 'e5fd69', '');
INSERT INTO `uc_members` VALUES ('2', 'y31', 'bff8792bc13568526ff353d4cdcd298d', 'y31x@163.com', '', '', '192.168.0.89', '1237209876', '0', '0', '48b26e', '');
INSERT INTO `uc_notelist` VALUES ('1', 'updateclient', '1', '0', '0', '', '<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n <item id=\"accessemail\"><![CDATA[]]></item>\r\n <item id=\"censoremail\"><![CDATA[]]></item>\r\n <item id=\"censorusername\"><![CDATA[]]></item>\r\n <item id=\"dateformat\"><![CDATA[y-n-j]]></item>\r\n <item id=\"doublee\"><![CDATA[1]]></item>\r\n <item id=\"timeoffset\"><![CDATA[28800]]></item>\r\n <item id=\"pmlimit1day\"><![CDATA[100]]></item>\r\n <item id=\"pmfloodctrl\"><![CDATA[15]]></item>\r\n <item id=\"pmcenter\"><![CDATA[1]]></item>\r\n <item id=\"sendpmseccode\"><![CDATA[1]]></item>\r\n <item id=\"pmsendregdays\"><![CDATA[0]]></item>\r\n <item id=\"maildefault\"><![CDATA[username@21cn.com]]></item>\r\n <item id=\"mailsend\"><![CDATA[1]]></item>\r\n <item id=\"mailserver\"><![CDATA[smtp.21cn.com]]></item>\r\n <item id=\"mailport\"><![CDATA[25]]></item>\r\n <item id=\"mailauth\"><![CDATA[1]]></item>\r\n <item id=\"mailfrom\"><![CDATA[UCenter <username@21cn.com>]]></item>\r\n <item id=\"mailauth_username\"><![CDATA[username@21cn.com]]></item>\r\n <item id=\"mailauth_password\"><![CDATA[password]]></item>\r\n <item id=\"maildelimiter\"><![CDATA[0]]></item>\r\n <item id=\"mailusername\"><![CDATA[1]]></item>\r\n <item id=\"mailsilent\"><![CDATA[1]]></item>\r\n <item id=\"timeformat\"><![CDATA[H:i]]></item>\r\n</root>', '0', '0');
INSERT INTO `uc_settings` VALUES ('accessemail', '');
INSERT INTO `uc_settings` VALUES ('censoremail', '');
INSERT INTO `uc_settings` VALUES ('censorusername', '');
INSERT INTO `uc_settings` VALUES ('dateformat', 'y-n-j');
INSERT INTO `uc_settings` VALUES ('doublee', '1');
INSERT INTO `uc_settings` VALUES ('nextnotetime', '0');
INSERT INTO `uc_settings` VALUES ('timeoffset', '28800');
INSERT INTO `uc_settings` VALUES ('pmlimit1day', '100');
INSERT INTO `uc_settings` VALUES ('pmfloodctrl', '15');
INSERT INTO `uc_settings` VALUES ('pmcenter', '1');
INSERT INTO `uc_settings` VALUES ('sendpmseccode', '1');
INSERT INTO `uc_settings` VALUES ('pmsendregdays', '0');
INSERT INTO `uc_settings` VALUES ('maildefault', 'username@21cn.com');
INSERT INTO `uc_settings` VALUES ('mailsend', '1');
INSERT INTO `uc_settings` VALUES ('mailserver', 'smtp.21cn.com');
INSERT INTO `uc_settings` VALUES ('mailport', '25');
INSERT INTO `uc_settings` VALUES ('mailauth', '1');
INSERT INTO `uc_settings` VALUES ('mailfrom', 'UCenter <username@21cn.com>');
INSERT INTO `uc_settings` VALUES ('mailauth_username', 'username@21cn.com');
INSERT INTO `uc_settings` VALUES ('mailauth_password', 'password');
INSERT INTO `uc_settings` VALUES ('maildelimiter', '0');
INSERT INTO `uc_settings` VALUES ('mailusername', '1');
INSERT INTO `uc_settings` VALUES ('mailsilent', '1');
INSERT INTO `uc_settings` VALUES ('version', '1.5.0');
INSERT INTO `uc_settings` VALUES ('timeformat', 'H:i');
