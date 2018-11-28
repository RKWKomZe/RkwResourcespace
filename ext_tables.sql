#
# Table structure for table 'tx_rkwresourcespace_domain_model_import'
#
CREATE TABLE tx_rkwresourcespace_domain_model_import (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	resource_space_image_id int(11) unsigned DEFAULT '0' NOT NULL,
	resource_space_user_id int(11) unsigned DEFAULT '0' NOT NULL,
	resource_space_user_name varchar(255) DEFAULT '' NOT NULL,
	resource_space_user_real_name varchar(255) DEFAULT '' NOT NULL,
	file varchar(255) DEFAULT '' NOT NULL,
	backend_user varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

);
