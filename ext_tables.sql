#
# Table structure for table 'tx_onetimesecret_domain_model_secret'
#
#
CREATE TABLE tx_onetimesecret_domain_model_onetimesecret
(
    uid              int(11)                   NOT NULL auto_increment,
    pid              int(11)      DEFAULT '0'  NOT NULL,
    tstamp           int(11)      DEFAULT '0'  NOT NULL,
    crdate           int(11)      DEFAULT '0'  NOT NULL,
    cruser_id        int(11)      DEFAULT '0'  NOT NULL,
    t3ver_oid        int(11)      DEFAULT '0'  NOT NULL,
    t3ver_id         int(11)      DEFAULT '0'  NOT NULL,
    t3ver_wsid       int(11)      DEFAULT '0'  NOT NULL,
    t3ver_label      varchar(30)  DEFAULT ''   NOT NULL,
    t3ver_state      tinyint(4)   DEFAULT '0'  NOT NULL,
    t3ver_stage      tinyint(4)   DEFAULT '0'  NOT NULL,
    t3ver_count      int(11)      DEFAULT '0'  NOT NULL,
    t3ver_tstamp     int(11)      DEFAULT '0'  NOT NULL,
    t3ver_move_id    int(11)      DEFAULT '0'  NOT NULL,
    t3_origuid       int(11)      DEFAULT '0'  NOT NULL,
    editlock         tinyint(4)   DEFAULT '0'  NOT NULL,
    sys_language_uid int(11)      DEFAULT '0'  NOT NULL,
    l10n_parent      int(11)      DEFAULT '0'  NOT NULL,
    l10n_diffsource  mediumtext,
    l10n_source      int(11)      DEFAULT '0'  NOT NULL,
    deleted          tinyint(4)   DEFAULT '0'  NOT NULL,
    hidden           tinyint(4)   DEFAULT '0'  NOT NULL,
    starttime        int(11)      DEFAULT '0'  NOT NULL,
    endtime          int(11)      DEFAULT '0'  NOT NULL,
    sorting          int(11)      DEFAULT '0'  NOT NULL,

    token            varchar(255) DEFAULT Null NULL,
    secret           TEXT  DEFAULT Null NULL,
    valid_until      int(11)      DEFAULT '0'  NOT NULL,
    last_hit                    int(11)             DEFAULT '0'  NOT NULL,
    hit_number                  int(11)             DEFAULT '0'  NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY sys_language_uid_l10n_parent (sys_language_uid, l10n_parent)

);


