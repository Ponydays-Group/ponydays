CREATE INDEX prefix_user_user_id_index ON prefix_user (user_id);
CREATE TABLE prefix_notification_type
(
    notification_type_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name text
);
CREATE UNIQUE INDEX prefix_notification_type_notification_type_id_uindex ON prefix_notification_type (notification_type_id);

create table prefix_notification
(
  notification_id   int auto_increment,
  user_id           int                                 not null,
  sender_user_id    int(11)                             NOT NULL,
  date              timestamp default CURRENT_TIMESTAMP not null,
  text              mediumtext                          null,
  title             mediumtext                          null,
  link              mediumtext                          null,
  rating            int default 0                       null,
  rating_result     int default 0                       null,
  notification_type int                                 not null,
  target_type       varchar(256)                        null,
  target_id         int                                 null,
  group_target_type varchar(256)                        null,
  group_target_id   int                                 null,
  constraint prefix_notification_notification_id_uindex
  unique (notification_id),
  constraint prefix_notification_prefix_notification_type_id_fk
  foreign key (notification_type) references prefix_notification_type (notification_type_id)
);

INSERT INTO prefix_notification_type (name) VALUES ('talk_new_topic'),(
                                                    'talk_new_comment'),(
                                                    'comment_response'),(
                                                    'comment_mention'),(
                                                    'topic_new_comment'),(
                                                    'comment_edit'),(
                                                    'comment_delete'),(
                                                    'comment_restore'),(
                                                    'comment_restore_deleted_by_you'),(
                                                    'comment_rank'),(
                                                    'topic_rank'),(
                                                    'topic_invite_ask'),(
                                                    'topic_invite_offer'),(
                                                    'talk_invite_offer'),(
                                                    'ban_in_blog'),(
                                                    'ban_global'),(
                                                    'topic_mention');
ALTER TABLE prefix_notification_type CONVERT TO CHARACTER SET utf8;
ALTER TABLE prefix_notification CONVERT TO CHARACTER SET utf8;