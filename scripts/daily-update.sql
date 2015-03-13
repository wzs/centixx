-- "resetowanie" roli użytkowników będacych manadzerami grup w zakonczonych projektach
UPDATE groups g
JOIN projects p ON p.project_id = g.group_project
JOIN users u ON u.user_id = g.group_manager
SET u.user_role = DEFAULT
WHERE p.project_stop < NOW()