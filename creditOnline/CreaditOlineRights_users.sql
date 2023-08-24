create table users
(
    id       int auto_increment
        primary key,
    username varchar(45) null,
    group_id int         null,
    constraint name_UNIQUE
        unique (username)
);

INSERT INTO CreaditOlineRights.users (id, username, group_id) VALUES (1, 'josh', 5);
INSERT INTO CreaditOlineRights.users (id, username, group_id) VALUES (3, 'josh2', 5);
