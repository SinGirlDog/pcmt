DROP TABLE IF EXISTS `v9_exam_paper`;

CREATE TABLE IF NOT EXISTS `v9_exam_paper` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `siteid` smallint(5) NOT NULL,
  `fileid` smallint(5) NOT NULL,
  `arrparentid` varchar(255) NOT NULL,
  `name` varchar(20) NOT NULL,
  `mobile` char(11) NOT NULL,
  `title` char(80) NOT NULL,
  `quest_choice_only` text NOT NULL,
  `quest_choice_more` text NOT NULL,
  `quest_fillinblank` text NOT NULL,
  `quest_objective` text NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` char(20) NOT NULL,
  `isdelete` char(1) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `v9_exam_answer`;

CREATE TABLE IF NOT EXISTS `v9_exam_answer` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `paper_id` smallint(5) NOT NULL,
  `siteid` smallint(5) NOT NULL,
  `fileid` smallint(5) NOT NULL,
  `title` char(80) NOT NULL,
  `name` varchar(20) NOT NULL,
  `mobile` char(11) NOT NULL,
  `answer_choice_only` text NOT NULL,
  `answer_choice_more` text NOT NULL,
  `answer_fillinblank` text NOT NULL,
  `answer_objective` text NOT NULL,
  `fenshu_choice_only` smallint(3) NOT NULL,
  `fenshu_choice_more` smallint(3) NOT NULL,
  `fenshu_fillinblank` smallint(3) NOT NULL,
  `fenshu_objective` smallint(3) NOT NULL,
  `fenshu_total` smallint(3) NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` char(20) NOT NULL,
  `isdelete` char(1) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `v9_exam_qanda`;

CREATE TABLE IF NOT EXISTS `v9_exam_qanda` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) NOT NULL,
  `siteid` smallint(5) NOT NULL,
  `title` char(80) NOT NULL,
  `num_choice_only` smallint(3) NOT NULL,
  `num_choice_more` smallint(3) NOT NULL,
  `num_fillinblank` smallint(3) NOT NULL,
  `num_objective` smallint(3) NOT NULL,
  `fenshu_choice_only` smallint(3) NOT NULL,
  `fenshu_choice_more` smallint(3) NOT NULL,
  `fenshu_fillinblank` smallint(3) NOT NULL,
  `fenshu_objective` smallint(3) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `v9_exam_file`;

CREATE TABLE IF NOT EXISTS `v9_exam_file` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) NOT NULL,
  `arrcatid` varchar(255) NOT NULL,
  `siteid` smallint(5) NOT NULL,
  `title` char(80) NOT NULL,
  `thumb` char(100) NOT NULL,
  `quest_ids` text NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `isdelete` char(1) NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `v9_exam_visitor`;

CREATE TABLE IF NOT EXISTS `v9_exam_visitor` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  `mobile` char(13) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `visittime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;
