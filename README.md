# What's this?
This is my implementation of the 🍎🥕 Fruits and Vegetables assignment as outlined in [this document](assignment.md)

* My implementation uses MySQL V9.1.0 MySQL Community Server
* PHP is updated to 8.3.15
* Symfony updated to V7.2

# 🏗️ Building and 🏃🏾Running

* Build (aka first run) with
```bash
    docker compose up -d --build  && docker exec -u root roadsurferCodingTask sh -c 'composer install -n'
```

* Running the tests
```bash
docker exec -it roadsurferCodingTask ./bin/phpunit
```

* Importing the `request.json`
```bash
docker exec -it roadsurferCodingTask ./bin/console import -v ./request.json
```

* Api endpoint

Use GET requests to query the whole collection
```bash
http://localhost:8080/api/v1/produce
```
or get one record by it's id
```bash
http://localhost:8080/api/v1/produce/{id}
```
you can select filters with query parameters:
- type list only item of one type
- unit display weight in gram / kilogram
- name filter by name

Examples:

- get item with id 12 http://localhost:8080/api/v1/produce/12
- get item with id 12, displayed in kg http://localhost:8080/api/v1/produce/12?unit=kg
- list all fruit items in kg http://localhost:8080/api/v1/produce?type=fruit&unit=kg
- list all produce items with the name bananas http://localhost:8080/api/v1/produce?name=bananas

* Use POST request to create a new produce item

```bash
curl -X POST 'http://localhost:8080/api/v1/produce' -d '{"id": 21, "name": "Green beans", "type": "vegetable", "quantity": 150, "unit": "g"}' -H "Content-Type: application/json"
```

note, this is CREATE only; when specifying an id field that already exists it will refuse to create the item.
update (or delete) funcionality was not requested in the assignment.
you can ommit the id in the POST data and the API will automatically one and persist the item, for example:

```bash
curl -X POST 'http://localhost:8080/api/v1/produce' -d '{"name": "Black beans", "type": "vegetable", "quantity": 2, "unit": "kg"}' -H "Content-Type: application/json"
```

# Misc:
If needed, You can empty the database with
```bash
mysql -h localhost -P 3306 -p produce --password=demo --protocol=tcp -u demo -e "use produce; truncate produce;"
```
