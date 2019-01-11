# MMPI API


# Installation

You need to clone the project with submodules `--recurse-submodules`

```
git clone --recurse-submodules {url}
composer install
```

# Configuration

Just copy **.env.example** file in **.env** and change database settings

# Run with Docker
```
./start.sh 
```

You can adjust containers settings in **docker-compose.yml** file


# Testing

All tests are located in **tests** directory

```
composer test

// run just the functional tests
composer test:functional

// run just the unit tests
composer test:unit 
```

If running docker: 
```
docker exec {container name or hash} composer test
```