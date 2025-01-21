# What's this?
This is my implementation of the 🍎🥕 Fruits and Vegetables assignment as outlined in [this document](assignment.md)

* My implementation uses MySQL V9.1.0 MySQL Community Server
* PHP is updated to 8.3.15
* Symfony updated to V7.2

# 🏗️ Building and 🏃🏾Running

* Build (aka first run) with
```bash
    docker compose up -d --build  && docker compose exec -u root roadsurferCodingTask sh -c 'composer install -n'
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

get item with id 12
`curl http://localhost:8080/api/v1/produce/12`
get item with id 12, displayed in kg
`curl http://localhost:8080/api/v1/produce/12?unit=kg`
list all fruit items in kg
`curl http://localhost:8080/api/v1/produce?type=fruit&unit=kg`
list all produce items with the name bananas
`curl http://localhost:8080/api/v1/produce?name=bananas`



## 🎯 Goal
We want to build a service which will take a `request.json` and:
* Process the file and create two separate collections for `Fruits` and `Vegetables`
* Each collection has methods like `add()`, `remove()`, `list()`;
* Units have to be stored as grams;
* Store the collections in a storage engine of your choice. (e.g. Database, In-memory)
* Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
* Provide another API endpoint to add new items to the collections (i.e., your storage engine).
* As a bonus you might:
  * consider giving option to decide which units are returned (kilograms/grams);
  * how to implement `search()` method collections;
  * use latest version of Symfony's to embbed your logic

### ✔️ How can I check if my code is working?
You have two ways of moving on:
* You call the Service from PHPUnit test like it's done in dummy test (just run `bin/phpunit` from the console)

or

* You create a Controller which will be calling the service with a json payload

## 💡 Hints before you start working on it
* Keep KISS, DRY, YAGNI, SOLID principles in mind
* Timebox your work - we expect that you would spend between 3 and 4 hours.
* Your code should be tested

## When you are finished
* Please upload your code to a public git repository (i.e. GitHub, Gitlab)

## 🐳 Docker image
Optional. Just here if you want to run it isolated.

### 📥 Pulling image
```bash
docker pull tturkowski/fruits-and-vegetables
```

### 🧱 Building image
```bash
docker build -t tturkowski/fruits-and-vegetables -f docker/Dockerfile .
```

### 🏃‍♂️ Running container
```bash
docker run -it -w/app -v$(pwd):/app tturkowski/fruits-and-vegetables sh
```

### 🛂 Running tests
```bash
docker run -it -w/app -v$(pwd):/app tturkowski/fruits-and-vegetables bin/phpunit
```

### ⌨️ Run development server
```bash
docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables php -S 0.0.0.0:8080 -t /app/public
# Open http://127.0.0.1:8080 in your browser
```
