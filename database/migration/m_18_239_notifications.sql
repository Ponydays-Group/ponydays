CREATE INDEX prefix_user_user_id_index ON prefix_user (user_id);
CREATE TABLE prefix_notification_type
(
    notification_type_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name text
);
CREATE UNIQUE INDEX prefix_notification_type_notification_type_id_uindex ON prefix_notification_type (notification_type_id);

CREATE TABLE prefix_notification
(
    notification_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    date timestamp DEFAULT current_timestamp,
    text text,
    title text,
    link text,
    rating int,
    notification_type int(11) NOT NULL,
    CONSTRAINT prefix_notification_prefix_notification_type_id_fk FOREIGN KEY (notification_type) REFERENCES prefix_notification_type (notification_type_id)
);
CREATE UNIQUE INDEX prefix_notification_notification_id_uindex ON prefix_notification (notification_id);

ALTER TABLE prefix_notification ADD target_type varchar(256) NULL;
ALTER TABLE prefix_notification ADD target_id int NULL;

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
                                                    'ban_global');
ALTER TABLE prefix_notification_type CONVERT TO CHARACTER SET utf8;
ALTER TABLE prefix_notification CONVERT TO CHARACTER SET utf8;