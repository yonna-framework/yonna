CREATE TABLE y_i18n
(
    unique_key text PRIMARY KEY NOT NULL DEFAULT '',
    zh_cn      text             NOT NULL DEFAULT '',
    zh_hk      text             NOT NULL DEFAULT '',
    zh_tw      text             NOT NULL DEFAULT '',
    en_us      text             NOT NULL DEFAULT '',
    ja_jp      text             NOT NULL DEFAULT '',
    ko_kr      text             NOT NULL DEFAULT ''
);
comment on table y_i18n is 'yonna i18n';
comment on column y_i18n.unique_key is '验证key';
comment on column y_i18n.zh_cn is '简体中文';
comment on column y_i18n.zh_hk is '香港繁体';
comment on column y_i18n.zh_tw is '台湾繁体';
comment on column y_i18n.en_us is '美国英语';
comment on column y_i18n.ja_jp is '日本语';
comment on column y_i18n.ko_kr is '韩国语';

CREATE TABLE y_log
(
    id               bigserial PRIMARY KEY NOT NULL,
    key              text                  NOT NULL DEFAULT 'default',
    type             text                  NOT NULL DEFAULT 'info',
    record_timestamp integer               NOT NULL CHECK ( y_log.record_timestamp >= 0 ),
    data             jsonb
);
comment on table y_log is 'yonna log';
comment on column y_log.id is 'id';
comment on column y_log.key is 'key';
comment on column y_log.type is '类型';
comment on column y_log.record_timestamp is '当记录时间戳';
comment on column y_log.data is 'data';
