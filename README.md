# About the project

**INODEBOX** is a simple file manager CRUD application. The term inode is borrowed from UNIX where it serves as an umbrella term for files and directories.

The project is designed in a RESTful manner following most best practices: user-friendly and intuitive route names, plural nouns in lower-case, respective HTTP verbs.
There are two create endpoints: one for a directory and one for a file, since under normal circumstances they would have different payloads (besides the name the file endpoint would also receive the file contents). But also, the consumer does not need to know how the backend differentiates between the two types of inodes.

**NB!** All routes manipulating a single inode expect an id parameter (e.g. `/api/nodes/{id}`), but due to the Laravel's built-in route model binding the parameter is called `{inode}` and Laravel injects it automatically inside the controller (or throws a `ModelNotFoundException` in case no inode can be found using the respective ID).

## Naming conventions

* route names correspond with controller methods: `[DELETE] /api/inodes/{id}` → `InodeController@delete_inode`
* controller methods correspond with form requests: `InodeController@delete_inode` → `DeleteInodeRequest`
* form requests correspond with actions classes: `DeleteInodeRequest` → `DeleteInodeAction`
* action classes utilize rule pools (where applicable): `DeleteInodeAction` → `DeleteInodeStateRulePool`

## Other notes

1. There are two types of validation taking place:
* input validation carried out in the form requests using the `rules` method
* state validation against a database object (Inode in this case) carried out in the action classes using the custom `StateValidator`
2. All exceptions (validation or other) are rendered using a custom `ErrorResource` class which guarantees consistency across all endpoints.
3. Resource property is redeclared together with its type in order for intellisense to be reliable instead of using proxy method to dynamically access resource properties and/or methods.
4. The `CreateInodeIndex` listener is not queued and as a result is executed in a synchronous manner (in order to have the updated index available right away). For that reason, the actions which dispatch the `InodeCreated` event create the respective inode using `new Inode` and then calling `$inode->parent()->associate($parent)`. This way the parent is directly available by calling the `$inode->parent` property. If the inodes were to be created through the parent (`$inode = $parent->children()->create()`), calling `$inode->parent` would trigger a redundant SQL query to fetch the parent. When there are hundreds of thousands of inodes, the index listeners would most probably be queued, so the listeners will be executed by another process (which means the parent won't be present either way since the event's model are serialized).

# How to set up locally

1. Clone repository
2. Copy `.env.example` to `.env`. Feel free to overwrite docker forwarding ports.
3. Build docker images: `docker compose build`
4. Start docker images: `docker compose up -d` (in detached mode)
5. Install dependencies: `docker exec -it inodebox-app composer install`
6. Generate an app key: `docker exec -it inodebox-app php artisan key:generate`
7. Run migrations and execute seeders on both environments:
* `docker exec -it inodebox-app php artisan migrate --seed`
* `docker exec -it inodebox-app php artisan migrate --seed --env=testing`
8. Make sure tests work: `docker exec -it inodebox-app php artisan test tests/`
9. Open the project in a browser: http://localhost:8000 (using the respective forwarding port)

