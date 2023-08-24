create table rights
(
    id                int auto_increment
        primary key,
    user_id           int null,
    module_id         int null,
    modulefunction_id int null,
    group_id          int null
);

INSERT INTO CreaditOlineRights.rights (id, user_id, module_id, modulefunction_id, group_id) VALUES (10, null, 1, null, 5);
INSERT INTO CreaditOlineRights.rights (id, user_id, module_id, modulefunction_id, group_id) VALUES (11, 3, null, 11, null);
